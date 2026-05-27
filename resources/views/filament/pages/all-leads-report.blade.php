<x-filament-panels::page>
    @php
        $rows = $this->getRows();
        $categories = $this->getCategories();

        $categoryStyles = [
            'Land Purchase' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/30',
            'Land Sale' => 'bg-amber-50 text-amber-800 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/30',
            'Rental' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/30',
            'Built Property Listing' => 'bg-purple-50 text-purple-700 ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/30',
            'Buy Built Property' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/30',
        ];

        $statusStyles = [
            'new' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            'in_review' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
            'contacted' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
            'converted' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            'closed' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        ];

        $formatBudget = function ($min, $max) {
            if (! $min && ! $max) return null;
            if ($min && $max && (float) $min !== (float) $max) {
                return 'D' . number_format((float) $min) . ' – D' . number_format((float) $max);
            }
            return 'D' . number_format((float) ($max ?? $min));
        };
    @endphp

    <div class="space-y-4">
        {{-- Filter bar --}}
        <div class="flex flex-wrap items-center gap-3 bg-white dark:bg-gray-900 rounded-xl p-4 ring-1 ring-gray-200 dark:ring-gray-800 shadow-sm">
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rows->count() }}</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">request{{ $rows->count() === 1 ? '' : 's' }}</span>
            </div>
            <div class="flex flex-wrap gap-2 ml-auto">
                <button
                    wire:click="$set('filterCategory', null)"
                    type="button"
                    class="px-3 py-1.5 rounded-full text-xs font-medium ring-1 transition {{ $this->filterCategory === null ? 'bg-primary-600 text-white ring-primary-600' : 'bg-white dark:bg-gray-800 ring-gray-300 dark:ring-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    All
                </button>
                @foreach($categories as $cat)
                    @php $isActive = $this->filterCategory === $cat; @endphp
                    <button
                        wire:click="$set('filterCategory', @js($cat))"
                        type="button"
                        class="px-3 py-1.5 rounded-full text-xs font-medium ring-1 transition {{ $isActive ? 'bg-primary-600 text-white ring-primary-600' : 'bg-white dark:bg-gray-800 ring-gray-300 dark:ring-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Table card --}}
        <div class="overflow-hidden rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Date</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Category</th>
                            <th class="px-4 py-3 text-left font-semibold">Client</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Contact</th>
                            <th class="px-4 py-3 text-left font-semibold">Location</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Pty Size</th>
                            <th class="px-4 py-3 text-right font-semibold whitespace-nowrap">Budget</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Referred By</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($rows as $row)
                            @php
                                $catCls = $categoryStyles[$row['category']] ?? 'bg-gray-100 text-gray-700 ring-gray-300';
                                $statusCls = $statusStyles[$row['status']] ?? 'bg-gray-100 text-gray-600';
                                $budget = $formatBudget($row['budget_min'], $row['budget_max']);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-300 tabular-nums">
                                    {{ $row['date']?->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $catCls }}">{{ $row['category'] }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $row['client_name'] ?: '—' }}</div>
                                    @if($row['email'])
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $row['email'] }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300 tabular-nums">
                                    {{ $row['contact'] ?: '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $row['location'] ?: '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    {{ $row['plot_size'] ?: '—' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap tabular-nums {{ $budget ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-400 dark:text-gray-600' }}">
                                    {{ $budget ?: '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($row['referred_by'])
                                        <span class="text-gray-700 dark:text-gray-300">{{ $row['referred_by'] }}</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium capitalize {{ $statusCls }}">
                                        {{ str_replace('_', ' ', $row['status']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <a href="{{ $row['url'] }}"
                                       class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-xs font-medium">
                                        View
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m-6-8h6M5 6a2 2 0 012-2h10a2 2 0 012 2v14l-7-3-7 3V6z"/></svg>
                                        <p class="text-sm">No requests match this filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
