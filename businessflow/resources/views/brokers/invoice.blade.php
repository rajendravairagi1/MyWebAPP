<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Commission Invoice - {{ $broker->name }}</title>
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
        .parties { width: 100%; margin-top: 16px; }
        .parties td { border: none; padding: 0 12px 0 0; vertical-align: top; width: 50%; }
        .parties .label { color: #718096; font-size: 10px; text-transform: uppercase; margin-bottom: 4px; }
        .totals { width: 100%; margin-top: 10px; }
        .totals td { border: none; padding: 4px 8px; }
        .totals .label { color: #718096; }
        .totals .value { text-align: right; }
        .totals .grand td { border-top: 2px solid #2d3748; padding-top: 8px; font-weight: bold; font-size: 15px; color: #c53030; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @include('pdf._business-header', ['docLabel' => 'Commission Invoice'])
            </td>
            <td class="text-right">
                <div class="muted">Date: {{ now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td>
                <div class="label">Bill To</div>
                <strong>{{ $broker->name }}</strong><br>
                @if ($broker->phone){{ $broker->phone }}<br>@endif
                @if ($broker->email){{ $broker->email }}@endif
            </td>
        </tr>
    </table>

    <h2>Commission Earned</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Property / Deal</th>
                <th>Commission</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($commissionLines as $line)
                <tr>
                    <td>{{ $line->transaction_date->format('d M Y') }}</td>
                    <td>
                        @if ($line->unit)
                            {{ $line->unit->project->name }} · {{ $line->unit->unit_number }}
                        @elseif ($line->deal)
                            {{ $line->deal->property_title }} (deal)
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $line->commission_percent ? rtrim(rtrim(number_format($line->commission_percent, 2), '0'), '.').'%' : '—' }}</td>
                    <td class="text-right">{{ $business->currencySymbol() }}{{ number_format($line->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">No commission recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Total Commission Earned</td>
            <td class="value">{{ $business->currencySymbol() }}{{ number_format($broker->totalCommissionAccrued(), 2) }}</td>
        </tr>
        <tr>
            <td class="label">Less: Already Paid</td>
            <td class="value">- {{ $business->currencySymbol() }}{{ number_format($broker->totalPaid(), 2) }}</td>
        </tr>
        <tr class="grand">
            <td>Balance Due Now</td>
            <td class="value">{{ $business->currencySymbol() }}{{ number_format($broker->balance(), 2) }}</td>
        </tr>
    </table>
</body>
</html>
