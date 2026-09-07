<li class="px-5 py-4 flex items-center justify-between gap-4">
    <div class="min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs px-2 py-0.5 rounded font-medium bg-accent-100 dark:bg-slate-700 text-accent-700">{{ $followup->categoryLabel() }}</span>
            <a href="{{ $followup->customer_id ? route('customers.show', $followup->customer_id) : route('leads.show', $followup->lead_id) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:underline">{{ $followup->contact()->name }}</a>
            @if ($followup->lead_id)
                <span class="text-xs px-2 py-0.5 rounded font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">{{ __('Lead') }}</span>
            @endif
            @if ($followup->project)
                <span class="text-xs text-gray-400">· {{ $followup->project->name }}</span>
            @endif
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $followup->note }}</div>
        <div class="text-xs text-gray-400 mt-0.5">{{ $followup->due_at->format('d M Y, h:i A') }}</div>
    </div>
    <div class="flex items-center gap-3 shrink-0">
        @if ($url = $followup->whatsappUrl())
            <a href="{{ $url }}" target="_blank" rel="noopener"
                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700">
                {{ __('WhatsApp') }}
            </a>
        @endif
        <form method="POST" action="{{ route('followups.complete', $followup) }}">
            @csrf
            <button class="text-xs text-accent-600 hover:underline">{{ __('Mark done') }}</button>
        </form>
        <details class="relative">
            <summary class="cursor-pointer text-xs text-accent-600 hover:underline list-none [&::-webkit-details-marker]:hidden">{{ __('Edit') }}</summary>
            @include('followups._edit-fields', ['followup' => $followup])
        </details>
        <form method="POST" action="{{ route('followups.destroy', $followup) }}" onsubmit="return confirm('{{ __('Remove this follow-up?') }}')">
            @csrf
            @method('DELETE')
            <button class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
        </form>
    </div>
</li>
