@extends('layouts.public')
@section('title', 'Maintenance Request')

@section('content')
    <x-public.forms.partials.page-header
        title="Maintenance Request"
        subtitle="Report a maintenance issue. Please provide as much detail as possible." />

    <section class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('forms.maintenance-request.store') }}" method="POST" class="space-y-8">
                @csrf
                <x-public.forms.partials.honeypot />

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Your Contact</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="first_name" label="First Name" required />
                        <x-public.forms.partials.field name="last_name" label="Last Name" required />
                        <x-public.forms.partials.field name="email" label="Email" type="email" />
                        <x-public.forms.partials.field name="phone" label="Phone" type="tel" required />
                        <x-public.forms.partials.field name="property_address" label="Property Address" required />
                        <x-public.forms.partials.field name="apartment_number" label="Apartment / Compound Number" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Maintenance Details</h2>
                    <div class="space-y-6">
                        <x-public.forms.partials.field name="description" label="Describe the work needed in detail" type="textarea" rows="5" required />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-public.forms.partials.field name="priority" label="Priority" type="select" :options="[
                                'urgent' => 'Urgent',
                                'immediate' => 'Immediate',
                                'emergency' => 'Emergency',
                            ]" />
                            <x-public.forms.partials.field name="category" label="Category" placeholder="e.g. plumbing, electrical, AC" />
                        </div>
                        <x-public.forms.partials.field name="preferred_visit" label="When should we visit?" type="select" :options="[
                            'home' => 'Yes, I prefer to be home',
                            'anytime' => 'No, come anytime',
                            'call_to_confirm' => 'Call to confirm',
                            'fix_appointment' => 'Fix an appointment',
                        ]" />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-public.forms.partials.field name="has_pets" label="Any pets we should know about?" type="yes_no" />
                            <x-public.forms.partials.field name="pet_notes" label="If yes, describe" />
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-amber-900">Permission & Signature</h3>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="permission_to_enter" value="1" required
                               class="mt-1 h-5 w-5 rounded border-gray-300 text-[#1c4736] focus:ring-[#1c4736] @error('permission_to_enter') border-red-500 @enderror">
                        <span class="text-sm text-gray-800">
                            I give permission to the company or its subcontractors to enter my apartment / compound and make the necessary repairs.
                        </span>
                    </label>
                    @error('permission_to_enter')
                        <p class="text-red-500 text-sm">You must grant entry permission to submit this request.</p>
                    @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-amber-200">
                        <x-public.forms.partials.field name="tenant_signature_name" label="Type your full name as electronic signature" required />
                        <div class="flex items-end text-sm text-gray-700">
                            <p>Date: <strong>{{ now()->format('d M Y') }}</strong></p>
                        </div>
                    </div>
                </div>

                <x-public.forms.partials.turnstile />

                <div>
                    <button type="submit" class="bg-[#a94a2a] hover:bg-[#8a3c22] text-white px-8 py-3 rounded-lg font-semibold transition">
                        Submit Maintenance Request
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
