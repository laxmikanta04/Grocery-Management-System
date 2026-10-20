<?php
$host = 'localhost';
$dbname = 'grocery_db';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset={$charset}",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    $pdo = null;
}
