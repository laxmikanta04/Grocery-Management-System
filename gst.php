<?php
require_once __DIR__ . '/config/database.php';

if (!$pdo) {
    echo '<div class="alert alert-warning">Database is not ready. Please run <strong>setup.php</strong> first.</div>';
    exit;
}

$pageTitle = 'GST Reports';
$activePage = 'gst';

$purchaseSummary = $pdo->query('SELECT COALESCE(SUM(cgst),0) AS total_cgst, COALESCE(SUM(sgst),0) AS total_sgst, COALESCE(SUM(igst),0) AS total_igst, COALESCE(SUM(total_amount),0) AS total_amount FROM purchases')->fetch();
$salesSummary = $pdo->query('SELECT COALESCE(SUM(cgst),0) AS total_cgst, COALESCE(SUM(sgst),0) AS total_sgst, COALESCE(SUM(igst),0) AS total_igst, COALESCE(SUM(total_amount),0) AS total_amount FROM sales')->fetch();

include __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Purchase GST Summary</h3></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between"><span>CGST</span><strong><?php echo formatCurrency($purchaseSummary['total_cgst']); ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>SGST</span><strong><?php echo formatCurrency($purchaseSummary['total_sgst']); ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>IGST</span><strong><?php echo formatCurrency($purchaseSummary['total_igst']); ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Total Amount</span><strong><?php echo formatCurrency($purchaseSummary['total_amount']); ?></strong></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">Sales GST Summary</h3></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between"><span>CGST</span><strong><?php echo formatCurrency($salesSummary['total_cgst']); ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>SGST</span><strong><?php echo formatCurrency($salesSummary['total_sgst']); ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>IGST</span><strong><?php echo formatCurrency($salesSummary['total_igst']); ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Total Amount</span><strong><?php echo formatCurrency($salesSummary['total_amount']); ?></strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
