<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6" x-data="{ avatar: '{{ old('avatar', $user->avatar) }}', photoPreview: null }">
        @csrf
        @method('patch')

        <div>
            <x-input-label :value="__('Photo')" />
            <div class="mt-2 flex items-center gap-4">
                <span class="h-16 w-16 rounded-full overflow-hidden shrink-0 bg-gray-100 dark:bg-slate-700">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!photoPreview">
                        @if ($user->photo_path)
                            <img src="{{ route('profile.photo') }}?v={{ $user->updated_at->timestamp }}" class="h-full w-full object-cover">
                        @else
                            <x-avatar-graphic :style="$user->avatar" :initials="collect(explode(' ', $user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')" class="h-full w-full" />
                        @endif
                    </template>
                </span>
                <div>
                    <input type="file" name="photo" accept="image/*"
                        x-on:change="photoPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                        class="block text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 dark:file:bg-slate-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-slate-600">
                    <p class="text-xs text-gray-400 mt-1">{{ __('Shown on your public profile page and in the header — replaces the style picker below once uploaded.') }}</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ __('No photo yet? Pick a placeholder style instead.') }}</p>
            <div class="flex gap-3">
                @foreach (['' => __('Default'), 'male' => __('Male'), 'female' => __('Female'), 'cartoon' => __('Cartoon')] as $value => $label)
                    <label class="flex flex-col items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="avatar" value="{{ $value }}" x-model="avatar" class="sr-only">
                        <span class="h-14 w-14 rounded-full ring-2 ring-offset-2 dark:ring-offset-slate-800 transition"
                            :class="avatar === '{{ $value }}' ? 'ring-accent-500' : 'ring-transparent'">
                            <x-avatar-graphic :style="$value ?: null" :initials="collect(explode(' ', $user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')" class="h-full w-full" />
                        </span>
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" class="mt-1 block w-full bg-gray-50 dark:bg-slate-900 text-gray-500 dark:text-gray-400" value="{{ $user->email }}" disabled />
            <p class="text-xs text-gray-400 mt-1">{{ __('Your login email — contact us if this needs to change.') }}</p>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-100">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Mobile number (optional)')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <p class="text-xs text-gray-400 mt-1">{{ __('Shown on your public profile page so customers can call you directly.') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="about" :value="__('About (optional)')" />
            <textarea id="about" name="about" rows="3" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500" placeholder="{{ __('A short line about you or your business, shown on your public profile page.') }}">{{ old('about', $user->about) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('about')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
