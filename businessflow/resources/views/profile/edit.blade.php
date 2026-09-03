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
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Public Profile') }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('A shareable, no-login page with your photo, contact details, and every property you currently have available for sale — send it to anyone.') }}
                    </p>

                    @if ($user->profile_token)
                        @php $profileUrl = route('public-profile.show', $user->profile_token); @endphp
                        <div class="mt-4 flex flex-wrap items-center gap-2" x-data="{ profileUrl: '{{ $profileUrl }}', copied: false }">
                            <input type="text" readonly x-model="profileUrl" x-on:click="$event.target.select()" class="flex-1 min-w-0 text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm">
                            <button type="button" x-on:click="navigator.clipboard.writeText(profileUrl); copied = true; setTimeout(() => copied = false, 2000)" class="inline-flex items-center h-9 px-3 rounded-lg text-sm font-medium border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700" x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></button>
                            <a :href="'https://wa.me/?text=' + encodeURIComponent(profileUrl)" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 h-9 px-3 rounded-lg text-sm font-medium bg-green-600 text-white hover:bg-green-700">
                                <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/><path d="M12.004 2.003c-5.514 0-9.997 4.483-9.997 9.997 0 1.762.462 3.478 1.34 4.985L2 22l5.146-1.35a9.955 9.955 0 004.858 1.237h.004c5.514 0 9.997-4.483 9.997-9.997 0-2.67-1.04-5.182-2.929-7.071a9.935 9.935 0 00-7.072-2.926zm0 18.19a8.222 8.222 0 01-4.19-1.147l-.301-.179-3.055.801.815-2.978-.196-.306a8.213 8.213 0 01-1.259-4.384c0-4.535 3.69-8.225 8.226-8.225 2.196 0 4.26.856 5.815 2.41a8.169 8.169 0 012.408 5.816c0 4.536-3.69 8.226-8.226 8.226z"/></svg>
                                {{ __('Share') }}
                            </a>
                            <a href="{{ $profileUrl }}" target="_blank" rel="noopener" class="text-sm text-accent-600 hover:underline">{{ __('View') }}</a>
                        </div>
                    @else
                        <form method="POST" action="{{ route('profile.link') }}" class="mt-4">
                            @csrf
                            <button class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                                {{ __('Create my public profile link') }}
                            </button>
                        </form>
                    @endif
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
