<?php
require_once __DIR__ . '/SimplePDF.php';

/** Keeps a table cell's text from overflowing past its column width. */
function pdf_truncate(string $text, int $maxChars): string
{
    return mb_strlen($text) > $maxChars ? rtrim(mb_substr($text, 0, $maxChars - 3)) . '...' : $text;
}

/**
 * Renders a single sale/invoice as a PDF and streams it to the browser.
 * $sale: row from `sales` joined with customer info.
 * $items: rows from `sale_items`.
 * $totals: ['paid' => float, 'balance' => float] for this customer overall.
 */
function render_invoice_pdf(array $sale, array $items, array $customer, string $dest = 'I'): void
{
    $pdf = new SimplePDF();
    $margin = 40;
    $y = $margin;

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

    // Table header - column widths sized so numbers never cross the outer
    // border, even for large amounts (e.g. 1,15,500.00).
    $tableW = $pdf->pageWidth() - 2 * $margin;
    $colW = ['product' => $tableW - 70 - 95 - 135, 'qty' => 70, 'price' => 95, 'amount' => 135];
    $colX = [$margin, $margin + $colW['product'], $margin + $colW['product'] + $colW['qty'], $margin + $colW['product'] + $colW['qty'] + $colW['price']];
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
        $pdf->text($colX[2] + 4, $y + 14, number_format((float)$item['price'], 2), 10);
        $pdf->text($colX[3] + 4, $y + 14, number_format((float)$item['line_total'], 2), 10);
        $y += $rowH;
    }

    $y += 10;
    $pdf->text($colX[2] + 4, $y + 14, 'Total:', 11, true);
    $pdf->text($colX[3] + 4, $y + 14, number_format((float)$sale['total_amount'], 2), 11, true);
    $y += 34;

    if (!empty($sale['notes'])) {
        $pdf->text($margin, $y, 'Note: ' . $sale['notes'], 10);
        $y += 20;
    }

    $y += 10;
    $pdf->line($margin, $y, $pdf->pageWidth() - $margin, $y);
    $y += 18;
    $pdf->text($margin, $y, 'This is a computer generated invoice - ' . APP_NAME, 8);

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

    // Column widths sized so large amounts (lakhs, e.g. 1,90,500.00) never
    // cross the outer border - this was overflowing before (Balance column
    // was only ~45pt wide).
    $tableW = $pdf->pageWidth() - 2 * $margin;
    $colW = ['date' => 62, 'desc' => 0, 'given' => 88, 'paid' => 88, 'balance' => 95];
    $colW['desc'] = $tableW - $colW['date'] - $colW['given'] - $colW['paid'] - $colW['balance'];
    $colX = [
        $margin,
        $margin + $colW['date'],
        $margin + $colW['date'] + $colW['desc'],
        $margin + $colW['date'] + $colW['desc'] + $colW['given'],
        $margin + $colW['date'] + $colW['desc'] + $colW['given'] + $colW['paid'],
    ];
    $pdf->rect($margin, $y, $tableW, 20);
    $pdf->text($colX[0] + 4, $y + 14, 'Date', 10, true);
    $pdf->text($colX[1] + 4, $y + 14, 'Description', 10, true);
    $pdf->text($colX[2] + 4, $y + 14, 'Given (Rs.)', 10, true);
    $pdf->text($colX[3] + 4, $y + 14, 'Paid (Rs.)', 10, true);
    $pdf->text($colX[4] + 4, $y + 14, 'Balance', 10, true);
    $y += 20;

    $balance = 0;
    foreach ($rows as $r) {
        if ($y > $pdf->pageHeight() - 80) { break; } // simple single-page cap for V1
        $balance += $r['debit'] - $r['credit'];
        $rowH = 18;
        $pdf->rect($margin, $y, $tableW, $rowH);
        $pdf->text($colX[0] + 4, $y + 13, date('d-M-y', strtotime($r['date'])), 9);
        $pdf->text($colX[1] + 4, $y + 13, pdf_truncate($r['desc'], 26), 9);
        $pdf->text($colX[2] + 4, $y + 13, $r['debit'] > 0 ? number_format($r['debit'], 2) : '-', 9);
        $pdf->text($colX[3] + 4, $y + 13, $r['credit'] > 0 ? number_format($r['credit'], 2) : '-', 9);
        $pdf->text($colX[4] + 4, $y + 13, number_format($balance, 2), 9);
        $y += $rowH;
    }

    $y += 20;
    $pdf->text($margin, $y, 'Total Given: Rs. ' . number_format(array_sum(array_column($rows, 'debit')), 2), 11, true);
    $y += 16;
    $pdf->text($margin, $y, 'Total Paid: Rs. ' . number_format(array_sum(array_column($rows, 'credit')), 2), 11, true);
    $y += 16;
    $pdf->text($margin, $y, 'Balance Due: Rs. ' . number_format($balance, 2), 12, true);

    $pdf->output('statement-' . preg_replace('/\s+/', '-', $customer['name']) . '.pdf', $dest);
}
