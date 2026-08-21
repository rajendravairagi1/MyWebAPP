<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Schedule Follow-up') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('followups.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="customer_id" :value="__('Customer')" />
                        <select id="customer_id" name="customer_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Select a customer') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }} @if($customer->phone) ({{ $customer->phone }}) @endif</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        @if ($customers->isEmpty())
                            <p class="mt-1 text-xs text-amber-600">{{ __('No customers yet — ') }}<a href="{{ route('customers.create') }}" class="underline">{{ __('add one first') }}</a>.</p>
                        @endif
                    </div>

                    <div>
                        <x-input-label for="project_id" :value="__('Project (optional)')" />
                        <select id="project_id" name="project_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('None') }}</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>{{ $project->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="note" :value="__('Reminder note')" />
                        <textarea id="note" name="note" rows="3" required placeholder="{{ __('e.g. Follow up on site visit, ask about payment plan') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('note') }}</textarea>
                        <x-input-error :messages="$errors->get('note')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-400">{{ __('This text is also used as the WhatsApp message when you click the WhatsApp button.') }}</p>
                    </div>

                    <div>
                        <x-input-label for="due_at" :value="__('Due')" />
                        <input id="due_at" name="due_at" type="datetime-local" value="{{ old('due_at', now()->addDay()->format('Y-m-d\TH:i')) }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <x-input-error :messages="$errors->get('due_at')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('followups.index') }}" class="px-4 py-2 text-sm text-gray-600">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Schedule') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
