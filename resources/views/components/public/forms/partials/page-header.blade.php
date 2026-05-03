@props(['title', 'subtitle' => null])

<section class="bg-gray-900 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-sm uppercase tracking-wider text-[#a94a2a] mb-2">
            <a href="{{ route('forms.index') }}" class="hover:text-white">Forms</a>
            <span class="text-gray-500"> / </span>
            <span class="text-gray-300">Submit</span>
        </p>
        <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-gray-400">{{ $subtitle }}</p>
        @endif
    </div>
</section>

@if($errors->any())
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
            <p class="font-semibold mb-2">Please correct the following:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
