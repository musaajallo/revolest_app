<div class="relative" x-data="{ open: false }" @click.outside="open = false" wire:poll.30s>
    <!-- Notification Bell Button -->
    <button
        @click="open = !open"
        type="button"
        class="relative inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 transition"
        style="overflow: visible;"
    >
        <x-heroicon-o-envelope class="h-6 w-6" />
        @if($this->newInquiriesCount > 0)
            <span
                class="flex items-center justify-center rounded-full shadow-lg"
                style="position: absolute; top: -4px; right: -4px; min-width: 20px; height: 20px; padding: 0 6px; z-index: 50; background-color: #d41313; color: #ffffff; font-size: 11px; font-weight: 700;"
            >
                {{ $this->newInquiriesCount > 99 ? '99+' : $this->newInquiriesCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        style="position: fixed; top: 60px; right: 80px; width: 380px;"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-[9999] overflow-hidden"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                New Inquiries
                @if($this->newInquiriesCount > 0)
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        {{ $this->newInquiriesCount }}
                    </span>
                @endif
            </h3>
            @if($this->newInquiriesCount > 0)
                <button wire:click="markAllAsRead" @click="open = false" type="button" class="text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400 font-medium">
                    Mark all read
                </button>
            @endif
        </div>

        <!-- Inquiry List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($this->recentInquiries as $inquiry)
                <div
                    wire:click="markAsRead({{ $inquiry->id }})"
                    x-on:click="setTimeout(() => { window.location.href = '{{ route('filament.admin.resources.inquiries.view', $inquiry) }}' }, 100)"
                    class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-primary-100 dark:bg-primary-900/50 rounded-full flex items-center justify-center">
                            <span class="text-sm font-semibold text-primary-700 dark:text-primary-300">
                                {{ strtoupper(substr($inquiry->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $inquiry->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $inquiry->email }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">
                                {{ Str::limit($inquiry->message, 60) }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                {{ $inquiry->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-center">
                    <x-heroicon-o-inbox class="h-8 w-8 mx-auto text-gray-300 dark:text-gray-600" />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No new inquiries</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <a href="{{ route('filament.admin.resources.inquiries.index') }}" class="block text-center text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 font-medium">
                View all inquiries →
            </a>
        </div>
    </div>
</div>
