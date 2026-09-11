@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'pricing'])

        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-md);">
            <a href="{{ route('admin.pricing.index') }}" class="btn btn-secondary">&larr; Back to list</a>
        </div>

        <form method="POST" action="{{ route('admin.pricing.update', $plan) }}" class="card" style="display: flex; flex-direction: column; gap: 16px;"
              x-data="{
                monthlyPrice: {{ old('monthly_price', $plan->monthly_price) }},
                cyclePricing(months) {
                    const original = this.monthlyPrice * months;
                    return { price: Math.round(original * (1 - {{ $discountPercent }} / 100)), original };
                }
              }">
            @csrf
            @method('PUT')

            <div class="grid-2">
                <div class="form-field">
                    <label for="name">Plan name</label>
                    <input id="name" name="name" required value="{{ old('name', $plan->name) }}" class="form-input">
                </div>
                <div class="form-field">
                    <label for="monthly_price">Full price / MRP (&#8377;) - before discount</label>
                    <input id="monthly_price" name="monthly_price" type="number" min="0" required
                           value="{{ old('monthly_price', $plan->monthly_price) }}" class="form-input" x-model.number="monthlyPrice">
                </div>
            </div>

            <div class="form-field">
                <label for="description">Description</label>
                <input id="description" name="description" required value="{{ old('description', $plan->description) }}" class="form-input">
            </div>

            <div class="form-field">
                <label for="features">Features (one per line)</label>
                <textarea id="features" name="features" rows="6" class="form-textarea">{{ old('features', implode("\n", $plan->features)) }}</textarea>
            </div>

            <div class="form-field">
                <label>Other currencies (optional)</label>
                <p style="color: var(--color-ink-soft); font-size: 0.85rem; margin: 0 0 var(--space-sm);">
                    Visitors detected in that country see this price instead of the &#8377; MRP above (the same discount %
                    still applies on top). Leave a currency blank and it falls back to showing the &#8377; MRP as a plain
                    number in that currency's symbol — set a real price here so it looks native instead.
                </p>
                <div class="grid-2">
                    @foreach ($otherCurrencies as $code => $symbol)
                        <div class="form-field">
                            <label for="extra_prices_{{ $code }}">{{ $symbol }} {{ $code }} full price / MRP</label>
                            <input id="extra_prices_{{ $code }}" name="extra_prices[{{ $code }}]" type="number" min="0"
                                   value="{{ old('extra_prices.'.$code, $plan->extra_prices[$code] ?? '') }}"
                                   class="form-input" placeholder="Falls back to ₹ price">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="admin-preview">
                <strong>Live preview - MRP &#8377;<span x-text="monthlyPrice"></span>/month, {{ $discountPercent }}% off:</strong>
                <ul style="margin: 8px 0 0; padding-left: 18px;">
                    <li>monthly: <s x-text="'₹' + cyclePricing(1).original"></s> ₹<span x-text="cyclePricing(1).price"></span></li>
                    <li>half_yearly: <s x-text="'₹' + cyclePricing(6).original"></s> ₹<span x-text="cyclePricing(6).price"></span> (6 months paid, 7 months of service)</li>
                    <li>yearly: <s x-text="'₹' + cyclePricing(12).original"></s> ₹<span x-text="cyclePricing(12).price"></span> (12 months paid, 14 months of service)</li>
                </ul>
            </div>

            @if ($errors->any())
                <p class="form-error">{{ $errors->first() }}</p>
            @endif

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.pricing.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
