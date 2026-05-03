<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Bot-spam hardening helpers for public form controllers.
 *
 * Apply at the top of every public POST handler:
 *
 *   $this->applyPublicFormHardening($request, signature: ['email', 'phone']);
 *
 * Layered defense — see docs/PUBLIC_FORM_SECURITY.md for the rationale.
 */
trait HardensPublicForms
{
    /**
     * Run all client-side bot defenses against the request. Throws
     * ValidationException on failure so the user sees the same UX as a
     * regular validation error.
     *
     * @param  array<int,string>  $signature  Field names to use for duplicate detection.
     */
    protected function applyPublicFormHardening(
        Request $request,
        array $signature = [],
        string $honeypotField = 'website',
    ): void {
        $this->validateHoneypot($request, $honeypotField);

        if ($signature) {
            $this->assertNotDuplicate($request, $request->path(), $signature);
        }

        $this->verifyTurnstile($request);
    }

    /**
     * Reject the submission if the honeypot field is non-empty. Bots tend to
     * fill every input they find; humans never see this one.
     */
    protected function validateHoneypot(Request $request, string $field = 'website'): void
    {
        if (filled($request->input($field))) {
            // Don't tell the bot why it failed — surface a generic 422.
            throw ValidationException::withMessages([
                $field => 'Submission rejected.',
            ]);
        }
    }

    /**
     * Reject if the same payload has been submitted from the same IP within
     * the cache TTL. Catches retry-storm bots and accidental double-submits
     * without bothering humans (who rarely re-submit identical content).
     *
     * @param  array<int,string>  $signatureFields
     */
    protected function assertNotDuplicate(
        Request $request,
        string $key,
        array $signatureFields,
        int $ttlMinutes = 5,
    ): void {
        $signature = collect($signatureFields)
            ->mapWithKeys(fn (string $field) => [$field => (string) $request->input($field, '')])
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

    /**
     * Verify the Cloudflare Turnstile token if Turnstile is configured.
     * Silently no-ops when no secret key is set so dev/test environments
     * work without Turnstile credentials.
     */
    protected function verifyTurnstile(Request $request): void
    {
        $secret = config('services.turnstile.secret_key');

        if (blank($secret)) {
            return;
        }

        $token = $request->input('cf-turnstile-response');

        if (blank($token)) {
            throw ValidationException::withMessages([
                '_form' => 'Please complete the human-verification challenge.',
            ]);
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (ConnectionException $e) {
            // Don't lock users out if Cloudflare is unreachable — log and
            // fail open. The other layers (rate limit, honeypot, dup-check)
            // still apply.
            Log::warning('Turnstile verify unreachable; failing open', ['error' => $e->getMessage()]);

            return;
        }

        if (! $response->successful() || ! ($response->json('success') === true)) {
            throw ValidationException::withMessages([
                '_form' => 'Human-verification check failed. Please try again.',
            ]);
        }
    }
}
