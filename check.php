<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::connect();
$tagihan = $db->query("SELECT * FROM keuangan_tagihan WHERE siswa_id = 681")->fetchAll(PDO::FETCH_ASSOC);
echo "Tagihan:\n";
print_r($tagihan);

$pembayaran = $db->query("SELECT * FROM keuangan_komite_pembayaran WHERE siswa_id = 681")->fetchAll(PDO::FETCH_ASSOC);
echo "Pembayaran:\n";
print_r($pembayaran);
