@extends('layouts.public')

@section('title', $page?->meta_title ?? 'Our Agents')
@section('meta_description', $page?->meta_description ?? 'Meet our professional real estate agents. Our experienced team is ready to help you find your perfect property.')

@section('content')
    <!-- Page Header -->
    <section class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $page?->getContent('page_title') ?? 'Our Agents' }}</h1>
            <p class="text-gray-400">{{ $page?->getContent('page_subtitle') ?? 'Meet our professional and dedicated real estate agents' }}</p>
        </div>
    </section>

    <!-- Agents Grid -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($agents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($agents as $agent)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                            <!-- Agent Photo -->
                            <div class="relative h-64 bg-gray-200">
                                @if($agent->photo)
                                    <img src="{{ asset('storage/' . $agent->photo) }}" alt="{{ $agent->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" onerror="this.onerror=null; this.src='{{ asset('images/placeholder-agent.svg') }}'">
                                @else
                                    <img src="{{ asset('images/placeholder-agent.svg') }}" alt="No photo available" class="w-full h-full object-cover">
                                @endif
                            </div>

                            <!-- Agent Info -->
                            <div class="p-5 text-center">
                                <h3 class="text-xl font-semibold text-gray-900 mb-1">
                                    <a href="{{ route('agents.show', $agent) }}" class="hover:text-[#a94a2a] transition">{{ $agent->name }}</a>
                                </h3>
                                <p class="text-[#a94a2a] font-medium mb-3">Real Estate Agent</p>

                                <div class="space-y-2 text-sm text-gray-600 mb-4">
                                    @if($agent->email)
                                        <p class="flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                            {{ $agent->email }}
                                        </p>
                                    @endif
                                    @if($agent->phone)
                                        <p class="flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                            {{ $agent->phone }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center justify-center gap-2 text-sm text-gray-500 mb-4">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    <span>{{ $agent->listings_count }} {{ Str::plural('Listing', $agent->listings_count) }}</span>
                                </div>

                                <a href="{{ route('agents.show', $agent) }}" class="inline-block w-full bg-[#a94a2a] hover:bg-[#8a3c22] text-white py-2 rounded-lg font-semibold transition">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $agents->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-gray-100 rounded-lg">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Agents Found</h3>
                    <p class="text-gray-600">We're currently building our team. Check back soon!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Join Our Team CTA -->
    <section class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#a94a2a] rounded-2xl p-8 md:p-12 text-center">
                <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Become an Agent</h2>
                <p class="text-red-100 mb-6 max-w-2xl mx-auto">
                    Are you a licensed real estate professional? Join our team and connect with clients looking for their dream properties.
                </p>
                <a href="{{ route('contact') }}" class="inline-block bg-white text-[#a94a2a] hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition">
                    Contact Us to Apply
                </a>
            </div>
        </div>
    </section>
@endsection
