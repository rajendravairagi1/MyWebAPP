<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modules for :role', ['role' => $role->name]) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="text-sm text-gray-600 mb-6">
                        Jo checkbox ON karoge, wo module <strong>{{ $role->name }}</strong> role wale sab users ko dikhega.
                        {{ $role->name === 'Owner' ? 'Owner ko hamesha sab modules milte hain.' : '' }}
                    </p>

                    <form method="POST" action="{{ route('roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                            @foreach ($modules as $key => $label)
                                <label class="flex items-center gap-2 border rounded-lg px-3 py-2 {{ $role->name === 'Owner' ? 'bg-gray-50 opacity-70' : 'hover:bg-gray-50' }}">
                                    <input type="checkbox" name="modules[]" value="{{ $key }}"
                                        class="rounded border-gray-300 text-[var(--brand-600)] focus:ring-[var(--brand-500)]"
                                        {{ in_array($key, $granted) ? 'checked' : '' }}
                                        {{ $role->name === 'Owner' ? 'disabled' : '' }}>
                                    <span class="text-sm">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        @if ($role->name !== 'Owner')
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                                Save Access
                            </button>
                        @endif
                    </form>

                    <a href="{{ route('roles.index') }}" class="inline-block mt-4 text-sm text-gray-500 hover:underline">&larr; Back to Roles</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
