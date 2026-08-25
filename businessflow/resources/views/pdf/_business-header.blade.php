{{--
    Shared business identity block for every PDF header (Invoice, Quotation,
    Customer Statement, Investor Statement) — logo beside the name with a
    divider between them, contact line right under the name, doc label
    (Invoice/Quotation/...) below that. Kept as one partial so a design
    change here doesn't have to be repeated by hand across every template.

    All styles are inline rather than in each template's <style> block —
    dompdf's CSS cascade is unreliable enough that inline is the safe
    choice, and the generic `table`/`td` rules in those <style> blocks
    (width:100%, padding, border-bottom) would otherwise leak into this
    inner table.
--}}
@if ($business?->logoDataUri())
    <table style="width: auto; border-collapse: collapse; margin: 0;">
        <tr>
            <td style="border: none; padding: 0 10px 0 0; vertical-align: middle;">
                <img src="{{ $business->logoDataUri() }}" alt="{{ $business->name }}" style="height: 40px; max-width: 140px;">
            </td>
            <td style="border: none; border-left: 2px solid #cbd5e0; padding: 0 0 0 10px; vertical-align: middle;">
                <h1 style="margin: 0; font-size: 20px;">{{ $business->name }}</h1>
            </td>
        </tr>
    </table>
@else
    <h1 style="margin: 0; font-size: 20px;">{{ $business?->name ?? config('app.name') }}</h1>
@endif

@if ($business && collect([$business->phone, $business->address, $business->email, $business->website])->filter()->isNotEmpty())
    <div class="muted" style="font-size: 10px; margin-top: 3px;">
        {{ collect([$business->phone, $business->address, $business->email, $business->website])->filter()->implode(' · ') }}
    </div>
@endif

<div style="font-size: 15px; font-weight: 600; color: #4a5568; margin-top: 6px;">{{ $docLabel }}</div>
