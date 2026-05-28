@php
    $siteName = \App\Models\Setting::get('site_name', 'Revolest');
    $siteLogo = \App\Models\Setting::get('site_logo');
    $siteLogoUrl = $siteLogo ? \Illuminate\Support\Facades\Storage::url($siteLogo) : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('code') · {{ $siteName }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --brand-green: #1c4736;
            --brand-orange: #a94a2a;
            --brand-orange-dark: #8a3c22;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: radial-gradient(ellipse at top, #f3f4f6 0%, #e5e7eb 100%);
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
            -webkit-font-smoothing: antialiased;
        }
        .card {
            width: 100%;
            max-width: 32rem;
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 50px -12px rgba(0,0,0,.18);
            padding: 3rem 2.5rem;
            text-align: center;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 2rem;
        }
        .brand img { height: 2.5rem; width: auto; object-fit: contain; }
        .brand .badge {
            width: 2.25rem; height: 2.25rem; border-radius: .6rem;
            background: var(--brand-green); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.15rem;
        }
        .brand .name { font-weight: 700; font-size: 1.15rem; color: #111827; }
        .code {
            font-size: 4.5rem;
            line-height: 1;
            font-weight: 700;
            color: var(--brand-green);
            letter-spacing: -.02em;
        }
        .title { font-size: 1.35rem; font-weight: 600; margin-top: 1rem; color: #111827; }
        .message { margin-top: .75rem; color: #6b7280; font-size: .975rem; line-height: 1.6; }
        .actions { margin-top: 2rem; display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: .7rem 1.5rem; border-radius: .6rem;
            font-weight: 600; font-size: .925rem; text-decoration: none;
            transition: background-color .15s, color .15s, border-color .15s;
        }
        .btn-primary { background: var(--brand-orange); color: #fff; }
        .btn-primary:hover { background: var(--brand-orange-dark); }
        .btn-secondary { background: transparent; color: var(--brand-green); border: 1.5px solid #d1d5db; }
        .btn-secondary:hover { border-color: var(--brand-green); }
        .divider { height: 1px; background: #e5e7eb; margin: 2rem 0 0; }
        .ref { margin-top: 1.5rem; font-size: .8rem; color: #9ca3af; }
        @media (prefers-color-scheme: dark) {
            body { background: radial-gradient(ellipse at top, #1f2937 0%, #111827 100%); color: #f9fafb; }
            .card { background: #1f2937; box-shadow: 0 20px 50px -12px rgba(0,0,0,.5); }
            .brand .name, .title { color: #f9fafb; }
            .code { color: #34d399; }
            .message { color: #9ca3af; }
            .btn-secondary { color: #34d399; border-color: #374151; }
            .btn-secondary:hover { border-color: #34d399; }
            .divider { background: #374151; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            @if($siteLogoUrl)
                <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" />
            @else
                <span class="badge">R</span>
                <span class="name">{{ $siteName }}</span>
            @endif
        </div>

        <div class="code">@yield('code')</div>
        <h1 class="title">@yield('title')</h1>
        <p class="message">@yield('message')</p>

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
            <a href="{{ url('/properties') }}" class="btn btn-secondary">Browse Properties</a>
        </div>

        <div class="divider"></div>
        <p class="ref">{{ $siteName }} — @yield('code') error</p>
    </div>
</body>
</html>
