@php
    $old = fn ($key, $default = null) => old($key, $meeting?->{$key} ?? $default);
@endphp

<div>
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="$old('title')" placeholder="{{ __('e.g. Site visit walkthrough') }}" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div>
    <x-input-label for="customer_id" :value="__('Customer (optional)')" />
    <select id="customer_id" name="customer_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
        <option value="">{{ __('None') }}</option>
        @foreach ($customers as $customer)
            <option value="{{ $customer->id }}" @selected($old('customer_id') == $customer->id)>{{ $customer->name }} @if($customer->phone) ({{ $customer->phone }}) @endif</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="project_id" :value="__('Project (optional)')" />
    <select id="project_id" name="project_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
        <option value="">{{ __('None') }}</option>
        @foreach ($projects as $project)
            <option value="{{ $project->id }}" @selected($old('project_id') == $project->id)>{{ $project->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="scheduled_at" :value="__('Date & time')" />
    <input id="scheduled_at" name="scheduled_at" type="datetime-local"
        value="{{ old('scheduled_at', $meeting?->scheduled_at?->format('Y-m-d\TH:i') ?? now()->addHour()->format('Y-m-d\TH:i')) }}" required
        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
</div>

<div>
    <x-input-label for="location" :value="__('Location or video call link (optional)')" />
    <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="$old('location')" placeholder="{{ __('e.g. Site office, or a Google Meet/Zoom link') }}" />
    <x-input-error :messages="$errors->get('location')" class="mt-2" />
</div>

<div>
    <x-input-label for="attendees" :value="__('Attendees (optional)')" />
    <x-text-input id="attendees" name="attendees" type="text" class="mt-1 block w-full" :value="$old('attendees')" placeholder="{{ __('e.g. Rakesh (Site Manager), Priya (Sales)') }}" />
    <x-input-error :messages="$errors->get('attendees')" class="mt-2" />
</div>

<div>
    <x-input-label for="notes" :value="__('Notes (optional)')" />
    <textarea id="notes" name="notes" rows="3" placeholder="{{ __('Agenda, things to discuss...') }}"
        class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">{{ $old('notes') }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>
