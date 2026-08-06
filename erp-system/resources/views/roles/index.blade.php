<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Roles & Permissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-600 mb-6">
                        Har role pe click karke uske modules ka access set karo. Owner ke paas hamesha sab modules honge.
                    </p>

                    <div class="divide-y divide-gray-200 border rounded-lg">
                        @foreach ($roles as $role)
                            <a href="{{ route('roles.edit', $role) }}"
                               class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                                <span class="font-medium text-gray-800">{{ $role->name }}</span>
                                <span class="text-sm text-gray-500">{{ $role->permissions_count }} modules &rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
