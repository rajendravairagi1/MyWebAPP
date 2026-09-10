@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ config('app.brand_logo_url') }}" alt="{{ $slot }}" height="32" style="height: 32px; width: auto; vertical-align: middle;">
@endif
</a>
</td>
</tr>
