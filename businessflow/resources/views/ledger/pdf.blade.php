<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ledger</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a202c; }
        h2 { font-size: 13px; margin: 20px 0 6px; color: #2d3748; }
        .muted { color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th { text-align: left; background: #f7fafc; padding: 5px 7px; font-size: 9px; text-transform: uppercase; color: #718096; }
        td { padding: 5px 7px; border-bottom: 1px solid #edf2f7; }
        .text-right { text-align: right; }
        .header { width: 100%; margin-bottom: 18px; }
        .header td { border: none; padding: 0; vertical-align: top; }
        .summary { width: 100%; margin-top: 8px; }
        .summary td { border: none; padding: 4px 12px 4px 0; }
        .summary .label { color: #718096; font-size: 9px; text-transform: uppercase; }
        .summary .value { font-weight: bold; font-size: 13px; }
        .positive { color: #2f855a; }
        .negative { color: #c53030; }
        .footer-note { margin-top: 20px; padding-top: 8px; border-top: 1px solid #edf2f7; font-size: 10px; color: #a0aec0; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @include('pdf._business-header', ['docLabel' => 'Ledger'])
            </td>
            <td class="text-right">
                <div class="muted">Generated: {{ now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td><div class="label">Total Sales</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($totalSaleValue, 0) }}</div></td>
            <td><div class="label">Collected</div><div class="value positive">{{ $business->currencySymbol() }}{{ number_format($totalCollected, 0) }}</div></td>
            <td><div class="label">Outstanding</div><div class="value {{ $totalOutstanding > 0 ? 'negative' : '' }}">{{ $business->currencySymbol() }}{{ number_format($totalOutstanding, 0) }}</div></td>
            <td><div class="label">Purchases / Costs</div><div class="value">{{ $business->currencySymbol() }}{{ number_format($totalPurchases, 0) }}</div></td>
            <td><div class="label">Net Profit</div><div class="value {{ $netProfit >= 0 ? 'positive' : 'negative' }}">{{ $business->currencySymbol() }}{{ number_format($netProfit, 0) }}</div></td>
        </tr>
    </table>

    <h2>By Project</h2>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th class="text-right">Units Sold</th>
                <th class="text-right">Sale Value</th>
                <th class="text-right">Collected</th>
                <th class="text-right">Outstanding</th>
                <th class="text-right">Purchases</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($projects as $row)
                <tr>
                    <td>{{ $row->project->name }}</td>
                    <td class="text-right">{{ $row->unitCount }}</td>
                    <td class="text-right">{{ $business->currencySymbol() }}{{ number_format($row->saleValue, 0) }}</td>
                    <td class="text-right positive">{{ $business->currencySymbol() }}{{ number_format($row->collected, 0) }}</td>
                    <td class="text-right {{ $row->outstanding > 0 ? 'negative' : '' }}">{{ $business->currencySymbol() }}{{ number_format($row->outstanding, 0) }}</td>
                    <td class="text-right">{{ $business->currencySymbol() }}{{ number_format($row->purchases, 0) }}</td>
                    <td class="text-right {{ $row->profit >= 0 ? 'positive' : 'negative' }}">{{ $business->currencySymbol() }}{{ number_format($row->profit, 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No projects with sales or costs yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>By Customer &amp; Property</h2>
    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Property</th>
                <th class="text-right">Sale Value</th>
                <th class="text-right">Collected</th>
                <th class="text-right">Outstanding</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customerRows as $row)
                <tr>
                    <td>{{ $row->customer?->name ?? '—' }}</td>
                    <td>{{ $row->unit->project->name }} · {{ $row->unit->unit_number }}</td>
                    <td class="text-right">{{ $business->currencySymbol() }}{{ number_format($row->unit->price, 0) }}</td>
                    <td class="text-right positive">{{ $business->currencySymbol() }}{{ number_format($row->unit->totalCollected(), 0) }}</td>
                    <td class="text-right {{ $row->unit->totalOutstanding() > 0 ? 'negative' : '' }}">{{ $business->currencySymbol() }}{{ number_format($row->unit->totalOutstanding(), 0) }}</td>
                    <td>{{ $row->unit->write_off_at ? 'Written off' : ucfirst($row->unit->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No booked or sold properties yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($deals->isNotEmpty())
        <h2>Property Deals</h2>
        <table>
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Seller</th>
                    <th class="text-right">Purchase</th>
                    <th>Buyer</th>
                    <th class="text-right">Sale</th>
                    <th class="text-right">Profit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deals as $deal)
                    @php $profit = $deal->profit(); @endphp
                    <tr>
                        <td>{{ $deal->property_title }}</td>
                        <td>{{ $deal->seller_name ?? '—' }}</td>
                        <td class="text-right">{{ $business->currencySymbol() }}{{ number_format($deal->purchase_price, 0) }}</td>
                        <td>{{ $deal->buyer_name ?? '—' }}</td>
                        <td class="text-right">{{ $deal->sale_price !== null ? $business->currencySymbol().number_format($deal->sale_price, 0) : '—' }}</td>
                        <td class="text-right {{ $profit === null ? '' : ($profit >= 0 ? 'positive' : 'negative') }}">{{ $profit !== null ? $business->currencySymbol().number_format($profit, 0) : '—' }}</td>
                        <td>{{ $deal->statusLabel() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Manual Entries</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Description</th>
                <th>Account</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $entry)
                <tr>
                    <td>{{ $entry->entry_date->format('d M Y') }}</td>
                    <td>{{ ucfirst($entry->type) }}</td>
                    <td>{{ $entry->category ?? '—' }}</td>
                    <td>{{ $entry->description }}</td>
                    <td>{{ $entry->account?->label() ?? '—' }}</td>
                    <td class="text-right {{ $entry->type === 'income' ? 'positive' : 'negative' }}">{{ $business->currencySymbol() }}{{ number_format($entry->amount, 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No manual entries yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">This is a computer-generated document and does not require a signature.</div>
</body>
</html>
