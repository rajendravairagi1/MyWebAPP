<?php
require_once __DIR__ . '/SimplePDF.php';

/** Keeps a table cell's text from overflowing past its column width. */
function pdf_truncate(string $text, int $maxChars): string
{
    return mb_strlen($text) > $maxChars ? rtrim(mb_substr($text, 0, $maxChars - 3)) . '...' : $text;
}

const PDF_DARK = '#141826';
const PDF_WHITE = '#ffffff';

/**
 * Draws a bordered signature box (with sign-here lines) right after the
 * content, in whatever space is left - so the customer/admin can sign for
 * verification. Returns the Y position after the box.
 */
function pdf_signature_box(SimplePDF $pdf, float $margin, float $tableW, float $y): float
{
    $boxH = 66;
    $pdf->rect($margin, $y, $tableW, $boxH, 0.7, '#9ca3af');
    $lineY = $y + 44;
    $lineW = 200;
    $pdf->line($margin + 24, $lineY, $margin + 24 + $lineW, $lineY, 0.7, '#374151');
    $pdf->text($margin + 24, $lineY + 15, 'Signature', 9, false, '#6b7280');

    $lineX2 = $margin + $tableW - 24 - $lineW;
    $pdf->line($lineX2, $lineY, $lineX2 + $lineW, $lineY, 0.7, '#374151');
    $pdf->text($lineX2, $lineY + 15, 'For ' . get_setting('company_name', APP_NAME), 9, false, '#6b7280');

    return $y + $boxH;
}

function pdf_footer(SimplePDF $pdf, float $margin, string $label): void
{
    $y = $pdf->pageHeight() - 34;
    $pdf->line($margin, $y, $pdf->pageWidth() - $margin, $y, 0.5, '#e5e7eb');
    $pdf->text($margin, $y + 16, $label . ' - ' . get_setting('company_name', APP_NAME), 8, false, '#6b7280');
}

/**
 * Renders a single sale/invoice as a PDF and streams it to the browser.
 */
function render_invoice_pdf(array $sale, array $items, array $customer, string $dest = 'I'): void
{
    $pdf = new SimplePDF();
    $margin = 40;
    $y = $margin;
    $tableW = $pdf->pageWidth() - 2 * $margin;

    $pdf->text($margin, $y, get_setting('company_name', APP_NAME), 18, true);
    $y += 18;
    $companyLine = trim(get_setting('company_address', '') . '  ' . get_setting('company_phone', ''));
    if ($companyLine !== '') {
        $pdf->text($margin, $y, $companyLine, 10);
        $y += 20;
    } else {
        $y += 10;
    }
    $pdf->line($margin, $y, $pdf->pageWidth() - $margin, $y);
    $y += 22;

    $pdf->text($margin, $y, 'TAX INVOICE', 14, true);
    $pdf->text($pdf->pageWidth() - $margin - 160, $y, 'Invoice No: ' . $sale['invoice_no'], 11, true);
    $y += 18;
    $pdf->text($pdf->pageWidth() - $margin - 160, $y, 'Date: ' . date('d-M-Y', strtotime($sale['sale_date'])), 10);
    $y += 26;

    $pdf->text($margin, $y, 'Bill To:', 10, true);
    $y += 14;
    $pdf->text($margin, $y, $customer['name'] . ($customer['shop_name'] ? ' (' . $customer['shop_name'] . ')' : ''), 11);
    $y += 14;
    if (!empty($customer['place'])) { $pdf->text($margin, $y, $customer['place'], 10); $y += 14; }
    $pdf->text($margin, $y, 'Mobile: ' . $customer['mobile'], 10);
    $y += 26;

    // Column widths sized so numbers never cross the outer border, even
    // for amounts up to several lakh.
    $colW = ['product' => $tableW - 70 - 95 - 135, 'qty' => 70, 'price' => 95, 'amount' => 135];
    $colX = [$margin, $margin + $colW['product'], $margin + $colW['product'] + $colW['qty'], $margin + $colW['product'] + $colW['qty'] + $colW['price']];
    $rightEdge = $margin + $tableW - 6;
    $priceRight = $colX[3] - 6;

    $pdf->rect($margin, $y, $tableW, 20);
    $pdf->text($colX[0] + 4, $y + 14, 'Product', 10, true);
    $pdf->text($colX[1] + 4, $y + 14, 'Qty', 10, true);
    $pdf->text($colX[2] + 4, $y + 14, 'Price', 10, true);
    $pdf->text($colX[3] + 4, $y + 14, 'Amount', 10, true);
    $y += 20;

    foreach ($items as $item) {
        $rowH = 20;
        $pdf->rect($margin, $y, $tableW, $rowH);
        $pdf->text($colX[0] + 4, $y + 14, pdf_truncate($item['product_name'], 34), 10);
        $pdf->text($colX[1] + 4, $y + 14, rtrim(rtrim(number_format((float)$item['qty'], 2), '0'), '.'), 10);
        $pdf->textRight($priceRight, $y + 14, number_format((float)$item['price'], 2), 10);
        $pdf->textRight($rightEdge, $y + 14, number_format((float)$item['line_total'], 2), 10);
        $y += $rowH;
    }
    $y += 10;

    // Total, in a dark highlight box.
    $boxH = 30;
    $pdf->rectFilled($margin, $y, $tableW, $boxH, PDF_DARK);
    $pdf->text($margin + 12, $y + 20, 'Total Amount', 12, true, PDF_WHITE);
    $pdf->textRight($margin + $tableW - 12, $y + 20, 'Rs. ' . number_format((float)$sale['total_amount'], 2), 13, true, PDF_WHITE);
    $y += $boxH + 16;

    if (!empty($sale['notes'])) {
        $pdf->text($margin, $y, 'Note: ' . $sale['notes'], 10);
        $y += 20;
    }

    $y = pdf_signature_box($pdf, $margin, $tableW, $y + 6);
    pdf_footer($pdf, $margin, 'This is a computer generated invoice');

    $pdf->output('invoice-' . $sale['invoice_no'] . '.pdf', $dest);
}

/**
 * Renders a full customer statement (all sales + all payments + running
 * balance) as a PDF.
 */
function render_statement_pdf(array $customer, array $sales, array $payments, string $dest = 'I'): void
{
    $pdf = new SimplePDF();
    $margin = 40;
    $y = $margin;
    $tableW = $pdf->pageWidth() - 2 * $margin;

    $pdf->text($margin, $y, get_setting('company_name', APP_NAME), 18, true);
    $y += 26;
    $pdf->text($margin, $y, 'Customer Statement', 14, true);
    $y += 20;
    $pdf->text($margin, $y, $customer['name'] . ($customer['shop_name'] ? ' (' . $customer['shop_name'] . ')' : '') . ' - ' . $customer['mobile'], 11);
    $y += 14;
    if (!empty($customer['place'])) { $pdf->text($margin, $y, $customer['place'], 10); $y += 14; }
    $y += 10;
    $pdf->line($margin, $y, $pdf->pageWidth() - $margin, $y);
    $y += 20;

    // Merge sales (debit) and payments (credit) into one chronological ledger
    $rows = [];
    foreach ($sales as $s) {
        $rows[] = ['date' => $s['sale_date'], 'desc' => 'Invoice ' . $s['invoice_no'], 'debit' => (float)$s['total_amount'], 'credit' => 0];
    }
    foreach ($payments as $p) {
        $rows[] = ['date' => $p['payment_date'], 'desc' => 'Payment (' . $p['mode'] . ')' . (!empty($p['note']) ? ' - ' . $p['note'] : ''), 'debit' => 0, 'credit' => (float)$p['amount']];
    }
    usort($rows, fn($a, $b) => strcmp($a['date'], $b['date']));

    // Column widths sized so large amounts (lakhs, e.g. 10,00,000.00) never
    // cross the outer border.
    $colW = ['date' => 62, 'desc' => 0, 'given' => 88, 'paid' => 88, 'balance' => 95];
    $colW['desc'] = $tableW - $colW['date'] - $colW['given'] - $colW['paid'] - $colW['balance'];
    $colX = [
        $margin,
        $margin + $colW['date'],
        $margin + $colW['date'] + $colW['desc'],
        $margin + $colW['date'] + $colW['desc'] + $colW['given'],
        $margin + $colW['date'] + $colW['desc'] + $colW['given'] + $colW['paid'],
    ];
    $givenRight = $colX[3] - 6;
    $paidRight = $colX[4] - 6;
    $balanceRight = $margin + $tableW - 6;

    $pdf->rect($margin, $y, $tableW, 20);
    $pdf->text($colX[0] + 4, $y + 14, 'Date', 10, true);
    $pdf->text($colX[1] + 4, $y + 14, 'Description', 10, true);
    $pdf->text($colX[2] + 4, $y + 14, 'Given (Rs.)', 10, true);
    $pdf->text($colX[3] + 4, $y + 14, 'Paid (Rs.)', 10, true);
    $pdf->textRight($balanceRight, $y + 14, 'Balance', 10, true);
    $y += 20;

    $balance = 0;
    foreach ($rows as $r) {
        if ($y > $pdf->pageHeight() - 240) { break; } // leave room for the 3 summary boxes + signature + footer
        $balance += $r['debit'] - $r['credit'];
        $rowH = 18;
        $pdf->rect($margin, $y, $tableW, $rowH);
        $pdf->text($colX[0] + 4, $y + 13, date('d-M-y', strtotime($r['date'])), 9);
        $pdf->text($colX[1] + 4, $y + 13, pdf_truncate($r['desc'], 26), 9);
        $pdf->textRight($givenRight, $y + 13, $r['debit'] > 0 ? number_format($r['debit'], 2) : '-', 9);
        $pdf->textRight($paidRight, $y + 13, $r['credit'] > 0 ? number_format($r['credit'], 2) : '-', 9);
        $pdf->textRight($balanceRight, $y + 13, number_format($balance, 2), 9);
        $y += $rowH;
    }
    $y += 14;

    // Total Given / Total Paid / Balance Due, each as its own dark box
    // with the label on the left and the amount right-aligned opposite it.
    $totalGiven = array_sum(array_column($rows, 'debit'));
    $totalPaid = array_sum(array_column($rows, 'credit'));
    $summary = [
        ['Total Given', $totalGiven],
        ['Total Paid', $totalPaid],
        ['Balance Due', $balance],
    ];
    $boxH = 26;
    foreach ($summary as [$label, $amount]) {
        $pdf->rectFilled($margin, $y, $tableW, $boxH, PDF_DARK);
        $pdf->text($margin + 12, $y + 17, $label, 11, true, PDF_WHITE);
        $pdf->textRight($margin + $tableW - 12, $y + 17, 'Rs. ' . number_format($amount, 2), 12, true, PDF_WHITE);
        $y += $boxH + 8;
    }

    $y = pdf_signature_box($pdf, $margin, $tableW, $y + 8);
    pdf_footer($pdf, $margin, 'This is a computer generated statement');

    $pdf->output('statement-' . preg_replace('/\s+/', '-', $customer['name']) . '.pdf', $dest);
}
