<?php
require_once __DIR__ . '/config/database.php';

if (!$pdo) {
    echo '<div class="alert alert-warning">Database is not ready. Please run <strong>setup.php</strong> first.</div>';
    exit;
}

$pageTitle = 'Products';
$activePage = 'products';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $unit = trim($_POST['unit']);
    $purchasePrice = (float) $_POST['purchase_price'];
    $salePrice = (float) $_POST['sale_price'];
    $gstRate = (float) $_POST['gst_rate'];
    $stockQty = (float) $_POST['stock_qty'];
    $minStock = (float) $_POST['min_stock'];

    $stmt = $pdo->prepare('INSERT INTO products (name, category, unit, purchase_price, sale_price, gst_rate, stock_qty, min_stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $category, $unit, $purchasePrice, $salePrice, $gstRate, $stockQty, $minStock]);
    $message = 'Product added successfully.';
}

$products = $pdo->query('SELECT * FROM products ORDER BY name ASC')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Add Product</h3></div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" class="form-control" placeholder="kg, pcs, box" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purchase Price</label>
                        <input type="number" name="purchase_price" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sale Price</label>
                        <input type="number" name="sale_price" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GST Rate (%)</label>
                        <input type="number" name="gst_rate" step="0.01" min="0" class="form-control" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock_qty" step="0.01" min="0" class="form-control" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimum Stock</label>
                        <input type="number" name="min_stock" step="0.01" min="0" class="form-control" value="0" required>
                    </div>
                    <button type="submit" name="save_product" class="btn btn-primary w-100">Save Product</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">Product List</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Purchase</th>
                            <th>Sale</th>
                            <th>GST</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="7" class="text-center text-muted">No products yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td><?php echo htmlspecialchars($product['unit']); ?></td>
                                    <td><?php echo formatCurrency($product['purchase_price']); ?></td>
                                    <td><?php echo formatCurrency($product['sale_price']); ?></td>
                                    <td><?php echo $product['gst_rate']; ?>%</td>
                                    <td><?php echo $product['stock_qty']; ?></td>
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
