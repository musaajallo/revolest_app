@extends('layouts.public')
@section('title', 'List Your Built Property for Sale')

@section('content')
    <x-public.forms.partials.page-header
        title="List Your Built Property for Sale"
        subtitle="Submit details about a built property you would like us to help sell." />

    <section class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('forms.property-listing.store') }}" method="POST" class="space-y-8">
                @csrf
                <x-public.forms.partials.honeypot />

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Owner Contact</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="first_name" label="First Name" required />
                        <x-public.forms.partials.field name="last_name" label="Last Name" required />
                        <x-public.forms.partials.field name="nationality" label="Nationality" />
                        <x-public.forms.partials.field name="email" label="Email" type="email" />
                        <x-public.forms.partials.field name="phone" label="Phone" type="tel" required />
                        <x-public.forms.partials.field name="street_address" label="Street Address" />
                        <x-public.forms.partials.field name="city" label="City / Town / Province" />
                        <x-public.forms.partials.field name="region" label="Region" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Property Description</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="legal_description" label="Property Legal Description" type="textarea" />
                        </div>
                        <x-public.forms.partials.field name="property_address" label="Property Address" required />
                        <x-public.forms.partials.field name="land_dimension" label="Dimension of land area" />
                        <x-public.forms.partials.field name="approximate_sqft" label="Approximate Sq. ft" />
                        <x-public.forms.partials.field name="property_status" label="Property Status" type="select" :options="[
                            'freehold' => 'Freehold',
                            'leasehold' => 'Leasehold',
                        ]" />
                        <x-public.forms.partials.field name="property_type" label="Property Type" type="select" :options="[
                            'residential' => 'Residential',
                            'single_family' => 'Single-family',
                            'multifamily' => 'Multifamily',
                            'commercial' => 'Commercial',
                            'recreation' => 'Recreation',
                            'farm' => 'Farm',
                            'industrial' => 'Industrial',
                            'land' => 'Land',
                            'other' => 'Other',
                        ]" />
                        <x-public.forms.partials.field name="asking_price" label="Asking Price (GMD)" type="number" />
                        <x-public.forms.partials.field name="possession" label="Possession" type="select" :options="[
                            'immediately' => 'Immediately',
                            'leased' => 'Leased',
                            'at_closing' => 'At Closing',
                            'to_be_arranged' => 'To be arranged',
                            'other' => 'Other',
                        ]" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="buildings_on_property" label="Buildings on property" type="checkboxes" :options="[
                                'house' => 'House',
                                'workshop' => 'Workshop',
                                'garage_single' => 'Garage (single)',
                                'garage_double' => 'Garage (double)',
                                'store' => 'Store',
                                'as_is' => 'As-is',
                                'other' => 'Other',
                            ]" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Showing & Building Detail</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="showing_instructions" label="Showing instructions" type="select" :options="[
                            'call_office' => 'Call listing office',
                            'show_anytime' => 'Show anytime',
                            'vacant' => 'Vacant',
                            'appointment_only' => 'Appointment only',
                            'other' => 'Other',
                        ]" />
                        <x-public.forms.partials.field name="number_of_rooms" label="Number of rooms" type="number" />
                        <x-public.forms.partials.field name="bedrooms_detail" label="Bedrooms (count + sizes)" />
                        <x-public.forms.partials.field name="bathrooms_detail" label="Bathrooms (count + sizes)" />
                        <x-public.forms.partials.field name="age_of_house" label="Age of house" />
                        <x-public.forms.partials.field name="square_footage" label="Square footage" />
                        <x-public.forms.partials.field name="roof_type" label="Roof type" />
                        <x-public.forms.partials.field name="furnace" label="Furnace" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="amenities" label="Amenities" type="textarea" />
                        </div>
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="natural_features" label="Natural features" type="checkboxes" :options="[
                                'hilly' => 'Hilly / Steep',
                                'open' => 'Open',
                                'slope' => 'Slope',
                                'rolling' => 'Rolling',
                                'flat' => 'Flat',
                                'other' => 'Other',
                            ]" />
                        </div>
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="site_documents" label="Site documents available" type="checkboxes" :options="[
                                'site_plan' => 'Site Plan',
                                'topo_plan' => 'Topo Plan',
                                'aerial_photo' => 'Aerial Photo',
                            ]" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Disclosures & Documents</h2>
                    <div class="space-y-6">
                        <x-public.forms.partials.field name="disclosures" label="Disclosures" type="checkboxes" :options="[
                            'flood' => 'Flood-affected area',
                            'appliance' => 'Appliance issue',
                            'security' => 'Security concerns',
                            'water' => 'Water issues',
                            'pest' => 'Pest infestations',
                        ]" />
                        <x-public.forms.partials.field name="disclosures_other" label="Other disclosures" type="textarea" />
                        <x-public.forms.partials.field name="documents_attached" label="Documents you can provide" type="checkboxes" :options="[
                            'title_deed' => 'Title deed',
                            'id_passport' => 'ID / Passport',
                            'physical_planning' => 'Physical planning documents',
                            'tax_rate' => 'Tax rate documents',
                            'lease_assignment' => 'Lease Assignment',
                            'building_plan' => 'Building plan',
                            'recent_appraisal' => 'Recent appraisal report',
                            'survey' => 'Survey of the property',
                        ]" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Referral</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="previous_company_contact" label="Contacted other companies before?" type="yes_no" />
                        <x-public.forms.partials.field name="referral_source" label="How did you learn about us?" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="previous_company_experience" label="If yes, describe the experience" type="textarea" />
                        </div>
                        <x-public.forms.partials.field name="referral_name" label="Name of person / media (if referral)" />
                        <x-public.forms.partials.field name="referred_by_name" label="Referred by (staff member or source)" />
                    </div>
                </div>

                <x-public.forms.partials.agreement :policyKeys="['policy.built_property_listing']" />

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
