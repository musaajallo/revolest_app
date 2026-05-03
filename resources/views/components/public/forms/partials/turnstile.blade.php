@php($siteKey = config('services.turnstile.site_key'))

@if(filled($siteKey))
    {{-- Cloudflare Turnstile widget. Renders only when Turnstile is configured;
         dev environments without keys silently skip both client and server checks. --}}
    <div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="light"></div>

    @once
        @push('scripts')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endpush
    @endonce

    @error('_form')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror
@endif
