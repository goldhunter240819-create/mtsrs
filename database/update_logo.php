<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=db_mts_rs;charset=utf8mb4", $user, $pass);
    $pdo->exec("UPDATE institusi SET logo = 'logo_default.png' WHERE logo IS NULL OR logo = ''");
    echo "Logo updated in database successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
