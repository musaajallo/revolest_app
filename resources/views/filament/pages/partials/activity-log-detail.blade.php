<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->user_name ?? 'System' }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->created_at->format('M d, Y H:i:s') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Action</dt>
            <dd class="mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @switch($record->action)
                        @case('created') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                        @case('updated') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                        @case('deleted') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                        @case('logged_in') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                        @case('logged_out') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @break
                        @default bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @endswitch
                ">
                    {{ ucfirst(str_replace('_', ' ', $record->action)) }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Resource</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->getModelName() }}</dd>
        </div>
        @if($record->model_label)
        <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Item</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->model_label }}</dd>
        </div>
        @endif
        <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->ip_address ?? '-' }}</dd>
        </div>
    </div>

    @if($record->description)
    <div>
        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->description }}</dd>
    </div>
    @endif

    @if($record->old_values && count($record->old_values) > 0)
    <div>
        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Previous Values</dt>
        <dd class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
            <dl class="space-y-1">
                @foreach($record->old_values as $key => $value)
                    <div class="flex">
                        <dt class="text-xs font-medium text-red-700 dark:text-red-300 w-32">{{ ucfirst(str_replace('_', ' ', $key)) }}:</dt>
                        <dd class="text-xs text-red-600 dark:text-red-400">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </dd>
    </div>
    @endif

    @if($record->new_values && count($record->new_values) > 0)
    <div>
        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">New Values</dt>
        <dd class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
            <dl class="space-y-1">
                @foreach($record->new_values as $key => $value)
                    <div class="flex">
                        <dt class="text-xs font-medium text-green-700 dark:text-green-300 w-32">{{ ucfirst(str_replace('_', ' ', $key)) }}:</dt>
                        <dd class="text-xs text-green-600 dark:text-green-400">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </dd>
    </div>
    @endif

    @if($record->user_agent)
    <div>
        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User Agent</dt>
        <dd class="mt-1 text-xs text-gray-500 dark:text-gray-400 break-all">{{ $record->user_agent }}</dd>
    </div>
    @endif
</div>
