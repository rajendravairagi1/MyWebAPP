<?php

namespace App\Models\Concerns;

/**
 * Shared by Quotation and Invoice — both are a header row plus line items
 * (quantity, unit_price, discount, tax_rate) that roll up into
 * subtotal/discount_total/tax_total/total on the header. Call
 * recalculateTotals() after items are created/updated/deleted.
 */
trait HasLineItemTotals
{
    public function recalculateTotals(): void
    {
        $subtotal = 0;
        $discountTotal = 0;
        $taxTotal = 0;

        foreach ($this->items as $item) {
            $lineBase = ($item->quantity * $item->unit_price) - $item->discount;
            $lineTax = round($lineBase * ($item->tax_rate / 100), 2);
            $lineTotal = round($lineBase + $lineTax, 2);

            if ($item->line_total != $lineTotal) {
                $item->forceFill(['line_total' => $lineTotal])->save();
            }

            $subtotal += $item->quantity * $item->unit_price;
            $discountTotal += $item->discount;
            $taxTotal += $lineTax;
        }

        $this->forceFill([
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'total' => round($subtotal - $discountTotal + $taxTotal, 2),
        ])->save();
    }
}
