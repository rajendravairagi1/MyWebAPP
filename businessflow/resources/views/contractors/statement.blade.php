<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statement - {{ $contractor->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a202c; }
        h1 { font-size: 20px; margin-bottom: 0; }
        h2 { font-size: 13px; margin: 22px 0 6px; color: #2d3748; }
        .muted { color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { text-align: left; background: #f7fafc; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #718096; }
        td { padding: 6px 8px; border-bottom: 1px solid #edf2f7; }
        .text-right { text-align: right; }
        .header { width: 100%; margin-bottom: 20px; }
        .header td { border: none; padding: 0; vertical-align: top; }
        .summary { width: 100%; margin-top: 10px; }
        .summary td { border: none; padding: 4px 12px 4px 0; }
        .summary .label { color: #718096; }
        .summary .value { font-weight: bold; font-size: 14px; }
        .balance-pos { color: #c53030; }
        .balance-zero { color: #2f855a; }
        .info { width: 100%; margin-top: 16px; }
        .info td { border: none; padding: 2px 12px 2px 0; }
        .info .label { color: #718096; width: 140px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @include('pdf._business-header', ['docLabel' => 'Contractor Statement'])
            </td>
            <td class="text-right">
                <div class="muted">Generated: {{ now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="info">
        <tr><td class="label">Name</td><td>{{ $contractor->name }}</td></tr>
        <tr><td class="label">Type</td><td>{{ $contractor->typeLabel() }}</td></tr>
        @if ($contractor->phone)
            <tr><td class="label">Phone</td><td>{{ $contractor->phone }}</td></tr>
        @endif
        @if ($contractor->email)
            <tr><td class="label">Email</td><td>{{ $contractor->email }}</td></tr>
        @endif
    </table>

    <table class="summary">
        <tr>
            <td><div class="label">Total Paid</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($contractor->totalPaid(), 2) }}</div></td>
            <td><div class="label">Outstanding Credit</div><div class="value {{ $contractor->totalOutstanding() > 0 ? 'balance-pos' : 'balance-zero' }}">{{ $business->currencySymbol() }}{{ number_format($contractor->totalOutstanding(), 2) }}</div></td>
            <td><div class="label">Grand Total</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($contractor->grandTotal(), 2) }}</div></td>
        </tr>
    </table>

    <h2>Payments ({{ $contractor->costs->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Project</th>
                <th>Description</th>
                <th>Status</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contractor->costs as $entry)
                <tr>
                    <td>{{ $entry->spent_on->format('d M Y') }}</td>
                    <td>{{ $entry->project->name }}</td>
                    <td>{{ $entry->description }}</td>
                    <td>{{ $entry->isOutstandingCredit() ? 'On Credit' : 'Paid' }}</td>
                    <td class="text-right">{{ $business->currencySymbol() }}{{ number_format($entry->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">No payments recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
