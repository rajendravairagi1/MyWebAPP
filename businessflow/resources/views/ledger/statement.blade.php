<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ledger Statement - {{ now()->format('d M Y') }}</title>
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
        .income { color: #2f855a; }
        .expense { color: #c53030; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @include('pdf._business-header', ['docLabel' => 'Ledger Statement'])
            </td>
            <td class="text-right">
                <div class="muted">Generated: {{ now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td><div class="label">Collected (Sold Units)</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($totalCollected, 0) }}</div></td>
            <td><div class="label">Manual Income</div><div class="value income">{{ $business->currencySymbol() }}{{ number_format($manualIncome, 0) }}</div></td>
            <td><div class="label">Purchases / Expenses</div><div class="value expense">{{ $business->currencySymbol() }}{{ number_format($totalPurchases, 0) }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Property Deals Profit</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($dealsProfit, 0) }}</div></td>
            <td><div class="label">Broker Commission Paid</div><div class="value expense">{{ $business->currencySymbol() }}{{ number_format($brokerCommissionPaid, 0) }}</div></td>
            <td><div class="label">Net Profit</div><div class="value {{ $netProfit >= 0 ? 'income' : 'expense' }}">{{ $business->currencySymbol() }}{{ number_format($netProfit, 0) }}</div></td>
        </tr>
    </table>
    <p class="muted" style="margin-top: 8px;">Net Profit = Collected + Manual Income + Deals Profit − Purchases/Expenses − Broker Commission Paid.</p>

    <h2>Manual Ledger Entries ({{ $entries->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Description</th>
                <th>Party / Project</th>
                <th>Account</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $entry)
                <tr>
                    <td>{{ $entry->entry_date->format('d M Y') }}</td>
                    <td class="{{ $entry->type === 'income' ? 'income' : 'expense' }}">{{ ucfirst($entry->type) }}</td>
                    <td>{{ $entry->category ?: '—' }}</td>
                    <td>{{ $entry->description }}</td>
                    <td>{{ $entry->customer?->name ?? $entry->project?->name ?? '—' }}</td>
                    <td>{{ $entry->account?->label() ?? '—' }}</td>
                    <td class="text-right {{ $entry->type === 'income' ? 'income' : 'expense' }}">{{ $entry->type === 'income' ? '+' : '-' }} {{ $business->currencySymbol() }}{{ number_format($entry->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No manual ledger entries recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
