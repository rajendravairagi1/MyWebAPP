@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
<span style="display: inline-block; width: 28px; height: 28px; line-height: 28px; background-color: #4f46e5; color: #ffffff; border-radius: 6px; font-weight: 800; font-size: 15px; text-align: center; vertical-align: middle; margin-right: 8px;">P</span><span style="vertical-align: middle;">{!! $slot !!}</span>
@endif
</a>
</td>
</tr>
