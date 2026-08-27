<li class="px-5 py-4 flex items-center justify-between gap-4">
    <div class="min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs px-2 py-0.5 rounded font-medium bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300">{{ $meeting->scheduled_at->format('h:i A') }}</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $meeting->title }}</span>
            @if ($meeting->customer)
                <a href="{{ route('customers.show', $meeting->customer) }}" class="text-xs text-accent-600 hover:underline">{{ $meeting->customer->name }}</a>
            @endif
            @if ($meeting->project)
                <span class="text-xs text-gray-400">· {{ $meeting->project->name }}</span>
            @endif
        </div>
        @if ($meeting->location)
            <div class="text-sm text-gray-600 dark:text-gray-400 truncate">
                @if ($meeting->isVideoLink())
                    <a href="{{ $meeting->location }}" target="_blank" rel="noopener" class="text-accent-600 hover:underline">{{ __('Join link') }} ↗</a>
                @else
                    📍 {{ $meeting->location }}
                @endif
            </div>
        @endif
        @if ($meeting->attendees)
            <div class="text-xs text-gray-400 mt-0.5">{{ __('With') }}: {{ $meeting->attendees }}</div>
        @endif
        @if (isset($showStatus) && $showStatus)
            <div class="text-xs mt-0.5 {{ $meeting->status === 'completed' ? 'text-green-600' : 'text-gray-400' }}">{{ ucfirst($meeting->status) }} · {{ $meeting->scheduled_at->format('d M Y') }}</div>
        @endif
    </div>
    <div class="flex items-center gap-3 shrink-0">
        @if ($url = $meeting->whatsappUrl())
            <a href="{{ $url }}" target="_blank" rel="noopener"
                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700">
                {{ __('WhatsApp') }}
            </a>
        @endif
        @if ($meeting->status === 'scheduled')
            <a href="{{ route('meetings.edit', $meeting) }}" class="text-xs text-accent-600 hover:underline">{{ __('Edit') }}</a>
            <form method="POST" action="{{ route('meetings.complete', $meeting) }}">
                @csrf
                <button class="text-xs text-accent-600 hover:underline">{{ __('Mark done') }}</button>
            </form>
            <form method="POST" action="{{ route('meetings.cancel', $meeting) }}">
                @csrf
                <button class="text-xs text-gray-500 dark:text-gray-400 hover:underline">{{ __('Cancel') }}</button>
            </form>
        @endif
        <form method="POST" action="{{ route('meetings.destroy', $meeting) }}" onsubmit="return confirm('{{ __('Remove this meeting?') }}')">
            @csrf
            @method('DELETE')
            <button class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
        </form>
    </div>
</li>
