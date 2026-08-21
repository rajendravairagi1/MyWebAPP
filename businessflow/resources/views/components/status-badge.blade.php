@props(['status'])

@php
    $styles = [
        'draft' => 'bg-gray-100 text-gray-700',
        'sent' => 'bg-blue-100 text-blue-700',
        'accepted' => 'bg-green-100 text-green-700',
        'rejected' => 'bg-red-100 text-red-700',
        'paid' => 'bg-green-100 text-green-700',
        'partially_paid' => 'bg-amber-100 text-amber-700',
        'overdue' => 'bg-red-100 text-red-700',
        'planning' => 'bg-gray-100 text-gray-700',
        'ongoing' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-green-100 text-green-700',
        'on_hold' => 'bg-amber-100 text-amber-700',
        'available' => 'bg-green-100 text-green-700',
        'booked' => 'bg-amber-100 text-amber-700',
        'sold' => 'bg-gray-100 text-gray-700',
        'pending' => 'bg-amber-100 text-amber-700',
        'done' => 'bg-green-100 text-green-700',
    ][$status] ?? 'bg-gray-100 text-gray-700';

    $label = str($status)->replace('_', ' ')->title();
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded text-xs font-medium $styles"]) }}>
    {{ $label }}
</span>
