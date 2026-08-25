<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statement - {{ $customer->name }}</title>
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
        .balance-pos { color: #c53030; }
        .balance-zero { color: #2f855a; }
        .qr { text-align: center; }
        .qr img { width: 70px; height: 70px; }
        .qr div { font-size: 8px; color: #a0aec0; margin-top: 2px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @if ($business?->logoDataUri())
                    <img src="{{ $business->logoDataUri() }}" alt="{{ $business->name }}" style="max-height: 48px; max-width: 180px; margin-bottom: 4px;">
                @endif
                <h1>{{ $business?->name ?? config('app.name') }}</h1>
                <div class="muted">Customer Statement</div>
                @if ($business?->address)<div class="muted" style="font-size: 10px; margin-top: 4px;">{{ $business->address }}</div>@endif
                @if ($business?->phone)<div class="muted" style="font-size: 10px;">{{ $business->phone }}</div>@endif
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
        <strong>{{ $customer->name }}</strong><br>
        @if ($customer->company){{ $customer->company }}<br>@endif
        @if ($customer->phone){{ $customer->phone }}<br>@endif
        @if ($customer->email){{ $customer->email }}@endif
    </div>

    @php
        $totalValue = $customer->units->sum(fn ($u) => $u->price);
        $totalPaid = $customer->units->sum(fn ($u) => $u->totalCollected());
        $totalDue = $customer->units->sum(fn ($u) => $u->totalOutstanding());
    @endphp

    <table class="summary">
        <tr>
            <td><div class="label">Sale value</div><div class="value">{{ number_format($totalValue, 2) }}</div></td>
            <td><div class="label">Total received</div><div class="value">{{ number_format($totalPaid, 2) }}</div></td>
            <td><div class="label">Balance due</div><div class="value {{ $totalDue > 0 ? 'balance-pos' : 'balance-zero' }}">{{ number_format($totalDue, 2) }}</div></td>
        </tr>
    </table>

    @if ($customer->units->isNotEmpty())
        <h2>Properties Purchased</h2>
        <table>
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Unit</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customer->units as $unit)
                    <tr>
                        <td>{{ $unit->project->name }}</td>
                        <td>{{ $unit->unit_number }}</td>
                        <td class="text-right">{{ number_format($unit->price, 2) }}</td>
                        <td class="text-right">{{ number_format($unit->totalCollected(), 2) }}</td>
                        <td class="text-right">{{ number_format($unit->totalOutstanding(), 2) }}</td>
                        <td>
                            {{ $unit->write_off_at ? 'Written off' : ucfirst($unit->status) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Invoices</h2>
    <table>
        <thead>
            <tr>
                <th>Number</th>
                <th>Date</th>
                <th>Project</th>
                <th class="text-right">Total</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customer->invoices as $invoice)
                <tr>
                    <td>{{ $invoice->number }}</td>
                    <td>{{ $invoice->created_at->format('d M Y') }}</td>
                    <td>{{ $invoice->project?->name ?? '—' }}{{ $invoice->projectUnit ? ' · '.$invoice->projectUnit->unit_number : '' }}</td>
                    <td class="text-right">{{ number_format($invoice->total, 2) }}</td>
                    <td class="text-right">{{ number_format($invoice->amount_paid, 2) }}</td>
                    <td class="text-right">{{ $invoice->balanceDue() }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No invoices yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Payment History</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Against</th>
                <th>For</th>
                <th>Method</th>
                <th>Reference</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $invoicePayments = $customer->invoices->flatMap(fn ($inv) => $inv->payments->map(fn ($p) => (object) [
                    'paid_at' => $p->paid_at,
                    'against' => $inv->number,
                    'for' => 'Invoice',
                    'method' => $p->method,
                    'reference' => $p->reference,
                    'amount' => $p->amount,
                ]));
                $unitPayments = $customer->units->flatMap(fn ($u) => $u->payments->map(fn ($p) => (object) [
                    'paid_at' => $p->paid_at,
                    'against' => $u->unit_number,
                    'for' => $p->purposeLabel().($p->description ? ' — '.$p->description : ''),
                    'method' => $p->method,
                    'reference' => $p->reference,
                    'amount' => $p->amount,
                ]));
                $allPayments = $invoicePayments->concat($unitPayments)->sortByDesc('paid_at');
            @endphp
            @forelse ($allPayments as $payment)
                <tr>
                    <td>{{ $payment->paid_at->format('d M Y') }}</td>
                    <td>{{ $payment->against }}</td>
                    <td>{{ $payment->for }}</td>
                    <td>{{ $payment->method ? ucfirst(str_replace('_', ' ', $payment->method)) : '—' }}</td>
                    <td>{{ $payment->reference ?? '—' }}</td>
                    <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No payments recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    @php $writtenOff = $customer->units->filter(fn ($u) => $u->write_off_at); @endphp
    @if ($writtenOff->isNotEmpty())
        <h2>Written Off</h2>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Date</th>
                    <th class="text-right">Amount</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($writtenOff as $unit)
                    <tr>
                        <td>{{ $unit->unit_number }}</td>
                        <td>{{ $unit->write_off_at->format('d M Y') }}</td>
                        <td class="text-right">{{ number_format($unit->write_off_amount, 2) }}</td>
                        <td>{{ $unit->write_off_note ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
