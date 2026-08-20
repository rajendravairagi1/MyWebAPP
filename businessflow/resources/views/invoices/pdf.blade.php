<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a202c; }
        h1 { font-size: 20px; margin-bottom: 0; }
        .muted { color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { text-align: left; background: #f7fafc; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #718096; }
        td { padding: 6px 8px; border-bottom: 1px solid #edf2f7; }
        .text-right { text-align: right; }
        .totals { width: 250px; margin-left: auto; margin-top: 12px; }
        .totals td { border: none; padding: 2px 8px; }
        .totals .grand { font-weight: bold; border-top: 1px solid #cbd5e0; }
        .header { width: 100%; margin-bottom: 20px; }
        .header td { border: none; padding: 0; vertical-align: top; }
        .balance { color: #c53030; font-weight: bold; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <h1>{{ $business?->name ?? config('app.name') }}</h1>
                <div class="muted">Invoice</div>
            </td>
            <td class="text-right">
                <div><strong>{{ $invoice->number }}</strong></div>
                <div class="muted">Date: {{ $invoice->created_at->format('d M Y') }}</div>
                @if ($invoice->due_date)
                    <div class="muted">Due: {{ $invoice->due_date->format('d M Y') }}</div>
                @endif
            </td>
        </tr>
    </table>

    <div>
        <div class="muted">Bill to</div>
        <strong>{{ $invoice->customer->name }}</strong><br>
        @if ($invoice->customer->company){{ $invoice->customer->company }}<br>@endif
        @if ($invoice->customer->address){{ $invoice->customer->address }}<br>@endif
        @if ($invoice->customer->email){{ $invoice->customer->email }}<br>@endif
        @if ($invoice->customer->phone){{ $invoice->customer->phone }}@endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit price</th>
                <th class="text-right">Line total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ rtrim(rtrim($item->quantity, '0'), '.') ?: '0' }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td></tr>
        <tr><td>Discount</td><td class="text-right">{{ number_format($invoice->discount_total, 2) }}</td></tr>
        <tr><td>Tax</td><td class="text-right">{{ number_format($invoice->tax_total, 2) }}</td></tr>
        <tr class="grand"><td>Total</td><td class="text-right">{{ number_format($invoice->total, 2) }}</td></tr>
        <tr><td>Paid</td><td class="text-right">{{ number_format($invoice->amount_paid, 2) }}</td></tr>
        <tr><td class="balance">Balance due</td><td class="text-right balance">{{ $invoice->balanceDue() }}</td></tr>
    </table>

    @if ($invoice->notes)
        <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
    @endif
</body>
</html>
