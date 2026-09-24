@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    // For a small alert/confirm-style modal (e.g. the idle-logout
    // warning) rather than a form — caps the width even on a phone
    // screen, where the sm:max-w-* class below has no effect at all
    // (it only applies at the 640px+ breakpoint), so a short "Still
    // there?" message doesn't stretch edge-to-edge on mobile the way a
    // long form reasonably does.
    'compact' => false,
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

{{--
    Teleported to <body> rather than left wherever Blade put it in the
    page — a modal used to render nested inside whatever card it was
    declared in (e.g. the Properties card on the customer page), and any
    ancestor with transform/filter/backdrop-filter/perspective/will-change
    becomes the containing block for a position:fixed descendant per the
    CSS spec. The Nova theme's dark-mode glass effect
    (`.dark[data-accent="nova"] main .dark\:bg-slate-800`) sets exactly
    that on every card, which silently confined the whole modal — panel
    and dimmed backdrop both — to that one card's box instead of the
    viewport: it rendered small, off-center, didn't dim the rest of the
    page, and content below the card (Quotations/Invoices, etc.) showed
    through untouched. Teleporting to <body> means no ancestor, now or
    added later, can ever trap it again.
--}}
<template x-teleport="body">
<div
    x-data="{
        show: @js($show),
        focusables() {
            // All focusable element types...
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                // All non-disabled elements...
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: {{ $show ? 'flex' : 'none' }};"
>
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="modal-backdrop absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    {{--
        Height is capped to the viewport (minus the padding above) and the
        panel scrolls internally, rather than the old pattern of letting
        the whole fixed overlay grow to the form's height and scrolling
        that — a long form (e.g. Add Payment, with its conditional
        contractor/credit fields) could end up taller than the viewport
        with no reliable way back to its own Save button. This way the
        backdrop is always a separate, full, static layer and the panel
        can never extend past what's visible.
    --}}
    <div
        x-show="show"
        class="modal-panel relative bg-white dark:bg-slate-800 rounded-lg shadow-xl transform transition-all w-full {{ $maxWidth }} max-h-[calc(100vh-2rem)] overflow-y-auto"
        {{--
            Two max-height declarations, not one — a browser that doesn't
            know 100dvh discards that whole line as invalid and keeps the
            100vh one above it; a browser that does know it applies the
            second, overriding the first. That's deliberate: on a phone,
            100vh is the height with no on-screen keyboard up, so a modal
            sized against it can end up taller than what's actually left
            once the keyboard opens for one of its inputs — the keyboard
            covers its own Save button, or the panel visibly grows/shrinks
            as the keyboard shows and hides while typing. 100dvh already
            tracks the space actually available and doesn't have either
            problem.
        --}}
        style="max-height: calc(100vh - 2rem); max-height: calc(100dvh - 2rem); overflow-y: auto;{{ $compact ? ' max-width: 20rem;' : '' }}"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        {{ $slot }}
    </div>
</div>
</template>
