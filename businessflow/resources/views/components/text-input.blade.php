@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 focus:border-accent-500 focus:ring-accent-500 rounded-md shadow-sm']) }}>
