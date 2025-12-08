<x-filament::card>
    <div class="p-4">
        <div class="font-bold text-lg mb-2">Recent Inquiries</div>
        <ul>
            @forelse ($inquiries as $inquiry)
                <li class="mb-2 border-b pb-2">
                    <div class="font-semibold">{{ $inquiry->name }} ({{ $inquiry->email }})</div>
                    <div class="text-sm text-gray-600">{{ $inquiry->message }}</div>
                    <div class="text-xs text-gray-400">Status: {{ $inquiry->status }} | {{ $inquiry->created_at->format('M d, Y H:i') }}</div>
                </li>
            @empty
                <li class="text-gray-500">No recent inquiries.</li>
            @endforelse
        </ul>
    </div>
</x-filament::card>
