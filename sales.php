<?php
require_once __DIR__ . '/config/database.php';

if (!$pdo) {
    echo '<div class="alert alert-warning">Database is not ready. Please run <strong>setup.php</strong> first.</div>';
    exit;
}

$pageTitle = 'Sales';
$activePage = 'sales';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_sale'])) {
    $productId = (int) $_POST['product_id'];
    $quantity = (float) $_POST['quantity'];
    $salePrice = (float) $_POST['sale_price'];
    $gstType = $_POST['gst_type'] ?? 'CGST/SGST';
    $gstRate = (float) $_POST['gst_rate'];
    $saleDate = $_POST['sale_date'] ?? date('Y-m-d');

    $product = $pdo->prepare('SELECT stock_qty FROM products WHERE id = ?');
    $product->execute([$productId]);
    $productData = $product->fetch();

    if (!$productData || $productData['stock_qty'] < $quantity) {
        $message = 'Error: Insufficient stock for selected product.';
    } else {
        $subtotal = $quantity * $salePrice;
        $gstAmount = $subtotal * ($gstRate / 100);
        $cgst = ($gstType === 'IGST') ? 0 : $gstAmount / 2;
        $sgst = ($gstType === 'IGST') ? 0 : $gstAmount / 2;
        $igst = ($gstType === 'IGST') ? $gstAmount : 0;
        $totalAmount = $subtotal + $gstAmount;

        $stmt = $pdo->prepare('INSERT INTO sales (product_id, quantity, sale_price, gst_type, gst_rate, cgst, sgst, igst, total_amount, sale_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$productId, $quantity, $salePrice, $gstType, $gstRate, $cgst, $sgst, $igst, $totalAmount, $saleDate]);

        $update = $pdo->prepare('UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?');
        $update->execute([$quantity, $productId]);

        $message = 'Sale recorded successfully.';
    }
}

$products = $pdo->query('SELECT id, name, stock_qty, sale_price FROM products ORDER BY name ASC')->fetchAll();
$sales = $pdo->query('SELECT s.*, p.name AS product_name FROM sales s JOIN products p ON p.id = s.product_id ORDER BY s.sale_date DESC, s.id DESC LIMIT 20')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-warning">
            <div class="card-header"><h3 class="card-title">New Sale</h3></div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Select product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?> (Stock: <?php echo $product['stock_qty']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sale Price</label>
                        <input type="number" name="sale_price" step="0.01" min="0" class="form-control" required>
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
                        <label class="form-label">Sale Date</label>
                        <input type="date" name="sale_date" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                    </div>
                    <button type="submit" name="save_sale" class="btn btn-warning w-100 text-white">Save Sale</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">Sales Ledger</h3></div>
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
                        <?php if (empty($sales)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No sales recorded.</td></tr>
                        <?php else: ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                                    <td><?php echo $sale['quantity']; ?></td>
                                    <td><?php echo formatCurrency($sale['cgst'] + $sale['sgst'] + $sale['igst']); ?></td>
                                    <td><?php echo formatCurrency($sale['total_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
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
