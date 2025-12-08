<x-filament-panels::page>
    <div x-data="{ activeTab: @entangle('activeTab') }">
        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <button
                    @click="activeTab = 'user'"
                    :class="{
                        'border-primary-500 text-primary-600 dark:text-primary-400': activeTab === 'user',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'user'
                    }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition"
                    type="button"
                >
                    <x-heroicon-o-users class="w-5 h-5 inline-block mr-2 -mt-0.5" />
                    User Activities
                </button>
                <button
                    @click="activeTab = 'application'"
                    :class="{
                        'border-primary-500 text-primary-600 dark:text-primary-400': activeTab === 'application',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'application'
                    }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition"
                    type="button"
                >
                    <x-heroicon-o-server class="w-5 h-5 inline-block mr-2 -mt-0.5" />
                    Application Logs
                </button>
            </nav>
        </div>

        <!-- User Activities Tab -->
        <div x-show="activeTab === 'user'" x-cloak>
            {{ $this->table }}
        </div>

        <!-- Application Logs Tab -->
        <div x-show="activeTab === 'application'" x-cloak>
            <x-filament::section>
                <x-slot name="heading">
                    Application Logs
                </x-slot>
                <x-slot name="description">
                    System-level application logs and errors
                </x-slot>

                @php
                    $logs = $this->getApplicationLogs();
                @endphp

                @if($logs->isEmpty())
                    <div class="text-center py-12">
                        <x-heroicon-o-clipboard-document-list class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No logs found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Application logs will appear here.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-44">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">Level</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Message</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $log['date'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @php
                                                $levelColors = [
                                                    'ERROR' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    'WARNING' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'INFO' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                    'DEBUG' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                ];
                                                $color = $levelColors[$log['level']] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                                {{ $log['level'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                            <div class="max-w-2xl truncate" title="{{ $log['message'] }}">
                                                {{ \Illuminate\Support\Str::limit($log['message'], 200) }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-4 flex justify-end">
                    <x-filament::button
                        wire:click="$refresh"
                        color="gray"
                        size="sm"
                    >
                        <x-heroicon-m-arrow-path class="w-4 h-4 mr-2" />
                        Refresh
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
