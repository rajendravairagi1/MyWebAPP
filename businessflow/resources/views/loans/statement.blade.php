<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Statement - {{ $loan->bank_name }} - {{ $loan->customer->name }}</title>
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
                @include('pdf._business-header', ['docLabel' => 'Bank Loan Statement'])
            </td>
            <td class="text-right">
                <div class="muted">Generated: {{ now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <div>
        <strong>{{ $loan->customer->name }}</strong><br>
        @if ($loan->customer->phone){{ $loan->customer->phone }}<br>@endif
        @if ($loan->unit)
            {{ $loan->unit->project->name }} · {{ $loan->unit->unit_number }}
        @endif
    </div>

    <table class="info">
        <tr><td class="label">Bank</td><td>{{ $loan->bank_name }}</td></tr>
        @if ($loan->loan_account_number)
            <tr><td class="label">Loan A/C No.</td><td>{{ $loan->loan_account_number }}</td></tr>
        @endif
        @if ($loan->interest_rate)
            <tr><td class="label">Interest Rate</td><td>{{ rtrim(rtrim(number_format($loan->interest_rate, 2), '0'), '.') }}% p.a.</td></tr>
        @endif
        @if ($loan->sanctioned_at)
            <tr><td class="label">Sanctioned On</td><td>{{ $loan->sanctioned_at->format('d M Y') }}</td></tr>
        @endif
        @if ($loan->notes)
            <tr><td class="label">Notes</td><td>{{ $loan->notes }}</td></tr>
        @endif
    </table>

    <table class="summary">
        <tr>
            <td><div class="label">Sanctioned Amount</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($loan->sanctioned_amount, 2) }}</div></td>
            <td><div class="label">Disbursed So Far</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($loan->totalDisbursed(), 2) }}</div> <span class="muted">({{ $loan->percentDisbursed() }}%)</span></td>
            <td><div class="label">Remaining to Disburse</div><div class="value {{ $loan->remainingToDisburse() > 0 ? 'balance-pos' : 'balance-zero' }}">{{ $business->currencySymbol() }}{{ number_format($loan->remainingToDisburse(), 2) }}</div></td>
        </tr>
    </table>

    <h2>Disbursements ({{ $loan->disbursements->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Method</th>
                <th>Reference / Cheque No.</th>
                <th>Received In</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loan->disbursements as $disbursement)
                <tr>
                    <td>{{ $disbursement->paid_at->format('d M Y') }}</td>
                    <td>{{ $disbursement->method ? ucfirst(str_replace('_', ' ', $disbursement->method)) : '—' }}</td>
                    <td>{{ $disbursement->reference ?: '—' }}</td>
                    <td>{{ $disbursement->account?->label() ?? '—' }}</td>
                    <td class="text-right">{{ $business->currencySymbol() }}{{ number_format($disbursement->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">No disbursements recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
