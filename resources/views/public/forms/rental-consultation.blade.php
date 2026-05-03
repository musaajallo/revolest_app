@extends('layouts.public')
@section('title', 'Rental Consultation')

@section('content')
    <x-public.forms.partials.page-header
        title="Rental Consultation"
        subtitle="Tell us what you are looking for and we will match you with available properties." />

    <section class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('forms.rental-consultation.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">General Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="consultation_date" label="Date" type="date" :value="now()->toDateString()" />
                        <x-public.forms.partials.field name="full_name" label="Full Name" required />
                        <x-public.forms.partials.field name="address" label="Current Address" />
                        <x-public.forms.partials.field name="nationality" label="Nationality" />
                        <x-public.forms.partials.field name="occupation" label="Occupation" />
                        <x-public.forms.partials.field name="institution" label="Institution / Company" />
                        <x-public.forms.partials.field name="marital_status" label="Marital Status" />
                        <x-public.forms.partials.field name="number_of_kids" label="Number of kids (if any)" type="number" />
                        <x-public.forms.partials.field name="phone" label="Phone Number" type="tel" required />
                        <x-public.forms.partials.field name="email" label="Email Address" type="email" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Property Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="preferred_areas" label="Preferred location(s) / area(s)" />
                        <x-public.forms.partials.field name="property_kind" label="Property kind" type="select" :options="[
                            'full_compound' => 'Full Compound',
                            'apartment' => 'Apartment',
                        ]" />
                        <x-public.forms.partials.field name="bedrooms" label="Number of bedrooms" type="number" />
                        <x-public.forms.partials.field name="furnished" label="Furnished?" type="yes_no" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="preferred_structure" label="Preferred property structure" />
                        </div>
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="desired_facilities" label="Facilities you consider on the property" type="textarea" />
                        </div>
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="property_suggestions" label="Other suggestions" type="textarea" />
                        </div>
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="reason_for_moving" label="Why do you want to leave where you are at present?" type="textarea" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Tenancy & Payment</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="occupants_count" label="How many will be living on the property?" type="number" />
                        <x-public.forms.partials.field name="move_in_window" label="When do you need the property?" />
                        <x-public.forms.partials.field name="rental_duration" label="For how long do you need the property?" />
                        <x-public.forms.partials.field name="payment_plan" label="Payment plan" />
                        <x-public.forms.partials.field name="payment_method" label="Payment method" type="select" :options="[
                            'cash' => 'Cash',
                            'bank_transfer' => 'Bank Transfer',
                            'cheque' => 'Cheque',
                        ]" />
                        <x-public.forms.partials.field name="payer" label="Who is making payment?" type="select" :options="[
                            'me' => 'Me',
                            'other' => 'Other',
                        ]" />
                    </div>

                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-4">If "Other" — payer details</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-public.forms.partials.field name="payer_name" label="Name of payer" />
                            <x-public.forms.partials.field name="payer_occupation" label="Occupation of payer" />
                            <x-public.forms.partials.field name="payer_address" label="Current address of payer" />
                            <x-public.forms.partials.field name="payer_phone" label="Contact number of payer" type="tel" />
                            <x-public.forms.partials.field name="payer_relationship" label="Relationship with you" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Referral</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="previous_company_contact" label="Have you contacted other companies / agents before?" type="yes_no" />
                        <x-public.forms.partials.field name="referral_source" label="How did you learn about us?" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="previous_company_experience" label="If yes, describe the experience" type="textarea" />
                        </div>
                        <x-public.forms.partials.field name="referral_name" label="Name of person / media (if referral)" />
                    </div>
                </div>

                <x-public.forms.partials.agreement :policyKeys="[
                    'policy.rental_consultation',
                    'policy.rental_weekly_agent_fees',
                    'policy.rental_yearly_agent_fees',
                ]" />

                <div>
                    <button type="submit" class="bg-[#a94a2a] hover:bg-[#8a3c22] text-white px-8 py-3 rounded-lg font-semibold transition">
                        Submit Rental Consultation
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
