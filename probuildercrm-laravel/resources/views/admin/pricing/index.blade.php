@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'pricing'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif

        <p style="color: var(--color-ink-soft); margin-bottom: var(--space-lg);">
            Set each plan's full price (MRP, before any discount) here - the pricing page works everything else out from there:
            the 6-month price is 6&times; that (with 1 month free added to the service), the yearly price is 12&times; that
            (with 2 months free added). The offer below decides what customers actually pay - raise or lower it any time and
            every plan's real price updates immediately, without touching the MRP.
        </p>

        <div class="card" style="margin-bottom: var(--space-lg);">
            <strong style="display: block; margin-bottom: 8px;">Offer</strong>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                The discount badge and struck-through original price shown on every plan, on the Pricing page. Change it here
                and it updates everywhere at once. Set to 0 to run no offer - the badge and struck-through price both disappear.
            </p>
            <form method="POST" action="{{ route('admin.pricing.discount') }}" style="display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;">
                @csrf
                @method('PUT')
                <div class="form-field" style="max-width: 160px;">
                    <label for="discount_percent">Discount %</label>
                    <input id="discount_percent" name="discount_percent" type="number" min="0" max="90" required
                           value="{{ old('discount_percent', $discountPercent) }}" class="form-input">
                </div>
                <button type="submit" class="btn btn-primary">Save Offer</button>
            </form>
            @error('discount_percent')
                <p class="form-error" style="margin-top: 8px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach ($plans as $plan)
                <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin-bottom: 4px;">{{ $plan->name }}</h3>
                        <p style="color: var(--color-ink-soft); font-size: 0.9rem;">
                            MRP &#8377;{{ $plan->monthly_price }}/month
                            @if ($discountPercent > 0)
                                &middot; now &#8377;{{ (int) round($plan->monthly_price * (1 - $discountPercent / 100)) }}/month at {{ $discountPercent }}% off
                            @endif
                            &middot; {{ count($plan->features) }} features
                        </p>
                    </div>
                    <a href="{{ route('admin.pricing.edit', $plan) }}" class="btn btn-secondary">Edit</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
