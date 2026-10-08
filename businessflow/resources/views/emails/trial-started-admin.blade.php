<x-mail::message>
# New trial started

**{{ $user->name }}** ({{ $user->email }}{{ $business->phone ? ', '.$business->phone : '' }}) just verified their email and started a 15-day trial — no approval needed on your end.

- **Business:** {{ $business->name }}
- **Plan:** {{ ucfirst($business->plan) }}
- **Type:** {{ config('business.types')[$business->business_type] ?? $business->business_type }}
- **Trial ends:** {{ $business->subscription_expires_at?->format('d M Y') }}

<x-mail::button :url="route('admin.index')">
View in Admin
</x-mail::button>

If this looks wrong (spam, duplicate, wrong plan), you can cut the trial short from the business list any time.

{{ config('app.name') }}
</x-mail::message>
