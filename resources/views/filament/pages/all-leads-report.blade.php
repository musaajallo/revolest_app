<x-filament-panels::page>
    @php
        $rows = $this->getRows();
        $categories = $this->getCategories();
    @endphp

    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $rows->count() }} request(s)</span>
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="$set('filterCategory', null)"
                class="px-3 py-1 rounded-full text-xs font-medium border {{ $this->filterCategory === null ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}">
                All
            </button>
            @foreach($categories as $cat)
                <button
                    wire:click="$set('filterCategory', @js($cat))"
                    class="px-3 py-1 rounded-full text-xs font-medium border {{ $this->filterCategory === $cat ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}">
                    {{ $cat }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-3 py-2 text-left font-medium">Date</th>
                    <th class="px-3 py-2 text-left font-medium">Category</th>
                    <th class="px-3 py-2 text-left font-medium">Client</th>
                    <th class="px-3 py-2 text-left font-medium">Contact</th>
                    <th class="px-3 py-2 text-left font-medium">Email</th>
                    <th class="px-3 py-2 text-left font-medium">Location</th>
                    <th class="px-3 py-2 text-left font-medium">Pty Size</th>
                    <th class="px-3 py-2 text-right font-medium">Budget Min</th>
                    <th class="px-3 py-2 text-right font-medium">Budget Max</th>
                    <th class="px-3 py-2 text-left font-medium">Referred By</th>
                    <th class="px-3 py-2 text-left font-medium">Status</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $row['date']?->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">{{ $row['category'] }}</span>
                        </td>
                        <td class="px-3 py-2 text-gray-900 dark:text-white font-medium">{{ $row['client_name'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $row['contact'] }}</td>
                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $row['email'] }}</td>
                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $row['location'] }}</td>
                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $row['plot_size'] }}</td>
                        <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">{{ $row['budget_min'] ? 'D' . number_format((float) $row['budget_min']) : '' }}</td>
                        <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">{{ $row['budget_max'] ? 'D' . number_format((float) $row['budget_max']) : '' }}</td>
                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $row['referred_by'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs
                                @switch($row['status'])
                                    @case('new') bg-blue-100 text-blue-700 @break
                                    @case('in_review') bg-amber-100 text-amber-700 @break
                                    @case('contacted') bg-purple-100 text-purple-700 @break
                                    @case('converted') bg-green-100 text-green-700 @break
                                    @case('closed') bg-gray-200 text-gray-700 @break
                                    @default bg-gray-100 text-gray-600
                                @endswitch">
                                {{ $row['status'] }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <a href="{{ $row['url'] }}" class="text-primary-600 hover:underline text-xs">View →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">No requests match this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
