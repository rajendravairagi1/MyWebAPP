<li class="py-3 flex items-center justify-between gap-4">
    <a href="{{ route('unit-media.show', [$unit, $item]) }}" target="_blank" rel="noopener" class="flex items-center gap-2 min-w-0 text-accent-600 hover:underline">
        @if ($item->isImage())
            <img src="{{ route('unit-media.show', [$unit, $item]) }}" alt="{{ $item->original_name }}" loading="lazy" class="h-8 w-8 object-cover rounded shrink-0 border border-gray-200 dark:border-slate-700">
        @else
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
        @endif
        <span class="truncate">{{ $item->original_name }}</span>
    </a>
    <div class="flex items-center gap-3 shrink-0 text-xs text-gray-400">
        <span>{{ $item->humanSize() }}</span>
        <span>{{ $item->created_at->format('d M Y') }}</span>
        <a href="{{ route('unit-media.download', [$unit, $item]) }}" class="text-accent-600 hover:underline">{{ __('Download') }}</a>
        <form method="POST" action="{{ route('unit-media.destroy', [$unit, $item]) }}" onsubmit="return confirm('{{ __('Delete this file?') }}')">
            @csrf
            @method('DELETE')
            <button class="text-red-600 hover:underline">{{ __('Delete') }}</button>
        </form>
    </div>
</li>
