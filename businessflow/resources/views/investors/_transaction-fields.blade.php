<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label :value="__('Amount')" />
        <input type="number" step="0.01" min="0.01" name="amount" required
            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    </div>
    <div>
        <x-input-label :value="__('Date')" />
        <input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required
            class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    </div>
</div>

@if ($projects->isNotEmpty())
    <div>
        <x-input-label :value="__('Project (optional)')" />
        <select name="project_id" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="">{{ __('— Not tied to a project —') }}</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
@endif

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label :value="__('Method (optional)')" />
        <select name="method" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
            <option value="">{{ __('—') }}</option>
            <option value="cash">{{ __('Cash') }}</option>
            <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
            <option value="upi">{{ __('UPI') }}</option>
            <option value="cheque">{{ __('Cheque') }}</option>
        </select>
    </div>
    <div>
        <x-input-label :value="__('Reference (optional)')" />
        <input type="text" name="reference" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
    </div>
</div>

<div>
    <x-input-label :value="__('Description (optional)')" />
    <input type="text" name="description" class="mt-1 block w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md shadow-sm focus:border-accent-500 focus:ring-accent-500">
</div>
