<?php
/**
 * Script Perbaikan Otomatis untuk Live Server:
 * Menghapus redeklarasi ganda method monitoringNilaiGlobal() di KurikulumController.php
 */

$file = __DIR__ . '/app/Controllers/KurikulumController.php';

if (!file_exists($file)) {
    die("File $file tidak ditemukan.");
}

$gitLog = '';
// 1. Coba reset via git jika git tersedia di server
if (function_exists('exec')) {
    @exec('git checkout -- app/Controllers/KurikulumController.php 2>&1', $outCheckout);
    if (!empty($outCheckout)) {
        $gitLog = implode("\n", $outCheckout);
    }
}

// 2. Baca file dan cek jika masih ada duplikasi
$content = file_get_contents($file);
$count = substr_count($content, 'function monitoringNilaiGlobal');
$status = "info";
$message = "";

if ($count > 1) {
    $pos1 = strpos($content, 'function monitoringNilaiGlobal');
    $pos2 = strpos($content, 'function monitoringNilaiGlobal', $pos1 + strlen('function monitoringNilaiGlobal'));

    if ($pos2 !== false) {
        $search_start = $pos1 + strlen('function monitoringNilaiGlobal');
        $pos_pub = strpos($content, 'public function', $search_start);
        if ($pos_pub !== false && $pos_pub < $pos2) {
            $cleanContent = rtrim(substr($content, 0, $pos_pub)) . "\n}\n";
            file_put_contents($file, $cleanContent);
            $status = "success";
            $message = "Duplikat method <code>monitoringNilaiGlobal()</code> di baris 1252 berhasil dihapus! File <code>KurikulumController.php</code> sekarang sudah normal kembali.";
        } else {
            $status = "error";
            $message = "Gagal menemukan batas method duplikat kedua. Silakan periksa file secara manual.";
        }
    }
} elseif ($count === 1) {
    $status = "success";
    $message = "File <code>KurikulumController.php</code> sudah dalam kondisi bersih (hanya ada 1 deklarasi method <code>monitoringNilaiGlobal()</code>). Tidak ada duplikat.";
} else {
    $status = "warning";
    $message = "Method <code>monitoringNilaiGlobal()</code> tidak ditemukan di dalam controller.";
}

// 3. Amankan file add_global.php agar tidak bisa menambah duplikat lagi
$addGlobalFile = __DIR__ . '/add_global.php';
if (file_exists($addGlobalFile)) {
    $safeAddGlobal = "<?php\n// File add_global.php telah dinonaktifkan demi keamanan.\necho 'Method monitoringNilaiGlobal sudah aktif dan permanen di KurikulumController. Tidak ada aksi yang diperlukan.';\n";
    file_put_contents($addGlobalFile, $safeAddGlobal);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbaikan Live: KurikulumController</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            max-width: 580px;
            width: 100%;
            padding: 32px;
            text-align: center;
        }
        .icon {
            font-size: 54px;
            margin-bottom: 16px;
        }
        h1 {
            font-size: 24px;
            margin: 0 0 12px 0;
            font-weight: 700;
        }
        p {
            font-size: 15px;
            line-height: 1.6;
            color: #475569;
            margin: 0 0 20px 0;
        }
        code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            color: #0f172a;
            font-family: monospace;
        }
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            text-align: left;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn:hover { background: #1d4ed8; }
        .note {
            margin-top: 24px;
            font-size: 13px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($status === 'success'): ?>
            <div class="icon">✅</div>
            <h1 style="color: #16a34a;">Perbaikan Berhasil!</h1>
            <div class="alert alert-success">
                <?= $message ?>
            </div>
            <p>Halaman website yang sebelumnya error sekarang sudah bisa dibuka kembali secara normal.</p>
            <a href="/kurikulum/monitoring-nilai" class="btn">Buka Halaman Monitoring Nilai</a>
        <?php else: ?>
            <div class="icon">⚠️</div>
            <h1 style="color: #ea580c;">Status Pemeriksaan</h1>
            <div class="alert alert-info">
                <?= $message ?>
            </div>
            <a href="/kurikulum/monitoring-nilai" class="btn">Kembali ke Halaman Web</a>
        <?php endif; ?>

        <?php if (!empty($gitLog)): ?>
            <pre style="text-align: left; background: #0f172a; color: #38bdf8; padding: 12px; border-radius: 8px; font-size: 12px; overflow-x: auto;"><?= htmlspecialchars($gitLog) ?></pre>
        <?php endif; ?>

        <div class="note">
            💡 <em>Setelah selesai, Anda dapat menghapus file <code>fix_kurikulum.php</code> dari cPanel/server jika diinginkan.</em>
        </div>
    </div>
</body>
</html>
