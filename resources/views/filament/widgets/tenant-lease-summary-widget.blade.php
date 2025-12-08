<x-filament::widget>
    <div class="p-6 bg-white rounded-xl shadow-md flex flex-col items-center">
        <h2 class="text-xl font-bold mb-2 text-primary">Lease Summary</h2>
        <div class="flex gap-8 justify-center mb-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary">{{ $this->leaseCount }}</div>
                <div class="text-sm text-gray-500">Total Leases</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ $this->activeLeases }}</div>
                <div class="text-sm text-gray-500">Active</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-red-500">{{ $this->expiredLeases }}</div>
                <div class="text-sm text-gray-500">Expired</div>
            </div>
        </div>
    </div>
</x-filament::widget>
