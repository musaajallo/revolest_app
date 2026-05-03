@extends('layouts.public')
@section('title', 'Customer Feedback')

@section('content')
    <x-public.forms.partials.page-header
        title="Customer Feedback"
        subtitle="Your suggestions, compliments and complaints help us improve." />

    <section class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('forms.customer-feedback.store') }}" method="POST" class="space-y-8">
                @csrf
                <x-public.forms.partials.honeypot />

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Contact (optional)</h2>
                    <p class="text-sm text-gray-500 mb-6">You can submit anonymously — leave these blank if you prefer.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <x-public.forms.partials.field name="full_name" label="Full Name" />
                        <x-public.forms.partials.field name="email" label="Email" type="email" />
                        <x-public.forms.partials.field name="phone" label="Phone" type="tel" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Your Experience</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="overall_satisfaction" label="Overall, were you satisfied with our service?" type="select" :options="[
                            'very_satisfied' => 'Very Satisfied',
                            'satisfied' => 'Satisfied',
                            'neutral' => 'Neutral',
                            'dissatisfied' => 'Dissatisfied',
                            'very_dissatisfied' => 'Very Dissatisfied',
                        ]" />
                        <x-public.forms.partials.field name="service_quality" label="How would you rate the quality of our product / service?" type="select" :options="[
                            'excellent' => 'Excellent',
                            'good' => 'Good',
                            'average' => 'Average',
                            'poor' => 'Poor',
                            'very_poor' => 'Very Poor',
                        ]" />
                        <x-public.forms.partials.field name="customer_service_experience" label="How was your experience with our customer service?" type="select" :options="[
                            'excellent' => 'Excellent',
                            'good' => 'Good',
                            'average' => 'Average',
                            'poor' => 'Poor',
                            'very_poor' => 'Very Poor',
                        ]" />
                        <x-public.forms.partials.field name="staff_helpful" label="Was our staff helpful and courteous?" type="select" :options="[
                            'yes' => 'Yes',
                            'no' => 'No',
                            'somewhat' => 'Somewhat',
                        ]" />
                        <x-public.forms.partials.field name="delivery_on_time" label="Was the product / service delivered on time?" type="select" :options="[
                            'yes' => 'Yes',
                            'no' => 'No',
                            'somewhat' => 'Somewhat',
                        ]" />
                        <x-public.forms.partials.field name="ease_of_finding" label="How easy was it to find what you were looking for?" type="select" :options="[
                            'very_easy' => 'Very Easy',
                            'easy' => 'Easy',
                            'neutral' => 'Neutral',
                            'difficult' => 'Difficult',
                            'very_difficult' => 'Very Difficult',
                        ]" />
                        <x-public.forms.partials.field name="would_recommend" label="Would you recommend us to others?" type="select" :options="[
                            'definitely' => 'Definitely',
                            'maybe' => 'Maybe',
                            'not_likely' => 'Not Likely',
                        ]" />
                        <x-public.forms.partials.field name="expectations_met" label="Were your expectations met?" type="select" :options="[
                            'yes' => 'Yes',
                            'no' => 'No',
                            'exceed' => 'Exceeded',
                        ]" />
                        <x-public.forms.partials.field name="accessibility_score" label="On 1–10, was customer service provided in an accessible manner?" type="select" :options="[
                            '1_3' => '1 – 3',
                            '4_6' => '4 – 6',
                            '7_10' => '7 – 10',
                        ]" />
                        <x-public.forms.partials.field name="brand_score" label="On 1–10, how would you rate your overall experience with our brand?" type="select" :options="[
                            '1_3' => '1 – 3',
                            '4_6' => '4 – 6',
                            '7_10' => '7 – 10',
                        ]" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Tell Us More</h2>
                    <div class="space-y-6">
                        <x-public.forms.partials.field name="improvement_suggestions" label="What could we do to improve our service?" type="textarea" />
                        <x-public.forms.partials.field name="additional_comments" label="Additional comments or suggestions" type="textarea" />
                        <x-public.forms.partials.field name="why_chose_us" label="Why did you choose our company?" type="textarea" />
                        <x-public.forms.partials.field name="missing_features" label="Are there any products / services we are missing? Anything you would recommend we stop doing?" type="textarea" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">How Did You Hear About Us?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="heard_about_us" label="Source" type="select" :options="[
                            'word_of_mouth' => 'Word of Mouth',
                            'social_media' => 'Social Media',
                            'online_ad' => 'Online Advertisement',
                            'friend_family' => 'Friend / Family Referral',
                            'other' => 'Other',
                        ]" />
                        <x-public.forms.partials.field name="heard_about_us_other" label="If other, specify" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Sign-off (optional)</h2>
                    <p class="text-sm text-gray-500 mb-6">You can attach your name to this feedback if you'd like — it isn't required.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="signed_name" label="Your name (signature)" />
                        <div class="flex items-end text-sm text-gray-700">
                            <p>Date: <strong>{{ now()->format('d M Y') }}</strong></p>
                        </div>
                    </div>
                </div>

                <x-public.forms.partials.turnstile />

                <div>
                    <button type="submit" class="bg-[#a94a2a] hover:bg-[#8a3c22] text-white px-8 py-3 rounded-lg font-semibold transition">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
