<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report['title'] }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1a202c; }
        .muted { color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { text-align: left; background: #f7fafc; padding: 5px 8px; font-size: 9px; text-transform: uppercase; color: #718096; }
        td { padding: 5px 8px; border-bottom: 1px solid #edf2f7; }
        .header { width: 100%; margin-bottom: 12px; }
        .header td { border: none; padding: 0; vertical-align: top; }
        .totals { margin-top: 14px; }
        .totals span { margin-right: 20px; font-size: 11px; }
        .totals strong { color: #1a202c; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @include('pdf._business-header', ['docLabel' => $report['title']])
            </td>
            <td style="text-align: right;">
                <div class="muted">Generated: {{ now()->format('d M Y, h:i A') }}</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                @foreach ($report['columns'] as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($report['rows'] as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($report['columns']) }}" class="muted">No data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        @foreach ($report['totals'] as $label => $value)
            <span>{{ $label }}: <strong>{{ $value }}</strong></span>
        @endforeach
    </div>
</body>
</html>
