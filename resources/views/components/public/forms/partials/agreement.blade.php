@props(['policyKeys' => []])

@php
    $policies = collect($policyKeys)
        ->map(fn ($key) => \App\Models\Setting::get($key))
        ->filter();
    $hasMarkdownClass = class_exists(\Illuminate\Support\Str::class) && method_exists(\Illuminate\Support\Str::class, 'markdown');
@endphp

<div class="bg-amber-50 border border-amber-200 rounded-xl p-6 space-y-4">
    <h3 class="text-lg font-semibold text-amber-900">Policies & Fees Agreement</h3>

    @forelse($policies as $policy)
        <div class="prose prose-sm max-w-none text-gray-800">
            {!! $hasMarkdownClass ? \Illuminate\Support\Str::markdown($policy) : nl2br(e($policy)) !!}
        </div>
        @if(! $loop->last)
            <hr class="border-amber-200">
        @endif
    @empty
        <p class="text-sm text-gray-700">By submitting this form you acknowledge our standard terms.</p>
    @endforelse

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-amber-200">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type your full name as electronic signature *</label>
            <input type="text" name="signed_name" required value="{{ old('signed_name') }}"
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1c4736] focus:border-transparent @error('signed_name') border-red-500 @enderror">
            @error('signed_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-end text-sm text-gray-700">
            <p>Date: <strong>{{ now()->format('d M Y') }}</strong></p>
        </div>
    </div>

    <label class="flex items-start gap-3 cursor-pointer">
        <input type="checkbox" name="agree_terms" value="1" required
               class="mt-1 h-5 w-5 rounded border-gray-300 text-[#1c4736] focus:ring-[#1c4736] @error('agree_terms') border-red-500 @enderror">
        <span class="text-sm text-gray-800">
            I confirm the information above is accurate to the best of my knowledge and I agree to the policies and fees outlined.
        </span>
    </label>
    @error('agree_terms')
        <p class="text-red-500 text-sm">You must agree to the terms before submitting.</p>
    @enderror
</div>
