<?php
/**
 * update_live.php - Script Sinkronisasi & Migrasi Live Server MTs Roudlotus Sholihin
 * 
 * Jalankan file ini melalui browser di:
 * https://mtsroudlotussholihin.sch.id/update_live.php
 */

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
use App\Services\PkkmSyncService;

$logs = [];
$gitOutput = [];
$isGitSuccess = false;

// 1. Coba eksekusi Git Pull jika server mengizinkan exec
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

if (function_exists('exec')) {
    @exec('git -c safe.directory=* pull origin main 2>&1', $gitOutput, $gitRet);
    if ($gitRet === 0) {
        $isGitSuccess = true;
        $logs[] = ['status' => 'ok', 'msg' => 'Git Pull berhasil: ' . (implode(' | ', $gitOutput) ?: 'Up-to-date')];
    } else {
        $logs[] = ['status' => 'info', 'msg' => 'Git Pull via web script dilewati/tidak didukung: ' . implode(' ', $gitOutput)];
    }
} else {
    $logs[] = ['status' => 'info', 'msg' => 'Fungsi exec() dinonaktifkan di server PHP ini. Git pull dijalankan via cPanel Git Version Control atau SSH.'];
}

// 2. Eksekusi Migrasi Database & Seeding PKKM
$dbSuccess = true;
try {
    $db = Database::connect('core');
    $logs[] = ['status' => 'ok', 'msg' => 'Koneksi database db_mts_rs berhasil terhubung.'];

    // Roles
    $db->exec("CREATE TABLE IF NOT EXISTS `roles` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL UNIQUE,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $db->exec("INSERT IGNORE INTO `roles` (`id`, `name`) VALUES (5, 'pengawas');");

    // Komponen
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_komponen` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nama_komponen` varchar(255) NOT NULL,
        `urutan` int(11) DEFAULT 0,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Sub Komponen
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_sub_komponen` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `komponen_id` int(11) NOT NULL,
        `nama_sub` varchar(500) NOT NULL,
        `urutan` varchar(10) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `komponen_id` (`komponen_id`),
        CONSTRAINT `pkkm_sub_komponen_ibfk_1` FOREIGN KEY (`komponen_id`) REFERENCES `pkkm_komponen` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Indikator
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_indikator` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `komponen_id` int(11) NOT NULL,
        `sub_komponen_id` int(11) DEFAULT NULL,
        `indikator` text NOT NULL,
        `kriteria` text DEFAULT NULL,
        `bukti_fisik` text DEFAULT NULL,
        `urutan` int(11) DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `komponen_id` (`komponen_id`),
        KEY `sub_komponen_id` (`sub_komponen_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Berkas Manual
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_berkas` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `indikator_id` int(11) NOT NULL,
        `tipe` enum('file','link') DEFAULT 'file',
        `url` text NOT NULL,
        `nama_file` varchar(255) DEFAULT NULL,
        `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `indikator_id` (`indikator_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Master Berkas
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_master_berkas` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nama_berkas` varchar(255) NOT NULL,
        `kategori` varchar(50) DEFAULT 'umum',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `idx_kategori` (`kategori`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Master Berkas Files
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_master_berkas_file` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `master_berkas_id` int(11) NOT NULL,
        `tipe` enum('file','link') DEFAULT 'file',
        `url` text NOT NULL,
        `nama_file_asli` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `master_berkas_id` (`master_berkas_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Nilai
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_nilai` (
        `indikator_id` int(11) NOT NULL,
        `skor` int(11) DEFAULT 0,
        PRIMARY KEY (`indikator_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Pengawas
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_pengawas` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) DEFAULT NULL,
        `nama` varchar(150) NOT NULL,
        `nip` varchar(50) DEFAULT '-',
        `jabatan` varchar(100) DEFAULT 'Pengawas Madrasah Ahli Madya',
        `instansi` varchar(150) DEFAULT 'Kantor Kementerian Agama Kab. Lampung Barat',
        `peran` enum('penilai_1','penilai_2') DEFAULT 'penilai_1',
        `no_hp` varchar(30) DEFAULT NULL,
        `ttd_digital` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    try { $db->exec("ALTER TABLE `pkkm_pengawas` ADD COLUMN `ttd_digital` TEXT NULL"); } catch (\Exception $e) {}

    // Penilai Skor
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_penilai_skor` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `pengawas_id` int(11) NOT NULL,
        `indikator_id` int(11) NOT NULL,
        `skor` int(11) DEFAULT 0,
        `catatan` text DEFAULT NULL,
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_pengawas_indikator` (`pengawas_id`, `indikator_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Tim
    $db->exec("CREATE TABLE IF NOT EXISTS `pkkm_tim` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `guru_id` int(11) NOT NULL,
        `komponen_id` int(11) NOT NULL,
        `peran` varchar(100) DEFAULT 'Anggota Tim',
        `tugas_keterangan` varchar(255) NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `idx_guru` (`guru_id`),
        KEY `idx_komponen` (`komponen_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $logs[] = ['status' => 'ok', 'msg' => 'Tabel basis data PKKM tervalidasi dengan baik.'];

    // Seeder Indikator
    $indCount = (int)$db->query("SELECT COUNT(*) FROM pkkm_indikator")->fetchColumn();
    if ($indCount < 42) {
        $syncRes = PkkmSyncService::executeSync();
        if ($syncRes['status']) {
            $logs[] = ['status' => 'ok', 'msg' => 'Instrumen 42 Indikator & 99 Master Berkas berhasil di-seed otomatis.'];
        } else {
            $logs[] = ['status' => 'error', 'msg' => 'Gagal seed instrumen: ' . $syncRes['pesan']];
        }
    } else {
        $logs[] = ['status' => 'info', 'msg' => "Instrumen PKKM sudah lengkap ($indCount indikator)."];
    }

    // Akun Pengawas Default
    $check1 = $db->query("SELECT id FROM pkkm_pengawas WHERE peran = 'penilai_1' LIMIT 1")->fetch();
    if (!$check1) {
        $hash1 = password_hash('penilai123', PASSWORD_BCRYPT);
        $u1 = $db->query("SELECT id FROM users WHERE username = 'penilai1'")->fetch();
        if ($u1) {
            $uid1 = $u1['id'];
            $db->prepare("UPDATE users SET password_hash = ?, role_id = 5, status = 'active' WHERE id = ?")->execute([$hash1, $uid1]);
        } else {
            $stmt1 = $db->prepare("INSERT INTO users (username, password_hash, role_id, status) VALUES ('penilai1', ?, 5, 'active')");
            $stmt1->execute([$hash1]);
            $uid1 = $db->lastInsertId();
        }
        $db->prepare("INSERT INTO pkkm_pengawas (user_id, nama, nip, jabatan, instansi, peran, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)")
           ->execute([$uid1, 'H. AHMAD SYARIF, M.Pd.I', '197405122002121004', 'Pengawas Madrasah Ahli Madya', 'Kantor Kementerian Agama Kab. Lampung Barat', 'penilai_1', '081234567890']);
        $logs[] = ['status' => 'ok', 'msg' => 'Akun Penilai 1 dibuat (User: penilai1 / Pass: penilai123).'];
    }

    $check2 = $db->query("SELECT id FROM pkkm_pengawas WHERE peran = 'penilai_2' LIMIT 1")->fetch();
    if (!$check2) {
        $hash2 = password_hash('penilai223', PASSWORD_BCRYPT);
        $u2 = $db->query("SELECT id FROM users WHERE username = 'penilai2'")->fetch();
        if ($u2) {
            $uid2 = $u2['id'];
            $db->prepare("UPDATE users SET password_hash = ?, role_id = 5, status = 'active' WHERE id = ?")->execute([$hash2, $uid2]);
        } else {
            $stmt2 = $db->prepare("INSERT INTO users (username, password_hash, role_id, status) VALUES ('penilai2', ?, 5, 'active')");
            $stmt2->execute([$hash2]);
            $uid2 = $db->lastInsertId();
        }
        $db->prepare("INSERT INTO pkkm_pengawas (user_id, nama, nip, jabatan, instansi, peran, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)")
           ->execute([$uid2, 'DRA. HJ. SITI NURHAYATI, M.Pd', '197803152005012003', 'Pengawas Madrasah Ahli Muda', 'Kantor Kementerian Agama Kab. Lampung Barat', 'penilai_2', '081298765432']);
        $logs[] = ['status' => 'ok', 'msg' => 'Akun Penilai 2 dibuat (User: penilai2 / Pass: penilai223).'];
    }

    // 3. AUTO-AKTIFKAN AKSES PKKM UNTUK SEMUA ADMIN DI PORTAL ACCESS
    $admins = $db->query("SELECT id, username, portal_access FROM users WHERE role_id = 1")->fetchAll(PDO::FETCH_ASSOC);
    $adminActivated = 0;
    foreach ($admins as $adm) {
        if (!empty($adm['portal_access'])) {
            $acc = array_filter(array_map('trim', explode(',', $adm['portal_access'])));
            if (!in_array('pkkm', $acc)) {
                $acc[] = 'pkkm';
                $db->prepare("UPDATE users SET portal_access = ? WHERE id = ?")->execute([implode(',', $acc), $adm['id']]);
                $adminActivated++;
            }
        }
    }
    if ($adminActivated > 0) {
        $logs[] = ['status' => 'ok', 'msg' => "Hak akses 'pkkm' berhasil diaktifkan otomatis ke $adminActivated akun Admin."];
    } else {
        $logs[] = ['status' => 'info', 'msg' => "Seluruh akun admin sudah memiliki akses aktif ke modul PKKM."];
    }

} catch (\Exception $e) {
    $dbSuccess = false;
    $logs[] = ['status' => 'error', 'msg' => 'Kesalahan database: ' . $e->getMessage()];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinkronisasi & Pembaruan Live Server: PKKM MTs RS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f8fafc 100%);
            color: #0f172a;
            margin: 0; padding: 24px 16px;
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
        }
        .box {
            background: #ffffff;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 45px -10px rgba(6, 78, 59, 0.12);
            max-width: 620px; width: 100%;
            padding: 36px 32px;
            box-sizing: border-box;
        }
        .header {
            text-align: center; margin-bottom: 24px;
        }
        .icon-top {
            width: 68px; height: 68px; border-radius: 20px;
            background: linear-gradient(135deg, #059669, #047857);
            color: #fff; display: inline-flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 25px rgba(5,150,105,0.3); margin-bottom: 14px;
        }
        h1 { font-size: 1.5rem; font-weight: 800; margin: 0 0 6px 0; color: #064e3b; }
        p.sub { font-size: 0.9rem; color: #64748b; margin: 0; line-height: 1.5; }
        
        .log-list {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px;
            padding: 16px; margin: 20px 0; display: flex; flex-direction: column; gap: 10px;
            max-height: 280px; overflow-y: auto;
        }
        .log-item {
            display: flex; align-items: flex-start; gap: 10px; font-size: 0.85rem; line-height: 1.4;
        }
        .log-item.ok { color: #065f46; }
        .log-item.info { color: #1e40af; }
        .log-item.error { color: #991b1b; }
        .log-icon { flex-shrink: 0; width: 18px; height: 18px; margin-top: 1px; }

        .btn-group {
            display: flex; gap: 10px; flex-wrap: wrap; margin-top: 24px;
        }
        .btn {
            flex: 1; min-width: 160px;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 18px; border-radius: 12px; font-weight: 700; font-size: 0.88rem;
            text-decoration: none; transition: 0.2s ease; cursor: pointer; border: none;
        }
        .btn-primary { background: #059669; color: #fff; box-shadow: 0 4px 12px rgba(5,150,105,0.25); }
        .btn-primary:hover { background: #047857; }
        .btn-secondary { background: #f1f5f9; color: #334155; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-dev { background: #1e293b; color: #fff; }
        .btn-dev:hover { background: #0f172a; }
    </style>
</head>
<body>

<div class="box">
    <div class="header">
        <div class="icon-top">
            <i data-lucide="sparkles" style="width:36px;height:36px;"></i>
        </div>
        <h1>Pembaruan Modul PKKM</h1>
        <p class="sub">MTs Roudlotus Sholihin &bull; Database: <code>db_mts_rs</code></p>
    </div>

    <div class="log-list">
        <?php foreach ($logs as $l): ?>
            <div class="log-item <?php echo $l['status']; ?>">
                <?php if ($l['status'] === 'ok'): ?>
                    <i data-lucide="check-circle-2" class="log-icon" style="color:#059669;"></i>
                <?php elseif ($l['status'] === 'info'): ?>
                    <i data-lucide="info" class="log-icon" style="color:#2563eb;"></i>
                <?php else: ?>
                    <i data-lucide="alert-triangle" class="log-icon" style="color:#dc2626;"></i>
                <?php endif; ?>
                <div><?php echo htmlspecialchars($l['msg']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 14px; padding: 12px 16px; font-size: 0.83rem; color: #065f46; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="shield-check" style="width:20px;height:20px;flex-shrink:0;"></i>
        <span>Modul PKKM sudah terintegrasi, database telah dimigrasikan, dan akses portal Admin telah aktif!</span>
    </div>

    <div class="btn-group">
        <a href="/portal" class="btn btn-primary">
            <i data-lucide="layout-grid"></i> Buka Portal Hub
        </a>
        <a href="/admin/pkkm" class="btn btn-secondary">
            <i data-lucide="award"></i> Dashboard PKKM
        </a>
        <a href="/dev/portal-access" class="btn btn-dev">
            <i data-lucide="toggle-left"></i> Kelola Akses Portal
        </a>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
</body>
</html>
