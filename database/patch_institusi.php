<?php
/**
 * Database Patch for Institusi Table in db_mts_rs
 */

date_default_timezone_set('Asia/Jakarta');

$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=db_mts_rs;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $columns = [
        "npsn VARCHAR(50) DEFAULT '20580001'",
        "nsm VARCHAR(50) DEFAULT '121235100001'",
        "kota VARCHAR(100) DEFAULT 'Banyuwangi'",
        "kodepos VARCHAR(20) DEFAULT '68461'",
        "logo VARCHAR(255) NULL",
        "website VARCHAR(255) DEFAULT 'https://mtsrs.sch.id'",
        "tahun_ajaran_id INT DEFAULT 1",
        "semester VARCHAR(20) DEFAULT 'Ganjil'"
    ];

    foreach ($columns as $col) {
        $colName = explode(' ', $col)[0];
        try {
            $pdo->exec("ALTER TABLE institusi ADD COLUMN $col");
        } catch (\PDOException $e) {
            // Column already exists
        }
    }

    echo "Institusi Table Patched Cleanly!\n";

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
