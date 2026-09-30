<?php
require 'app/Core/Database.php';
$db = \App\Core\Database::connect('core');

$tx = $db->query("SELECT id, keterangan FROM keuangan_transaksi WHERE kategori IS NULL OR TRIM(kategori) = ''")->fetchAll();
$updated = 0;
foreach($tx as $t) {
    $ket = $t['keterangan'];
    $kategori = '';
    
    // Parse from "Pembayaran Tagihan: NAMA TAGIHAN"
    if (preg_match('/Pembayaran Tagihan:\s*(.+)/', $ket, $matches)) {
        $kategori = str_replace(' - Auto-Split (APK Kasir)', '', $matches[1]);
        $kategori = str_replace(' - Auto-Split', '', $kategori);
        $kategori = trim($kategori);
    }
    // Parse from "Pembayaran Kas (NAMA TAGIHAN): APK Kasir"
    else if (preg_match('/Pembayaran Kas \((.*?)\): APK Kasir/', $ket, $matches)) {
        $kategori = trim($matches[1]);
    }
    // Parse from "Pembayaran NAMA TAGIHAN - NAMA SISWA" (Web)
    else if (preg_match('/Pembayaran (.*?) - /', $ket, $matches)) {
        $kategori = trim($matches[1]);
    }

    if (!empty($kategori)) {
        $db->prepare("UPDATE keuangan_transaksi SET kategori = ? WHERE id = ?")->execute([$kategori, $t['id']]);
        $updated++;
    }
}
echo "<h1>Mantap! Berhasil update $updated baris data kategori.</h1>";
echo "<p>Silakan hapus file ini demi keamanan.</p>";
