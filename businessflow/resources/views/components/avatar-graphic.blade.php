@props(['style' => null, 'initials' => ''])

<span {{ $attributes->merge(['class' => 'rounded-full overflow-hidden shrink-0 flex items-center justify-center']) }}>
    @if ($style === 'male')
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" class="h-full w-full">
            <circle cx="32" cy="32" r="32" fill="#DBEAFE" />
            <circle cx="32" cy="46" r="20" fill="#4B6FA8" />
            <circle cx="32" cy="27" r="13" fill="#F2C6A0" />
            <path d="M19 25c0-8 6-14 13-14s13 6 13 14c0-2-1-4-3-4-1-3-4-5-7-5-1 2-4 3-7 3-3 0-5 1-6 3-2 0-3 1-3 3z" fill="#2B2118" />
        </svg>
    @elseif ($style === 'female')
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" class="h-full w-full">
            <circle cx="32" cy="32" r="32" fill="#FCE7F3" />
            <path d="M14 58c1-11 8-17 18-17s17 6 18 17z" fill="#C2578A" />
            <path d="M16 30c0-11 7-19 16-19s16 8 16 19c0 4-1 8-3 11-1-6-3-9-6-9 0 3-2 5-5 5h-4c-3 0-5-2-5-5-3 0-5 3-6 9-2-3-3-7-3-11z" fill="#3A2A20" />
            <circle cx="32" cy="28" r="12" fill="#F2C6A0" />
        </svg>
    @elseif ($style === 'cartoon')
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" class="h-full w-full">
            <circle cx="32" cy="32" r="32" fill="#FEF3C7" />
            <rect x="14" y="18" width="36" height="32" rx="12" fill="#34D399" />
            <circle cx="32" cy="10" r="3" fill="#34D399" />
            <rect x="30.5" y="10" width="3" height="8" fill="#34D399" />
            <circle cx="24" cy="34" r="5" fill="#ffffff" />
            <circle cx="40" cy="34" r="5" fill="#ffffff" />
            <circle cx="24" cy="34" r="2.4" fill="#134E4A" />
            <circle cx="40" cy="34" r="2.4" fill="#134E4A" />
            <path d="M23 43c3 3 15 3 18 0" stroke="#134E4A" stroke-width="2.4" stroke-linecap="round" fill="none" />
        </svg>
    @else
        <span class="h-full w-full bg-accent-500 text-white text-sm font-semibold flex items-center justify-center">{{ $initials }}</span>
    @endif
</span>
