@extends('layouts.public')
@section('title', 'List Your Land for Sale')

@section('content')
    <x-public.forms.partials.page-header
        title="List Your Land for Sale"
        subtitle="Tell us about the land you would like us to help sell." />

    <section class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('forms.land-sale.store') }}" method="POST" class="space-y-8">
                @csrf
                <x-public.forms.partials.honeypot />

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="full_name" label="Full Name" required />
                        <x-public.forms.partials.field name="email" label="Email" type="email" />
                        <x-public.forms.partials.field name="phone_primary" label="Primary Phone" type="tel" required />
                        <x-public.forms.partials.field name="phone_secondary" label="Secondary Phone" type="tel" />
                        <x-public.forms.partials.field name="phone_tertiary" label="Other Phone" type="tel" />
                        <x-public.forms.partials.field name="current_address" label="Current Address" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Land Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="land_location" label="Land location / address" type="textarea" placeholder="Specific location details or coordinates" />
                        </div>
                        <x-public.forms.partials.field name="land_size" label="Size of land" placeholder="acres / m² / sq ft" />
                        <x-public.forms.partials.field name="current_use" label="Current use" type="select" :options="[
                            'residential' => 'Residential',
                            'commercial' => 'Commercial',
                            'agricultural' => 'Agricultural',
                            'vacant' => 'Vacant',
                            'other' => 'Other',
                        ]" />
                        <x-public.forms.partials.field name="current_use_other" label="If other, specify" />
                        <x-public.forms.partials.field name="zoning" label="Zoning" type="select" :options="[
                            'residential' => 'Residential',
                            'commercial' => 'Commercial',
                        ]" />
                        <x-public.forms.partials.field name="jointly_owned" label="Is the land jointly owned?" type="yes_no" />
                        <x-public.forms.partials.field name="ownership_disputes" label="Any disputes over ownership?" type="yes_no" />
                        <x-public.forms.partials.field name="asking_price" label="Selling price (GMD)" type="number" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Purpose of Consultation</h2>
                    <div class="space-y-6">
                        <x-public.forms.partials.field name="consultation_purpose" label="Reason for this consultation (check all that apply)" type="checkboxes" :options="[
                            'sell' => 'Selling the land',
                            'development' => 'Development opportunities',
                            'valuation' => 'Land valuation',
                            'environmental' => 'Environmental assessment',
                            'other' => 'Other',
                        ]" />
                        <x-public.forms.partials.field name="consultation_purpose_other" label="If other, specify" />
                        <x-public.forms.partials.field name="plans_for_land" label="Specific plans or goals for the land" type="textarea" />
                        <x-public.forms.partials.field name="current_issues" label="Current issues or concerns with the land" type="textarea" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Legal & Financial</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="has_liens" label="Existing liens or mortgages?" type="yes_no" />
                        <x-public.forms.partials.field name="taxes_up_to_date" label="Property taxes up to date?" type="yes_no" />
                        <x-public.forms.partials.field name="has_legal_documents" label="Legal documentation available?" type="yes_no" />
                        <x-public.forms.partials.field name="free_from_disputes" label="Free from legal disputes / encumbrances?" type="yes_no" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="documents_provided" label="Documents you can provide" type="checkboxes" :options="[
                                'title_deed' => 'Title Deed',
                                'tax_papers' => 'Tax Papers',
                                'physical_planning' => 'Physical Planning Document',
                                'lease_assignment' => 'Assignment of Lease',
                                'alkalo_transfer' => 'Alkalo Transfer',
                                'sketch_plan' => 'Sketch Plan of the Plot',
                            ]" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Site Conditions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="utilities" label="Utilities connected" type="checkboxes" :options="[
                                'electricity' => 'Electricity',
                                'sewage' => 'Sewage',
                                'water' => 'Water',
                                'none' => 'None',
                            ]" />
                        </div>
                        <x-public.forms.partials.field name="road_accessible" label="Accessible by road?" type="yes_no" />
                        <x-public.forms.partials.field name="has_recent_survey" label="Recent survey or appraisal?" type="yes_no" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="existing_structures" label="Existing structures (describe if yes)" type="textarea" />
                        </div>
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="environmental_concerns" label="Environmental risks or concerns" type="textarea" />
                        </div>
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="land_history" label="Brief history of the land and how you acquired it" type="textarea" rows="4" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Referral</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="previous_company_contact" label="Have you contacted other companies before?" type="yes_no" />
                        <x-public.forms.partials.field name="referral_source" label="How did you learn about us?" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="previous_company_experience" label="If yes, describe the experience" type="textarea" />
                        </div>
                        <x-public.forms.partials.field name="referral_notes" label="Name of person / media (if referral)" />
                        <x-public.forms.partials.field name="referred_by_name" label="Referred by (staff member or source)" />
                    </div>
                </div>

                <x-public.forms.partials.agreement :policyKeys="['policy.land_sale']" />

                <x-public.forms.partials.turnstile />

                <div>
                    <button type="submit" class="bg-[#a94a2a] hover:bg-[#8a3c22] text-white px-8 py-3 rounded-lg font-semibold transition">
                        Submit Listing Request
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
