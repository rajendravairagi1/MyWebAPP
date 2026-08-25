<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statement - {{ $investor->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a202c; }
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
        .type-investment { color: #2f855a; }
        .type-payout { color: #b7791f; }
        .qr { text-align: center; }
        .qr img { width: 70px; height: 70px; }
        .qr div { font-size: 8px; color: #a0aec0; margin-top: 2px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <h1>{{ $business?->name ?? config('app.name') }}</h1>
                <div class="muted">Investor Statement</div>
            </td>
            <td class="text-right">
                <div class="muted">Generated: {{ now()->format('d M Y') }}</div>
            </td>
            @if (isset($verifyQr))
                <td class="qr" style="width: 90px;">
                    <img src="{{ $verifyQr }}" alt="Verify QR">
                    <div>Scan to verify</div>
                </td>
            @endif
        </tr>
    </table>

    <div>
        <strong>{{ $investor->name }}</strong><br>
        @if ($investor->phone){{ $investor->phone }}<br>@endif
        @if ($investor->email){{ $investor->email }}@endif
    </div>

    <table class="summary">
        <tr>
            <td><div class="label">Total invested</div><div class="value">{{ number_format($investor->totalInvested(), 2) }}</div></td>
            <td><div class="label">Total paid out</div><div class="value">{{ number_format($investor->totalPaidOut(), 2) }}</div></td>
            <td><div class="label">Balance</div><div class="value">{{ number_format($investor->balance(), 2) }}</div></td>
        </tr>
    </table>

    <h2>Transaction History</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Project</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($investor->transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                    <td class="{{ $transaction->type === 'investment' ? 'type-investment' : 'type-payout' }}">{{ $transaction->typeLabel() }}</td>
                    <td>{{ $transaction->project?->name ?? '—' }}</td>
                    <td>{{ $transaction->method ? ucfirst(str_replace('_', ' ', $transaction->method)) : '—' }}</td>
                    <td>{{ $transaction->reference ?? '—' }}</td>
                    <td>{{ $transaction->description ?? '—' }}</td>
                    <td class="text-right">{{ number_format($transaction->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No transactions recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
