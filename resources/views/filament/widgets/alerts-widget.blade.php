<x-filament-widgets::widget>
    <x-filament::section heading="System Alerts" icon="heroicon-o-bell-alert">
        <div class="space-y-4">
            <!-- Open Repair Requests -->
            <a href="{{ route('filament.admin.resources.repair-requests.index') }}" 
               class="flex items-center justify-between p-4 rounded-lg border transition hover:bg-gray-50 dark:hover:bg-gray-800 
                      {{ $this->getViewData()['openRepairs'] > 0 ? 'border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800' : 'border-gray-200 dark:border-gray-700' }}">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-full {{ $this->getViewData()['openRepairs'] > 0 ? 'bg-amber-100 dark:bg-amber-900' : 'bg-gray-100 dark:bg-gray-800' }}">
                        <svg class="w-5 h-5 {{ $this->getViewData()['openRepairs'] > 0 ? 'text-amber-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Open Repair Requests</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Needs attention</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $this->getViewData()['openRepairs'] > 0 ? 'bg-amber-200 text-amber-800 dark:bg-amber-800 dark:text-amber-200' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                    {{ $this->getViewData()['openRepairs'] }}
                </span>
            </a>

            <!-- Open Complaints -->
            <a href="{{ route('filament.admin.resources.complaints.index') }}" 
               class="flex items-center justify-between p-4 rounded-lg border transition hover:bg-gray-50 dark:hover:bg-gray-800 
                      {{ $this->getViewData()['openComplaints'] > 0 ? 'border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800' : 'border-gray-200 dark:border-gray-700' }}">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-full {{ $this->getViewData()['openComplaints'] > 0 ? 'bg-red-100 dark:bg-red-900' : 'bg-gray-100 dark:bg-gray-800' }}">
                        <svg class="w-5 h-5 {{ $this->getViewData()['openComplaints'] > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Open Complaints</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Requires resolution</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $this->getViewData()['openComplaints'] > 0 ? 'bg-red-200 text-red-800 dark:bg-red-800 dark:text-red-200' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                    {{ $this->getViewData()['openComplaints'] }}
                </span>
            </a>

            <!-- Pending Inquiries -->
            <a href="{{ route('filament.admin.resources.inquiries.index') }}" 
               class="flex items-center justify-between p-4 rounded-lg border transition hover:bg-gray-50 dark:hover:bg-gray-800 
                      {{ $this->getViewData()['pendingInquiries'] > 0 ? 'border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800' : 'border-gray-200 dark:border-gray-700' }}">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-full {{ $this->getViewData()['pendingInquiries'] > 0 ? 'bg-blue-100 dark:bg-blue-900' : 'bg-gray-100 dark:bg-gray-800' }}">
                        <svg class="w-5 h-5 {{ $this->getViewData()['pendingInquiries'] > 0 ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Pending Inquiries</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Awaiting response</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $this->getViewData()['pendingInquiries'] > 0 ? 'bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-200' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                    {{ $this->getViewData()['pendingInquiries'] }}
                </span>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
