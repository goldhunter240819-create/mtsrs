<?php
// Autoloader sederhana (sama seperti di index.php)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Database;

try {
    $db = Database::connect('core');
    $db->beginTransaction();

    // 1. Hapus semua pembayaran siswa yang nyangkut di tabel transaksi APK
    $db->exec("DELETE FROM keuangan_transaksi WHERE siswa_id IS NOT NULL");
    
    // 2. Masukkan ulang data yang 100% valid dari tabel pembayaran Web
    $stmt = $db->prepare("INSERT INTO keuangan_transaksi (tanggal_bayar, keterangan, jenis, jumlah, siswa_id, guru_id) 
                          SELECT tanggal_bayar, CONCAT('Pembayaran Tagihan: ', jenis_pembayaran), 'Pemasukan', jumlah, siswa_id, COALESCE(petugas_id, 1) 
                          FROM keuangan_komite_pembayaran");
    $stmt->execute();

    $db->commit();
    
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h1 style='color: #16a34a;'>Sinkronisasi Berhasil! ✅</h1>";
    echo "<p>Data BKU APK telah disinkronkan 100% dengan data Web Dashboard.</p>";
    echo "<p style='color: #dc2626; font-weight: bold; padding: 15px; background: #fee2e2; display: inline-block; border-radius: 8px;'>PENTING: Segera hapus file ini (fix_sync.php) dari cPanel/server kamu setelah dijalankan demi keamanan!</p>";
    echo "</div>";
} catch (\Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h1 style='color: #dc2626;'>Terjadi Kesalahan ❌</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
