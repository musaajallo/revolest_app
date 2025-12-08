<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Inquiry Requests -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Inquiry Requests</h3>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </button>
                </div>
                <div class="flex flex-col items-center">
                    <!-- Circular Progress -->
                    <div class="relative w-32 h-32">
                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="50" stroke="#e5e7eb" stroke-width="10" fill="none" class="dark:stroke-gray-700"/>
                            <circle cx="60" cy="60" r="50" stroke="#3b82f6" stroke-width="10" fill="none"
                                stroke-dasharray="{{ $this->getViewData()['inquiries']['percentage'] * 3.14 }} 314"
                                stroke-linecap="round" class="transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-blue-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->getViewData()['inquiries']['total']) }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Requests</span>
                        </div>
                    </div>
                    <!-- Stats -->
                    <div class="flex gap-6 mt-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span class="text-gray-600 dark:text-gray-400">New:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($this->getViewData()['inquiries']['new']) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-gray-600 dark:text-gray-400">Responded:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($this->getViewData()['inquiries']['responded']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Listings -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active Listings</h3>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </button>
                </div>
                <div class="flex flex-col items-center">
                    <!-- Circular Progress -->
                    <div class="relative w-32 h-32">
                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="50" stroke="#e5e7eb" stroke-width="10" fill="none" class="dark:stroke-gray-700"/>
                            <circle cx="60" cy="60" r="50" stroke="#10b981" stroke-width="10" fill="none"
                                stroke-dasharray="{{ $this->getViewData()['listings']['percentage'] * 3.14 }} 314"
                                stroke-linecap="round" class="transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-green-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->getViewData()['listings']['active']) }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Listings</span>
                        </div>
                    </div>
                    <!-- Stats -->
                    <div class="flex gap-6 mt-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-gray-600 dark:text-gray-400">Active:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($this->getViewData()['listings']['active']) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span class="text-gray-600 dark:text-gray-400">New:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($this->getViewData()['listings']['new']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Occupancy Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Occupancy Rate</h3>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </button>
                </div>
                <div class="flex flex-col items-center">
                    <!-- Circular Progress -->
                    <div class="relative w-32 h-32">
                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="50" stroke="#e5e7eb" stroke-width="10" fill="none" class="dark:stroke-gray-700"/>
                            <circle cx="60" cy="60" r="50" stroke="#d41313" stroke-width="10" fill="none"
                                stroke-dasharray="{{ $this->getViewData()['occupancy']['rate'] * 3.14 }} 314"
                                stroke-linecap="round" class="transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xs text-green-500 font-medium mb-1">
                                @if($this->getViewData()['occupancy']['rate'] >= 80)
                                    Excellent!
                                @elseif($this->getViewData()['occupancy']['rate'] >= 60)
                                    Good
                                @elseif($this->getViewData()['occupancy']['rate'] >= 40)
                                    Fair
                                @else
                                    Low
                                @endif
                            </span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->getViewData()['occupancy']['rate'] }}%</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Occupied</span>
                        </div>
                    </div>
                    <!-- Stats -->
                    <div class="flex gap-6 mt-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            <span class="text-gray-600 dark:text-gray-400">Occupied:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($this->getViewData()['occupancy']['occupied']) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                            <span class="text-gray-600 dark:text-gray-400">Total:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($this->getViewData()['occupancy']['total']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
