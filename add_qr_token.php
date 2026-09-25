<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('core');
try {
    $db->query("ALTER TABLE guru ADD COLUMN qr_token VARCHAR(255) NULL");
    echo "Column guru added.\n";
} catch (Exception $e) {
    echo "Error guru: " . $e->getMessage() . "\n";
}
try {
    $db->query("ALTER TABLE siswa ADD COLUMN qr_token VARCHAR(255) NULL");
    echo "Column siswa added.\n";
} catch (Exception $e) {
    echo "Error siswa: " . $e->getMessage() . "\n";
}
