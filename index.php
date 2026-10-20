<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

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
