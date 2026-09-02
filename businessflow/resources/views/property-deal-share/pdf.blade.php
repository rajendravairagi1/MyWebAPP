<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $deal->property_title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a202c; }
        h1 { font-size: 20px; margin-bottom: 0; }
        h2 { font-size: 16px; margin: 22px 0 6px; color: #2d3748; }
        .muted { color: #718096; }
        .header { width: 100%; margin-bottom: 20px; }
        .header td { border: none; padding: 0; vertical-align: top; }
        .price { font-size: 26px; font-weight: bold; color: #2f855a; margin-top: 4px; }
        .info { width: 100%; margin-top: 14px; }
        .info td { border: none; padding: 4px 12px 4px 0; }
        .info .label { color: #718096; width: 120px; }
        .photos { width: 100%; margin-top: 10px; }
        .photos td { border: none; padding: 4px; text-align: center; }
        .photos img { width: 100%; max-height: 160px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @include('pdf._business-header', ['docLabel' => 'Property Details', 'business' => $deal->business])
            </td>
        </tr>
    </table>

    <h2>{{ $deal->property_title }}</h2>
    @if ($deal->asking_price)
        <div class="price">{{ $deal->business?->currencySymbol() ?? '₹' }}{{ number_format($deal->asking_price, 0) }}</div>
    @endif

    @if ($deal->address)
        <table class="info">
            <tr><td class="label">Location</td><td>{{ $deal->address }}</td></tr>
        </table>
    @endif

    @if ($deal->contact_name || $deal->contact_phone || $deal->contact_email)
        <h2>Contact for this Property</h2>
        <table class="info">
            @if ($deal->contact_name)
                <tr><td class="label">Name</td><td>{{ $deal->contact_name }}</td></tr>
            @endif
            @if ($deal->contact_phone)
                <tr><td class="label">Mobile</td><td>{{ $deal->contact_phone }}</td></tr>
            @endif
            @if ($deal->contact_email)
                <tr><td class="label">Email</td><td>{{ $deal->contact_email }}</td></tr>
            @endif
        </table>
    @endif

    @if ($photos->isNotEmpty())
        <h2>Photos</h2>
        <table class="photos">
            @foreach ($photos->chunk(2) as $row)
                <tr>
                    @foreach ($row as $photo)
                        <td style="width: 50%;"><img src="{{ $photo->dataUri }}" alt="{{ $photo->original_name }}"></td>
                    @endforeach
                    @if ($row->count() === 1)
                        <td style="width: 50%;"></td>
                    @endif
                </tr>
            @endforeach
        </table>
    @endif
</body>
</html>
