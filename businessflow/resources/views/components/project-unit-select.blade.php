@props(['projects', 'selectedProjectId' => null, 'selectedUnitId' => null, 'showPrice' => true])

{{--
    Project shows if it still has a unit available for anyone, or a unit
    already assigned to whichever customer is picked in the Customer field
    above (so e.g. Mohan can get a quotation/invoice for the property
    already booked to him), or it's the project already tied to this
    record (so editing doesn't lose that context).

    `customerId` is intentionally not declared in this component's own
    x-data — it's read from the parent form's x-data so it reacts live as
    the Customer select above changes, without redeclaring/shadowing it.
--}}
<div x-data="{
        projectId: '{{ old('project_id', $selectedProjectId) }}',
        unitId: '{{ old('project_unit_id', $selectedUnitId) }}',
        selectedProjectId: {{ $selectedProjectId !== null ? (int) $selectedProjectId : 'null' }},
        selectedUnitId: {{ $selectedUnitId !== null ? (int) $selectedUnitId : 'null' }},
        allProjects: @js($projects->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->values()),
        units: @js($projects->mapWithKeys(fn ($p) => [$p->id => $p->units->map(fn ($u) => [
            'id' => $u->id,
            'label' => $u->unit_number.($u->type ? ' · '.$u->type : '').($showPrice ? ' · '.number_format($u->price, 0) : ''),
            'status' => $u->status,
            'customerId' => $u->customer_id,
            'archived' => $u->archived_at !== null,
        ])->values()])),
        unitVisible(u) {
            return u.status === 'available' || u.id === this.selectedUnitId || (!u.archived && this.customerId && u.customerId == this.customerId);
        },
        visibleUnits(pid) {
            return (this.units[pid] || []).filter(u => this.unitVisible(u));
        },
        projectVisible(pid) {
            return pid === this.selectedProjectId || this.visibleUnits(pid).length > 0;
        },
    }" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="project_id" :value="__('Project (optional)')" />
        <select id="project_id" name="project_id" x-model="projectId" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="">{{ __('No project — general item') }}</option>
            <template x-for="project in allProjects.filter(p => projectVisible(p.id))" :key="project.id">
                <option :value="project.id" x-text="project.name"></option>
            </template>
        </select>
        <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
    </div>
    <div x-show="projectId">
        <x-input-label for="project_unit_id" :value="__('Unit (optional)')" />
        <select id="project_unit_id" name="project_unit_id" x-model="unitId" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="">{{ __('No specific unit') }}</option>
            <template x-for="unit in visibleUnits(projectId)" :key="unit.id">
                <option :value="unit.id" x-text="unit.label"></option>
            </template>
        </select>
        <x-input-error :messages="$errors->get('project_unit_id')" class="mt-2" />
    </div>
</div>
