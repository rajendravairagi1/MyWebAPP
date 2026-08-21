@props(['projects'])

<div x-data="{
        projectId: '{{ old('project_id') }}',
        units: @js($projects->mapWithKeys(fn ($p) => [$p->id => $p->units->map(fn ($u) => ['id' => $u->id, 'label' => $u->unit_number.($u->type ? ' · '.$u->type : '').' · '.number_format($u->price, 0), 'available' => $u->status === 'available'])])),
    }" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="project_id" :value="__('Project (optional)')" />
        <select id="project_id" name="project_id" x-model="projectId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="">{{ __('No project — general item') }}</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
    </div>
    <div x-show="projectId">
        <x-input-label for="project_unit_id" :value="__('Unit (optional)')" />
        <select id="project_unit_id" name="project_unit_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="">{{ __('No specific unit') }}</option>
            <template x-for="unit in (units[projectId] || [])" :key="unit.id">
                <option :value="unit.id" x-text="unit.label + (unit.available ? '' : ' (not available)')"></option>
            </template>
        </select>
        <x-input-error :messages="$errors->get('project_unit_id')" class="mt-2" />
    </div>
</div>
