@php
    $isCustomCategory = ! array_key_exists($followup->category, \App\Models\Followup::CATEGORIES);
@endphp
<form method="POST" action="{{ route('followups.update', $followup) }}" x-data="{ category: '{{ $isCustomCategory ? 'other' : $followup->category }}' }" class="absolute right-0 z-10 mt-2 w-72 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 shadow-lg p-3 rounded-md space-y-2 text-left">
    @csrf
    @method('PUT')
    <select name="category" x-model="category" class="w-full text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
        @foreach (\App\Models\Followup::CATEGORIES as $val => $label)
            <option value="{{ $val }}" @selected($isCustomCategory ? $val === 'other' : $followup->category === $val)>{{ $label }}</option>
        @endforeach
    </select>
    <div x-show="category === 'other'" x-cloak>
        <input type="text" name="category_other" value="{{ $isCustomCategory ? $followup->category : '' }}" placeholder="{{ __('If Other, specify') }}" class="w-full text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
    </div>
    <textarea name="note" rows="2" required class="w-full text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">{{ $followup->note }}</textarea>
    <input type="datetime-local" name="due_at" value="{{ $followup->due_at->format('Y-m-d\TH:i') }}" required class="w-full text-xs rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500">
    <button class="w-full mt-1 px-2 py-1 bg-accent-600 text-white text-[11px] font-semibold rounded hover:bg-accent-700">{{ __('Save') }}</button>
</form>
