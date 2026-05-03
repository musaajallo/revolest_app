@extends('layouts.public')
@section('title', 'Submission Received')

@section('content')
    <section class="py-20">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Thank you</h1>
            <p class="text-gray-600 mb-8">{{ $copy }}</p>
            <a href="{{ route('home') }}" class="inline-block bg-[#a94a2a] hover:bg-[#8a3c22] text-white px-6 py-3 rounded-lg font-semibold transition">
                Back to home
            </a>
        </div>
    </section>
@endsection
