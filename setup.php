<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_database'])) {
    $mysqli = new mysqli('localhost', 'root', '');
    if ($mysqli->connect_error) {
        die('MySQL connection error: ' . $mysqli->connect_error);
    }

    $mysqli->query("CREATE DATABASE IF NOT EXISTS grocery_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $mysqli->select_db('grocery_db');

    $sql = file_get_contents(__DIR__ . '/database/schema.sql');
    $mysqli->multi_query($sql);
    do {
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
    } while ($mysqli->next_result());

    echo '<div class="alert alert-success">Database and tables were created successfully.</div>';
    echo '<p><a href="index.php" class="btn btn-primary">Open Dashboard</a></p>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Grocery Database</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm mx-auto" style="max-width: 700px;">
            <div class="card-body">
                <h3 class="mb-3">Grocery Management System Setup</h3>
                <p>This setup creates the MySQL database and required tables.</p>
                <form method="post">
                    <button type="submit" name="create_database" class="btn btn-primary">Create Database & Tables</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
