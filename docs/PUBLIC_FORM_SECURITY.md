# Public Form Hardening Standard

A portable checklist for any Laravel project that exposes public-facing form
endpoints (contact, lead capture, applications, feedback). The aim is not
"perfect" security — it's pushing the cost of spam high enough that bots move on
while keeping the UX seamless for humans.

This standard is implemented in this repo under `App\Support\HardensPublicForms`,
the `public-form` named rate limiter, and the `<x-public.forms.partials.*>`
components — copy them as-is into new projects.

## The five layers

Apply all five. Each layer catches a different attacker class. None of them is
sufficient alone.

### 1. Rate limiting (named limiter, scoped per IP + path)

**What:** Cap how many POSTs a single IP can make to a given form-path per
minute. Use a named limiter rather than the inline `throttle:5,1` so every form
gets its own bucket — otherwise a legitimate user submitting two different
forms back-to-back can be throttled.

**Where:** Defined in `App\Providers\AppServiceProvider::boot()`:

```php
RateLimiter::for('public-form', fn (Request $request) =>
    Limit::perMinute(5)->by($request->ip().':'.$request->path())
);
```

**Applied via:** `Route::middleware('throttle:public-form')->group(...)` in
`routes/web.php`.

**Tune:** 5/min/IP per path is the default. Forms that legitimately accept rapid
re-submission (e.g. a multi-step wizard saving each step) should get a higher
ceiling on a separate limiter.

### 2. Honeypot field

**What:** A hidden form input (e.g. `name="website"`) that humans never see and
never fill, but naive bots fill in indiscriminately. Reject any submission where
the field is non-empty.

**Why it works:** Catches the long tail of dumb bots that submit every visible
input. Costs nothing for legitimate users. Should NOT be your only line of
defense — sophisticated bots ignore honeypots.

**Implementation:**

- Add `<x-public.forms.partials.honeypot />` directly inside every `<form>` tag.
- The component renders a hidden text input with `tabindex="-1"`, `autocomplete="off"`,
  and `aria-hidden="true"`.
- The controller's hardening trait calls `validateHoneypot()` which throws a
  validation exception if the field is non-empty.

**Field name:** Pick something plausible (`website`, `url`, `homepage`) — bots
match on input type, not name, but a plausible name reduces false positives if
a password manager auto-fills the field.

### 3. Duplicate suppression

**What:** Hash the meaningful payload fields + the IP, and reject if the same
hash has been seen in the last N minutes. Catches retry-storm bots and
accidental double-submits.

**Why:** Bots that pass the honeypot often re-submit the same payload many
times. This kills that without affecting humans (who rarely re-submit identical
content).

**Implementation:**

```php
// in HardensPublicForms
protected function assertNotDuplicate(Request $request, string $key, array $signatureFields, int $ttlMinutes = 5): void
{
    $signature = collect($signatureFields)
        ->mapWithKeys(fn ($f) => [$f => $request->input($f)])
        ->toArray();

    $hash = hash('sha256', $key.'|'.$request->ip().'|'.json_encode($signature));
    $cacheKey = "public-form:dup:$hash";

    if (Cache::has($cacheKey)) {
        throw ValidationException::withMessages([
            '_form' => 'This submission looks like a duplicate. Please wait a few minutes before re-submitting.',
        ]);
    }
    Cache::put($cacheKey, 1, now()->addMinutes($ttlMinutes));
}
```

**Caveats:** Pick signature fields that are *content* (name, email, message) —
not timestamps or random tokens. The cache TTL should be short enough that a
human re-submitting after fixing a typo isn't blocked (5 minutes is a sane
default).

### 4. Cloudflare Turnstile (or hCaptcha)

**What:** A privacy-preserving CAPTCHA that runs client-side and gives you a
token to verify server-side. No image-puzzle UX cost when the user looks human.

**Why Turnstile (not Google reCAPTCHA):** No user tracking, free at production
scale, no Google dependency, and Cloudflare's bot detection is the strongest
publicly available.

**Configuration:**

- `services.turnstile.site_key` and `services.turnstile.secret_key` in
  `config/services.php`, populated from `TURNSTILE_SITE_KEY` /
  `TURNSTILE_SECRET_KEY` env vars.
- The blade component `<x-public.forms.partials.turnstile />` renders the widget
  only if the site key is configured. **If the site key is unset, both the
  client widget and server check no-op silently** — this is intentional, so dev
  environments work without Turnstile keys.

**Server-side check:** The hardening trait POSTs the `cf-turnstile-response`
field to `https://challenges.cloudflare.com/turnstile/v0/siteverify` with the
secret key. On failure, throws a validation exception.

**Tip:** Turnstile's "managed" mode is invisible for ~99% of users. Don't switch
to "interactive" unless you're seeing real abuse — UX cost is real.

### 5. WAF / edge bot protection (Cloudflare)

**What:** Push attackers off your origin entirely by terminating bots at
Cloudflare's edge.

**Configuration (no code change):**

- Cloudflare → Security → Bots → enable "Bot Fight Mode" (free) or "Super Bot
  Fight Mode" (paid, much better signals).
- Cloudflare → WAF → enable the Managed Ruleset.
- Add a custom rule for `/forms/*` that challenges any request scoring < 30 on
  Cloudflare's bot score.

**Why it's listed last:** It's the most effective layer but sits outside your
codebase, so it can't be version-controlled or required in CI. Treat it as a
runtime backstop; don't rely on it as your only line of defense.

## Applying this to a new project

1. Copy `app/Support/HardensPublicForms.php` and the rate-limiter registration
   in `AppServiceProvider::boot()`.
2. Copy `resources/views/components/public/forms/partials/{honeypot,turnstile}.blade.php`.
3. Add the Turnstile config block to `config/services.php`.
4. In every public form's controller method, `use HardensPublicForms;` then call
   `$this->applyPublicFormHardening($request, signature: ['email', 'phone'])` at
   the top of the action — before validation. Adjust the signature fields per
   form.
5. In every public form's blade, drop `<x-public.forms.partials.honeypot />` and
   `<x-public.forms.partials.turnstile />` directly inside the `<form>` tag.
6. In `routes/web.php`, wrap the POST routes in
   `Route::middleware('throttle:public-form')->group(...)`.
7. Set `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY` in production `.env`.
   Leave them blank in dev.
8. (Optional, recommended) Enable Cloudflare Bot Fight Mode and a `/forms/*` WAF
   rule.

## What this standard does NOT cover

- **Authenticated form abuse** — once you have logged-in users, rate-limit per
  user ID, not IP, and use the application's existing authorization layer.
- **File upload abuse** — public file uploads need their own hardening (size
  limits, MIME validation, virus scanning, isolated storage). Don't accept
  public file uploads if you can avoid it.
- **Email-based forms** — if a form's only effect is sending an email, also
  verify the email isn't being used to relay spam by validating the recipient
  against an allow-list rather than `request->input('to')`.
