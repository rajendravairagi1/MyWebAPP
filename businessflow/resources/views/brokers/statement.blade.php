<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Broker Statement - {{ $broker->name }}</title>
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
                @include('pdf._business-header', ['docLabel' => 'Broker Statement'])
            </td>
            <td class="text-right">
                <div class="muted">Generated: {{ now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="info">
        <tr><td class="label">Broker</td><td>{{ $broker->name }}</td></tr>
        @if ($broker->phone)
            <tr><td class="label">Phone</td><td>{{ $broker->phone }}</td></tr>
        @endif
        @if ($broker->email)
            <tr><td class="label">Email</td><td>{{ $broker->email }}</td></tr>
        @endif
        @if ($broker->notes)
            <tr><td class="label">Notes</td><td>{{ $broker->notes }}</td></tr>
        @endif
    </table>

    <table class="summary">
        <tr>
            <td><div class="label">Commission Earned</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($broker->totalCommissionAccrued(), 2) }}</div></td>
            <td><div class="label">Paid Out</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($broker->totalPaid(), 2) }}</div></td>
            <td><div class="label">Balance Owed</div><div class="value {{ $broker->balance() > 0 ? 'balance-pos' : 'balance-zero' }}">{{ $business->currencySymbol() }}{{ number_format($broker->balance(), 2) }}</div></td>
        </tr>
    </table>

    <h2>Transactions ({{ $broker->transactions->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Property / Deal</th>
                <th>Method</th>
                <th>Reference</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($broker->transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                    <td>
                        {{ $transaction->typeLabel() }}{{ $transaction->commission_percent ? ' ('.rtrim(rtrim(number_format($transaction->commission_percent, 2), '0'), '.').'%)' : '' }}
                    </td>
                    <td>
                        @if ($transaction->unit)
                            {{ $transaction->unit->project->name }} · {{ $transaction->unit->unit_number }}
                        @elseif ($transaction->deal)
                            {{ $transaction->deal->property_title }} (deal)
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $transaction->method ? ucfirst(str_replace('_', ' ', $transaction->method)) : '—' }}</td>
                    <td>{{ $transaction->reference ?: '—' }}</td>
                    <td class="text-right">{{ $business->currencySymbol() }}{{ number_format($transaction->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No transactions recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
