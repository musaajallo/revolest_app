@extends('layouts.public')
@section('title', 'Pet Application')

@section('content')
    <x-public.forms.partials.page-header
        title="Pet Application"
        subtitle="Apply to keep one or more pets at your rental property." />

    <section class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('forms.pet-application.store') }}" method="POST" class="space-y-8"
                  x-data="{
                    pets: @js(old('pets', [['type' => 'dog', 'spayed_neutered' => false, 'house_trained' => false, 'vaccinations_up_to_date' => false, 'aggression_history' => false, 'special_medical_needs' => false]])),
                    add() {
                        if (this.pets.length >= 5) return;
                        this.pets.push({type: '', spayed_neutered: false, house_trained: false, vaccinations_up_to_date: false, aggression_history: false, special_medical_needs: false});
                    },
                    remove(i) {
                        if (this.pets.length <= 1) return;
                        this.pets.splice(i, 1);
                    },
                  }">
                @csrf
                <x-public.forms.partials.honeypot />

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Tenant & Property</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="tenant_name" label="Tenant Name" required />
                        <x-public.forms.partials.field name="phone" label="Phone" type="tel" required />
                        <x-public.forms.partials.field name="email" label="Email" type="email" />
                        <x-public.forms.partials.field name="property_address" label="Property Address" required />
                        <x-public.forms.partials.field name="lease_start_date" label="Lease Start Date" type="date" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Pets</h2>
                        <button type="button" @click="add()" :disabled="pets.length >= 5"
                                class="text-sm bg-[#1c4736] hover:bg-[#15382a] disabled:opacity-50 text-white px-4 py-2 rounded-lg font-medium transition">
                            + Add another pet
                        </button>
                    </div>

                    <template x-for="(pet, i) in pets" :key="i">
                        <div class="border border-gray-200 rounded-lg p-4 mb-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-700">Pet <span x-text="i + 1"></span></h3>
                                <button type="button" @click="remove(i)" x-show="pets.length > 1"
                                        class="text-sm text-red-600 hover:text-red-800">Remove</button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pet's name</label>
                                    <input type="text" :name="`pets[${i}][name]`" x-model="pet.name"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1c4736]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                    <input type="text" :name="`pets[${i}][type]`" x-model="pet.type" required placeholder="dog, cat, bird, …"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1c4736]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Breed</label>
                                    <input type="text" :name="`pets[${i}][breed]`" x-model="pet.breed"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1c4736]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                    <input type="text" :name="`pets[${i}][age]`" x-model="pet.age"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1c4736]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Weight</label>
                                    <input type="text" :name="`pets[${i}][weight]`" x-model="pet.weight"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1c4736]">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                                <label class="inline-flex items-center gap-2">
                                    <input type="hidden" :name="`pets[${i}][spayed_neutered]`" :value="pet.spayed_neutered ? 1 : 0">
                                    <input type="checkbox" x-model="pet.spayed_neutered" class="rounded text-[#1c4736] focus:ring-[#1c4736]">
                                    <span>Spayed / neutered</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input type="hidden" :name="`pets[${i}][house_trained]`" :value="pet.house_trained ? 1 : 0">
                                    <input type="checkbox" x-model="pet.house_trained" class="rounded text-[#1c4736] focus:ring-[#1c4736]">
                                    <span>House-trained</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input type="hidden" :name="`pets[${i}][vaccinations_up_to_date]`" :value="pet.vaccinations_up_to_date ? 1 : 0">
                                    <input type="checkbox" x-model="pet.vaccinations_up_to_date" class="rounded text-[#1c4736] focus:ring-[#1c4736]">
                                    <span>Vaccinations up to date</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input type="hidden" :name="`pets[${i}][aggression_history]`" :value="pet.aggression_history ? 1 : 0">
                                    <input type="checkbox" x-model="pet.aggression_history" class="rounded text-[#1c4736] focus:ring-[#1c4736]">
                                    <span>History of aggression</span>
                                </label>
                                <label class="inline-flex items-center gap-2">
                                    <input type="hidden" :name="`pets[${i}][special_medical_needs]`" :value="pet.special_medical_needs ? 1 : 0">
                                    <input type="checkbox" x-model="pet.special_medical_needs" class="rounded text-[#1c4736] focus:ring-[#1c4736]">
                                    <span>Special medical needs</span>
                                </label>
                            </div>

                            <div x-show="pet.aggression_history">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Aggression details</label>
                                <textarea :name="`pets[${i}][aggression_notes]`" x-model="pet.aggression_notes" rows="2"
                                          class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1c4736]"></textarea>
                            </div>
                            <div x-show="pet.special_medical_needs">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Medical / treatment details</label>
                                <textarea :name="`pets[${i}][medical_notes]`" x-model="pet.medical_notes" rows="2"
                                          class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1c4736]"></textarea>
                            </div>
                        </div>
                    </template>

                    @error('pets')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    @foreach($errors->keys() as $key)
                        @if(str_starts_with($key, 'pets.'))
                            <p class="text-red-500 text-sm">{{ $errors->first($key) }}</p>
                        @endif
                    @endforeach
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Living Arrangements</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-public.forms.partials.field name="keep_location" label="Will the pet be kept indoors, outdoors, or both?" type="select" :options="[
                            'indoors' => 'Indoors',
                            'outdoors' => 'Outdoors',
                            'both' => 'Both',
                        ]" />
                        <x-public.forms.partials.field name="supervised_outdoors" label="Will the pet be supervised while outdoors?" type="yes_no" />
                        <x-public.forms.partials.field name="past_complaints" label="Any history of pet-related complaints / damages at previous rentals?" type="yes_no" />
                        <div class="md:col-span-2">
                            <x-public.forms.partials.field name="past_complaints_notes" label="If yes, explain" type="textarea" />
                        </div>
                        <x-public.forms.partials.field name="emergency_contact_name" label="Emergency contact name" />
                        <x-public.forms.partials.field name="emergency_contact_phone" label="Emergency contact phone" type="tel" />
                    </div>
                </div>

                <x-public.forms.partials.agreement :policyKeys="['policy.pet_application']" />

                <x-public.forms.partials.turnstile />

                <div>
                    <button type="submit" class="bg-[#a94a2a] hover:bg-[#8a3c22] text-white px-8 py-3 rounded-lg font-semibold transition">
                        Submit Pet Application
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
