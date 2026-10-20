<?php
require_once __DIR__ . '/config/database.php';

if (!$pdo) {
    echo '<div class="alert alert-warning">Database is not ready. Please run <strong>setup.php</strong> first.</div>';
    exit;
}

$pageTitle = 'Purchases';
$activePage = 'purchases';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_purchase'])) {
    $productId = (int) $_POST['product_id'];
    $quantity = (float) $_POST['quantity'];
    $unitPrice = (float) $_POST['unit_price'];
    $gstType = $_POST['gst_type'] ?? 'CGST/SGST';
    $gstRate = (float) $_POST['gst_rate'];
    $purchaseDate = $_POST['purchase_date'] ?? date('Y-m-d');

    $subtotal = $quantity * $unitPrice;
    $gstAmount = $subtotal * ($gstRate / 100);
    $cgst = ($gstType === 'IGST') ? 0 : $gstAmount / 2;
    $sgst = ($gstType === 'IGST') ? 0 : $gstAmount / 2;
    $igst = ($gstType === 'IGST') ? $gstAmount : 0;
    $totalAmount = $subtotal + $gstAmount;

    $stmt = $pdo->prepare('INSERT INTO purchases (product_id, quantity, unit_price, gst_type, gst_rate, cgst, sgst, igst, total_amount, purchase_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$productId, $quantity, $unitPrice, $gstType, $gstRate, $cgst, $sgst, $igst, $totalAmount, $purchaseDate]);

    $update = $pdo->prepare('UPDATE products SET stock_qty = stock_qty + ? WHERE id = ?');
    $update->execute([$quantity, $productId]);

    $message = 'Purchase recorded successfully.';
}

$products = $pdo->query('SELECT id, name, stock_qty FROM products ORDER BY name ASC')->fetchAll();
$purchases = $pdo->query('SELECT p.*, pr.name AS product_name FROM purchases p JOIN products pr ON pr.id = p.product_id ORDER BY p.purchase_date DESC, p.id DESC LIMIT 20')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">New Purchase</h3></div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Select product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="unit_price" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GST Type</label>
                        <select name="gst_type" class="form-select">
                            <option value="CGST/SGST">CGST/SGST</option>
                            <option value="IGST">IGST</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GST Rate (%)</label>
                        <input type="number" name="gst_rate" step="0.01" min="0" value="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" name="purchase_date" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                    </div>
                    <button type="submit" name="save_purchase" class="btn btn-primary w-100">Save Purchase</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">Purchase Ledger</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>GST</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchases)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No purchases recorded.</td></tr>
                        <?php else: ?>
                            <?php foreach ($purchases as $purchase): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($purchase['product_name']); ?></td>
                                    <td><?php echo $purchase['quantity']; ?></td>
                                    <td><?php echo formatCurrency($purchase['cgst'] + $purchase['sgst'] + $purchase['igst']); ?></td>
                                    <td><?php echo formatCurrency($purchase['total_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($purchase['purchase_date']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
