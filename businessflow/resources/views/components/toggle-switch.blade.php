@props(['name', 'checked' => false])

<label class="inline-flex items-center cursor-pointer">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" @checked($checked) class="sr-only peer">
    <span class="w-10 h-6 bg-gray-300 dark:bg-slate-600 rounded-full peer-checked:bg-accent-600 transition-colors relative after:content-[''] after:absolute after:left-1 after:top-1 after:w-4 after:h-4 after:bg-white after:rounded-full after:transition-transform peer-checked:after:translate-x-4"></span>
</label>
