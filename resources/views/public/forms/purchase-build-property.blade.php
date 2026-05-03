@extends('layouts.public')
@section('title', 'Purchase a Built Property — Consultation')

@section('content')
    <x-public.forms.partials.page-header
        title="Consultation — Purchase of Built Properties"
        subtitle="Tell us about the home, mansion, or apartment you want to acquire." />

    <section class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('forms.purchase-build-property.store') }}" method="POST" class="space-y-8">
                @csrf
                <x-public.forms.partials.honeypot />

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">1. Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="full_name" label="Full Name" required />
                        <x-public.forms.partials.field name="email" label="Email Address" type="email" />
                        <x-public.forms.partials.field name="phone_primary" label="Contact Number" type="tel" required />
                        <x-public.forms.partials.field name="phone_secondary" label="Alternate Contact" type="tel" />
                        <x-public.forms.partials.field name="phone_tertiary" label="Additional Contact" type="tel" />
                        <x-public.forms.partials.field name="current_address" label="Current Address" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">2. Preferences</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="property_type" label="Property type" type="select" :options="[
                            'house' => 'House',
                            'mansion' => 'Mansion',
                            'apartment' => 'Apartment',
                        ]" />
                        <x-public.forms.partials.field name="build_status" label="New build, pre-owned, or custom-built?" />
                        <x-public.forms.partials.field name="preferred_location" label="Preferred location or neighborhood" />
                        <x-public.forms.partials.field name="avoid_areas" label="Areas to avoid" type="textarea" />
                        <x-public.forms.partials.field name="architectural_style" label="Architectural style preference" placeholder="e.g. modern, traditional, colonial" />
                        <x-public.forms.partials.field name="bedrooms_bathrooms" label="Bedrooms & bathrooms needed" placeholder="e.g. 4 bed / 3 bath" />
                        <x-public.forms.partials.field name="special_features" label="Specific features" placeholder="e.g. home office, gym, pool, garden" type="textarea" />
                        <x-public.forms.partials.field name="luxury_features" label="Luxury amenities of interest" placeholder="e.g. home theater, smart home, wine cellar" type="textarea" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">3. Budget & Financing</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="budget" label="Budget range" />
                        <x-public.forms.partials.field name="financing_method" label="Financing method" type="select" :options="[
                            'mortgage' => 'Mortgage',
                            'cash' => 'Cash',
                            'mixed' => 'Mixed',
                            'other' => 'Other',
                        ]" />
                        <x-public.forms.partials.field name="mortgage_preapproval" label="Pre-approved for a mortgage? Amount?" />
                        <x-public.forms.partials.field name="needs_mortgage_advice" label="Need help with mortgage advice?" type="yes_no" />
                        <x-public.forms.partials.field name="open_to_negotiation" label="Open to negotiating / renovation properties?" type="yes_no" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">4. Property Size & Layout</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="min_square_footage" label="Minimum square footage" />
                        <x-public.forms.partials.field name="needs_extra_space" label="Need extra room (office / gym / guest)?" type="yes_no" />
                        <x-public.forms.partials.field name="lot_size_preference" label="Lot size / yard preference" />
                        <x-public.forms.partials.field name="storey_preference" label="Single or multi-story?" type="select" :options="[
                            'single' => 'Single-story',
                            'multi' => 'Multi-story',
                            'no_preference' => 'No preference',
                        ]" />
                        <x-public.forms.partials.field name="layout_preference" label="Layout / design preferences" type="textarea" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">5. Location & Neighborhood</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="proximity_preference" label="Proximity to schools / work / transport" type="textarea" />
                        <x-public.forms.partials.field name="area_kind" label="Area type" type="select" :options="[
                            'city' => 'City',
                            'suburban' => 'Suburban',
                            'rural' => 'Rural',
                        ]" />
                        <x-public.forms.partials.field name="amenities_importance" label="Importance of local amenities" type="textarea" />
                        <x-public.forms.partials.field name="community_type" label="Community preference" type="select" :options="[
                            'gated' => 'Gated',
                            'private' => 'Private',
                            'open' => 'Open',
                        ]" />
                        <x-public.forms.partials.field name="landmarks" label="Important landmarks (beach, golf, mountains, etc.)" type="textarea" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">6. Timeline & Urgency</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="move_in_target" label="Target move-in" />
                        <x-public.forms.partials.field name="time_sensitivity" label="Time-sensitive? Why?" type="textarea" />
                        <x-public.forms.partials.field name="readiness_preference" label="Move-in ready, under construction, or renovation OK?" type="select" :options="[
                            'ready' => 'Move-in ready',
                            'under_construction' => 'Under construction OK',
                            'renovation_ok' => 'Renovation OK',
                        ]" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">7. Long-Term Goals</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="use_purpose" label="Intended use" type="select" :options="[
                            'primary' => 'Primary residence',
                            'vacation' => 'Vacation home',
                            'investment' => 'Investment',
                        ]" />
                        <x-public.forms.partials.field name="long_term_value" label="Long-term value / development priorities" type="textarea" />
                        <x-public.forms.partials.field name="open_to_developments" label="Open to upcoming developments / projects?" type="yes_no" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">8. Legal & Regulatory</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="legal_requirements" label="Legal / zoning / permit needs" type="textarea" />
                        <x-public.forms.partials.field name="needs_inspection_help" label="Need help with inspection / legal matters?" type="yes_no" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">9. Maintenance & Future Needs</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="maintenance_effort" label="Time / effort willing to spend on maintenance" />
                        <x-public.forms.partials.field name="maintenance_preference" label="Maintenance preference" type="select" :options="[
                            'low' => 'Low maintenance',
                            'medium' => 'Medium',
                            'high' => 'High (hands-on)',
                        ]" />
                        <x-public.forms.partials.field name="additional_services" label="Additional services needed (security / cleaning / landscaping)" type="textarea" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">10. Family & Personal Considerations</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="household_type" label="Living arrangement" type="select" :options="[
                            'alone' => 'Alone',
                            'family' => 'With family',
                            'with_pets' => 'With pets',
                        ]" />
                        <x-public.forms.partials.field name="accessibility_needs" label="Accessibility / safety needs (children, elderly)" type="textarea" />
                        <x-public.forms.partials.field name="pet_accommodations" label="Pet accommodation requirements" type="textarea" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">11. Sustainability & Efficiency</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="eco_priority" label="Energy / eco priority" type="select" :options="[
                            'high' => 'High priority',
                            'medium' => 'Medium',
                            'low' => 'Low',
                            'none' => 'Not important',
                        ]" />
                        <x-public.forms.partials.field name="smart_home_interest" label="Interested in smart home tech?" type="yes_no" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">12. Post-Purchase Considerations</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="customizable_required" label="Need a property easy to customize / renovate?" type="yes_no" />
                        <x-public.forms.partials.field name="needs_reno_design_help" label="Need renovation / design assistance after purchase?" type="yes_no" />
                        <x-public.forms.partials.field name="resale_plan" label="Resale plans / high-resale-value preference" type="textarea" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">13. Miscellaneous</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="property_age_preference" label="Age of property preference" type="select" :options="[
                            'new' => 'Brand-new',
                            'older' => 'Older / character',
                            'no_preference' => 'No preference',
                        ]" />
                        <x-public.forms.partials.field name="turnkey_preference" label="Turnkey or personalize?" type="select" :options="[
                            'turnkey' => 'Turnkey (move-in ready)',
                            'personalize' => 'Personalize / develop',
                            'either' => 'Either',
                        ]" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="other_considerations" label="Other special considerations or needs" type="textarea" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Referral Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="previous_company_contact" label="Contacted other companies / agents before?" type="yes_no" />
                        <x-public.forms.partials.field name="previous_company_experience" label="Describe that experience (if any)" type="textarea" />
                        <x-public.forms.partials.field name="referral_source" label="How did you learn about us?" />
                        <x-public.forms.partials.field name="referral_name" label="Name of person / media (if applicable)" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="notes" label="Internal notes (optional)" type="textarea" />
                        </div>
                    </div>
                </div>

                <x-public.forms.partials.agreement :policyKeys="['policy.purchase_build']" />

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
