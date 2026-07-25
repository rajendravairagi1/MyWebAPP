<?php
require_once __DIR__ . '/includes/functions.php';
no_store_headers();

$valid = false;
$details = [];
$title = 'Document';

if (isset($_GET['i'])) {
    // Invoice verification - signature only covers the sale id (kept short
    // for the QR code); everything shown is looked up live from the DB, so
    // it always reflects the real current invoice record.
    $id = (int)$_GET['i'];
    $sig = (string)($_GET['s'] ?? '');
    if ($sig !== '' && hash_equals(verify_sign(['invoice', $id]), $sig)) {
        $stmt = db()->prepare('SELECT s.*, c.name AS customer_name, c.mobile FROM sales s JOIN customers c ON c.id = s.customer_id WHERE s.id = ?');
        $stmt->execute([$id]);
        $sale = $stmt->fetch();
        if ($sale) {
            $valid = true;
            $title = 'Invoice';
            $details = [
                'Invoice No' => $sale['invoice_no'],
                'Date' => date('d-M-Y', strtotime($sale['sale_date'])),
                'Customer' => $sale['customer_name'],
                'Amount' => 'Rs. ' . number_format((float)$sale['total_amount'], 2),
            ];
        }
    }
} elseif (isset($_GET['c'])) {
    // Statement verification - shows the customer's live running balance.
    $id = (int)$_GET['c'];
    $sig = (string)($_GET['s'] ?? '');
    if ($sig !== '' && hash_equals(verify_sign(['statement', $id]), $sig)) {
        $stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
        $stmt->execute([$id]);
        $customer = $stmt->fetch();
        if ($customer) {
            $stmt = db()->prepare('SELECT COALESCE(SUM(total_amount),0) FROM sales WHERE customer_id = ?');
            $stmt->execute([$id]);
            $given = (float)$stmt->fetchColumn();
            $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) FROM payments WHERE customer_id = ?');
            $stmt->execute([$id]);
            $paid = (float)$stmt->fetchColumn();
            $valid = true;
            $title = 'Statement';
            $details = [
                'Customer' => $customer['name'] . ($customer['shop_name'] ? ' (' . $customer['shop_name'] . ')' : ''),
                'Total Given' => 'Rs. ' . number_format($given, 2),
                'Total Paid' => 'Rs. ' . number_format($paid, 2),
                'Balance Due (as of now)' => 'Rs. ' . number_format($given - $paid, 2),
            ];
        }
    }
} elseif (isset($_GET['inv'])) {
    // Investor statement verification - shows the investor's live totals.
    $id = (int)$_GET['inv'];
    $sig = (string)($_GET['s'] ?? '');
    if ($sig !== '' && hash_equals(verify_sign(['investor', $id]), $sig)) {
        $stmt = db()->prepare('SELECT * FROM investors WHERE id = ?');
        $stmt->execute([$id]);
        $investor = $stmt->fetch();
        if ($investor) {
            $stmt = db()->prepare("SELECT COALESCE(SUM(amount),0) FROM investor_transactions WHERE investor_id = ? AND type = 'investment'");
            $stmt->execute([$id]);
            $invested = (float)$stmt->fetchColumn();
            $stmt = db()->prepare("SELECT COALESCE(SUM(amount),0) FROM investor_transactions WHERE investor_id = ? AND type = 'payout'");
            $stmt->execute([$id]);
            $paidOut = (float)$stmt->fetchColumn();
            $valid = true;
            $title = 'Investor Statement';
            $details = [
                'Investor' => $investor['name'],
                'Total Investment' => 'Rs. ' . number_format($invested, 2),
                'Total Profit Paid' => 'Rs. ' . number_format($paidOut, 2),
                'Net (as of now)' => 'Rs. ' . number_format($invested - $paidOut, 2),
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Document Verification · <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(base_url('assets/css/style.css')) ?>">
</head>
<body>
<div class="login-wrap">
  <div class="login-box" style="max-width:400px">
    <h1><?= e(APP_NAME) ?></h1>
    <p class="sub"><?= e($title) ?> Verification</p>
    <?php if ($valid): ?>
      <div class="alert success" style="text-align:center;font-weight:700;font-size:16px">&#10003; Genuine Document</div>
      <table style="margin-top:10px;min-width:0;width:100%">
        <?php foreach ($details as $label => $val): ?>
          <tr><td class="text-muted"><?= e($label) ?></td><td style="text-align:right;font-weight:700"><?= e($val) ?></td></tr>
        <?php endforeach; ?>
      </table>
      <p class="text-muted" style="font-size:12px;margin-top:14px;text-align:center">Ye document <?= e(get_setting('company_name', APP_NAME)) ?> dwara generate kiya gaya hai aur genuine hai.</p>
    <?php else: ?>
      <div class="alert error" style="text-align:center;font-weight:700;font-size:16px">&#10007; Not Verified</div>
      <p class="text-muted" style="font-size:13px;text-align:center">Ye link invalid hai ya document match nahi ho raha. Agar aapko lagta hai ye galti hai, seedha <?= e(get_setting('company_name', APP_NAME)) ?> se sampark karein.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
