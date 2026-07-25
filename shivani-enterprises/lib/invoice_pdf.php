<?php
require_once __DIR__ . '/SimplePDF.php';
require_once __DIR__ . '/QRCode.php';

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

/** Draws a small "scan to verify" QR code in the top-right corner, top edge at $y. */
function pdf_verify_qr(SimplePDF $pdf, float $margin, string $url, float $y): void
{
    $box = 62;
    $x = $pdf->pageWidth() - $margin - $box;
    try {
        $matrix = QRCode::encodeMatrix($url);
        $pdf->drawQrMatrix($matrix, $x, $y, $box);
        $pdf->text($x, $y + $box + 11, 'Scan to verify', 7, false, '#6b7280');
    } catch (Throwable $e) {
        // QR generation should never break invoice/statement rendering.
    }
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

    pdf_verify_qr($pdf, $margin, invoice_verify_url($sale), $y - 12);

    $pdf->text($margin, $y, 'TAX INVOICE', 14, true);
    $y += 18;
    $pdf->text($margin, $y, 'Invoice No: ' . $sale['invoice_no'], 11, true);
    $y += 16;
    $pdf->text($margin, $y, 'Date: ' . date('d-M-Y', strtotime($sale['sale_date'])), 10);
    $y += 24;

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

    // sale['notes'] is an internal admin note (not customer-facing) and is
    // intentionally never printed on the invoice PDF.

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

    // Merge sales (debit) and payments (credit) into one chronological ledger,
    // and total everything up-front - the verify QR signs these exact totals
    // so it needs to know them before anything is drawn.
    $rows = [];
    foreach ($sales as $s) {
        $rows[] = ['date' => $s['sale_date'], 'desc' => 'Invoice ' . $s['invoice_no'], 'debit' => (float)$s['total_amount'], 'credit' => 0];
    }
    foreach ($payments as $p) {
        // p['note'] is an internal admin note (not customer-facing) and is
        // intentionally never printed on the statement.
        $rows[] = ['date' => $p['payment_date'], 'desc' => 'Payment (' . $p['mode'] . ')', 'debit' => 0, 'credit' => (float)$p['amount']];
    }
    usort($rows, fn($a, $b) => strcmp($a['date'], $b['date']));
    $totalGiven = array_sum(array_column($rows, 'debit'));
    $totalPaid = array_sum(array_column($rows, 'credit'));
    $finalBalance = $totalGiven - $totalPaid;

    pdf_verify_qr($pdf, $margin, statement_verify_url($customer), $margin - 4);

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
    $summary = [
        ['Total Given', $totalGiven],
        ['Total Paid', $totalPaid],
        ['Balance Due', $finalBalance],
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

/**
 * Renders an investor's ledger (investment + profit-credited entries add
 * to the balance owed to them; payment entries subtract) as a PDF, same
 * visual style as the customer statement.
 */
function render_investor_statement_pdf(array $investor, array $transactions, string $dest = 'I'): void
{
    $pdf = new SimplePDF();
    $margin = 40;
    $y = $margin;
    $tableW = $pdf->pageWidth() - 2 * $margin;

    $descLabels = ['investment' => 'Investment', 'profit' => 'Profit Credited', 'payment' => 'Payment Paid'];
    $rows = [];
    foreach ($transactions as $t) {
        // t['note'] is an internal admin note (not investor-facing) and is
        // intentionally never printed on the statement.
        $desc = $descLabels[$t['type']] ?? $t['type'];
        $rows[] = [
            'date' => $t['txn_date'],
            'desc' => $desc,
            'credit' => $t['type'] !== 'payment' ? (float)$t['amount'] : 0,
            'paid' => $t['type'] === 'payment' ? (float)$t['amount'] : 0,
        ];
    }
    usort($rows, fn($a, $b) => strcmp($a['date'], $b['date']));
    $totalInvested = array_sum(array_map(fn($t) => $t['type'] === 'investment' ? (float)$t['amount'] : 0, $transactions));
    $totalProfit = array_sum(array_map(fn($t) => $t['type'] === 'profit' ? (float)$t['amount'] : 0, $transactions));
    $totalPaid = array_sum(array_map(fn($t) => $t['type'] === 'payment' ? (float)$t['amount'] : 0, $transactions));
    $finalBalance = $totalInvested + $totalProfit - $totalPaid;

    pdf_verify_qr($pdf, $margin, investor_statement_verify_url($investor), $margin - 4);

    $pdf->text($margin, $y, get_setting('company_name', APP_NAME), 18, true);
    $y += 26;
    $pdf->text($margin, $y, 'Investor Statement', 14, true);
    $y += 20;
    $pdf->text($margin, $y, $investor['name'] . (!empty($investor['mobile']) ? ' - ' . $investor['mobile'] : ''), 11);
    $y += 24;
    $pdf->line($margin, $y, $pdf->pageWidth() - $margin, $y);
    $y += 20;

    // Column widths sized so large amounts (lakhs, e.g. 10,00,000.00) never
    // cross the outer border.
    $colW = ['date' => 62, 'desc' => 0, 'credit' => 90, 'paid' => 90, 'balance' => 95];
    $colW['desc'] = $tableW - $colW['date'] - $colW['credit'] - $colW['paid'] - $colW['balance'];
    $colX = [
        $margin,
        $margin + $colW['date'],
        $margin + $colW['date'] + $colW['desc'],
        $margin + $colW['date'] + $colW['desc'] + $colW['credit'],
        $margin + $colW['date'] + $colW['desc'] + $colW['credit'] + $colW['paid'],
    ];
    $creditRight = $colX[3] - 6;
    $paidRight = $colX[4] - 6;
    $balanceRight = $margin + $tableW - 6;

    $pdf->rect($margin, $y, $tableW, 20);
    $pdf->text($colX[0] + 4, $y + 14, 'Date', 10, true);
    $pdf->text($colX[1] + 4, $y + 14, 'Description', 10, true);
    $pdf->text($colX[2] + 4, $y + 14, 'Credited (Rs.)', 10, true);
    $pdf->text($colX[3] + 4, $y + 14, 'Paid (Rs.)', 10, true);
    $pdf->textRight($balanceRight, $y + 14, 'Balance', 10, true);
    $y += 20;

    $balance = 0;
    foreach ($rows as $r) {
        if ($y > $pdf->pageHeight() - 260) { break; } // leave room for the summary boxes + signature + footer
        $balance += $r['credit'] - $r['paid'];
        $rowH = 18;
        $pdf->rect($margin, $y, $tableW, $rowH);
        $pdf->text($colX[0] + 4, $y + 13, date('d-M-y', strtotime($r['date'])), 9);
        $pdf->text($colX[1] + 4, $y + 13, pdf_truncate($r['desc'], 30), 9);
        $pdf->textRight($creditRight, $y + 13, $r['credit'] > 0 ? number_format($r['credit'], 2) : '-', 9);
        $pdf->textRight($paidRight, $y + 13, $r['paid'] > 0 ? number_format($r['paid'], 2) : '-', 9);
        $pdf->textRight($balanceRight, $y + 13, number_format($balance, 2), 9);
        $y += $rowH;
    }
    $y += 14;

    // Total Investment / Total Profit Credited / Total Paid / Balance,
    // each as its own dark box with the label on the left and the amount
    // right-aligned opposite it.
    $summary = [
        ['Total Investment', $totalInvested],
        ['Total Profit Credited', $totalProfit],
        ['Total Paid', $totalPaid],
        ['Balance (Owed to Investor)', $finalBalance],
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

    $pdf->output('investor-statement-' . preg_replace('/\s+/', '-', $investor['name']) . '.pdf', $dest);
}
