<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Backup') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('This backs up everything for') }} <span class="font-medium text-gray-800 dark:text-gray-100">{{ $business->name }}</span> — {{ __('every project, customer, quotation, invoice, payment and uploaded file — into one .zip you can download and, if you ever need to, upload right back to fully restore it.') }}
            </p>

            {{-- Download --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 space-y-3">
                <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ __('Download a backup') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keep this file somewhere safe (your computer, Google Drive, etc.). Do this regularly, and always before a big change.') }}</p>
                <a href="{{ route('backup.download') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-accent-600 text-white text-sm font-medium rounded-md hover:bg-accent-700">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    {{ __('Download backup (.zip)') }}
                </a>
            </div>

            {{-- Restore --}}
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ __('Restore from a backup') }}</h3>
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 text-sm rounded-md p-3">
                    {{ __('This replaces everything currently in') }} "{{ $business->name }}" {{ __('with what\'s in the backup file. Anything added since that backup was taken will be lost. This cannot be undone — download a fresh backup first if you\'re unsure.') }}
                </div>

                <form method="POST" action="{{ route('backup.restore') }}" enctype="multipart/form-data" class="space-y-4" onsubmit="return confirm('{{ __('This will replace all current data with the backup. Continue?') }}')">
                    @csrf
                    <div>
                        <x-input-label for="backup" :value="__('Backup file (.zip)')" />
                        <input id="backup" name="backup" type="file" accept=".zip" required class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                        <x-input-error :messages="$errors->get('backup')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="confirm" :value="__('Type RESTORE to confirm')" />
                        <x-text-input id="confirm" name="confirm" type="text" class="mt-1 block w-full" required autocomplete="off" placeholder="RESTORE" />
                        <x-input-error :messages="$errors->get('confirm')" class="mt-2" />
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        {{ __('Restore from this backup') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
