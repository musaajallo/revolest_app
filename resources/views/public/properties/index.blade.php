@extends('layouts.public')

@section('title', $page?->meta_title ?? 'Properties')
@section('meta_description', $page?->meta_description ?? 'Browse our extensive collection of properties for sale and rent. Find houses, apartments, condos, and more.')

@section('content')
    <!-- Page Header -->
    <section class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $page?->getContent('page_title') ?? 'Properties' }}</h1>
            <p class="text-gray-400">{{ $page?->getContent('page_subtitle') ?? 'Find your perfect property from our extensive listings' }}</p>
        </div>
    </section>

    <!-- Search & Filter -->
    <section class="bg-white shadow-sm py-6 sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('properties.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by location or property name..."
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent">
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:w-auto">
                    <select name="type" class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>House</option>
                        <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="condo" {{ request('type') == 'condo' ? 'selected' : '' }}>Condo</option>
                        <option value="land" {{ request('type') == 'land' ? 'selected' : '' }}>Land</option>
                        <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                    </select>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min Price"
                           class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent">
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max Price"
                           class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent">
                    <select name="bedrooms" class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent">
                        <option value="">Bedrooms</option>
                        <option value="1" {{ request('bedrooms') == '1' ? 'selected' : '' }}>1+</option>
                        <option value="2" {{ request('bedrooms') == '2' ? 'selected' : '' }}>2+</option>
                        <option value="3" {{ request('bedrooms') == '3' ? 'selected' : '' }}>3+</option>
                        <option value="4" {{ request('bedrooms') == '4' ? 'selected' : '' }}>4+</option>
                        <option value="5" {{ request('bedrooms') == '5' ? 'selected' : '' }}>5+</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-[#d41313] hover:bg-[#b91111] text-white px-6 py-2 rounded-lg font-semibold transition">
                        Search
                    </button>
                    <a href="{{ route('properties.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- Properties Grid -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Results Count -->
            <div class="mb-6 flex justify-between items-center">
                <p class="text-gray-600">
                    Showing <span class="font-semibold">{{ $properties->firstItem() ?? 0 }}</span> -
                    <span class="font-semibold">{{ $properties->lastItem() ?? 0 }}</span> of
                    <span class="font-semibold">{{ $properties->total() }}</span> properties
                </p>
            </div>

            @if($properties->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($properties as $property)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                            <!-- Property Image -->
                            <div class="relative h-48 bg-gray-200">
                                @if($property->images)
                                    @php
                                        $images = is_array($property->images) ? $property->images : json_decode($property->images, true);
                                    @endphp
                                    @if(!empty($images) && isset($images[0]))
                                        <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
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
                                    <span class="bg-[#d41313] text-white px-2 py-1 rounded-full text-xs font-medium capitalize">{{ $property->type }}</span>
                                </div>
                            </div>

                            <!-- Property Details -->
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-[#b91111] transition mb-1 truncate">
                                    <a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
                                </h3>
                                <p class="text-gray-500 text-sm mb-3 flex items-center truncate">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ $property->address }}
                                </p>
                                <div class="flex items-center gap-3 text-xs text-gray-600 mb-3">
                                    @if($property->bedrooms)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                            {{ $property->bedrooms }} Beds
                                        </span>
                                    @endif
                                    @if($property->bathrooms)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                                            {{ $property->bathrooms }} Baths
                                        </span>
                                    @endif
                                    @if($property->area)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                            {{ number_format($property->area) }} sqft
                                        </span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t">
                                    <span class="text-xl font-bold text-[#b91111]">D{{ number_format($property->price) }}</span>
                                    <a href="{{ route('properties.show', $property) }}" class="text-[#b91111] hover:text-[#990e0e] text-sm font-medium">
                                        Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $properties->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-gray-100 rounded-lg">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Properties Found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your search criteria or filters.</p>
                    <a href="{{ route('properties.index') }}" class="inline-block bg-[#d41313] hover:bg-[#b91111] text-white px-6 py-2 rounded-lg font-semibold transition">
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
