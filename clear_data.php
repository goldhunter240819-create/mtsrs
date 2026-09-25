<?php
require 'app/Core/Database.php';

$db = \App\Core\Database::connect('core');

$tables_to_truncate = [
    'keuangan_bos_transaksi',
    'keuangan_komite_pembayaran',
    'keuangan_komite_transaksi',
    'keuangan_tabungan',
    'keuangan_tabungan_mutasi',
    'keuangan_tagihan',
    'keuangan_transaksi',
    'keuangan_gaji' // Gaji tiap bulan
];

foreach ($tables_to_truncate as $table) {
    try {
        $db->query("TRUNCATE TABLE $table");
        echo "Truncated $table\n";
    } catch (Exception $e) {
        echo "Error truncating $table: " . $e->getMessage() . "\n";
    }
}

// Optionally, truncate master tables if they want a completely fresh start
// 'keuangan_komite_jenis', 'keuangan_komite_kategori', 'keuangan_komponen'
// I will skip them for now so they don't have to re-enter all master rules.

echo "Selesai!";
