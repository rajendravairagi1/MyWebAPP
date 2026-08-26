<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Team') }}</h2>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-member')" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap bg-accent-600 text-white hover:bg-accent-700">
                {{ __('+ Add Team Member') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm rounded-md p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('People who can log in to this business. Add a supervisor and choose exactly which modules they can see — everything else stays hidden to them.') }}
            </p>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/60 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Email') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Role') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Modules') }}</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach ($members as $member)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $member->name }}</td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $member->email }}</td>
                                <td class="px-5 py-3">
                                    <span @class([
                                        'text-xs px-2 py-0.5 rounded font-medium',
                                        'bg-accent-100 dark:bg-accent-900/30 text-accent-700 dark:text-accent-400' => $member->pivot->role === 'owner',
                                        'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400' => $member->pivot->role !== 'owner',
                                    ])>{{ ucfirst($member->pivot->role) }}</span>
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                    @if ($member->pivot->role === 'owner')
                                        <span class="text-gray-400">{{ __('All (owner)') }}</span>
                                    @else
                                        @php $modules = $member->pivot->permissions['modules'] ?? []; @endphp
                                        @if (empty($modules))
                                            <span class="text-gray-400">{{ __('None granted') }}</span>
                                        @else
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($modules as $key)
                                                    <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400">{{ \App\Support\Modules::ALL[$key] ?? $key }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    @if ($member->pivot->role !== 'owner')
                                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-member-{{ $member->id }}')" class="text-accent-600 hover:underline text-xs">{{ __('Edit') }}</button>
                                        <form method="POST" action="{{ route('team.destroy', $member) }}" onsubmit="return confirm('{{ __('Remove this team member?') }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline text-xs ml-2">{{ __('Remove') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modal name="add-member" max-width="md" :show="$errors->has('email')">
        <form method="POST" action="{{ route('team.store') }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Team Member') }}</h2>
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <input type="text" id="password" name="password" required
                    class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
                <p class="text-xs text-gray-400 mt-1">{{ __('Share this with them — they log in with this email and password.') }}</p>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label :value="__('Modules they can see')" />
                <div class="mt-2 grid grid-cols-2 gap-2">
                    @foreach (\App\Support\Modules::ALL as $key => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="modules[]" value="{{ $key }}" class="rounded border-gray-300 text-accent-600 focus:ring-accent-500">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                <x-primary-button>{{ __('Add') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    @foreach ($members as $member)
        @if ($member->pivot->role !== 'owner')
            <x-modal name="edit-member-{{ $member->id }}" max-width="md">
                <form method="POST" action="{{ route('team.update', $member) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Permissions') }} — {{ $member->name }}</h2>
                    <div>
                        <x-input-label :value="__('Modules they can see')" />
                        @php $memberModules = $member->pivot->permissions['modules'] ?? []; @endphp
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach (\App\Support\Modules::ALL as $key => $label)
                                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="modules[]" value="{{ $key }}" @checked(in_array($key, $memberModules)) class="rounded border-gray-300 text-accent-600 focus:ring-accent-500">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" x-on:click="show = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cancel') }}</button>
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </x-modal>
        @endif
    @endforeach
</x-app-layout>
