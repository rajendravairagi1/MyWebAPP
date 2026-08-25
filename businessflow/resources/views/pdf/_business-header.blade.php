{{--
    Shared business identity block for every PDF header (Invoice, Quotation,
    Customer Statement, Investor Statement) — logo on the left with a
    divider, name/contact-line/doc-label stacked to its right so they all
    start at the same left edge as the name (not under the logo). Kept as
    one partial so a design change here doesn't have to be repeated by
    hand across every template.

    All styles are inline rather than in each template's <style> block —
    dompdf's CSS cascade is unreliable enough that inline is the safe
    choice, and the generic `table`/`td` rules in those <style> blocks
    (width:100%, padding, border-bottom) would otherwise leak into this
    inner table.
--}}
@php
    $contactLine = $business ? collect([$business->phone, $business->email, $business->website])->filter()->implode(' · ') : '';
@endphp

<table style="width: auto; border-collapse: collapse; margin: 0;">
    <tr>
        @if ($business?->logoDataUri())
            <td style="border: none; padding: 0 10px 0 0; vertical-align: top;">
                <img src="{{ $business->logoDataUri() }}" alt="{{ $business->name }}" style="height: 40px; max-width: 140px;">
            </td>
            <td style="border: none; border-left: 2px solid #cbd5e0; padding: 0 0 0 10px; vertical-align: top;">
        @else
            <td style="border: none; padding: 0;">
        @endif
                <h1 style="margin: 0; font-size: 20px;">{{ $business?->name ?? config('app.name') }}</h1>
                @if ($contactLine)
                    <div class="muted" style="font-size: 10px; margin-top: 3px;">{{ $contactLine }}</div>
                @endif
                <div style="font-size: 15px; font-weight: 600; color: #4a5568; margin-top: 6px;">{{ $docLabel }}</div>
            </td>
    </tr>
</table>
