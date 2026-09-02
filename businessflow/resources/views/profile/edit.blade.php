<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Language') }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Which language the app is shown in — just for you. Everyone else on your team keeps their own choice.') }}
                    </p>
                    <form method="POST" action="{{ route('profile.locale') }}" class="mt-4 flex flex-wrap items-center gap-3">
                        @csrf
                        @method('PUT')
                        <select name="locale" class="border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                            @foreach (config('locales') as $code => $label)
                                <option value="{{ $code }}" @selected(($user->locale ?? 'en') === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        @if (session('status') === 'locale-updated')
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                        @endif
                    </form>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">
                <div class="max-w-xl flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Two-Factor Authentication') }}</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ Auth::user()->hasEnabledTwoFactor() ? __('Enabled — a code from your authenticator app is required at login.') : __('Add an extra layer of security to your account with Google Authenticator.') }}
                        </p>
                    </div>
                    <a href="{{ route('two-factor.show') }}" class="shrink-0 inline-flex items-center h-9 px-4 rounded-lg text-sm font-medium border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700">
                        {{ Auth::user()->hasEnabledTwoFactor() ? __('Manage') : __('Enable') }}
                    </a>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
