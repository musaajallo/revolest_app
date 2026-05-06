@extends('layouts.public')
@section('title', 'Forms & Consultations')

@php
    $bannerBg = \App\Models\Setting::get('banner_background');
    $bannerBgUrl = $bannerBg ? \Illuminate\Support\Facades\Storage::url($bannerBg) : null;
@endphp
@section('content')
    <section
        class="bg-gray-900 bg-cover bg-center text-white py-16"
        @if($bannerBgUrl) style="background-image: linear-gradient(rgba(17,24,39,.75), rgba(17,24,39,.75)), url('{{ $bannerBgUrl }}');" @endif
    >
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">Forms & Consultations</h1>
            <p class="text-gray-400">Submit a request and one of our agents will be in touch.</p>
        </div>
    </section>

    <section class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $cards = [
                    ['title' => 'Land Purchase Consultation', 'desc' => 'Tell us what land you are looking for and your budget.', 'route' => 'forms.land-purchase'],
                    ['title' => 'List Your Land for Sale', 'desc' => 'Submit details about a plot you would like us to help sell.', 'route' => 'forms.land-sale'],
                    ['title' => 'Rental Consultation', 'desc' => 'Find a rental property that matches your needs.', 'route' => 'forms.rental-consultation'],
                    ['title' => 'List Your Built Property', 'desc' => 'Submit details for a built property you wish to sell.', 'route' => 'forms.property-listing'],
                    ['title' => 'Purchase a Built Property', 'desc' => 'Tell us about the home, mansion, or apartment you want to buy.', 'route' => 'forms.purchase-build-property'],
                    ['title' => 'Customer Feedback', 'desc' => 'Share your experience — compliments, complaints, suggestions.', 'route' => 'forms.customer-feedback'],
                    ['title' => 'Maintenance Request', 'desc' => 'Report a repair or maintenance issue at your property.', 'route' => 'forms.maintenance-request'],
                    ['title' => 'Pet Application', 'desc' => 'Apply to keep pets at your rental property.', 'route' => 'forms.pet-application'],
                ];
            @endphp

            @foreach($cards as $card)
                <a href="{{ route($card['route']) }}" class="block bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg hover:border-[#1c4736] transition">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $card['title'] }}</h2>
                    <p class="text-gray-600 text-sm">{{ $card['desc'] }}</p>
                    <span class="inline-block mt-4 text-sm font-semibold text-[#a94a2a]">Open form →</span>
                </a>
            @endforeach
        </div>
    </section>
@endsection
