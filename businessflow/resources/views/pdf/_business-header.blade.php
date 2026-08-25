{{--
    Shared business identity block for every PDF header (Invoice, Quotation,
    Customer Statement, Investor Statement) — logo beside the name with a
    divider between them, address + phone on one line underneath. Kept as
    one partial so a design change here (like this one) doesn't have to be
    repeated by hand across every PDF template.

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

<div class="muted">{{ $docLabel }}</div>

@if ($business?->address || $business?->phone)
    <div class="muted" style="font-size: 10px; margin-top: 4px;">
        {{ collect([$business->address, $business->phone])->filter()->implode(' · ') }}
    </div>
@endif
