<?php
require_once __DIR__ . '/config/database.php';

if (!$pdo) {
    echo '<div class="alert alert-warning">Database is not ready. Please run <strong>setup.php</strong> first.</div>';
    exit;
}

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

$stats = getDashboardStats($pdo);
$lowStockProducts = getLowStockProducts($pdo);
$recentSales = $pdo->query("SELECT s.*, p.name AS product_name FROM sales s JOIN products p ON p.id = s.product_id ORDER BY s.sale_date DESC, s.id DESC LIMIT 5")->fetchAll();
$recentPurchases = $pdo->query("SELECT p2.*, pr.name AS product_name FROM purchases p2 JOIN products pr ON pr.id = p2.product_id ORDER BY p2.purchase_date DESC, p2.id DESC LIMIT 5")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?php echo $stats['total_products']; ?></h3>
                <p>Total Products</p>
            </div>
            <div class="icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            <a href="products.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo number_format($stats['total_stock'], 0); ?></h3>
                <p>Total Stock</p>
            </div>
            <div class="icon"><i class="fa-solid fa-warehouse"></i></div>
            <a href="inventory.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo formatCurrency($stats['today_sales']); ?></h3>
                <p>Today Sales</p>
            </div>
            <div class="icon"><i class="fa-solid fa-cash-register"></i></div>
            <a href="sales.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?php echo formatCurrency($stats['today_purchases']); ?></h3>
                <p>Today Purchases</p>
            </div>
            <div class="icon"><i class="fa-solid fa-cart-plus"></i></div>
            <a href="purchases.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Recent Sales</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>GST</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentSales)): ?>
                            <tr><td colspan="4" class="text-center text-muted">No sales found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentSales as $sale): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                                    <td><?php echo $sale['quantity']; ?></td>
                                    <td><?php echo formatCurrency($sale['cgst'] + $sale['sgst'] + $sale['igst']); ?></td>
                                    <td><?php echo formatCurrency($sale['total_amount']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Low Stock Alert</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lowStockProducts)): ?>
                            <tr><td colspan="3" class="text-center text-muted">No low stock items.</td></tr>
                        <?php else: ?>
                            <?php foreach ($lowStockProducts as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo $product['stock_qty']; ?></td>
                                    <td><?php echo $product['min_stock']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Recent Purchases</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>GST</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentPurchases)): ?>
                            <tr><td colspan="4" class="text-center text-muted">No purchases found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentPurchases as $purchase): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($purchase['product_name']); ?></td>
                                    <td><?php echo $purchase['quantity']; ?></td>
                                    <td><?php echo formatCurrency($purchase['cgst'] + $purchase['sgst'] + $purchase['igst']); ?></td>
                                    <td><?php echo formatCurrency($purchase['total_amount']); ?></td>
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
