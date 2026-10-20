<?php
require_once __DIR__ . '/config/database.php';

if (!$pdo) {
    echo '<div class="alert alert-warning">Database is not ready. Please run <strong>setup.php</strong> first.</div>';
    exit;
}

$pageTitle = 'Inventory';
$activePage = 'inventory';

$inventory = $pdo->query('SELECT * FROM products ORDER BY stock_qty ASC')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Stock Inventory</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Sale Price</th>
                    <th>Stock Qty</th>
                    <th>Min Stock</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inventory)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No inventory records found.</td></tr>
                <?php else: ?>
                    <?php foreach ($inventory as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['category']); ?></td>
                            <td><?php echo htmlspecialchars($item['unit']); ?></td>
                            <td><?php echo formatCurrency($item['sale_price']); ?></td>
                            <td><?php echo $item['stock_qty']; ?></td>
                            <td><?php echo $item['min_stock']; ?></td>
                            <td>
                                <?php if ((float) $item['stock_qty'] <= (float) $item['min_stock']): ?>
                                    <span class="badge bg-danger">Low</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Healthy</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
