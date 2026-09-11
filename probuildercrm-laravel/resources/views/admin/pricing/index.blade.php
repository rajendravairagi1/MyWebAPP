@extends('layouts.admin')

@section('content')
<div class="admin-shell">
    <div class="container" style="max-width: 900px;">
        @include('admin.partials.tabs', ['active' => 'pricing'])

        @if (session('status'))
            <p style="color: var(--color-success); margin-bottom: var(--space-md);">{{ session('status') }}</p>
        @endif
        @error('geoip')
            <p class="form-error" style="margin-bottom: var(--space-md);">{{ $message }}</p>
        @enderror

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

        <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: var(--space-lg);">
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
                        @if (! empty($plan->extra_prices))
                            <p style="color: var(--color-ink-soft); font-size: 0.85rem; margin-top: 4px;">
                                Also priced in: {{ collect($plan->extra_prices)->map(fn ($price, $code) => \App\Support\Currency::symbol($code).$price.' '.$code)->join(', ') }}
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('admin.pricing.edit', $plan) }}" class="btn btn-secondary">Edit</a>
                </div>
            @endforeach
        </div>

        <div class="card" style="margin-bottom: var(--space-lg);">
            <strong style="display: block; margin-bottom: 8px;">GeoIP Database (country-based pricing)</strong>
            <p style="color: var(--color-ink-soft); font-size: 0.9rem; margin-bottom: var(--space-md);">
                Powers the currency shown automatically on the Pricing page, based on a visitor's country. Without this
                database installed, everyone sees the default (&#8377; INR) unless they pick a currency themselves from
                the dropdown there.
            </p>

            @if (! $maxmindKeyConfigured)
                <div style="background: var(--color-primary-light); border-radius: var(--radius-sm); padding: var(--space-md); font-size: 0.85rem; color: var(--color-ink-soft);">
                    <strong style="color: var(--color-ink); display: block; margin-bottom: 6px;">One-time setup needed first:</strong>
                    <ol style="margin: 0; padding-left: 18px; display: flex; flex-direction: column; gap: 4px;">
                        <li>Create a free account at <a href="https://www.maxmind.com/en/geolite2/signup" target="_blank" rel="noopener">maxmind.com/en/geolite2/signup</a>.</li>
                        <li>Once logged in, go to <em>Manage License Keys</em> and generate a new key.</li>
                        <li>Add it to the server's <code>.env</code> file as <code>MAXMIND_LICENSE_KEY=your-key-here</code>, then reload.</li>
                    </ol>
                </div>
            @else
                <p style="color: var(--color-ink-soft); font-size: 0.85rem; margin-bottom: var(--space-md);">
                    Status:
                    @if ($geoIpInstalled)
                        Installed, last updated {{ \Illuminate\Support\Carbon::createFromTimestamp($geoIpUpdatedAt)->diffForHumans() }}.
                    @else
                        Not installed yet — click below to download it.
                    @endif
                </p>
                <form method="POST" action="{{ route('admin.pricing.geoip.update') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">{{ $geoIpInstalled ? 'Update GeoIP Database Now' : 'Install GeoIP Database Now' }}</button>
                </form>
                <p style="color: var(--color-ink-soft); font-size: 0.8rem; margin-top: var(--space-sm);">
                    MaxMind refreshes this data regularly — re-run this every few months to keep country detection accurate.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
