<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $unit->project->name }} - {{ $unit->unit_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a202c; }
        h1 { font-size: 20px; margin-bottom: 0; }
        h2 { font-size: 16px; margin: 22px 0 6px; color: #2d3748; }
        .muted { color: #718096; }
        .header { width: 100%; margin-bottom: 20px; }
        .header td { border: none; padding: 0; vertical-align: top; }
        .price { font-size: 26px; font-weight: bold; color: #2f855a; margin-top: 4px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #e6fffa; color: #2c7a7b; }
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
                @include('pdf._business-header', ['docLabel' => 'Property Details', 'business' => $unit->business])
            </td>
        </tr>
    </table>

    <h2>{{ $unit->project->name }} — {{ $unit->unit_number }}</h2>
    <span class="badge">{{ ucfirst($unit->status) }}</span>
    <div class="price">{{ $unit->business?->currencySymbol() ?? '₹' }}{{ number_format($unit->price, 0) }}</div>

    <table class="info">
        @if ($unit->type)
            <tr><td class="label">Type</td><td>{{ $unit->type }}</td></tr>
        @endif
        @if ($unit->area_sqft)
            <tr><td class="label">Area</td><td>{{ number_format($unit->area_sqft, 0) }} sqft</td></tr>
        @endif
        @if ($unit->project->location)
            <tr><td class="label">Location</td><td>{{ $unit->project->location }}</td></tr>
        @endif
    </table>

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
