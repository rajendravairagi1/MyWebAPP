<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Statement - {{ $account->name }}</title>
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
        .in { color: #2f855a; }
        .out { color: #c53030; }
        .info { width: 100%; margin-top: 16px; }
        .info td { border: none; padding: 2px 12px 2px 0; }
        .info .label { color: #718096; width: 140px; }
        .footer-note { margin-top: 24px; padding-top: 8px; border-top: 1px solid #edf2f7; font-size: 10px; color: #a0aec0; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @include('pdf._business-header', ['docLabel' => 'Account Statement'])
            </td>
            <td class="text-right">
                <div class="muted">Generated: {{ now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="info">
        <tr><td class="label">Account</td><td>{{ $account->name }}</td></tr>
        <tr><td class="label">Type</td><td>{{ \App\Models\PaymentAccount::TYPES[$account->type] ?? ucfirst($account->type) }}</td></tr>
        @if ($account->bank_name)
            <tr><td class="label">Bank</td><td>{{ $account->bank_name }}</td></tr>
        @endif
        @if ($account->maskedAccountNumber())
            <tr><td class="label">Account No.</td><td>{{ $account->maskedAccountNumber() }}</td></tr>
        @endif
    </table>

    <table class="summary">
        <tr>
            <td><div class="label">Total In</div><div class="value in">{{ $business->currencySymbol() }}{{ number_format($totalIn, 2) }}</div></td>
            <td><div class="label">Total Out</div><div class="value out">{{ $business->currencySymbol() }}{{ number_format($totalOut, 2) }}</div></td>
            <td><div class="label">Balance</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($balance, 2) }}</div></td>
        </tr>
    </table>

    <h2>Transactions ({{ $rows->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Context</th>
                <th>Party</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row->date->format('d M Y') }}</td>
                    <td>{{ $row->description }}</td>
                    <td>{{ $row->context ?: '—' }}</td>
                    <td>{{ $row->party ?: '—' }}</td>
                    <td class="text-right {{ $row->direction === 'in' ? 'in' : 'out' }}">{{ $row->direction === 'in' ? '+' : '-' }} {{ $business->currencySymbol() }}{{ number_format($row->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">No transactions recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">This is a computer-generated document and does not require a signature.</div>
</body>
</html>
