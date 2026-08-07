<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Customer') }} — {{ $customer->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-50 text-green-700 text-sm">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('customers.update', $customer) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('customers._form')

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-[var(--brand-600)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--brand-700)]">
                            Update Customer
                        </button>
                        <a href="{{ route('customers.index') }}" class="ml-3 text-sm text-gray-500 hover:underline">Cancel</a>
                    </form>

                    @if ($customer->documents->count())
                        <div class="mt-8 border-t pt-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Uploaded Documents</h3>
                            <ul class="text-sm space-y-1">
                                @foreach ($customer->documents as $doc)
                                    <li>
                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($doc->file_path) }}" target="_blank" class="text-[var(--brand-600)] hover:underline">{{ $doc->original_name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-6 border-t pt-4">
                        <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                              onsubmit="return confirm('Ye customer delete karna hai? Iske documents bhi delete ho jayenge.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Delete Customer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
