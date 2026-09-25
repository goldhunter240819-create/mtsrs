<?php
/**
 * Database Initializer & Seeder for MTs RS System
 * Database Name: db_mts_rs
 * School Name: MTs Roudlotus Sholihin (MTs RS)
 */

date_default_timezone_set('Asia/Jakarta');

$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS db_mts_rs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE db_mts_rs");

    // Update Institusi
    $pdo->exec("INSERT INTO institusi (id, nama, singkatan, alamat, desa, kecamatan, kabupaten, provinsi, telepon, email, website, nama_kepala, nip_kepala)
        VALUES (1, 'MTs Roudlotus Sholihin', 'MTs RS', 'Jl. Raden Said No. 12', 'Mulyoagung', 'Singojuru', 'Banyuwangi', 'Jawa Timur', '081234567890', 'info@mtsrs.sch.id', 'https://mtsrs.sch.id', 'Ahmad Rifa\'i, M.Pd.', '198501012010011001')
        ON DUPLICATE KEY UPDATE nama = VALUES(nama), singkatan = VALUES(singkatan)");

    // Update Website Settings
    $pdo->exec("INSERT INTO website_settings (id, hero_title, about_title, kepsek_nama) 
        VALUES (1, 'MTs Roudlotus Sholihin System', 'Pendidikan Islam Modern & Berkarakter', 'Ahmad Rifa\'i, M.Pd.')
        ON DUPLICATE KEY UPDATE hero_title = VALUES(hero_title)");

    echo "School Name updated to 'MTs Roudlotus Sholihin' successfully.\n";

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
