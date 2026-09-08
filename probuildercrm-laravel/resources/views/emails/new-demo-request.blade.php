<p>New demo request from the {{ config('site.name') }} website:</p>

<table cellpadding="4" cellspacing="0">
    <tr><td><strong>Name</strong></td><td>{{ $submission->name }}</td></tr>
    <tr><td><strong>Email</strong></td><td>{{ $submission->email }}</td></tr>
    <tr><td><strong>Phone</strong></td><td>{{ $submission->phone ?: '-' }}</td></tr>
    <tr><td><strong>Submitted</strong></td><td>{{ $submission->created_at->format('F j, Y g:i A') }}</td></tr>
</table>

<p><strong>Message:</strong><br>{{ $submission->message }}</p>

<p>
    <a href="{{ config('site.url') }}/admin/leads">View all demo requests in the admin panel</a>
</p>
