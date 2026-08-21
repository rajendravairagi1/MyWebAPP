@php $project = $project ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <x-input-label for="name" :value="__('Project name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $project?->name)" placeholder="{{ __('e.g. Green Valley Apartments') }}" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="type" :value="__('Type')" />
        <select id="type" name="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            @foreach (['residential' => 'Residential', 'commercial' => 'Commercial', 'plot' => 'Plots', 'mixed' => 'Mixed-use'] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $project?->type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            @foreach (['planning' => 'Planning', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'on_hold' => 'On hold'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $project?->status ?? 'planning') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="start_date" :value="__('Start date')" />
        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', $project?->start_date?->toDateString())" />
        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="expected_completion_date" :value="__('Expected completion')" />
        <x-text-input id="expected_completion_date" name="expected_completion_date" type="date" class="mt-1 block w-full" :value="old('expected_completion_date', $project?->expected_completion_date?->toDateString())" />
        <x-input-error :messages="$errors->get('expected_completion_date')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="location" :value="__('Location / address')" />
    <textarea id="location" name="location" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('location', $project?->location) }}</textarea>
    <x-input-error :messages="$errors->get('location')" class="mt-2" />
</div>

<div>
    <x-input-label for="notes" :value="__('Notes')" />
    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ old('notes', $project?->notes) }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>
