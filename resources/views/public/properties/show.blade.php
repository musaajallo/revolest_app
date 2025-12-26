@extends('layouts.public')

@section('title', $property->title)
@section('meta_description', Str::limit($property->description, 160))

@section('content')
    <!-- Breadcrumb -->
    <section class="bg-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-sm">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#b91111]">Home</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('properties.index') }}" class="text-gray-500 hover:text-[#b91111]">Properties</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900">{{ $property->title }}</span>
            </nav>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Property Images -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                        <div class="relative h-64 md:h-96 bg-gray-200">
                            @if($property->images)
                                @php
                                    $images = is_array($property->images) ? $property->images : json_decode($property->images, true);
                                @endphp
                                @if(!empty($images) && isset($images[0]))
                                    <img id="main-image" src="{{ asset('storage/' . $images[0]) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                @else
                                    <img id="main-image" src="{{ asset('images/placeholder-property.svg') }}" alt="No image available" class="w-full h-full object-cover">
                                @endif
                            @else
                                <img id="main-image" src="{{ asset('images/placeholder-property.svg') }}" alt="No image available" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="bg-[#d41313] text-white px-3 py-1 rounded-full text-sm font-medium capitalize">{{ $property->type }}</span>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium capitalize">{{ $property->status }}</span>
                            </div>
                        </div>

                        <!-- Thumbnail Gallery -->
                        @if($property->images)
                            @php
                                $images = is_array($property->images) ? $property->images : json_decode($property->images, true);
                            @endphp
                            @if(!empty($images) && count($images) > 1)
                                <div class="p-4 flex gap-2 overflow-x-auto">
                                    @foreach($images as $index => $image)
                                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $property->title }} - Image {{ $index + 1 }}"
                                             onclick="document.getElementById('main-image').src = this.src"
                                             class="w-20 h-20 object-cover rounded-lg cursor-pointer hover:opacity-75 transition {{ $index === 0 ? 'ring-2 ring-[#d41313]' : '' }}">
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Property Details -->
                    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-6">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $property->title }}</h1>
                                <p class="text-gray-500 flex items-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ $property->address }}
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <span class="text-3xl font-bold text-[#b91111]">D{{ number_format($property->price) }}</span>
                            </div>
                        </div>

                        <!-- Features Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-6 border-y">
                            @if($property->bedrooms)
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <svg class="w-8 h-8 mx-auto text-[#d41313] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                    <div class="text-2xl font-bold text-gray-900">{{ $property->bedrooms }}</div>
                                    <div class="text-sm text-gray-500">Bedrooms</div>
                                </div>
                            @endif
                            @if($property->bathrooms)
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <svg class="w-8 h-8 mx-auto text-[#d41313] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                                    <div class="text-2xl font-bold text-gray-900">{{ $property->bathrooms }}</div>
                                    <div class="text-sm text-gray-500">Bathrooms</div>
                                </div>
                            @endif
                            @if($property->area)
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <svg class="w-8 h-8 mx-auto text-[#d41313] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                    <div class="text-2xl font-bold text-gray-900">{{ number_format($property->area) }}</div>
                                    <div class="text-sm text-gray-500">Sq Ft</div>
                                </div>
                            @endif
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-[#d41313] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                <div class="text-2xl font-bold text-gray-900 capitalize">{{ $property->type }}</div>
                                <div class="text-sm text-gray-500">Type</div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
                            <div class="prose prose-gray max-w-none">
                                {!! nl2br(e($property->description)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Owner Info -->
                    @if($property->owner)
                        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Property Owner</h2>
                            <div class="flex items-center">
                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl font-bold text-[#b91111]">{{ substr($property->owner->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $property->owner->name }}</h3>
                                    @if($property->owner->email)
                                        <p class="text-gray-500">{{ $property->owner->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Inquiry Form -->
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Interested in this property?</h2>
                        <p class="text-gray-600 mb-6">Send us an inquiry and we'll get back to you shortly.</p>

                        @if($property->listings->first())
                            <form action="{{ route('inquiry.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="listing_id" value="{{ $property->listings->first()->id }}">

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                                        <input type="text" name="name" required value="{{ old('name') }}"
                                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent @error('name') border-red-500 @enderror">
                                        @error('name')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                        <input type="email" name="email" required value="{{ old('email') }}"
                                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent @error('email') border-red-500 @enderror">
                                        @error('email')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}"
                                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                                        <textarea name="message" rows="4" required
                                                  class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#d41313] focus:border-transparent @error('message') border-red-500 @enderror">{{ old('message', "Hi, I'm interested in this property: " . $property->title) }}</textarea>
                                        @error('message')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" class="w-full bg-[#d41313] hover:bg-[#b91111] text-white py-3 rounded-lg font-semibold transition">
                                        Send Inquiry
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="text-center py-4 bg-gray-100 rounded-lg">
                                <p class="text-gray-600">Contact form is currently unavailable for this property.</p>
                                <a href="{{ route('contact') }}" class="text-[#b91111] hover:text-[#990e0e] font-medium mt-2 inline-block">
                                    Contact us directly →
                                </a>
                            </div>
                        @endif

                        <!-- Agent Info -->
                        @if($property->listings->first() && $property->listings->first()->agent)
                            @php $agent = $property->listings->first()->agent; @endphp
                            <div class="mt-6 pt-6 border-t">
                                <h3 class="font-semibold text-gray-900 mb-3">Listed by</h3>
                                <a href="{{ route('agents.show', $agent) }}" class="flex items-center group">
                                    <div class="w-12 h-12 bg-gray-200 rounded-full overflow-hidden">
                                        @if($agent->photo)
                                            <img src="{{ asset('storage/' . $agent->photo) }}" alt="{{ $agent->name }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/placeholder-agent.svg') }}" alt="No photo available" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900 group-hover:text-[#b91111] transition">{{ $agent->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $agent->phone ?? $agent->email }}</p>
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Properties -->
            @if($relatedProperties->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Similar Properties</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedProperties as $related)
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                                <div class="relative h-48 bg-gray-200">
                                    @if($related->images)
                                        @php
                                            $images = is_array($related->images) ? $related->images : json_decode($related->images, true);
                                        @endphp
                                        @if(!empty($images) && isset($images[0]))
                                            <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                        @else
                                            <img src="{{ asset('images/placeholder-property.svg') }}" alt="No image available" class="w-full h-full object-cover">
                                        @endif
                                    @else
                                        <img src="{{ asset('images/placeholder-property.svg') }}" alt="No image available" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-[#b91111] transition truncate">
                                        <a href="{{ route('properties.show', $related) }}">{{ $related->title }}</a>
                                    </h3>
                                    <p class="text-gray-500 text-sm truncate">{{ $related->address }}</p>
                                    <div class="flex justify-between items-center mt-3 pt-3 border-t">
                                        <span class="text-lg font-bold text-[#b91111]">${{ number_format($related->price) }}</span>
                                        <a href="{{ route('properties.show', $related) }}" class="text-[#b91111] hover:text-[#990e0e] text-sm font-medium">View →</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
