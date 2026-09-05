<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customers</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a202c; }
        h2 { font-size: 13px; margin: 22px 0 6px; color: #2d3748; }
        .muted { color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { text-align: left; background: #f7fafc; padding: 6px 8px; font-size: 9px; text-transform: uppercase; color: #718096; }
        td { padding: 6px 8px; border-bottom: 1px solid #edf2f7; }
        .header { width: 100%; margin-bottom: 20px; }
        .header td { border: none; padding: 0; vertical-align: top; }
        .text-right { text-align: right; }
        .footer-note { margin-top: 24px; padding-top: 8px; border-top: 1px solid #edf2f7; font-size: 10px; color: #a0aec0; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @include('pdf._business-header', ['docLabel' => 'Customer List'])
            </td>
            <td class="text-right">
                <div class="muted">Generated: {{ now()->format('d M Y') }}</div>
                <div class="muted">Total: {{ $customers->count() }} customers</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Company</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Source</th>
                <th>Added On</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->company ?: '—' }}</td>
                    <td>{{ $customer->phone ?: '—' }}</td>
                    <td>{{ $customer->email ?: '—' }}</td>
                    <td>{{ $customer->source ?: '—' }}</td>
                    <td>{{ $customer->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No customers yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">This is a computer-generated document and does not require a signature.</div>
</body>
</html>
