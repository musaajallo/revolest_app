@extends('layouts.public')

@section('title', $page?->meta_title ?? 'Contact Us')
@section('meta_description', $page?->meta_description ?? 'Get in touch with SÀ Property. We\'re here to help with all your real estate needs.')

@php
    // Use CMS page content first, then fall back to Site Settings
    $contactEmail = $page?->getContent('email_1') ?? \App\Models\Setting::get('contact_email', 'info@saproperty.gm');
    $contactPhone = $page?->getContent('phone_1') ?? \App\Models\Setting::get('contact_phone', '+220 123 4567');
    $contactPhone2 = $page?->getContent('phone_2') ?? \App\Models\Setting::get('contact_phone_2');
    $contactAddress = $page?->getContent('office_address') ?? \App\Models\Setting::get('contact_address', "Kairaba Avenue\nSerrekunda, The Gambia");
    $businessHours = $page?->getContent('business_hours') ?? \App\Models\Setting::get('business_hours', "Mon - Fri: 9:00 AM - 6:00 PM\nSat: 10:00 AM - 4:00 PM\nSun: Closed");
@endphp

@section('content')
    <!-- Success Modal -->
    @if(session('success'))
    <div id="success-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 transform transition-all">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Message Sent!</h3>
                <p class="text-gray-600 mb-6">{{ session('success') }}</p>
                <button onclick="closeModal()" class="bg-[#d41313] hover:bg-[#b91111] text-white px-6 py-2 rounded-lg font-semibold transition">
                    Close
                </button>
            </div>
        </div>
    </div>
    <script>
        function closeModal() {
            document.getElementById('success-modal').remove();
        }
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>
    @endif

    <!-- Page Header -->
    <section class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $page?->getContent('page_title') ?? 'Contact Us' }}</h1>
            <p class="text-gray-400">{{ $page?->getContent('page_subtitle') ?? "We'd love to hear from you. Get in touch with our team." }}</p>
        </div>
    </section>

    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Contact Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ $page?->getContent('get_in_touch_title') ?? 'Get In Touch' }}</h2>

                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-[#b91111]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-900">Our Office</h3>
                                    <p class="text-gray-600 mt-1">{!! nl2br(e($contactAddress)) !!}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-[#b91111]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-900">Email Us</h3>
                                    <p class="text-gray-600 mt-1">
                                        <a href="mailto:{{ $contactEmail }}" class="hover:text-[#b91111] transition">{{ $contactEmail }}</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-[#b91111]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-900">Call Us</h3>
                                    <p class="text-gray-600 mt-1">
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}" class="hover:text-[#b91111] transition">{{ $contactPhone }}</a>
                                        @if($contactPhone2)
                                        <br><a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone2) }}" class="hover:text-[#b91111] transition">{{ $contactPhone2 }}</a>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-[#b91111]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-900">Business Hours</h3>
                                    <p class="text-gray-600 mt-1">{!! nl2br(e($businessHours)) !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600 hover:bg-[#d41313] hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600 hover:bg-[#d41313] hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm3 8h-1.35c-.538 0-.65.221-.65.778v1.222h2l-.209 2h-1.791v7h-3v-7h-2v-2h2v-2.308c0-1.769.931-2.692 3.029-2.692h1.971v3z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600 hover:bg-[#d41313] hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600 hover:bg-[#d41313] hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md p-6 md:p-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-2">{{ $page?->getContent('form_title') ?? 'Send Us a Message' }}</h2>
                        <p class="text-gray-600 mb-6">{{ $page?->getContent('form_subtitle') ?? "Fill out the form below and we'll get back to you as soon as possible." }}</p>

                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                                    <input type="text" name="name" required value="{{ old('name') }}"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent @error('name') border-red-500 @enderror"
                                           placeholder="John Doe">
                                    @error('name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                    <input type="email" name="email" required value="{{ old('email') }}"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent @error('email') border-red-500 @enderror"
                                           placeholder="john@example.com">
                                    @error('email')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent"
                                           placeholder="+1 (555) 123-4567">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                                    <select name="subject" required
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent @error('subject') border-red-500 @enderror">
                                        <option value="">Select a subject</option>
                                        <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                        <option value="Property Inquiry" {{ old('subject') == 'Property Inquiry' ? 'selected' : '' }}>Property Inquiry</option>
                                        <option value="Schedule Viewing" {{ old('subject') == 'Schedule Viewing' ? 'selected' : '' }}>Schedule Viewing</option>
                                        <option value="List My Property" {{ old('subject') == 'List My Property' ? 'selected' : '' }}>List My Property</option>
                                        <option value="Become an Agent" {{ old('subject') == 'Become an Agent' ? 'selected' : '' }}>Become an Agent</option>
                                        <option value="Support" {{ old('subject') == 'Support' ? 'selected' : '' }}>Support</option>
                                        <option value="Other" {{ old('subject') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('subject')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                                    <textarea name="message" rows="6" required
                                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent @error('message') border-red-500 @enderror"
                                              placeholder="How can we help you?">{{ old('message') }}</textarea>
                                    @error('message')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="w-full md:w-auto bg-[#d41313] hover:bg-[#b91111] text-white px-8 py-3 rounded-lg font-semibold transition">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(\App\Models\Setting::get('google_maps_enabled') && \App\Models\Setting::get('google_maps_api_key'))
                <div id="map" class="rounded-xl h-96 w-full"></div>

                @push('scripts')
                <script>
                    function initMap() {
                        const location = {
                            lat: {{ \App\Models\Setting::get('google_maps_default_lat', '13.4549') }},
                            lng: {{ \App\Models\Setting::get('google_maps_default_lng', '-16.5790') }}
                        };

                        const map = new google.maps.Map(document.getElementById("map"), {
                            zoom: {{ \App\Models\Setting::get('google_maps_default_zoom', '14') }},
                            center: location,
                            styles: [
                                {
                                    "featureType": "poi",
                                    "elementType": "labels",
                                    "stylers": [{ "visibility": "off" }]
                                }
                            ]
                        });

                        const marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            title: "{{ \App\Models\Setting::get('site_name', 'SÀ Property') }}",
                            animation: google.maps.Animation.DROP
                        });

                        const infowindow = new google.maps.InfoWindow({
                            content: `<div class="p-2">
                                <strong>{{ \App\Models\Setting::get('site_name', 'SÀ Property') }}</strong><br>
                                {{ \App\Models\Setting::get('contact_address', '') }}
                            </div>`
                        });

                        marker.addListener("click", () => {
                            infowindow.open(map, marker);
                        });
                    }
                </script>
                <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\Setting::get('google_maps_api_key') }}&callback=initMap"></script>
                @endpush
            @else
                <div class="bg-gray-300 rounded-xl h-96 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <p class="text-gray-500">Map integration can be added here</p>
                        <p class="text-gray-400 text-sm">Google Maps or Leaflet</p>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
