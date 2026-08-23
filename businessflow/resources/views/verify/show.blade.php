<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Document — {{ $business?->name ?? config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 24px; color: #1a202c; }
        .card { max-width: 420px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .badge { background: #16a34a; color: #fff; text-align: center; padding: 20px; }
        .badge .check { font-size: 32px; line-height: 1; }
        .badge .label { font-weight: 600; margin-top: 6px; font-size: 15px; }
        .body { padding: 24px; }
        .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        .row:last-child { border-bottom: none; }
        .row .k { color: #718096; }
        .row .v { font-weight: 600; text-align: right; }
        .business { text-align: center; padding: 16px 24px 0; font-size: 13px; color: #718096; }
        .business strong { color: #1a202c; font-size: 15px; }
        .footer { text-align: center; padding: 16px; font-size: 12px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">
            <div class="check">&#10003;</div>
            <div class="label">Verified Document</div>
        </div>
        <div class="business">
            Issued by<br><strong>{{ $business?->name ?? config('app.name') }}</strong>
        </div>
        <div class="body">
            <div class="row"><span class="k">Document</span><span class="v">{{ $docType }}</span></div>
            <div class="row"><span class="k">Number</span><span class="v">{{ $docNumber }}</span></div>
            <div class="row"><span class="k">Customer</span><span class="v">{{ $customerName }}</span></div>
            <div class="row"><span class="k">{{ $amountLabel ?? 'Amount' }}</span><span class="v">{{ number_format($amount, 2) }}</span></div>
            <div class="row"><span class="k">Date</span><span class="v">{{ $date->format('d M Y') }}</span></div>
            @if ($status)
                <div class="row"><span class="k">Status</span><span class="v">{{ ucfirst(str_replace('_', ' ', $status)) }}</span></div>
            @endif
        </div>
        <div class="footer">This page confirms the document above is genuine.</div>
    </div>
</body>
</html>
