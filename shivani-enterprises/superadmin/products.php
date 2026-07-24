<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('super_admin');
$pageTitle = 'Products';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $unit = trim($_POST['unit'] ?? '') ?: 'Piece';
        $price = (float)($_POST['default_price'] ?? 0);
        if ($name === '') {
            flash('error', 'Product name is required.');
        } else {
            $stmt = db()->prepare('INSERT INTO products (name, unit, default_price) VALUES (?,?,?)');
            $stmt->execute([$name, $unit, $price]);
            flash('success', 'Product added.');
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $unit = trim($_POST['unit'] ?? '') ?: 'Piece';
        $price = (float)($_POST['default_price'] ?? 0);
        $stmt = db()->prepare('UPDATE products SET name=?, unit=?, default_price=? WHERE id=?');
        $stmt->execute([$name, $unit, $price, $id]);
        flash('success', 'Product updated.');
    } elseif ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = db()->prepare('UPDATE products SET is_active = 1 - is_active WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'Product status updated.');
    }
    redirect('superadmin/products.php');
}

$products = db()->query('SELECT * FROM products ORDER BY is_active DESC, name')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h3 class="mt-0">Add Product</h3>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="form-row">
      <div class="form-group"><label>Product Name</label><input name="name" required placeholder="e.g. Desert Cooler 55L"></div>
      <div class="form-group"><label>Unit</label><input name="unit" value="Piece"></div>
      <div class="form-group"><label>Default Price (Rs.)</label><input type="number" step="0.01" name="default_price" required></div>
    </div>
    <button class="btn" type="submit">Add Product</button>
  </form>
</div>

<div class="card">
  <h3 class="mt-0">All Products</h3>
  <table>
    <tr><th>Name</th><th>Unit</th><th>Default Price</th><th>Status</th><th>Update</th><th></th></tr>
    <?php foreach ($products as $p): $fid = 'edit-' . (int)$p['id']; ?>
    <tr>
        <td>
          <form id="<?= $fid ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
          </form>
          <input form="<?= $fid ?>" name="name" value="<?= e($p['name']) ?>" style="min-width:180px">
        </td>
        <td><input form="<?= $fid ?>" name="unit" value="<?= e($p['unit']) ?>" style="width:90px"></td>
        <td><input form="<?= $fid ?>" type="number" step="0.01" name="default_price" value="<?= e($p['default_price']) ?>" style="width:100px"></td>
        <td><span class="badge <?= $p['is_active'] ? 'green' : 'gray' ?>"><?= $p['is_active'] ? 'Active' : 'Inactive' ?></span></td>
        <td><button form="<?= $fid ?>" class="btn small" type="submit">Save</button></td>
      <td>
        <form method="post" onsubmit="return confirm('Change status?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
          <button class="btn small secondary" type="submit"><?= $p['is_active'] ? 'Deactivate' : 'Activate' ?></button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
