@extends('layouts.public')

@section('title', $agent->name . ' - Agent Profile')
@section('meta_description', $agent->bio ? Str::limit($agent->bio, 160) : 'View ' . $agent->name . '\'s profile and property listings at SÀ Property.')

@section('content')
    <!-- Breadcrumb -->
    <section class="bg-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-sm">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#b91111]">Home</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('agents.index') }}" class="text-gray-500 hover:text-[#b91111]">Agents</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900">{{ $agent->name }}</span>
            </nav>
        </div>
    </section>

    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Agent Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden sticky top-24">
                        <!-- Agent Photo -->
                        <div class="h-64 bg-gray-200">
                            @if($agent->photo)
                                <img src="{{ asset('storage/' . $agent->photo) }}" alt="{{ $agent->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-400 to-[#b91111]">
                                    <span class="text-8xl font-bold text-white">{{ substr($agent->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-6">
                            <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $agent->name }}</h1>
                            <p class="text-[#b91111] font-medium mb-4">Real Estate Agent</p>

                            <!-- Contact Info -->
                            <div class="space-y-3 mb-6">
                                @if($agent->email)
                                    <a href="mailto:{{ $agent->email }}" class="flex items-center text-gray-600 hover:text-[#b91111] transition">
                                        <svg class="w-5 h-5 mr-3 text-[#d41313]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        {{ $agent->email }}
                                    </a>
                                @endif
                                @if($agent->phone)
                                    <a href="tel:{{ $agent->phone }}" class="flex items-center text-gray-600 hover:text-[#b91111] transition">
                                        <svg class="w-5 h-5 mr-3 text-[#d41313]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        {{ $agent->phone }}
                                    </a>
                                @endif
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-2 gap-4 py-4 border-y">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-[#b91111]">{{ $activeListings->count() }}</div>
                                    <div class="text-sm text-gray-500">Active Listings</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-[#b91111]">{{ $agent->listings->count() }}</div>
                                    <div class="text-sm text-gray-500">Total Listings</div>
                                </div>
                            </div>

                            <!-- Bio -->
                            @if($agent->bio)
                                <div class="mt-6">
                                    <h3 class="font-semibold text-gray-900 mb-2">About</h3>
                                    <p class="text-gray-600 text-sm leading-relaxed">{{ $agent->bio }}</p>
                                </div>
                            @endif

                            <!-- Contact Button -->
                            <div class="mt-6">
                                <a href="mailto:{{ $agent->email }}" class="block w-full bg-[#d41313] hover:bg-[#b91111] text-white text-center py-3 rounded-lg font-semibold transition">
                                    Contact Agent
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agent Listings -->
                <div class="lg:col-span-2">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Active Listings</h2>
                        <span class="text-gray-500">{{ $activeListings->count() }} {{ Str::plural('property', $activeListings->count()) }}</span>
                    </div>

                    @if($activeListings->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($activeListings as $listing)
                                @if($listing->property)
                                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                                        <!-- Property Image -->
                                        <div class="relative h-48 bg-gray-200">
                                            @if($listing->property->images)
                                                @php
                                                    $images = is_array($listing->property->images) ? $listing->property->images : json_decode($listing->property->images, true);
                                                @endphp
                                                @if(!empty($images) && isset($images[0]))
                                                    <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $listing->property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                                </div>
                                            @endif
                                            <div class="absolute top-3 left-3">
                                                <span class="bg-[#d41313] text-white px-2 py-1 rounded-full text-xs font-medium capitalize">{{ $listing->property->type }}</span>
                                            </div>
                                        </div>

                                        <!-- Property Details -->
                                        <div class="p-4">
                                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-[#b91111] transition mb-1 truncate">
                                                <a href="{{ route('properties.show', $listing->property) }}">{{ $listing->property->title }}</a>
                                            </h3>
                                            <p class="text-gray-500 text-sm mb-3 flex items-center truncate">
                                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                {{ $listing->property->address }}
                                            </p>
                                            <div class="flex items-center gap-3 text-xs text-gray-600 mb-3">
                                                @if($listing->property->bedrooms)
                                                    <span>{{ $listing->property->bedrooms }} Beds</span>
                                                @endif
                                                @if($listing->property->bathrooms)
                                                    <span>{{ $listing->property->bathrooms }} Baths</span>
                                                @endif
                                                @if($listing->property->area)
                                                    <span>{{ number_format($listing->property->area) }} sqft</span>
                                                @endif
                                            </div>
                                            <div class="flex justify-between items-center pt-3 border-t">
                                                <span class="text-xl font-bold text-[#b91111]">${{ number_format($listing->price ?? $listing->property->price) }}</span>
                                                <a href="{{ route('properties.show', $listing->property) }}" class="text-[#b91111] hover:text-[#990e0e] text-sm font-medium">
                                                    Details →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-100 rounded-lg">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Active Listings</h3>
                            <p class="text-gray-600">This agent doesn't have any active listings at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
