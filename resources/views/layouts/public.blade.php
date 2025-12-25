@php
    $siteName = \App\Models\Setting::get('site_name', 'SÀ Property');
    $siteTagline = \App\Models\Setting::get('site_tagline', 'Your trusted real estate partner');
    $contactEmail = \App\Models\Setting::get('contact_email', 'info@saproperty.gm');
    $contactPhone = \App\Models\Setting::get('contact_phone', '+220 123 4567');
    $contactAddress = \App\Models\Setting::get('contact_address', 'Kairaba Avenue, Serrekunda, The Gambia');
    $footerText = \App\Models\Setting::get('footer_text', 'Your trusted partner in real estate. We help you find the perfect property for your needs.');
    $facebookUrl = \App\Models\Setting::get('facebook_url');
    $twitterUrl = \App\Models\Setting::get('twitter_url');
    $instagramUrl = \App\Models\Setting::get('instagram_url');
    $linkedinUrl = \App\Models\Setting::get('linkedin_url');
    $youtubeUrl = \App\Models\Setting::get('youtube_url');

    // WhatsApp settings
    $whatsappEnabled = \App\Models\Setting::get('whatsapp_enabled', false);
    $whatsappPhone = \App\Models\Setting::get('whatsapp_phone_number');
    $whatsappMessage = \App\Models\Setting::get('whatsapp_default_message', 'Hello! I\'m interested in your properties.');
    $whatsappShowButton = \App\Models\Setting::get('whatsapp_show_floating_button', true);
@endphp
<!DOCTYPE html>
<html lang="en" x-data="{
    darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
    }
}" x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', $siteName . ' - ' . $siteTagline)">
    <title>@yield('title', $siteName) - Real Estate Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-200">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-[#d41313] rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">SÀ</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">{{ str_replace('SÀ Property', 'Property', $siteName) }}</span>
                    </a>
                </div>

                <!-- Search Bar (Desktop) -->
                <div class="hidden lg:flex items-center flex-1 max-w-md mx-8">
                    <form action="{{ route('properties.index') }}" method="GET" class="w-full">
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   placeholder="Search properties..."
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#d41313] focus:border-transparent transition-colors">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-700 dark:text-gray-300 hover:text-[#d41313] dark:hover:text-[#d41313] font-medium transition {{ request()->routeIs('home') ? 'text-[#d41313]' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('properties.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-[#d41313] dark:hover:text-[#d41313] font-medium transition {{ request()->routeIs('properties.*') ? 'text-[#d41313]' : '' }}">
                        Properties
                    </a>
                    <a href="{{ route('agents.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-[#d41313] dark:hover:text-[#d41313] font-medium transition {{ request()->routeIs('agents.*') ? 'text-[#d41313]' : '' }}">
                        Agents
                    </a>
                    <a href="{{ route('contact') }}" class="text-gray-700 dark:text-gray-300 hover:text-[#d41313] dark:hover:text-[#d41313] font-medium transition {{ request()->routeIs('contact') ? 'text-[#d41313]' : '' }}">
                        Contact
                    </a>

                    <!-- Dark Mode Toggle -->
                    <button @click="toggleDarkMode()"
                            class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
                        <!-- Sun icon (shown in dark mode) -->
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon icon (shown in light mode) -->
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    @auth
                        <a href="/admin" class="bg-[#d41313] hover:bg-[#b91111] text-white px-4 py-2 rounded-lg font-medium transition">
                            Admin
                        </a>
                    @else
                        <a href="/admin/login" class="text-gray-700 dark:text-gray-300 hover:text-[#d41313] font-medium transition">
                            Login
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center space-x-2">
                    <!-- Dark Mode Toggle (Mobile) -->
                    <button @click="toggleDarkMode()"
                            class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    <button type="button" id="mobile-menu-button" class="text-gray-700 dark:text-gray-300 hover:text-[#d41313] focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white dark:bg-gray-800 border-t dark:border-gray-700">
            <div class="px-4 py-3 space-y-2">
                <!-- Mobile Search -->
                <form action="{{ route('properties.index') }}" method="GET" class="mb-3">
                    <div class="relative">
                        <input type="text"
                               name="search"
                               placeholder="Search properties..."
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#d41313]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </form>
                <a href="{{ route('home') }}" class="block text-gray-700 dark:text-gray-300 hover:text-[#d41313] font-medium py-2">Home</a>
                <a href="{{ route('properties.index') }}" class="block text-gray-700 dark:text-gray-300 hover:text-[#d41313] font-medium py-2">Properties</a>
                <a href="{{ route('agents.index') }}" class="block text-gray-700 dark:text-gray-300 hover:text-[#d41313] font-medium py-2">Agents</a>
                <a href="{{ route('contact') }}" class="block text-gray-700 dark:text-gray-300 hover:text-[#d41313] font-medium py-2">Contact</a>
                <hr class="my-2 dark:border-gray-700">
                @auth
                    <a href="/admin" class="block bg-[#d41313] hover:bg-[#b91111] text-white px-4 py-2 rounded-lg font-medium text-center">Admin</a>
                @else
                    <a href="/admin/login" class="block text-gray-700 dark:text-gray-300 hover:text-[#d41313] font-medium py-2">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-[#d41313] rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">SÀ</span>
                        </div>
                        <span class="text-xl font-bold">{{ str_replace('SÀ ', '', $siteName) }}</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        {{ $footerText }}
                    </p>
                    <div class="flex space-x-4">
                        @if($twitterUrl)
                        <a href="{{ $twitterUrl }}" target="_blank" class="text-gray-400 hover:text-[#d41313] transition">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        @endif
                        @if($facebookUrl)
                        <a href="{{ $facebookUrl }}" target="_blank" class="text-gray-400 hover:text-[#d41313] transition">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm3 8h-1.35c-.538 0-.65.221-.65.778v1.222h2l-.209 2h-1.791v7h-3v-7h-2v-2h2v-2.308c0-1.769.931-2.692 3.029-2.692h1.971v3z"/></svg>
                        </a>
                        @endif
                        @if($instagramUrl)
                        <a href="{{ $instagramUrl }}" target="_blank" class="text-gray-400 hover:text-[#d41313] transition">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        @endif
                        @if($linkedinUrl)
                        <a href="{{ $linkedinUrl }}" target="_blank" class="text-gray-400 hover:text-[#d41313] transition">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                        @endif
                        @if($youtubeUrl)
                        <a href="{{ $youtubeUrl }}" target="_blank" class="text-gray-400 hover:text-[#d41313] transition">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-[#d41313] transition">Home</a></li>
                        <li><a href="{{ route('properties.index') }}" class="text-gray-400 hover:text-[#d41313] transition">Properties</a></li>
                        <li><a href="{{ route('agents.index') }}" class="text-gray-400 hover:text-[#d41313] transition">Agents</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-[#d41313] transition">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center space-x-2">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>{{ $contactAddress }}</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span>{{ $contactEmail }}</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            <span>{{ $contactPhone }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

    @if($whatsappEnabled && $whatsappShowButton && $whatsappPhone)
    <!-- Floating WhatsApp Button -->
    @php
        // Clean phone number - remove spaces, dashes, and ensure it starts with country code
        $cleanPhone = preg_replace('/[^0-9+]/', '', $whatsappPhone);
        // Remove leading + if present for the URL
        $cleanPhone = ltrim($cleanPhone, '+');
        $encodedMessage = urlencode($whatsappMessage);
        $whatsappUrl = "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    @endphp
    <a href="{{ $whatsappUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-50 bg-[#25D366] hover:bg-[#20BA5A] text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 group"
       aria-label="Chat on WhatsApp">
        <!-- WhatsApp Icon -->
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        <!-- Tooltip -->
        <span class="absolute right-full mr-3 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-sm px-3 py-2 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
            Chat with us on WhatsApp
        </span>
    </a>

    <style>
        /* Pulse animation for WhatsApp button */
        @keyframes whatsapp-pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }

        .fixed.bottom-6.right-6 {
            animation: whatsapp-pulse 2s infinite;
        }

        .fixed.bottom-6.right-6:hover {
            animation: none;
        }
    </style>
    @endif

    @stack('scripts')
</body>
</html>
