@extends('layouts.public')

@section('title', $page?->meta_title ?? 'Home')
@section('meta_description', $page?->meta_description ?? 'Find your dream property with Revolest. Browse homes for sale and rent, connect with trusted agents, and make your real estate journey seamless.')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-gray-900 to-gray-800 text-white">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                    {!! $page?->getContent('hero_title') ?? 'Find Your Perfect <span class="text-[#1c4736]">Property</span>' !!}
                </h1>
                <p class="text-xl text-gray-300 mb-8">
                    {{ $page?->getContent('hero_subtitle') ?? 'Discover thousands of properties for sale and rent. Your dream home is just a click away.' }}
                </p>

                <!-- Search Form -->
                <form action="{{ route('properties.index') }}" method="GET" class="bg-white rounded-lg p-4 shadow-lg">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" placeholder="Search by location or property name..."
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-900 focus:ring-2 focus:ring-[#1c4736] focus:border-transparent">
                        </div>
                        <div class="md:w-48">
                            <select name="type" class="w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-900 focus:ring-2 focus:ring-[#1c4736] focus:border-transparent">
                                <option value="">All Types</option>
                                <option value="house">House</option>
                                <option value="apartment">Apartment</option>
                                <option value="condo">Condo</option>
                                <option value="land">Land</option>
                                <option value="commercial">Commercial</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-[#a94a2a] hover:bg-[#8a3c22] text-white px-8 py-3 rounded-lg font-semibold transition">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white py-12 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-[#1c4736]">{{ number_format($totalProperties) }}+</div>
                    <div class="text-gray-600 mt-1">Properties</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-[#1c4736]">{{ number_format($totalAgents) }}+</div>
                    <div class="text-gray-600 mt-1">Agents</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-[#1c4736]">{{ number_format($totalListings) }}+</div>
                    <div class="text-gray-600 mt-1">Active Listings</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-[#1c4736]">500+</div>
                    <div class="text-gray-600 mt-1">Happy Clients</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Properties -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $page?->getContent('featured_title') ?? 'Featured Properties' }}</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">{{ $page?->getContent('featured_subtitle') ?? 'Explore our handpicked selection of premium properties available for sale and rent.' }}</p>
            </div>

            @if($featuredProperties->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($featuredProperties as $property)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                            <!-- Property Image -->
                            <div class="relative h-56 bg-gray-200">
                                @if($property->images)
                                    @php
                                        $images = is_array($property->images) ? $property->images : json_decode($property->images, true);
                                    @endphp
                                    @if(!empty($images) && isset($images[0]))
                                        <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" onerror="this.onerror=null; this.src='{{ asset('images/placeholder-property.svg') }}'">
                                    @else
                                        <img src="{{ asset('images/placeholder-property.svg') }}" alt="No image available" class="w-full h-full object-cover">
                                    @endif
                                @else
                                    <img src="{{ asset('images/placeholder-property.svg') }}" alt="No image available" class="w-full h-full object-cover">
                                @endif
                                <div class="absolute top-4 left-4">
                                    <span class="bg-[#a94a2a] text-white px-3 py-1 rounded-full text-sm font-medium capitalize">{{ $property->type }}</span>
                                </div>
                            </div>

                            <!-- Property Details -->
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-[#1c4736] transition">
                                        <a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
                                    </h3>
                                </div>
                                <p class="text-gray-500 text-sm mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ $property->address }}
                                </p>
                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
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
                                <div class="flex justify-between items-center pt-4 border-t">
                                    <span class="text-2xl font-bold text-[#1c4736]">D{{ number_format($property->price) }}</span>
                                    <a href="{{ route('properties.show', $property) }}" class="text-[#1c4736] hover:text-[#a94a2a] font-medium">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-12">
                    <a href="{{ route('properties.index') }}" class="inline-block bg-[#a94a2a] hover:bg-[#8a3c22] text-white px-8 py-3 rounded-lg font-semibold transition">
                        View All Properties
                    </a>
                </div>
            @else
                <div class="text-center py-12 bg-gray-100 rounded-lg">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    <p class="text-gray-600">No properties available at the moment. Check back soon!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $page?->getContent('why_choose_title') ?? 'Why Choose Revolest?' }}</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">{{ $page?->getContent('why_choose_subtitle') ?? 'We make finding your perfect property simple, transparent, and stress-free.' }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#1c4736]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Trusted & Verified</h3>
                    <p class="text-gray-600">All our properties and agents are thoroughly verified to ensure you get the best experience.</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#1c4736]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Best Prices</h3>
                    <p class="text-gray-600">We offer competitive pricing and help you find properties that fit your budget.</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#1c4736]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">24/7 Support</h3>
                    <p class="text-gray-600">Our dedicated team is always ready to assist you with any questions or concerns.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-[#a94a2a]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ $page?->getContent('cta_title') ?? 'Ready to Find Your Dream Home?' }}</h2>
            <p class="text-red-100 mb-8 max-w-2xl mx-auto">{{ $page?->getContent('cta_subtitle') ?? "Whether you're buying, selling, or renting, we're here to help you every step of the way." }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('properties.index') }}" class="bg-white text-[#1c4736] hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition">
                    Browse Properties
                </a>
                <a href="{{ route('contact') }}" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-[#1c4736] px-8 py-3 rounded-lg font-semibold transition">
                    Contact Us
                </a>
            </div>
        </div>
    </section>
@endsection
