@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'pricing'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif

        <p style="color: var(--color-ink-soft); margin-bottom: var(--space-lg);">
            Set each plan's real monthly price here - the pricing page works it out from there: the 6-month price is 6&times; that
            (with 1 month free added to the service), the yearly price is 12&times; that (with 2 months free added). The permanent
            "40% OFF" badge and struck-through price shown to visitors are calculated automatically too - you only ever edit the
            one real monthly number per plan.
        </p>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach ($plans as $plan)
                <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin-bottom: 4px;">{{ $plan->name }}</h3>
                        <p style="color: var(--color-ink-soft); font-size: 0.9rem;">&#8377;{{ $plan->monthly_price }}/month &middot; {{ count($plan->features) }} features</p>
                    </div>
                    <a href="{{ route('admin.pricing.edit', $plan) }}" class="btn btn-secondary">Edit</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
