<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Notifications') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Every notification this business has had — new leads, follow-ups, possession commitments and meetings. Delete anything you no longer need to keep.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                @if ($notifications->isEmpty())
                    <div class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No notifications yet.') }}</div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach ($notifications as $notification)
                            <div class="flex items-start gap-3 px-5 py-4 {{ $notification->dismissed_at ? '' : 'bg-indigo-50/70 dark:bg-indigo-900/10' }}">
                                <div class="shrink-0 mt-0.5 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 flex items-center justify-center">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">{!! $notification->icon() !!}</svg>
                                </div>
                                <a href="{{ $notification->url }}" class="min-w-0 flex-1">
                                    <div class="text-sm text-gray-800 dark:text-gray-100 font-medium">{{ $notification->title }}</div>
                                    @if ($notification->body)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $notification->body }}</div>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $notification->created_at->format('d M Y, h:i A') }}
                                        @unless ($notification->dismissed_at)
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300">{{ __('Active') }}</span>
                                        @endunless
                                    </div>
                                </a>
                                <div class="flex items-center gap-2 shrink-0">
                                    @unless ($notification->dismissed_at)
                                        <form method="POST" action="{{ route('notifications.dismiss', $notification) }}">
                                            @csrf
                                            <button class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-slate-600 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700">{{ __('Done') }}</button>
                                        </form>
                                    @endunless
                                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" onsubmit="return confirm('{{ __('Delete this notification?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs px-2 py-1 rounded border border-red-200 dark:border-red-900 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
