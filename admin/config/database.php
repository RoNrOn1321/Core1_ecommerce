<?php
// Database configuration
$db_config = [
    'host' => 'localhost',
    'dbname' => 'core1_ecommerce', // Change this to your database name
    'username' => 'root', // Change if different
    'password' => '', // Change if you have a password
    'charset' => 'utf8mb4'
];

try {
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset={$db_config['charset']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Helper function to get database connection
function getDB() {
    global $pdo;
    return $pdo;
}
?>