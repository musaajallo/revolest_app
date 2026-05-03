@extends('layouts.public')
@section('title', 'Land Purchase Consultation')

@section('content')
    <x-public.forms.partials.page-header
        title="Land Purchase Consultation"
        subtitle="Tell us about the land you would like to acquire." />

    <section class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('forms.land-purchase.store') }}" method="POST" class="space-y-8">
                @csrf
                <x-public.forms.partials.honeypot />

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="full_name" label="Full Name" required />
                        <x-public.forms.partials.field name="phone" label="Phone Number" type="tel" required />
                        <x-public.forms.partials.field name="email" label="Email Address" type="email" />
                        <x-public.forms.partials.field name="address" label="Current Address" />
                        <x-public.forms.partials.field name="identification_type" label="ID Type" placeholder="e.g. Driver's License, Passport" />
                        <x-public.forms.partials.field name="identification_number" label="ID Number" />
                        <div class="md:col-span-2">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="id_attached" value="1" @checked(old('id_attached'))
                                       class="rounded text-[#1c4736] focus:ring-[#1c4736]">
                                <span class="text-sm text-gray-700">I will attach / present my ID document</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Plot Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="preferred_locations" label="Preferred areas / locations" />
                        <x-public.forms.partials.field name="plot_size" label="Plot size" placeholder="e.g. 25m × 30m" />
                        <x-public.forms.partials.field name="with_buildings" label="With buildings or empty land?" />
                        <x-public.forms.partials.field name="future_development" label="Interested in future-development potential?" type="yes_no" />
                        <x-public.forms.partials.field name="land_type" label="Land type" type="select" :options="[
                            'residential' => 'Residential',
                            'commercial' => 'Commercial',
                            'agricultural' => 'Agricultural',
                            'recreation' => 'Recreation',
                            'industrial' => 'Industrial',
                        ]" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Budget & Timeline</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="budget" label="Budget for purchase" />
                        <x-public.forms.partials.field name="payment_plan" label="Payment plan" placeholder="Full / instalment" />
                        <x-public.forms.partials.field name="payment_method" label="Payment method" type="select" :options="[
                            'bank' => 'Bank',
                            'transfer' => 'Transfer',
                            'check' => 'Check',
                            'cash' => 'Cash',
                            'other' => 'Other',
                        ]" />
                        <x-public.forms.partials.field name="timeframe" label="When are you looking to buy?" />
                        <x-public.forms.partials.field name="completion_target" label="When would you like to complete?" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Other Comments</h2>
                    <div class="space-y-6">
                        <x-public.forms.partials.field name="special_requirements" label="Special requirements or requests" type="textarea" />
                        <x-public.forms.partials.field name="notes" label="Other questions or concerns about the buying process" type="textarea" />
                    </div>
                </div>

                <x-public.forms.partials.agreement :policyKeys="['policy.land_purchase']" />

                <x-public.forms.partials.turnstile />

                <div>
                    <button type="submit" class="bg-[#a94a2a] hover:bg-[#8a3c22] text-white px-8 py-3 rounded-lg font-semibold transition">
                        Submit Consultation Request
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
