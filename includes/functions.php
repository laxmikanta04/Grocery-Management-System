<?php
function formatCurrency($amount): string
{
    return '₹ ' . number_format((float) $amount, 2);
}

function getLowStockProducts(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM products WHERE stock_qty <= min_stock ORDER BY stock_qty ASC LIMIT 5');
    return $stmt->fetchAll();
}

function getDashboardStats(PDO $pdo): array
{
    $stats = [];

    $stats['total_products'] = (int) $pdo->query('SELECT COUNT(*) AS total FROM products')->fetch()['total'];
    $stats['total_stock'] = (float) $pdo->query('SELECT COALESCE(SUM(stock_qty), 0) AS total FROM products')->fetch()['total'];
    $stats['today_sales'] = (float) $pdo->query("SELECT COALESCE(SUM(total_amount), 0) AS total FROM sales WHERE DATE(sale_date) = CURDATE()")->fetch()['total'];
    $stats['today_purchases'] = (float) $pdo->query("SELECT COALESCE(SUM(total_amount), 0) AS total FROM purchases WHERE DATE(purchase_date) = CURDATE()")->fetch()['total'];
    $stats['monthly_revenue'] = (float) $pdo->query("SELECT COALESCE(SUM(total_amount), 0) AS total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())")->fetch()['total'];

    return $stats;
}
