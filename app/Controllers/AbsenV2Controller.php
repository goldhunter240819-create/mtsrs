<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Branding;

class AbsenV2Controller {
    
    public static function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $roleId = $_SESSION['role_id'];
        $isSuperAdmin = in_array($roleId, [1, 99]);
        $isKamad = !empty($_SESSION['is_kamad']);

        $db_core = Database::connect();
        $db_siakad = Database::connect();

        $nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Administrator';
        $initial = strtoupper(substr($nama, 0, 2));
        $title = "Dashboard Absensi V2";
        
        // Auto-create tabel absensi_guru jika belum ada
        $db_siakad->exec("CREATE TABLE IF NOT EXISTS absensi_guru (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tanggal DATE NOT NULL,
            guru_id INT NOT NULL,
            status ENUM('Hadir','Terlambat','Sakit','Izin','Alpa') DEFAULT 'Hadir',
            jam_masuk TIME NULL,
            jam_pulang TIME NULL,
            keterangan VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tanggal_guru (tanggal, guru_id)
        )");

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        // -- Stats Siswa --
        $sql_siswa = "SELECT COUNT(DISTINCT s.id) FROM siswa s LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name') WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
        $totalSiswa = $db_core->query($sql_siswa)->fetchColumn();
        
        $today = date('Y-m-d');
        
        // -- AUTO ALPA LOGIC --
        // Hanya berjalan di atas jam 14:00 dan di hari kerja (Sabtu-Kamis, tidak jalan di hari Jumat dimana Jumat = 5)
        $nowHour = (int)date('H');
        $hari_ini = (int)date('N');
        if ($nowHour >= 14 && $hari_ini != 5) {
            $cek_auto = $db_core->query("SELECT nilai FROM absensi_pengaturan WHERE kunci = 'last_auto_alpa'")->fetchColumn();
            if ($cek_auto !== $today) {
                // Eksekusi Auto Alpa
                // Ambil daftar siswa aktif
                $sql_get_siswa = "SELECT DISTINCT s.id FROM siswa s LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name') WHERE s.status = 'Aktif' AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
                $list_siswa_aktif = $db_core->query($sql_get_siswa)->fetchAll(\PDO::FETCH_COLUMN);
                
                if (!empty($list_siswa_aktif)) {
                    // Ambil yang sudah absen hari ini
                    $sudah_absen = $db_siakad->query("SELECT siswa_id FROM absensi_siswa WHERE tanggal = '$today'")->fetchAll(\PDO::FETCH_COLUMN);
                    
                    // Cari selisihnya (Siswa yang belum absen)
                    $belum_absen = array_diff($list_siswa_aktif, $sudah_absen);
                    
                    if (!empty($belum_absen)) {
                        $insert_values = [];
                        foreach ($belum_absen as $sid) {
                            $insert_values[] = "('$today', $sid, 'Alpa', 'Otomatis by System')";
                        }
                        
                        $sql_insert = "INSERT INTO absensi_siswa (tanggal, siswa_id, status, keterangan) VALUES " . implode(',', $insert_values);
                        $db_siakad->exec($sql_insert);
                    }
                }
                
                // Simpan flag sukses
                $db_core->exec("INSERT INTO absensi_pengaturan (kunci, nilai) VALUES ('last_auto_alpa', '$today') ON DUPLICATE KEY UPDATE nilai = '$today'");
            }
        }
        
        $absenStats = $db_siakad->query("SELECT status, COUNT(*) as count FROM absensi_siswa WHERE tanggal = '$today' GROUP BY status")->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        $hadir = isset($absenStats['Hadir']) ? $absenStats['Hadir'] : 0;
        $sakit = isset($absenStats['Sakit']) ? $absenStats['Sakit'] : 0;
        $izin  = isset($absenStats['Izin'])  ? $absenStats['Izin']  : 0;
        $alpa  = isset($absenStats['Alpa'])  ? $absenStats['Alpa']  : 0;
        $bolos = isset($absenStats['Bolos']) ? $absenStats['Bolos'] : 0;
        $totalAbsen = $hadir + $sakit + $izin + $alpa + $bolos;

        // -- Stats Guru --
        $totalGuru = $db_core->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        
        $guruStats = $db_siakad->query("SELECT status, COUNT(*) as count FROM absensi_guru WHERE tanggal = '$today' GROUP BY status")->fetchAll(\PDO::FETCH_KEY_PAIR);
        $hadirGuru      = ($guruStats['Hadir'] ?? 0) + ($guruStats['Terlambat'] ?? 0);
        $sakitGuru      = $guruStats['Sakit'] ?? 0;
        $izinGuru       = $guruStats['Izin'] ?? 0;
        $alpaGuru       = $guruStats['Alpa'] ?? 0;
        $bolosGuru      = $guruStats['Bolos'] ?? 0;
        $tidakHadirGuru = $sakitGuru + $izinGuru + $alpaGuru + $bolosGuru;
        
        // Log aktivitas terbaru (scan terakhir)
        $activities = [];
        $lastScansGuru = $db_siakad->query("
            SELECT ag.*, g.nama, ag.jam_masuk, ag.jam_pulang
            FROM absensi_guru ag
            JOIN guru g ON ag.guru_id = g.id
            WHERE ag.tanggal = '$today'
            ORDER BY ag.created_at DESC
            LIMIT 3
        ")->fetchAll();
        foreach ($lastScansGuru as $ls) {
            $activities[] = [
                'icon' => 'user-check',
                'color' => '#3b82f6',
                'text' => $ls['nama'] . ' (Guru)',
                'sub'  => 'Hadir pukul ' . ($ls['jam_masuk'] ? substr($ls['jam_masuk'], 0, 5) : '-'),
                'time' => $ls['created_at']
            ];
        }
        $lastScansSiswa = $db_siakad->query("
            SELECT ab.*, s.nama
            FROM absensi_siswa ab
            JOIN siswa s ON ab.siswa_id = s.id
            WHERE ab.tanggal = '$today'
            ORDER BY ab.created_at DESC
            LIMIT 3
        ")->fetchAll();
        foreach ($lastScansSiswa as $ls) {
            $activities[] = [
                'icon' => 'check-circle',
                'color' => '#10b981',
                'text' => $ls['nama'] . ' (Siswa)',
                'sub'  => 'Status: ' . $ls['status'],
                'time' => $ls['created_at']
            ];
        }
        if (empty($activities)) {
            $activities[] = ['icon' => 'check-circle', 'color' => '#10b981', 'text' => 'Sistem Absensi Aktif', 'sub' => 'V2 Engine berjalan dengan baik', 'time' => date('Y-m-d H:i:s')];
        }

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    public static function scanner() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $title = "Scanner Absensi Siswa";
        include __DIR__ . '/../../resources/views/absen_v2/scanner.php';
    }

    public static function scannerGuru() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $title = "Scanner Absensi Guru Khusus";
        include __DIR__ . '/../../resources/views/absen_v2/scanner_guru.php';
    }

    /**
     * Proses scan QR — mendukung siswa DAN guru.
     * QR berisi qr_token dari tabel users.
     * Guru: catat jam_masuk (scan 1) atau jam_pulang (scan 2).
     * Siswa: cukup catat hadir tanpa jam.
     */
    public static function processScan() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please login.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $raw_qr_text = isset($input['nis']) ? trim($input['nis']) : '';
        $mode = isset($input['mode']) ? $input['mode'] : 'siswa'; // default siswa if not sent

        // Akses diatur secara global lewat AuthMiddleware dan portal_access
        
        // Log untuk debugging
        file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Mode: $mode | Raw Input: " . $raw_qr_text . "\n", FILE_APPEND);

        if (empty($raw_qr_text)) {
            echo json_encode(['success' => false, 'message' => 'QR Code kosong atau tidak terbaca.']);
            exit;
        }

        try {
            $db_core   = Database::connect();
            $db_siakad = Database::connect();
            
            // Auto-create tabel
            $db_siakad->exec("CREATE TABLE IF NOT EXISTS absensi_guru (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tanggal DATE NOT NULL,
                guru_id INT NOT NULL,
                status ENUM('Hadir','Terlambat','Sakit','Izin','Alpa') DEFAULT 'Hadir',
                jam_masuk TIME NULL,
                jam_pulang TIME NULL,
                keterangan VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_tanggal_guru (tanggal, guru_id)
            )");

            $today  = date('Y-m-d');
            $jamNow = date('H:i:s');
            
            // ─── LANGKAH 1: Cari user berdasarkan qr_token (format baru UUID) ───
            $user = null;
            $stmt_u = $db_core->prepare("SELECT id, role_id FROM users WHERE qr_token = ?");
            $stmt_u->execute([$raw_qr_text]);
            $user = $stmt_u->fetch(\PDO::FETCH_ASSOC);

            // ─── LANGKAH 2: Fallback — format lama (NIS/USERNAME di teks) ───
            $nis = $raw_qr_text;
            $username_extracted = '';
            if (!$user) {
                if (preg_match('/NIS(?:\/NISN)?:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $m)) {
                    $nis = trim($m[1]);
                }
                if (preg_match('/USERNAME:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $m)) {
                    $username_extracted = trim($m[1]);
                }
                // Cari user via username
                if (!empty($username_extracted)) {
                    $stmt_uu = $db_core->prepare("SELECT id, role_id FROM users WHERE username = ?");
                    $stmt_uu->execute([$username_extracted]);
                    $user = $stmt_uu->fetch(\PDO::FETCH_ASSOC);
                }
            }

            // ─── LANGKAH 3: Cari siswa atau guru ───
            // A. Cari SISWA
            $siswa = null;
            if ($user) {
                $stmt_s = $db_core->prepare("SELECT id, nis, nama, kelas_id FROM siswa WHERE user_id = ?");
                $stmt_s->execute([$user['id']]);
                $siswa = $stmt_s->fetch(\PDO::FETCH_ASSOC);
            }
            if (!$siswa && !empty($nis)) {
                $stmt_s = $db_core->prepare("SELECT id, nis, nama, kelas_id FROM siswa WHERE nis = ?");
                $stmt_s->execute([$nis]);
                $siswa = $stmt_s->fetch(\PDO::FETCH_ASSOC);
            }

            // B. Cari GURU (jika siswa tidak ketemu)
            $guru = null;
            if (!$siswa && $user) {
                $stmt_g = $db_core->prepare("SELECT id, nama, jabatan, jenis_kelamin FROM guru WHERE user_id = ?");
                $stmt_g->execute([$user['id']]);
                $guru = $stmt_g->fetch(\PDO::FETCH_ASSOC);
            }
            // Fallback: cari guru by NIP (format lama)
            if (!$siswa && !$guru && !empty($nis)) {
                $stmt_g = $db_core->prepare("SELECT id, nama, jabatan, jenis_kelamin FROM guru WHERE nip = ? OR nik = ?");
                $stmt_g->execute([$nis, $nis]);
                $guru = $stmt_g->fetch(\PDO::FETCH_ASSOC);
            }

            // ─── Tidak ditemukan sama sekali ───
            if (!$siswa && !$guru) {
                $msg = 'Data tidak ditemukan. QR tidak dikenali sebagai siswa maupun guru.';
                file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Failed: $msg\n", FILE_APPEND);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }

            // ─── Validasi Mode Scanner ───
            if ($mode === 'siswa' && $guru && !$siswa) {
                echo json_encode(['success' => false, 'message' => 'Salah kamar! Mohon gunakan Scanner Khusus Guru.']);
                exit;
            }
            if ($mode === 'guru' && $siswa && !$guru) {
                echo json_encode(['success' => false, 'message' => 'Salah kamar! Mohon gunakan Scanner Khusus Siswa.']);
                exit;
            }

            // ══════════════════════════════════════════
            // PROSES SISWA (tanpa jam)
            // ══════════════════════════════════════════
            if ($siswa) {
                $nama_kelas = '-';
                if ($siswa['kelas_id']) {
                    $stmtK = $db_core->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
                    $stmtK->execute([$siswa['kelas_id']]);
                    $kI = $stmtK->fetch();
                    if ($kI) $nama_kelas = $kI['nama_kelas'];
                }

                $stmtCek = $db_siakad->prepare("SELECT id, status FROM absensi_siswa WHERE tanggal = ? AND siswa_id = ?");
                $stmtCek->execute([$today, $siswa['id']]);
                $absenAda = $stmtCek->fetch();

                // Ambil pengaturan jam terlambat global
                $settings_siswa = ['jam_terlambat' => '07:15'];
                try {
                    $res = $db_core->query("SELECT * FROM absensi_pengaturan")->fetchAll();
                    foreach ($res as $row) { $settings_siswa[$row['kunci']] = $row['nilai']; }
                } catch (\Exception $e) {}

                $batas_terlambat_siswa = $settings_siswa['jam_terlambat'] . ':00';
                if (strlen($batas_terlambat_siswa) == 5) $batas_terlambat_siswa .= ':00';
                
                $status_scan = 'Hadir';
                if ($jamNow > substr($batas_terlambat_siswa, 0, 5) . ':00') {
                    $status_scan = 'Terlambat';
                }

                if ($absenAda) {
                    if ($absenAda['status'] == 'Hadir' || $absenAda['status'] == 'Terlambat') {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Siswa sudah absen hari ini.',
                            'type'    => 'siswa',
                            'data'    => ['nama' => $siswa['nama'], 'kelas' => $nama_kelas, 'status' => 'Sudah Absen']
                        ]);
                        exit;
                    } else {
                        $db_siakad->prepare("UPDATE absensi_siswa SET status = ?, jam_masuk = ? WHERE id = ?")->execute([$status_scan, $jamNow, $absenAda['id']]);
                    }
                } else {
                    $db_siakad->prepare("INSERT INTO absensi_siswa (tanggal, siswa_id, kelas_id, status, jam_masuk, created_at) VALUES (?, ?, ?, ?, ?, NOW())")
                              ->execute([$today, $siswa['id'], $siswa['kelas_id'] ?: 0, $status_scan, $jamNow]);
                }

                // Notifikasi OneSignal siswa
                require_once __DIR__ . '/../Services/OneSignalService.php';
                $jam = date('H:i');
                \App\Services\OneSignalService::sendNotification("Absensi Kehadiran", "Ananda " . $siswa['nama'] . " telah " . $status_scan . " di madrasah pada pukul " . $jam, null, "siswa_" . $siswa['id']);

                $responseStatus = $status_scan === 'Terlambat' ? '⚠️ Terlambat' : '✅ Hadir';
                file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Siswa OK: " . $siswa['nama'] . "\n", FILE_APPEND);
                echo json_encode([
                    'success' => true,
                    'message' => 'Berhasil! Siswa tercatat ' . strtolower($status_scan) . '.',
                    'type'    => 'siswa',
                    'data'    => ['nama' => $siswa['nama'], 'kelas' => $nama_kelas, 'status' => $responseStatus]
                ]);
                exit;
            }

            // ══════════════════════════════════════════
            // PROSES GURU (hanya jam masuk, mendukung custom)
            // ══════════════════════════════════════════
            if ($guru) {
                $prefix_jk = '';
                if (isset($guru['jenis_kelamin'])) {
                    if ($guru['jenis_kelamin'] === 'L') $prefix_jk = 'Bapak ';
                    elseif ($guru['jenis_kelamin'] === 'P') $prefix_jk = 'Ibu ';
                }
                $nama_tampil = $prefix_jk . $guru['nama'];

                // Ambil pengaturan jam terlambat global
                $settings = ['jam_terlambat' => '07:15'];
                try {
                    $res = $db_core->query("SELECT * FROM absensi_pengaturan")->fetchAll();
                    foreach ($res as $row) { $settings[$row['kunci']] = $row['nilai']; }
                } catch (\Exception $e) {}

                // Tentukan jam terlambat (prioritas custom, lalu global)
                $batas_terlambat = !empty($guru['custom_batas_terlambat']) ? $guru['custom_batas_terlambat'] : $settings['jam_terlambat'] . ':00';
                if (strlen($batas_terlambat) == 5) $batas_terlambat .= ':00';

                $stmtCekG = $db_siakad->prepare("SELECT id, status, jam_masuk, jam_pulang FROM absensi_guru WHERE tanggal = ? AND guru_id = ?");
                $stmtCekG->execute([$today, $guru['id']]);
                $absenGuru = $stmtCekG->fetch();

                if (!$absenGuru) {
                    // Scan pertama = jam masuk
                    $status = 'Hadir';
                    if ($jamNow > substr($batas_terlambat, 0, 5) . ':00') {
                        $status = 'Terlambat';
                    }
                    $db_siakad->prepare("INSERT INTO absensi_guru (tanggal, guru_id, status, jam_masuk, created_at) VALUES (?, ?, ?, ?, NOW())")
                              ->execute([$today, $guru['id'], $status, $jamNow]);

                    require_once __DIR__ . '/../Services/OneSignalService.php';
                    \App\Services\OneSignalService::sendNotification("Absen Masuk", "Bapak/Ibu " . $guru['nama'] . ", absen masuk Anda berhasil tercatat pada pukul " . substr($jamNow, 0, 5), null, "guru_" . $guru['id']);

                    $responseStatus = $status === 'Terlambat' ? '⚠️ Terlambat — ' . substr($jamNow, 0, 5) : '✅ Masuk — ' . substr($jamNow, 0, 5);
                    file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Guru MASUK OK: " . $guru['nama'] . "\n", FILE_APPEND);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Berhasil! ' . ($status === 'Terlambat' ? 'Guru tercatat terlambat.' : 'Guru tercatat hadir.'),
                        'type'    => 'guru',
                        'data'    => [
                            'nama'   => $nama_tampil,
                            'kelas'  => $guru['jabatan'] ?: 'Guru',
                            'status' => $responseStatus,
                            'jam'    => substr($jamNow, 0, 5)
                        ]
                    ]);
                    exit;
                } else {
                    // Sudah scan masuk (pulang ditiadakan)
                    echo json_encode([
                        'success' => false,
                        'message' => 'Anda sudah absen masuk hari ini.',
                        'type'    => 'guru',
                        'data'    => [
                            'nama'   => $nama_tampil,
                            'kelas'  => $guru['jabatan'] ?: 'Guru',
                            'status' => 'Sudah Absen'
                        ]
                    ]);
                    exit;
                }
            }

        } catch (\PDOException $e) {
            $msg = 'Database Error: ' . $e->getMessage();
            file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Exception: $msg\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => $msg]);
        }
    }

    // ─────────────────────────────────────────────────────────
    // REKAP HARIAN SISWA
    // ─────────────────────────────────────────────────────────
    public static function rekapHarian() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $tanggal  = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        // Auto-create tabel absensi_permapel jika belum ada
        $db_siakad->exec("CREATE TABLE IF NOT EXISTS absensi_permapel (
            id INT AUTO_INCREMENT PRIMARY KEY,
            jurnal_id INT NOT NULL,
            guru_id INT,
            siswa_id INT NOT NULL,
            kelas_id INT,
            mapel_id INT,
            tanggal DATE,
            status VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_jurnal_siswa (jurnal_id, siswa_id)
        )");

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

        $sql = "SELECT a.*, s.nama, s.nis, COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas, COALESCE(rks.kelas_id, s.kelas_id) as actual_kelas_id 
                FROM absensi_siswa a 
                JOIN siswa s ON a.siswa_id = s.id 
                LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                    AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
                LEFT JOIN kelas k ON s.kelas_id = k.id 
                LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id
                WHERE a.tanggal = :tanggal 
                AND s.status IN ('Aktif', 'Alumni') 
                AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
        
        $params = [':tanggal' => $tanggal];
        if (!empty($kelas_id)) {
            $sql .= " AND COALESCE(rks.kelas_id, s.kelas_id) = :kelas_id";
            $params[':kelas_id'] = $kelas_id;
        }
        $sql .= " ORDER BY nama_kelas ASC, s.nama ASC";

        $stmt = $db_siakad->prepare($sql);
        $stmt->execute($params);
        $absensi = $stmt->fetchAll();
        
        // ─── HITUNG MONITORING KELAS ───
        $stmt_all_siswa = $db_core->prepare("
            SELECT s.id, s.nama, COALESCE(rks.kelas_id, s.kelas_id) as kelas_id
            FROM siswa s 
            LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
            WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')
        ");
        $stmt_all_siswa->execute();
        $semuaSiswa = $stmt_all_siswa->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_all_absen = $db_siakad->prepare("SELECT siswa_id, status FROM absensi_siswa WHERE tanggal = :tgl");
        $stmt_all_absen->execute([':tgl' => $tanggal]);
        $allAbsen = $stmt_all_absen->fetchAll(\PDO::FETCH_KEY_PAIR);

        $monitoring_kelas = [];
        foreach ($kelasList as $k) {
            $monitoring_kelas[$k['id']] = [
                'nama_kelas' => $k['nama_kelas'],
                'Hadir' => 0, 'SakitIzin' => 0, 'Alpa' => 0, 'Bolos' => 0, 'Belum' => 0,
                'list_Hadir' => [], 'list_SakitIzin' => [], 'list_Alpa' => [], 'list_Bolos' => [], 'list_Belum' => []
            ];
        }

        foreach ($semuaSiswa as $sw) {
            $kid = $sw['kelas_id'];
            if (!isset($monitoring_kelas[$kid])) continue;
            
            $sid = $sw['id'];
            if (isset($allAbsen[$sid])) {
                $st = $allAbsen[$sid];
                if ($st === 'Hadir' || $st === 'Terlambat') {
                    $monitoring_kelas[$kid]['Hadir']++;
                    $monitoring_kelas[$kid]['list_Hadir'][] = $sw['nama'];
                } else if ($st === 'Sakit' || $st === 'Izin') {
                    $monitoring_kelas[$kid]['SakitIzin']++;
                    $monitoring_kelas[$kid]['list_SakitIzin'][] = $sw['nama'] . " ($st)";
                } else if ($st === 'Alpa' || $st === 'Alpha') {
                    $monitoring_kelas[$kid]['Alpa']++;
                    $monitoring_kelas[$kid]['list_Alpa'][] = $sw['nama'];
                } else if ($st === 'Bolos') {
                    $monitoring_kelas[$kid]['Bolos']++;
                    $monitoring_kelas[$kid]['list_Bolos'][] = $sw['nama'];
                } else {
                    $monitoring_kelas[$kid]['Hadir']++;
                    $monitoring_kelas[$kid]['list_Hadir'][] = $sw['nama'];
                }
            } else {
                $monitoring_kelas[$kid]['Belum']++;
                $monitoring_kelas[$kid]['list_Belum'][] = $sw['nama'];
            }
        }
        // ────────────────────────────────

        $title   = "Rekap Harian Absensi Siswa";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/rekap_harian.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    public static function updateStatusSiswa() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';

        if (empty($id) || empty($status)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
            exit;
        }

        try {
            $db_siakad = Database::connect();
            
            // Ambil data absensi_siswa untuk sinkronisasi
            $stmt_get = $db_siakad->prepare("SELECT siswa_id, tanggal FROM absensi_siswa WHERE id = ?");
            $stmt_get->execute([$id]);
            $absen = $stmt_get->fetch(\PDO::FETCH_ASSOC);

            if ($absen) {
                // Update absensi_siswa (rekap harian dan dashboard siswa)
                $stmt = $db_siakad->prepare("UPDATE absensi_siswa SET status = ? WHERE id = ?");
                $stmt->execute([$status, $id]);

                // Sinkronkan juga ke absensi_permapel agar monitoring wali kelas / mapel ikut update
                $stmt_sync = $db_siakad->prepare("UPDATE absensi_permapel SET status = ? WHERE siswa_id = ? AND tanggal = ?");
                $stmt_sync->execute([$status, $absen['siswa_id'], $absen['tanggal']]);
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Status absensi berhasil diperbarui dan disinkronkan.']);
        } catch (\PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────
    // CETAK ABSEN MANUAL
    // ─────────────────────────────────────────────────────────
    public static function cetakManual() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
        $bulan_name = date('F Y');

        $db_core = Database::connect();
        $db_siakad = Database::connect();
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        // Get instansi info for Kop Surat
        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();

        // Get class list for filter
        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

        // Get class info if selected
        $nama_kelas = '';
        $nama_wali_kelas = "...................................................";
        if (!empty($kelas_id)) {
            $stmt_kelas = $db_core->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
            $stmt_kelas->execute([$kelas_id]);
            $kelas = $stmt_kelas->fetch();
            $nama_kelas = $kelas ? $kelas['nama_kelas'] : 'Tidak Diketahui';

            if ($nama_kelas !== 'Tidak Diketahui') {
                $nama_k_prefix = "Kelas " . $nama_kelas;
                $stmt_wk = $db_core->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND (gt.keterangan = ? OR gt.keterangan = ?) AND gt.tahun_ajaran = ? LIMIT 1");
                $stmt_wk->execute([$nama_kelas, $nama_k_prefix, $active_year_name]);
                $wk = $stmt_wk->fetchColumn();
                if ($wk) {
                    $nama_wali_kelas = $wk;
                }
            }
        }

        // Get students
        $siswaList = [];
        if (!empty($kelas_id)) {
            $sqlSiswa = "SELECT s.id, s.nama, s.nis, s.jenis_kelamin as jk 
                         FROM siswa s 
                         LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                             AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
                         WHERE s.status IN ('Aktif', 'Alumni') 
                         AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')
                         AND COALESCE(rks.kelas_id, s.kelas_id) = ?
                         ORDER BY s.nama ASC";
            $stmt = $db_core->prepare($sqlSiswa);
            $stmt->execute([$kelas_id]);
            $siswaList = $stmt->fetchAll();
        }

        $format = isset($_GET['format']) ? $_GET['format'] : 'kosongan';
        $bulan_filter = isset($_GET['bulan']) ? $_GET['bulan'] : '';
        $tahun_filter = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $tahun = $tahun_filter;

        // Ambil data hari libur
        $holidays = [];
        $stmtLibur = $db_core->prepare("SELECT tanggal_mulai, tanggal_selesai, kegiatan FROM kalender_pendidikan WHERE kategori = 'Libur'");
        $stmtLibur->execute();
        while ($lbr = $stmtLibur->fetch(\PDO::FETCH_ASSOC)) {
            $start = strtotime($lbr['tanggal_mulai']);
            $end = strtotime($lbr['tanggal_selesai']);
            for ($d = $start; $d <= $end; $d += 86400) {
                $holidays[date('Y-m-d', $d)] = $lbr['kegiatan'];
            }
        }

        $absenData = [];
        if ($format === 'berisi_data' && !empty($kelas_id) && !empty($siswaList)) {
            $siswaIds = array_column($siswaList, 'id');
            $placeholders = implode(',', array_fill(0, count($siswaIds), '?'));
            $sqlAbsen = "SELECT siswa_id, MONTH(tanggal) as bln, DAY(tanggal) as tgl, status 
                         FROM absensi_siswa 
                         WHERE YEAR(tanggal) = ? AND siswa_id IN ($placeholders)";
            $params = array_merge([$tahun], $siswaIds);
            
            $stmtAbsen = $db_siakad->prepare($sqlAbsen);
            $stmtAbsen->execute($params);
            while ($row = $stmtAbsen->fetch()) {
                $absenData[$row['siswa_id']][$row['bln']][$row['tgl']] = $row['status'];
            }
        }

        // Langsung tampilkan view cetak (tanpa layout siakad agar bersih)
        include __DIR__ . '/../../resources/views/absen_v2/cetak_manual.php';
    }

    // ─────────────────────────────────────────────────────────
    // REKAP DETAIL (TIMELINE)
    // ─────────────────────────────────────────────────────────
    public static function rekapDetail() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

        $timelineData = [];
        $mapelColumns = [];
        $selectedKelas = '';

        if (!empty($kelas_id)) {
            self::syncStatusPusat($tanggal, $kelas_id);

            // Get selected class name
            $kls = array_filter($kelasList, function($k) use ($kelas_id) { return $k['id'] == $kelas_id; });
            $kls = reset($kls);
            $selectedKelas = $kls ? $kls['nama_kelas'] : '';

            // 1. Get all students in class
            $sqlSiswa = "SELECT s.id, s.nama, s.nis 
                         FROM siswa s 
                         LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                             AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
                         WHERE s.status IN ('Aktif', 'Alumni') 
                           AND COALESCE(rks.kelas_id, s.kelas_id) = ?
                         ORDER BY s.nama ASC";
            $stmt = $db_core->prepare($sqlSiswa);
            $stmt->execute([$kelas_id]);
            $siswaList = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // 2. Get schedule (mapels) for that day and class (from jadwal_pelajaran)
            $dayNum = date('N', strtotime($tanggal));
            $days = [1=>'Senin', 2=>'Selasa', 3=>'Rabu', 4=>'Kamis', 5=>'Jumat', 6=>'Sabtu', 7=>'Ahad'];
            $hari = $days[$dayNum];

            $stmtMapel = $db_siakad->prepare("SELECT DISTINCT jp.mapel_id, m.nama_mapel, m.kode_mapel, MIN(jp.jam_ke) as min_jam 
                                              FROM jadwal_pelajaran jp 
                                              JOIN mapel m ON jp.mapel_id = m.id 
                                              WHERE jp.hari = ? AND jp.kelas_id = ? 
                                              GROUP BY jp.mapel_id, m.nama_mapel, m.kode_mapel
                                              ORDER BY min_jam ASC");
            $stmtMapel->execute([$hari, $kelas_id]);
            $mapelColumns = $stmtMapel->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($mapelColumns as &$mc) {
                $stmtJurnal = $db_siakad->prepare("SELECT id FROM jurnal_guru WHERE tanggal = ? AND kelas_id = ? AND mapel_id = ? ORDER BY id DESC LIMIT 1");
                $stmtJurnal->execute([$tanggal, $kelas_id, $mc['mapel_id']]);
                $jurnal = $stmtJurnal->fetch(\PDO::FETCH_ASSOC);
                $mc['jurnal_id'] = $jurnal ? $jurnal['id'] : null;
            }
            unset($mc);

            // 3. Collect attendance for each student
            foreach ($siswaList as $s) {
                $row = [
                    'nama' => $s['nama'],
                    'masuk' => '-',
                    'status_pusat' => '-'
                ];

                // Gate attendance and status
                $stmtGate = $db_siakad->prepare("SELECT jam_masuk, status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                $stmtGate->execute([$s['id'], $tanggal]);
                $gate = $stmtGate->fetch(\PDO::FETCH_ASSOC);
                
                if ($gate) {
                    $row['masuk'] = !empty($gate['jam_masuk']) ? date('H:i', strtotime($gate['jam_masuk'])) : '-';
                }

                // Get Status Pusat from absensi_siswa first
                if ($gate && !empty($gate['status'])) {
                    $row['status_pusat'] = $gate['status'] === 'Alpha' ? 'Alpa' : $gate['status'];
                } else {
                    $row['status_pusat'] = '-';
                }

                $global_override = in_array($row['status_pusat'], ['Sakit', 'Izin', 'Alpa']) ? $row['status_pusat'] : false;

                // Mapel attendance
                $row['mapel_status'] = [];
                $statuses = [];
                foreach ($mapelColumns as $mc) {
                    if ($mc['jurnal_id']) {
                        if ($global_override) {
                            $status = $global_override;
                        } else {
                            $stmtMapelStatus = $db_siakad->prepare("SELECT status FROM absensi_permapel WHERE jurnal_id = ? AND siswa_id = ?");
                            $stmtMapelStatus->execute([$mc['jurnal_id'], $s['id']]);
                            $ms = $stmtMapelStatus->fetch(\PDO::FETCH_ASSOC);
                            $status = $ms ? $ms['status'] : '-';
                            
                            // Mapel hanya punya Alpa, bukan Bolos. Jika ada data Bolos, anggap Alpa.
                            if ($status === 'Bolos' || $status === 'Alpha') {
                                $status = 'Alpa';
                            }
                        }
                        
                        $row['mapel_status'][$mc['mapel_id']] = $status;
                        if (in_array($status, ['Hadir', 'Sakit', 'Izin', 'Alpa'])) {
                            $statuses[] = $status;
                        }
                    } else {
                        $row['mapel_status'][$mc['mapel_id']] = $global_override ? $global_override : '?';
                    }
                }

                if (!$global_override) {
                    if ($row['masuk'] === '-' && in_array('Hadir', $statuses)) {
                        $row['status_pusat'] = 'Bolos';
                    }
                }

                $timelineData[] = $row;
            }
        }

        $title   = "Rekap Detail (Timeline)";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/rekap_detail.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    public static function rekapDetailCetak() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $kls = array_filter($kelasList, function($k) use ($kelas_id) { return $k['id'] == $kelas_id; });
        $kls = reset($kls);
        $selectedKelas = $kls ? $kls['nama_kelas'] : '';

        $timelineData = [];
        $mapelColumns = [];

        if (!empty($kelas_id)) {
            $sqlSiswa = "SELECT s.id, s.nama, s.nis 
                         FROM siswa s 
                         LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                             AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
                         WHERE s.status IN ('Aktif', 'Alumni') 
                           AND COALESCE(rks.kelas_id, s.kelas_id) = ?
                         ORDER BY s.nama ASC";
            $stmt = $db_core->prepare($sqlSiswa);
            $stmt->execute([$kelas_id]);
            $siswaList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $dayNum = date('N', strtotime($tanggal));
            $days = [1=>'Senin', 2=>'Selasa', 3=>'Rabu', 4=>'Kamis', 5=>'Jumat', 6=>'Sabtu', 7=>'Ahad'];
            $hari = $days[$dayNum];

            $stmtMapel = $db_siakad->prepare("SELECT DISTINCT jp.mapel_id, m.nama_mapel, m.kode_mapel, MIN(jp.jam_ke) as min_jam 
                                              FROM jadwal_pelajaran jp 
                                              JOIN mapel m ON jp.mapel_id = m.id 
                                              WHERE jp.hari = ? AND jp.kelas_id = ? 
                                              GROUP BY jp.mapel_id, m.nama_mapel, m.kode_mapel
                                              ORDER BY min_jam ASC");
            $stmtMapel->execute([$hari, $kelas_id]);
            $mapelColumns = $stmtMapel->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($mapelColumns as &$mc) {
                $stmtJurnal = $db_siakad->prepare("SELECT id FROM jurnal_guru WHERE tanggal = ? AND kelas_id = ? AND mapel_id = ? ORDER BY id DESC LIMIT 1");
                $stmtJurnal->execute([$tanggal, $kelas_id, $mc['mapel_id']]);
                $jurnal = $stmtJurnal->fetch(\PDO::FETCH_ASSOC);
                $mc['jurnal_id'] = $jurnal ? $jurnal['id'] : null;
            }
            unset($mc);

            foreach ($siswaList as $s) {
                $row = [
                    'nama' => $s['nama'],
                    'masuk' => '-',
                    'status_pusat' => '-'
                ];

                $stmtGate = $db_siakad->prepare("SELECT jam_masuk, status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                $stmtGate->execute([$s['id'], $tanggal]);
                $gate = $stmtGate->fetch(\PDO::FETCH_ASSOC);
                
                if ($gate) {
                    $row['masuk'] = !empty($gate['jam_masuk']) ? date('H:i', strtotime($gate['jam_masuk'])) : '-';
                }

                if ($gate && !empty($gate['status'])) {
                    $row['status_pusat'] = $gate['status'] === 'Alpha' ? 'Alpa' : $gate['status'];
                } else {
                    $row['status_pusat'] = '-';
                }

                $global_override = in_array($row['status_pusat'], ['Sakit', 'Izin', 'Alpa']) ? $row['status_pusat'] : false;

                $row['mapel_status'] = [];
                $statuses = [];
                foreach ($mapelColumns as $mc) {
                    if ($mc['jurnal_id']) {
                        if ($global_override) {
                            $status = $global_override;
                        } else {
                            $stmtMapelStatus = $db_siakad->prepare("SELECT status FROM absensi_permapel WHERE jurnal_id = ? AND siswa_id = ?");
                            $stmtMapelStatus->execute([$mc['jurnal_id'], $s['id']]);
                            $ms = $stmtMapelStatus->fetch(\PDO::FETCH_ASSOC);
                            $status = $ms ? $ms['status'] : '-';
                            if ($status === 'Bolos' || $status === 'Alpha') {
                                $status = 'Alpa';
                            }
                        }
                        
                        $row['mapel_status'][$mc['mapel_id']] = $status;
                        if (in_array($status, ['Hadir', 'Sakit', 'Izin', 'Alpa'])) {
                            $statuses[] = $status;
                        }
                    } else {
                        $row['mapel_status'][$mc['mapel_id']] = $global_override ? $global_override : '?';
                    }
                }

                if (!$global_override) {
                    if ($row['masuk'] === '-' && in_array('Hadir', $statuses)) {
                        $row['status_pusat'] = 'Bolos';
                    }
                }

                $timelineData[] = $row;
            }
        }

        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/rekap_detail_cetak.php';
        $content = ob_get_clean();
        echo $content;
    }

    // ─────────────────────────────────────────────────────────
    // SYNC STATUS PUSAT DARI JURNAL
    // ─────────────────────────────────────────────────────────
    public static function syncStatusPusat($tanggal, $kelas_id) {
        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $sqlSiswa = "SELECT s.id
                     FROM siswa s 
                     LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                         AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
                     WHERE s.status IN ('Aktif', 'Alumni') 
                       AND COALESCE(rks.kelas_id, s.kelas_id) = ?";
        $stmt = $db_core->prepare($sqlSiswa);
        $stmt->execute([$kelas_id]);
        $siswaList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $dayNum = date('N', strtotime($tanggal));
        $days = [1=>'Senin', 2=>'Selasa', 3=>'Rabu', 4=>'Kamis', 5=>'Jumat', 6=>'Sabtu', 7=>'Ahad'];
        $hari = $days[$dayNum];

        $stmtMapel = $db_siakad->prepare("SELECT DISTINCT jp.mapel_id 
                                          FROM jadwal_pelajaran jp 
                                          WHERE jp.hari = ? AND jp.kelas_id = ?");
        $stmtMapel->execute([$hari, $kelas_id]);
        $mapels = $stmtMapel->fetchAll(\PDO::FETCH_ASSOC);

        $jurnals = [];
        foreach ($mapels as $mc) {
            $stmtJurnal = $db_siakad->prepare("SELECT id FROM jurnal_guru WHERE tanggal = ? AND kelas_id = ? AND mapel_id = ? ORDER BY id DESC LIMIT 1");
            $stmtJurnal->execute([$tanggal, $kelas_id, $mc['mapel_id']]);
            $j = $stmtJurnal->fetch(\PDO::FETCH_ASSOC);
            if ($j) {
                $jurnals[] = $j['id'];
            }
        }

        $stmtSync = $db_siakad->prepare("
            INSERT INTO absensi_siswa (siswa_id, tanggal, status) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE status = VALUES(status)
        ");

        $stmtCheckGate = $db_siakad->prepare("SELECT id FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ? LIMIT 1");

        foreach ($siswaList as $s) {
            $stmtGate = $db_siakad->prepare("SELECT jam_masuk, status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
            $stmtGate->execute([$s['id'], $tanggal]);
            $gate = $stmtGate->fetch(\PDO::FETCH_ASSOC);
            $has_masuk = ($gate && !empty($gate['jam_masuk']));
            $gate_status = $gate ? $gate['status'] : null;

            $statuses = [];
            foreach ($jurnals as $jid) {
                $stmtMS = $db_siakad->prepare("SELECT status FROM absensi_permapel WHERE jurnal_id = ? AND siswa_id = ?");
                $stmtMS->execute([$jid, $s['id']]);
                $ms = $stmtMS->fetch(\PDO::FETCH_ASSOC);
                
                if ($ms) {
                    $status = $ms['status'];
                    if ($status === 'Bolos' || $status === 'Alpha') {
                        $status = 'Alpa';
                    }
                    if (in_array($status, ['Hadir', 'Sakit', 'Izin', 'Alpa'])) {
                        $statuses[] = $status;
                    }
                }
            }

            $statusPusat = null;
            if (in_array($gate_status, ['Sakit', 'Izin', 'Alpa', 'Alpha'])) {
                $statusPusat = $gate_status === 'Alpha' ? 'Alpa' : $gate_status;
            } else {
                if (empty($statuses) && !$has_masuk) {
                    $statusPusat = null;
                } else {
                    if (in_array('Hadir', $statuses) || $has_masuk) {
                        if (in_array('Alpa', $statuses)) {
                            $statusPusat = 'Bolos';
                        } elseif (!$has_masuk) {
                            $statusPusat = null; // biarkan dulu jika hadir tapi belum scan
                        } else {
                            $statusPusat = ($gate_status === 'Terlambat') ? 'Terlambat' : 'Hadir';
                        }
                    } else {
                        if (in_array('Alpa', $statuses)) {
                            $statusPusat = 'Alpa';
                        } elseif (in_array('Sakit', $statuses)) {
                            $statusPusat = 'Sakit';
                        } elseif (in_array('Izin', $statuses)) {
                            $statusPusat = 'Izin';
                        }
                    }
                }
            }

            if ($statusPusat) {
                $stmtCheck = $db_siakad->prepare("SELECT id FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                $stmtCheck->execute([$s['id'], $tanggal]);
                if ($stmtCheck->fetch()) {
                    $stmtUpdate = $db_siakad->prepare("UPDATE absensi_siswa SET status = ? WHERE siswa_id = ? AND tanggal = ?");
                    $stmtUpdate->execute([$statusPusat, $s['id'], $tanggal]);
                } else {
                    $stmtInsert = $db_siakad->prepare("INSERT INTO absensi_siswa (siswa_id, tanggal, status) VALUES (?, ?, ?)");
                    $stmtInsert->execute([$s['id'], $tanggal, $statusPusat]);
                }
            } else {
                $stmtCheckGate->execute([$s['id'], $tanggal]);
                if ($stmtCheckGate->fetch()) {
                    $db_siakad->prepare("UPDATE absensi_siswa SET status = NULL WHERE siswa_id = ? AND tanggal = ?")->execute([$s['id'], $tanggal]);
                }
            }
        }
    }

    // ─────────────────────────────────────────────────────────
    // CETAK BULANAN SISWA DETAIL (DAFTAR SCAN QR)
    // ─────────────────────────────────────────────────────────
    public static function cetakBulananSiswaDetail() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $bulan    = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
        $tahun    = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $nama_kelas_dipilih = 'Semua Kelas';
        if (!empty($kelas_id)) {
            $kls = array_filter($kelasList, function($k) use ($kelas_id) { return $k['id'] == $kelas_id; });
            $kls = reset($kls);
            $nama_kelas_dipilih = $kls ? $kls['nama_kelas'] : '';
        }

        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();

        // Ambil pengaturan absen siswa
        $settings = ['jam_terlambat' => '07:15'];
        try {
            $res = $db_core->query("SELECT * FROM absensi_pengaturan")->fetchAll();
            foreach ($res as $row) { $settings[$row['kunci']] = $row['nilai']; }
        } catch (\Exception $e) {}
        
        $batas_terlambat = $settings['jam_terlambat'] . ':00';
        if (strlen($batas_terlambat) == 5) $batas_terlambat .= ':00';

        $where = "s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
        $params = [];
        if (!empty($kelas_id)) {
            $where .= " AND COALESCE(rks.kelas_id, s.kelas_id) = :kelas_id";
            $params[':kelas_id'] = $kelas_id;
        }

        $sql = "SELECT s.id, s.nis, s.nama, COALESCE(rks.kelas_id, s.kelas_id) as kelas_id, k.nama_kelas
                FROM siswa s 
                LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
                LEFT JOIN kelas k ON COALESCE(rks.kelas_id, s.kelas_id) = k.id
                WHERE $where ORDER BY k.nama_kelas ASC, s.nama ASC";
        
        $stmt = $db_core->prepare($sql);
        $stmt->execute($params);
        $siswaList = $stmt->fetchAll();

        // Dates for the month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, intval($bulan), intval($tahun));
        $dates = [];
        $dayNames = ['Sunday'=>'Ahad', 'Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat', 'Saturday'=>'Sabtu'];
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateStr = sprintf("%04d-%02d-%02d", $tahun, $bulan, $i);
            $hari_en = date('l', strtotime($dateStr));
            $is_jumat = ($hari_en == 'Friday');
            $dates[] = [
                'date' => $dateStr,
                'tgl' => $i,
                'is_libur' => $is_jumat
            ];
        }

        $absenBulanIni = [];
        $stmtAbs = $db_siakad->prepare("SELECT siswa_id, tanggal, status, jam_masuk FROM absensi_siswa WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?");
        $stmtAbs->execute([$bulan, $tahun]);
        foreach ($stmtAbs->fetchAll() as $ab) {
            $absenBulanIni[$ab['siswa_id']][$ab['tanggal']] = $ab;
        }

        $monthsIndo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                       '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
        $bulan_name = $monthsIndo[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? $bulan;

        include __DIR__ . '/../../resources/views/absen_v2/cetak_bulanan_siswa_detail.php';
    }

    // ─────────────────────────────────────────────────────────
    // CETAK TOP 3 SISWA (PENGHARGAAN)
    // ─────────────────────────────────────────────────────────
    public static function cetakTop3Siswa() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
        $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $db_core   = Database::connect();
        $db_siakad = Database::connect();
        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

        // Get Active Days Per Class
        $sqlActiveDays = "SELECT COALESCE(rks.kelas_id, s.kelas_id) as kelas_id, COUNT(DISTINCT a.tanggal) as active_days
                          FROM absensi_siswa a
                          JOIN siswa s ON a.siswa_id = s.id
                          LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = ?)
                          WHERE MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
                          GROUP BY COALESCE(rks.kelas_id, s.kelas_id)";
        $stmtActive = $db_siakad->prepare($sqlActiveDays);
        $stmtActive->execute([$active_year_name, $bulan, $tahun]);
        
        $class_active_days_count = [];
        while ($row = $stmtActive->fetch(\PDO::FETCH_ASSOC)) {
            $class_active_days_count[$row['kelas_id']] = $row['active_days'];
        }

        // Fetch all Top Data
        $sqlTop = "SELECT s.id, s.nama, s.nis, COALESCE(rks.kelas_id, s.kelas_id) as kelas_id, COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas,
                   SUM(CASE WHEN a.status IN ('Hadir', 'Terlambat') THEN 1 ELSE 0 END) as total_hadir,
                   SUM(CASE WHEN a.status = 'Alpa' THEN 1 ELSE 0 END) as total_alpa,
                   COUNT(a.id) as total_hari_tercatat,
                   SEC_TO_TIME(AVG(TIME_TO_SEC(a.jam_masuk))) as avg_jam_masuk
                   FROM siswa s
                   JOIN absensi_siswa a ON s.id = a.siswa_id
                   LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = ?)
                   LEFT JOIN kelas k ON s.kelas_id = k.id 
                   LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id 
                   WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = ?)
                   AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
                   GROUP BY s.id
                   ORDER BY total_hadir DESC, total_alpa ASC, avg_jam_masuk ASC, s.nama ASC";
        $stmtTop = $db_core->prepare($sqlTop);
        $stmtTop->execute([$active_year_name, $active_year_name, $bulan, $tahun]);
        $allTop = $stmtTop->fetchAll();

        $top3Instansi = array_slice($allTop, 0, 3);
        $top3PerKelas = [];
        foreach ($kelasList as $kls) {
            $inClass = array_filter($allTop, function($t) use ($kls) {
                return $t['kelas_id'] == $kls['id'];
            });
            $top3PerKelas[$kls['id']] = array_values(array_slice($inClass, 0, 3));
        }

        $monthsIndo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                       '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
        $bulan_name = $monthsIndo[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? $bulan;

        include __DIR__ . '/../../resources/views/absen_v2/cetak_top3_siswa.php';
    }

    // ─────────────────────────────────────────────────────────
    // REKAP BULANAN SISWA
    // ─────────────────────────────────────────────────────────
    public static function rekapBulanan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $bulan    = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
        $tahun    = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

        $where = "WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
        if (!empty($kelas_id)) {
            $where .= " AND COALESCE(rks.kelas_id, s.kelas_id) = " . intval($kelas_id);
        }

        $sqlSiswa = "SELECT s.id, s.nama, s.nis, COALESCE(rks.kelas_id, s.kelas_id) as kelas_id, COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas 
                     FROM siswa s 
                     LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                         AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
                     LEFT JOIN kelas k ON s.kelas_id = k.id 
                     LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id 
                     $where
                     ORDER BY COALESCE(k_hist.nama_kelas, k.nama_kelas) ASC, s.nama ASC";
                     
        $siswaList = $db_core->query($sqlSiswa)->fetchAll();

        $rekap = [];
        foreach ($siswaList as $s) {
            $stmt = $db_siakad->prepare("SELECT status, COUNT(*) as total FROM absensi_siswa WHERE siswa_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? GROUP BY status");
            $stmt->execute([$s['id'], $bulan, $tahun]);
            $res = $stmt->fetchAll();
            $r = ['Hadir'=>0, 'Sakit'=>0, 'Izin'=>0, 'Alpa'=>0, 'Bolos'=>0, 'Terlambat'=>0];
            foreach ($res as $row) { $r[$row['status']] = $row['total']; }
            $s['rekap'] = $r;
            $kls = array_filter($kelasList, function($k) use ($s) { return $k['id'] == $s['kelas_id']; });
            $kls = reset($kls);
            $s['nama_kelas'] = $kls ? $kls['nama_kelas'] : '-';
            $rekap[] = $s;
        }

        // --- Get Active Days Per Class (All Classes) ---
        $sqlActiveDays = "SELECT COALESCE(rks.kelas_id, s.kelas_id) as kelas_id, COUNT(DISTINCT a.tanggal) as active_days
                          FROM absensi_siswa a
                          JOIN siswa s ON a.siswa_id = s.id
                          LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = ?)
                          WHERE MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
                          GROUP BY COALESCE(rks.kelas_id, s.kelas_id)";
        $stmtActive = $db_siakad->prepare($sqlActiveDays);
        $stmtActive->execute([$active_year_name, $bulan, $tahun]);
        
        $class_active_days_count = [];
        while ($row = $stmtActive->fetch(\PDO::FETCH_ASSOC)) {
            $class_active_days_count[$row['kelas_id']] = $row['active_days'];
        }

        // --- Fetch Top 3 Instansi & Per Kelas ---
        $sqlTop = "SELECT s.id, s.nama, s.nis, COALESCE(rks.kelas_id, s.kelas_id) as kelas_id, COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas,
                   SUM(CASE WHEN a.status IN ('Hadir', 'Terlambat') THEN 1 ELSE 0 END) as total_hadir,
                   SUM(CASE WHEN a.status = 'Alpa' THEN 1 ELSE 0 END) as total_alpa,
                   COUNT(a.id) as total_hari_tercatat,
                   SEC_TO_TIME(AVG(TIME_TO_SEC(a.jam_masuk))) as avg_jam_masuk
                   FROM siswa s
                   JOIN absensi_siswa a ON s.id = a.siswa_id
                   LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = ?)
                   LEFT JOIN kelas k ON s.kelas_id = k.id 
                   LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id 
                   WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = ?)
                   AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
                   GROUP BY s.id
                   ORDER BY total_hadir DESC, total_alpa ASC, avg_jam_masuk ASC, s.nama ASC";
        $stmtTop = $db_core->prepare($sqlTop);
        $stmtTop->execute([$active_year_name, $active_year_name, $bulan, $tahun]);
        $allTop = $stmtTop->fetchAll();

        $top3Instansi = array_slice($allTop, 0, 3);
        $top3PerKelas = [];
        foreach ($kelasList as $kls) {
            $inClass = array_filter($allTop, function($t) use ($kls) {
                return $t['kelas_id'] == $kls['id'];
            });
            // Reset array keys
            $top3PerKelas[$kls['id']] = array_values(array_slice($inClass, 0, 3));
        }

        $title   = "Rekap Bulanan Absensi Siswa";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/rekap_bulanan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    // ─────────────────────────────────────────────────────────
    // REKAP HARIAN GURU
    // ─────────────────────────────────────────────────────────
    public static function rekapHarianGuru() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        $jabatan = isset($_GET['jabatan']) ? $_GET['jabatan'] : '';

        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        // Auto-create tabel
        $db_siakad->exec("CREATE TABLE IF NOT EXISTS absensi_guru (
            id INT AUTO_INCREMENT PRIMARY KEY, tanggal DATE NOT NULL, guru_id INT NOT NULL,
            status ENUM('Hadir','Terlambat','Sakit','Izin','Alpa') DEFAULT 'Hadir',
            jam_masuk TIME NULL, jam_pulang TIME NULL, keterangan VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tanggal_guru (tanggal, guru_id)
        )");

        // Daftar jabatan unik untuk filter
        $jabatanList = $db_core->query("SELECT DISTINCT jabatan FROM guru WHERE jabatan IS NOT NULL AND jabatan != '' ORDER BY jabatan ASC")->fetchAll(\PDO::FETCH_COLUMN);

        $days = ['Sunday'=>'Ahad', 'Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat', 'Saturday'=>'Sabtu'];
        $hari_ini = $days[date('l', strtotime($tanggal))];
        
        if ($hari_ini == 'Jumat') {
            echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>";
            echo "<h2 style='color:#f43f5e;'>Hari Jumat Libur</h2>";
            echo "<p>Tidak ada jadwal absensi pada hari libur.</p>";
            echo "</div>";
            exit;
        }

        $sql = "SELECT ag.*, g.nama, g.jabatan, g.foto
                FROM absensi_guru ag
                JOIN guru g ON ag.guru_id = g.id
                WHERE ag.tanggal = :tanggal";
        $params = [':tanggal' => $tanggal];
        if (!empty($jabatan)) {
            $sql .= " AND g.jabatan = :jabatan";
            $params[':jabatan'] = $jabatan;
        }
        $sql .= " ORDER BY ag.status ASC, g.nama ASC";

        $stmt = $db_siakad->prepare($sql);
        $stmt->execute($params);
        $absensiGuru = $stmt->fetchAll();

        // Guru yang belum absen sama sekali
        $sqlBelumAbsen = "SELECT g.id, g.nama, g.jabatan, g.foto FROM guru g
                          WHERE g.id NOT IN (SELECT guru_id FROM siakad_db.absensi_guru WHERE tanggal = :tanggal)";
        $pBelum = [':tanggal' => $tanggal];
        if (!empty($jabatan)) {
            $sqlBelumAbsen .= " AND g.jabatan = :jabatan";
            $pBelum[':jabatan'] = $jabatan;
        }
        $sqlBelumAbsen .= " ORDER BY g.nama ASC";
        $stmtBelum = $db_core->prepare($sqlBelumAbsen);
        $stmtBelum->execute($pBelum);
        $guruBelumAbsenRaw = $stmtBelum->fetchAll();

        // Cari tahu apakah guru-guru ini punya jadwal hari ini
        $days = ['Sunday'=>'Ahad', 'Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat', 'Saturday'=>'Sabtu'];
        $hari_indonesia = $days[date('l', strtotime($tanggal))];
        
        $active_year = \App\Core\AcademicYear::current();
        $ta_id = $active_year ? $active_year['id'] : 0;
        $semester = $active_year ? $active_year['semester'] : 'Ganjil';

        $guruBelumAbsen = [];
        $stmtJadwal = $db_siakad->prepare("SELECT id FROM jadwal_pelajaran WHERE guru_id = ? AND hari = ? AND tahun_ajaran_id = ? AND semester = ? LIMIT 1");
        // Fallback jika tidak filter tahun ajaran
        $stmtJadwalFallback = $db_siakad->prepare("SELECT id FROM jadwal_pelajaran WHERE guru_id = ? AND hari = ? LIMIT 1");
        
        foreach ($guruBelumAbsenRaw as $gb) {
            $stmtJadwal->execute([$gb['id'], $hari_indonesia, $ta_id, $semester]);
            $adaJadwal = $stmtJadwal->fetch();
            if (!$adaJadwal) { // Coba tanpa tahun ajaran filter jika tidak ketemu (jaga-jaga data lama)
                $stmtJadwalFallback->execute([$gb['id'], $hari_indonesia]);
                $adaJadwal = $stmtJadwalFallback->fetch();
            }
            $gb['ada_jam'] = $adaJadwal ? true : false;
            $guruBelumAbsen[] = $gb;
        }

        $title   = "Rekap Harian Absensi Guru";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/rekap_harian_guru.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    // ─────────────────────────────────────────────────────────
    // REKAP BULANAN GURU
    // ─────────────────────────────────────────────────────────
    public static function rekapBulananGuru() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $bulan   = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
        $tahun   = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $jabatan = isset($_GET['jabatan']) ? $_GET['jabatan'] : '';

        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        // Auto-create
        $db_siakad->exec("CREATE TABLE IF NOT EXISTS absensi_guru (
            id INT AUTO_INCREMENT PRIMARY KEY, tanggal DATE NOT NULL, guru_id INT NOT NULL,
            status ENUM('Hadir','Terlambat','Sakit','Izin','Alpa') DEFAULT 'Hadir',
            jam_masuk TIME NULL, jam_pulang TIME NULL, keterangan VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tanggal_guru (tanggal, guru_id)
        )");

        $jabatanList = $db_core->query("SELECT DISTINCT jabatan FROM guru WHERE jabatan IS NOT NULL AND jabatan != '' ORDER BY jabatan ASC")->fetchAll(\PDO::FETCH_COLUMN);

        $whereGuru = "WHERE 1=1";
        $paramsGuru = [];
        if (!empty($jabatan)) {
            $whereGuru .= " AND g.jabatan = :jabatan";
            $paramsGuru[':jabatan'] = $jabatan;
        }

        $sqlGuru = "SELECT g.id, g.nama, g.jabatan, g.foto FROM guru g $whereGuru ORDER BY g.nama ASC";
        $stmtG = $db_core->prepare($sqlGuru);
        $stmtG->execute($paramsGuru);
        $guruList = $stmtG->fetchAll();

        $rekap = [];
        foreach ($guruList as $g) {
            $stmt = $db_siakad->prepare("SELECT status, COUNT(*) as total FROM absensi_guru WHERE guru_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? GROUP BY status");
            $stmt->execute([$g['id'], $bulan, $tahun]);
            $res = $stmt->fetchAll();
            $r = ['Hadir'=>0, 'Terlambat'=>0, 'Sakit'=>0, 'Izin'=>0, 'Alpa'=>0];
            foreach ($res as $row) { $r[$row['status']] = $row['total']; }
            $g['rekap'] = $r;
            $rekap[] = $g;
        }

        $title   = "Rekap Bulanan Absensi Guru";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/rekap_bulanan_guru.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    // ─────────────────────────────────────────────────────────
    // CETAK BULANAN GURU PER GURU
    // ─────────────────────────────────────────────────────────
    public static function cetakBulananGuruDetail() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $bulan   = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
        $tahun   = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $jabatan = isset($_GET['jabatan']) ? $_GET['jabatan'] : '';
        $guru_id = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;

        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        $whereGuru = "WHERE 1=1";
        $paramsGuru = [];
        if (!empty($jabatan)) {
            $whereGuru .= " AND g.jabatan = :jabatan";
            $paramsGuru[':jabatan'] = $jabatan;
        }
        if ($guru_id > 0) {
            $whereGuru .= " AND g.id = :guru_id";
            $paramsGuru[':guru_id'] = $guru_id;
        }

        $sqlGuru = "SELECT g.id, g.nama, g.jabatan, g.npk FROM guru g $whereGuru ORDER BY g.nama ASC";
        $stmtG = $db_core->prepare($sqlGuru);
        $stmtG->execute($paramsGuru);
        $guruList = $stmtG->fetchAll();

        // Get instansi info
        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();

        // Siapkan tanggal-tanggal dalam bulan ini
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, intval($bulan), intval($tahun));
        $dates = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateStr = sprintf("%04d-%02d-%02d", $tahun, $bulan, $i);
            $dates[] = $dateStr;
        }

        // Cari tahu hari apa saja tiap tanggal, dan apakah ada jadwal
        $active_year = \App\Core\AcademicYear::current();
        $ta_id = $active_year ? $active_year['id'] : 0;
        $semester = $active_year ? $active_year['semester'] : 'Ganjil';
        $dayNames = ['Sunday'=>'Ahad', 'Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat', 'Saturday'=>'Sabtu'];

        $rekapPerGuru = [];
        $stmtJadwal = $db_siakad->prepare("SELECT id FROM jadwal_pelajaran WHERE guru_id = ? AND hari = ? AND tahun_ajaran_id = ? AND semester = ? LIMIT 1");
        $stmtJadwalFallback = $db_siakad->prepare("SELECT id FROM jadwal_pelajaran WHERE guru_id = ? AND hari = ? LIMIT 1");

        foreach ($guruList as $g) {
            $absensi = [];
            // Ambil semua absen di bulan ini untuk guru ini
            $stmt = $db_siakad->prepare("SELECT tanggal, status, jam_masuk, jam_pulang, keterangan FROM absensi_guru WHERE guru_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?");
            $stmt->execute([$g['id'], $bulan, $tahun]);
            $res = $stmt->fetchAll();
            $absenMap = [];
            foreach ($res as $row) {
                $absenMap[$row['tanggal']] = $row;
            }

            $detailHarian = [];
            $summary = ['Hadir'=>0, 'Terlambat'=>0, 'Sakit'=>0, 'Izin'=>0, 'Alpa'=>0, 'TidakAdaJam'=>0];

            foreach ($dates as $d) {
                $hari_en = date('l', strtotime($d));
                $hari_id = $dayNames[$hari_en];
                
                $status = '-';
                $jam_masuk = '-';
                $jam_pulang = '-';
                $ket = '-';

                if (isset($absenMap[$d])) {
                    $status = $absenMap[$d]['status'];
                    $jam_masuk = $absenMap[$d]['jam_masuk'] ? substr($absenMap[$d]['jam_masuk'], 0, 5) : '-';
                    $jam_pulang = $absenMap[$d]['jam_pulang'] ? substr($absenMap[$d]['jam_pulang'], 0, 5) : '-';
                    $ket = $absenMap[$d]['keterangan'] ?: '-';
                    if (isset($summary[$status])) $summary[$status]++;
                } else {
                    // Cek jadwal
                    if ($hari_en == 'Friday') {
                        $status = 'Libur';
                    } else {
                        $stmtJadwal->execute([$g['id'], $hari_id, $ta_id, $semester]);
                        $ada = $stmtJadwal->fetch();
                        if (!$ada) {
                            $stmtJadwalFallback->execute([$g['id'], $hari_id]);
                            $ada = $stmtJadwalFallback->fetch();
                        }
                        if (!$ada) {
                            $status = 'Tidak Ada Jam';
                            $summary['TidakAdaJam']++;
                        } else {
                            if ($d <= date('Y-m-d')) { // Jika tanggal sudah lewat atau hari ini, dan belum absen, berarti alpa (hanya untuk tampilan rekap, belum masuk DB)
                                $status = 'Alpa (Belum Absen)';
                                // Catatan: ini tidak menambah summary alpa karena tidak ada di DB, tapi opsional bisa ditambah
                            }
                        }
                    }
                }

                $detailHarian[] = [
                    'tanggal' => $d,
                    'hari' => $hari_id,
                    'status' => $status,
                    'jam_masuk' => $jam_masuk,
                    'jam_pulang' => $jam_pulang,
                    'keterangan' => $ket
                ];
            }

            $g['detail'] = $detailHarian;
            $g['summary'] = $summary;
            $rekapPerGuru[] = $g;
        }

        $monthsIndo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                       '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
        $bulan_name = $monthsIndo[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? $bulan;

        include __DIR__ . '/../../resources/views/absen_v2/cetak_bulanan_guru_detail.php';
    }

    // ─────────────────────────────────────────────────────────
    // VERIFIKASI DOKUMEN CETAK
    // ─────────────────────────────────────────────────────────
    public static function verifyAbsen() {
        $guru_id = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
        $bulan   = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
        $tahun   = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

        $db_core = Database::connect();
        
        $guru = false;
        if ($guru_id > 0) {
            $stmt = $db_core->prepare("SELECT nama, jabatan, npk FROM guru WHERE id = ?");
            $stmt->execute([$guru_id]);
            $guru = $stmt->fetch();
        }

        $monthsIndo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                       '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
        $bulan_name = $monthsIndo[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? $bulan;

        include __DIR__ . '/../../resources/views/siakad/verify_absen.php';
    }

    // ─────────────────────────────────────────────────────────
    // INPUT MANUAL ABSEN GURU
    // ─────────────────────────────────────────────────────────
    public static function inputAbsenGuru() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        $db_siakad->exec("CREATE TABLE IF NOT EXISTS absensi_guru (
            id INT AUTO_INCREMENT PRIMARY KEY, tanggal DATE NOT NULL, guru_id INT NOT NULL,
            status ENUM('Hadir','Terlambat','Sakit','Izin','Alpa') DEFAULT 'Hadir',
            jam_masuk TIME NULL, jam_pulang TIME NULL, keterangan VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tanggal_guru (tanggal, guru_id)
        )");

        $guruList = $db_core->query("SELECT id, nama, jabatan FROM guru ORDER BY nama ASC")->fetchAll();
        $tanggal  = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

        // Ambil data absen yang sudah ada untuk tanggal ini, di-index by guru_id
        $existingAbsenByGuruId = [];
        $stmtEx = $db_siakad->prepare("SELECT * FROM absensi_guru WHERE tanggal = ?");
        $stmtEx->execute([$tanggal]);
        foreach ($stmtEx->fetchAll() as $row) {
            $existingAbsenByGuruId[$row['guru_id']] = $row;
        }

        $msg     = isset($_GET['msg']) ? $_GET['msg'] : null;
        $title   = "Input Manual Absensi Guru";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/input_absen_guru.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    // ─────────────────────────────────────────────────────────
    // SIMPAN ABSEN GURU MANUAL
    // ─────────────────────────────────────────────────────────
    public static function inputAbsenGuruSave() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $db_siakad = Database::connect();
        $tanggal   = $_POST['tanggal'] ?? date('Y-m-d');
        $absenData = $_POST['absen'] ?? [];

        foreach ($absenData as $guru_id => $data) {
            $guru_id   = intval($guru_id);
            $status    = $data['status']     ?? 'Hadir';
            $jam_masuk = !empty($data['jam_masuk'])  ? $data['jam_masuk']  : null;
            $keterangan= $data['keterangan'] ?? null;

            $cek = $db_siakad->prepare("SELECT id FROM absensi_guru WHERE tanggal = ? AND guru_id = ?");
            $cek->execute([$tanggal, $guru_id]);
            $existing = $cek->fetch();

            if ($existing) {
                $db_siakad->prepare("UPDATE absensi_guru SET status = ?, jam_masuk = ?, keterangan = ? WHERE id = ?")
                          ->execute([$status, $jam_masuk, $keterangan, $existing['id']]);
            } else {
                $db_siakad->prepare("INSERT INTO absensi_guru (tanggal, guru_id, status, jam_masuk, keterangan, created_at) VALUES (?, ?, ?, ?, ?, NOW())")
                          ->execute([$tanggal, $guru_id, $status, $jam_masuk, $keterangan]);
            }
        }

        header('Location: /absen/input-absen-guru?tanggal=' . $tanggal . '&msg=success');
        exit;
    }

    // ─────────────────────────────────────────────────────────
    // KETIDAKHADIRAN SISWA
    // ─────────────────────────────────────────────────────────
    public static function ketidakhadiran() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        
        $db_core   = Database::connect();
        $db_siakad = Database::connect();

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $sql = "SELECT a.*, s.nama, s.nis, COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas 
                FROM absensi_siswa a 
                JOIN siswa s ON a.siswa_id = s.id 
                LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                    AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = ?)
                LEFT JOIN kelas k ON s.kelas_id = k.id 
                LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id 
                WHERE a.tanggal = ? AND a.status IN ('Sakit', 'Izin', 'Alpa')
                AND s.status IN ('Aktif', 'Alumni') 
                AND (rks.id IS NOT NULL OR s.tahun_ajaran = ?)
                ORDER BY COALESCE(k_hist.nama_kelas, k.nama_kelas) ASC, s.nama ASC";
        
        $stmt = $db_siakad->prepare($sql);
        $stmt->execute([$active_year_name, $tanggal, $active_year_name]);
        $absensi = $stmt->fetchAll();

        $title   = "Data Ketidakhadiran";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/ketidakhadiran.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    public static function statistikSiswa() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date   = $_GET['end_date'] ?? date('Y-m-t');

        $db = Database::connect();
        
        // 1. Get Summary Counts
        $sql_summary = "SELECT status, COUNT(*) as count 
                        FROM absensi_siswa 
                        WHERE tanggal BETWEEN ? AND ? 
                        GROUP BY status";
        $stmt = $db->prepare($sql_summary);
        $stmt->execute([$start_date, $end_date]);
        $summary = $stmt->fetchAll();

        $totalHadir = 0;
        $totalSakit = 0;
        $totalIzin  = 0;
        $totalAlpa  = 0;
        $totalBolos = 0;

        foreach ($summary as $row) {
            if ($row['status'] == 'Hadir') $totalHadir = $row['count'];
            if ($row['status'] == 'Sakit') $totalSakit = $row['count'];
            if ($row['status'] == 'Izin') $totalIzin = $row['count'];
            if ($row['status'] == 'Alpa') $totalAlpa = $row['count'];
            if ($row['status'] == 'Bolos') $totalBolos = $row['count'];
        }

        // 2. Get Daily Trend for Line Chart
        $sql_trend = "SELECT tanggal, 
                             SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) as hadir,
                             SUM(CASE WHEN status IN ('Sakit','Izin','Alpa','Bolos') THEN 1 ELSE 0 END) as tidak_hadir
                      FROM absensi_siswa 
                      WHERE tanggal BETWEEN ? AND ? 
                      GROUP BY tanggal 
                      ORDER BY tanggal ASC";
        $stmt = $db->prepare($sql_trend);
        $stmt->execute([$start_date, $end_date]);
        $trendData = $stmt->fetchAll();

        $trendDates = [];
        $trendHadir = [];
        $trendTidakHadir = [];
        foreach ($trendData as $row) {
            $trendDates[] = date('d M', strtotime($row['tanggal']));
            $trendHadir[] = $row['hadir'];
            $trendTidakHadir[] = $row['tidak_hadir'];
        }

        $title   = "Statistik Kehadiran Siswa";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/statistik_siswa.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    public static function statistikSiswaCetak() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date   = $_GET['end_date'] ?? date('Y-m-t');

        $db_core = Database::connect();
        $db = Database::connect();
        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();
        
        $sql_summary = "SELECT status, COUNT(*) as count 
                        FROM absensi_siswa 
                        WHERE tanggal BETWEEN ? AND ? 
                        GROUP BY status";
        $stmt = $db->prepare($sql_summary);
        $stmt->execute([$start_date, $end_date]);
        $summary = $stmt->fetchAll();

        $totalHadir = 0; $totalSakit = 0; $totalIzin  = 0; $totalAlpa  = 0; $totalBolos = 0;

        foreach ($summary as $row) {
            if ($row['status'] == 'Hadir') $totalHadir = $row['count'];
            if ($row['status'] == 'Sakit') $totalSakit = $row['count'];
            if ($row['status'] == 'Izin') $totalIzin = $row['count'];
            if ($row['status'] == 'Alpa') $totalAlpa = $row['count'];
            if ($row['status'] == 'Bolos') $totalBolos = $row['count'];
        }

        $sql_trend = "SELECT tanggal, 
                             SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) as hadir,
                             SUM(CASE WHEN status IN ('Sakit','Izin','Alpa','Bolos') THEN 1 ELSE 0 END) as tidak_hadir
                      FROM absensi_siswa 
                      WHERE tanggal BETWEEN ? AND ? 
                      GROUP BY tanggal 
                      ORDER BY tanggal ASC";
        $stmt = $db->prepare($sql_trend);
        $stmt->execute([$start_date, $end_date]);
        $trendData = $stmt->fetchAll();

        $title   = "Cetak Statistik Kehadiran Siswa";

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/statistik_siswa_cetak.php';
        $content = ob_get_clean();
        echo $content;
    }

    // ─────────────────────────────────────────────────────────
    // PENGATURAN
    // ─────────────────────────────────────────────────────────
    public static function pengaturan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $db_core = Database::connect();
        
        $db_core->exec("CREATE TABLE IF NOT EXISTS absensi_pengaturan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kunci VARCHAR(50) NOT NULL UNIQUE,
            nilai TEXT NULL
        )");

        $settings = [
            'jam_masuk_mulai' => '05:00', 'jam_masuk' => '07:00', 'jam_terlambat' => '07:15', 'jam_pulang' => '15:00', 
            'admin_absen_guru' => '', 'admin_absen_siswa' => '',
            'tts_guru_masuk' => "Selamat pagi Bapak Ibu [nama], kehadiran Anda telah tercatat.\nKehadiran berhasil. Selamat bertugas, Bapak Ibu [nama].\nAbsen masuk sukses. Semangat mengajar, Bapak Ibu [nama].",
            'tts_guru_pulang' => "Terima kasih Bapak Ibu [nama], selamat beristirahat.\nAbsen pulang berhasil. Hati-hati di jalan, [nama].\nSampai jumpa Bapak Ibu [nama], terima kasih atas dedikasinya hari ini.",
            'tts_guru_telat' => "Kehadiran Bapak Ibu [nama] tercatat. Anda terlambat.\nAbsen berhasil, namun Bapak Ibu [nama] terlambat masuk.",
            'tts_guru_sudah' => "Mohon maaf, Bapak Ibu [nama] sudah absen sebelumnya.\nKehadiran Bapak Ibu [nama] sudah tercatat hari ini.\nBapak Ibu [nama] sudah melakukan absensi.",
            'tts_siswa_masuk' => "Mantap! Kehadiran [nama] sudah tercatat.\nHalo [nama], selamat datang di sekolah!\nSip, [nama] hadir! Semangat belajarnya!\nWah, [nama] rajin sekali hari ini!",
            'tts_siswa_pulang' => "Sampai jumpa [nama], hati-hati di jalan ya!\nAbsen pulang sukses. Selamat beristirahat [nama]!\nTerima kasih [nama], sampai bertemu besok!\nDadah [nama], semoga harimu menyenangkan!",
            'tts_siswa_telat' => "Yah, [nama] terlambat. Besok lebih pagi ya!\nWaduh [nama], kok telat sih?\n[nama] hadir, tapi jangan kesiangan lagi ya.",
            'tts_siswa_sudah' => "Hehe, [nama] kan sudah absen.\nLoh, [nama] sudah absen tadi.\nKehadiran [nama] sudah tercatat kok.",
            'tts_gagal' => "Aduh, gagal. [desc]\nScan ditolak. [desc]\nMohon maaf, [desc]"
        ];
        $res = $db_core->query("SELECT * FROM absensi_pengaturan")->fetchAll();
        foreach ($res as $row) { $settings[$row['kunci']] = $row['nilai']; }

        // Fetch users (Admins & Gurus) for Admin Khusus Absen Guru selection
        $userList = $db_core->query("
            SELECT u.id, u.username, u.role_id, 
                   COALESCE(g.nama, s.nama, u.username) as nama
            FROM users u
            LEFT JOIN guru g ON u.id = g.user_id
            LEFT JOIN siswa s ON u.id = s.user_id
            WHERE u.role_id IN (1, 2)
            ORDER BY u.role_id ASC, nama ASC
        ")->fetchAll();

        $title   = "Konfigurasi Jam Absensi";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama    = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/pengaturan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    public static function savePengaturan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            echo "Unauthorized"; exit;
        }

        $db_core = Database::connect();
        $db_siakad = Database::connect('siakad');
        $keys = [
            'jam_masuk_mulai', 'jam_masuk', 'jam_pulang', 'toleransi_terlambat', 'jam_terlambat', 'admin_absen_guru', 'admin_absen_siswa', 'admin_izin_piket',
            'tts_guru_masuk', 'tts_guru_telat', 'tts_guru_sudah',
            'tts_siswa_masuk', 'tts_siswa_pulang', 'tts_siswa_telat', 'tts_siswa_sudah', 'tts_gagal'
        ];
        
        if (isset($_POST['jam_masuk']) && isset($_POST['toleransi_terlambat'])) {
            $jm = $_POST['jam_masuk'];
            $tol = (int)$_POST['toleransi_terlambat'];
            $_POST['jam_terlambat'] = date('H:i', strtotime($jm . " +$tol minutes"));
        }
        
        $jam_masuk_mulai = '05:00';
        $jam_terlambat = '07:30';
        
        foreach ($keys as $k) {
            if (isset($_POST[$k])) {
                $v = $_POST[$k];
                if (is_array($v)) {
                    $v = implode(',', $v);
                }
                $stmt = $db_core->prepare("INSERT INTO absensi_pengaturan (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = ?");
                $stmt->execute([$k, $v, $v]);
                
                if ($k === 'jam_masuk_mulai') $jam_masuk_mulai = $v;
                if ($k === 'jam_terlambat') $jam_terlambat = $v;
            }
        }

        $jam_pulang_mulai = isset($_POST['jam_pulang']) ? $_POST['jam_pulang'] . ':00' : '13:00:00';

        // Sinkronisasi otomatis ke db_siakad agar APK Scanner ikut berubah
        $db_siakad->exec("UPDATE jam_absen SET jam_masuk_mulai = '{$jam_masuk_mulai}:00', jam_masuk_batas = '{$jam_terlambat}:00', jam_masuk_akhir = '12:00:00', jam_pulang_mulai = '{$jam_pulang_mulai}', jam_pulang_akhir = '23:59:00'");
        $db_siakad->exec("UPDATE jam_absen_guru SET jam_masuk_mulai = '{$jam_masuk_mulai}:00', jam_masuk_batas = '{$jam_terlambat}:00', jam_masuk_akhir = '12:00:00', jam_pulang_mulai = '{$jam_pulang_mulai}', jam_pulang_akhir = '23:59:00'");

        if (!headers_sent()) {
            header('Location: /absen/pengaturan?msg=success');
        }
        echo "<script>window.location.href='/absen/pengaturan?msg=success';</script>";
        exit;
    }

    public static function pengaturanGuruCustom() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            echo "Unauthorized"; exit;
        }

        $title = "Konfigurasi Jam Khusus Guru";
        $activeMenu = "absen-pengaturan-guru";
        
        $db_core = Database::connect();
        
        $gurus = $db_core->query("
            SELECT id, nama, custom_jam_masuk, custom_batas_terlambat 
            FROM guru 
            ORDER BY nama ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/pengaturan_guru_custom.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    public static function savePengaturanGuruCustom() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            echo "Unauthorized"; exit;
        }

        $db_core = Database::connect();
        $gurus = $_POST['gurus'] ?? [];
        
        foreach ($gurus as $id => $data) {
            $masuk = !empty($data['jam_masuk']) ? $data['jam_masuk'] : null;
            $terlambat = !empty($data['batas_terlambat']) ? $data['batas_terlambat'] : null;
            
            $stmt = $db_core->prepare("UPDATE guru SET custom_jam_masuk = ?, custom_batas_terlambat = ? WHERE id = ?");
            $stmt->execute([$masuk, $terlambat, $id]);
        }

        if (!headers_sent()) {
            header('Location: /absen/pengaturan-guru?msg=success');
        }
        echo "<script>window.location.href='/absen/pengaturan-guru?msg=success';</script>";
        exit;
    }

    // =====================================================
    // ADMIN IZIN & INVAL PIKET
    // =====================================================

    public static function adminIzinPiket() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $db = Database::connect('siakad');
        $activeMenu = 'absen_izin_piket';
        $today = date('Y-m-d');
        $branding = Branding::getInstitusi();

        // Fetch pending + today's pengajuan
        $stmt = $db->prepare("
            SELECT p.*, g.nama as nama_guru 
            FROM pengajuan_izin_guru p 
            JOIN guru g ON p.guru_id = g.id 
            ORDER BY 
                CASE WHEN p.status_approval = 'Pending' THEN 0 ELSE 1 END,
                p.tanggal DESC, p.created_at DESC
            LIMIT 50
        ");
        $stmt->execute();
        $pengajuanList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Enrich tugas_inval with class/mapel names
        foreach ($pengajuanList as &$p) {
            $tugasData = json_decode($p['tugas_inval'], true) ?: [];
            foreach ($tugasData as &$t) {
                $stK = $db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
                $stK->execute([$t['kelas_id'] ?? 0]);
                $t['nama_kelas'] = $stK->fetchColumn() ?: 'Kelas ?';
                
                $stM = $db->prepare("SELECT nama_mapel FROM mapel WHERE id = ?");
                $stM->execute([$t['mapel_id'] ?? 0]);
                $t['nama_mapel'] = $stM->fetchColumn() ?: 'Mapel ?';
            }
            $p['tugas_inval'] = json_encode($tugasData);
        }
        unset($p);

        $pendingCount = 0;
        foreach ($pengajuanList as $pp) {
            if ($pp['status_approval'] === 'Pending') $pendingCount++;
        }

        // Build Tugas Inval list for today
        $tugasInvalList = [];
        $stmtApproved = $db->prepare("
            SELECT p.*, g.nama as nama_guru 
            FROM pengajuan_izin_guru p 
            JOIN guru g ON p.guru_id = g.id 
            WHERE p.tanggal = ? AND p.status_approval = 'Disetujui'
        ");
        $stmtApproved->execute([$today]);
        $approvedToday = $stmtApproved->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($approvedToday as $ap) {
            $tugasData = json_decode($ap['tugas_inval'], true) ?: [];
            foreach ($tugasData as $t) {
                $stK = $db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
                $stK->execute([$t['kelas_id'] ?? 0]);
                $namaKelas = $stK->fetchColumn() ?: '?';
                
                $stM = $db->prepare("SELECT nama_mapel FROM mapel WHERE id = ?");
                $stM->execute([$t['mapel_id'] ?? 0]);
                $namaMapel = $stM->fetchColumn() ?: '?';

                // Check jam from jadwal
                $stJ = $db->prepare("SELECT jam_mulai, jam_selesai FROM jadwal_pelajaran WHERE id = ?");
                $stJ->execute([$t['jadwal_id'] ?? 0]);
                $jdw = $stJ->fetch(\PDO::FETCH_ASSOC);

                // Check if already inval-ed (jurnal exists with inval keterangan)
                $checkInval = $db->prepare("SELECT id FROM jurnal_guru WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tanggal = ? AND keterangan LIKE '%[INVAL%'");
                $checkInval->execute([$ap['guru_id'], $t['kelas_id'], $t['mapel_id'], $today]);
                $sudahInval = $checkInval->fetch();

                $tugasInvalList[] = [
                    'pengajuan_id' => $ap['id'],
                    'guru_id' => $ap['guru_id'],
                    'nama_guru' => $ap['nama_guru'],
                    'kelas_id' => $t['kelas_id'],
                    'mapel_id' => $t['mapel_id'],
                    'nama_kelas' => $namaKelas,
                    'nama_mapel' => $namaMapel,
                    'jam_mulai' => isset($jdw['jam_mulai']) ? substr($jdw['jam_mulai'], 0, 5) : '--:--',
                    'jam_selesai' => isset($jdw['jam_selesai']) ? substr($jdw['jam_selesai'], 0, 5) : '--:--',
                    'materi' => $t['materi'] ?? '',
                    'sumber' => $ap['jenis_izin'],
                    'tanggal' => $today,
                    'sudah_inval' => $sudahInval ? true : false,
                ];
            }
        }

        // Guru list for Alpa reporting
        $guruList = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

        $activeMenu = 'absen_izin_piket';
        $nama = $_SESSION['nama'] ?? 'Admin';
        $initial = strtoupper(substr($nama, 0, 1));
        $institusi = $branding;

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/admin_izin_piket.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/absen_v2_layout.php';
    }

    public static function approveIzinGuru() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $db = Database::connect('siakad');
        $id = $_POST['id'] ?? 0;
        $action = $_POST['action'] ?? '';
        $approvedBy = $_SESSION['guru_id'] ?? $_SESSION['user_id'] ?? 0;

        if ($action === 'approve') {
            // Update status
            $stmt = $db->prepare("UPDATE pengajuan_izin_guru SET status_approval = 'Disetujui', approved_by = ? WHERE id = ?");
            $stmt->execute([$approvedBy, $id]);

            // Also insert into absensi_guru
            $pengajuan = $db->prepare("SELECT * FROM pengajuan_izin_guru WHERE id = ?");
            $pengajuan->execute([$id]);
            $p = $pengajuan->fetch(\PDO::FETCH_ASSOC);

            if ($p) {
                // Check if absensi_guru entry already exists
                $check = $db->prepare("SELECT id FROM absensi_guru WHERE guru_id = ? AND tanggal = ?");
                $check->execute([$p['guru_id'], $p['tanggal']]);
                if (!$check->fetch()) {
                    $ins = $db->prepare("INSERT INTO absensi_guru (tanggal, guru_id, status, keterangan) VALUES (?, ?, ?, ?)");
                    $statusIzinRaw = explode(' - ', $p['jenis_izin'])[0];
                    $statusIzin = in_array(trim($statusIzinRaw), ['Sakit', 'Izin']) ? trim($statusIzinRaw) : 'Izin';
                    $ins->execute([$p['tanggal'], $p['guru_id'], $statusIzin, 'Pengajuan Izin Disetujui']);
                }
            }
        } elseif ($action === 'reject') {
            $stmt = $db->prepare("UPDATE pengajuan_izin_guru SET status_approval = 'Ditolak' WHERE id = ?");
            $stmt->execute([$id]);
        }

        header('Location: /absen/admin-izin-piket');
        exit;
    }

    public static function laporAlpa() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $db = Database::connect('siakad');
        $guru_id = $_POST['guru_id'] ?? 0;
        $today = date('Y-m-d');
        $approvedBy = $_SESSION['guru_id'] ?? $_SESSION['user_id'] ?? 0;

        if (empty($guru_id)) {
            header('Location: /absen/admin-izin-piket');
            exit;
        }

        // Insert absensi_guru as Alpa
        $check = $db->prepare("SELECT id FROM absensi_guru WHERE guru_id = ? AND tanggal = ?");
        $check->execute([$guru_id, $today]);
        if (!$check->fetch()) {
            $ins = $db->prepare("INSERT INTO absensi_guru (tanggal, guru_id, status, keterangan) VALUES (?, ?, 'Alpa', 'Dilaporkan oleh Guru Piket')");
            $ins->execute([$today, $guru_id]);
        }

        // Get jadwal of alpa guru for today
        $hariArr = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $hari = $hariArr[date('l')] ?? 'Senin';
        $stmt_ta = $db->query("SELECT * FROM siakad_db.tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC);
        $ta_id = $ta ? $ta['id'] : 1;
        $semester = $ta ? $ta['semester'] : 'Ganjil';

        $stmtJ = $db->prepare("
            SELECT jp.id as jadwal_id, jp.kelas_id, jp.mapel_id as mapel_id
            FROM jadwal_pelajaran jp
            WHERE jp.guru_id = ? AND jp.hari = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
        ");
        $stmtJ->execute([$guru_id, $hari, $ta_id, $semester]);
        $jadwalList = $stmtJ->fetchAll(\PDO::FETCH_ASSOC);

        // Create pengajuan_izin_guru entry for Alpa (so inval can be tracked)
        $tugasInval = [];
        foreach ($jadwalList as $j) {
            $tugasInval[] = [
                'jadwal_id' => (int)$j['jadwal_id'],
                'kelas_id' => (int)$j['kelas_id'],
                'mapel_id' => (int)$j['mapel_id'],
                'materi' => ''
            ];
        }

        $checkExisting = $db->prepare("SELECT id FROM pengajuan_izin_guru WHERE guru_id = ? AND tanggal = ?");
        $checkExisting->execute([$guru_id, $today]);
        if (!$checkExisting->fetch()) {
            $ins = $db->prepare("INSERT INTO pengajuan_izin_guru (guru_id, tanggal, jenis_izin, tugas_inval, status_approval, approved_by) VALUES (?, ?, 'Izin', ?, 'Disetujui', ?)");
            $ins->execute([$guru_id, $today, json_encode($tugasInval), $approvedBy]);
        }

        header('Location: /absen/admin-izin-piket');
        exit;
    }

    public static function eksekusiInval() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $db = Database::connect('siakad');
        $branding = Branding::getInstitusi();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process Inval submission
            $pengajuanId = $_POST['pengajuan_id'] ?? 0;
            $kelasId = $_POST['kelas_id'] ?? 0;
            $mapelId = $_POST['mapel_id'] ?? 0;
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $guruIzinId = $_POST['guru_izin_id'] ?? 0;
            $jurnalMateri = $_POST['jurnal_materi'] ?? '';
            $absen = $_POST['absen'] ?? [];
            $piketGuruId = $_SESSION['guru_id'] ?? $_SESSION['user_id'] ?? 0;

            // Get piket guru name
            $stmtPiket = $db->prepare("SELECT nama FROM guru WHERE id = ?");
            $stmtPiket->execute([$piketGuruId]);
            $piketName = $stmtPiket->fetchColumn() ?: 'Guru Piket';

            // Get izin guru name
            $stmtIzin = $db->prepare("SELECT nama FROM guru WHERE id = ?");
            $stmtIzin->execute([$guruIzinId]);
            $izinName = $stmtIzin->fetchColumn() ?: 'Guru';

            // 1. Insert jurnal_guru (atas nama guru yang izin, tapi dengan catatan inval)
            $keteranganJurnal = "[INVAL oleh " . $piketName . "] " . $jurnalMateri;
            $stmtJurnal = $db->prepare("INSERT INTO jurnal_guru (guru_id, kelas_id, mapel_id, tanggal, materi, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtJurnal->execute([$guruIzinId, $kelasId, $mapelId, $tanggal, $jurnalMateri, $keteranganJurnal]);
            $jurnalId = $db->lastInsertId();

            $cek_global = $db->prepare("SELECT status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ? AND status IN ('Sakit', 'Izin', 'Alpa') LIMIT 1");

            // 2. Insert absensi_permapel for each student
            foreach ($absen as $siswaId => $status) {
                // Override dengan status mutlak jika ada
                $cek_global->execute([$siswaId, $tanggal]);
                $global_status = $cek_global->fetchColumn();
                if ($global_status) {
                    $status = $global_status;
                }

                $stmtAbsen = $db->prepare("INSERT INTO absensi_permapel (jurnal_id, guru_id, siswa_id, kelas_id, mapel_id, tanggal, status, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtAbsen->execute([$jurnalId, $guruIzinId, $siswaId, $kelasId, $mapelId, $tanggal, $status, 'Diabsenkan oleh ' . $piketName . ' (Piket Inval)']);
            }

            // 3. Update pengajuan_izin_guru with inval_by
            $stmtUpd = $db->prepare("UPDATE pengajuan_izin_guru SET inval_by = ? WHERE id = ?");
            $stmtUpd->execute([$piketGuruId, $pengajuanId]);

            header('Location: /absen/admin-izin-piket?success=1');
            exit;
        }

        // GET: Show eksekusi form
        $pengajuanId = $_GET['id'] ?? 0;
        $kelasId = $_GET['kelas_id'] ?? 0;
        $mapelId = $_GET['mapel_id'] ?? 0;
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

        // Get pengajuan info
        $stmtP = $db->prepare("SELECT p.*, g.nama as nama_guru FROM pengajuan_izin_guru p JOIN guru g ON p.guru_id = g.id WHERE p.id = ?");
        $stmtP->execute([$pengajuanId]);
        $pengajuan = $stmtP->fetch(\PDO::FETCH_ASSOC);

        $namaGuruIzin = $pengajuan['nama_guru'] ?? 'Guru';
        $guruIzinId = $pengajuan['guru_id'] ?? 0;
        $sumberIzin = $pengajuan['jenis_izin'] ?? 'Izin';

        // Get kelas & mapel names
        $namaKelas = $db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
        $namaKelas->execute([$kelasId]);
        $namaKelas = $namaKelas->fetchColumn() ?: 'Kelas ?';

        $namaMapel = $db->prepare("SELECT nama_mapel FROM mapel WHERE id = ?");
        $namaMapel->execute([$mapelId]);
        $namaMapel = $namaMapel->fetchColumn() ?: 'Mapel ?';

        // Get jam from jadwal
        $tugasData = json_decode($pengajuan['tugas_inval'] ?? '[]', true);
        $materiGuru = '';
        $jamMulai = '--:--';
        $jamSelesai = '--:--';
        foreach ($tugasData as $t) {
            if (($t['kelas_id'] ?? 0) == $kelasId && ($t['mapel_id'] ?? 0) == $mapelId) {
                $materiGuru = $t['materi'] ?? '';
                $stJ = $db->prepare("SELECT jam_mulai, jam_selesai FROM jadwal_pelajaran WHERE id = ?");
                $stJ->execute([$t['jadwal_id'] ?? 0]);
                $jdw = $stJ->fetch(\PDO::FETCH_ASSOC);
                if ($jdw) {
                    $jamMulai = substr($jdw['jam_mulai'], 0, 5);
                    $jamSelesai = substr($jdw['jam_selesai'], 0, 5);
                }
                break;
            }
        }

        // Get siswa list for kelas
        $stmtSiswa = $db->prepare("SELECT id, nama, nis FROM siswa WHERE kelas_id = ? ORDER BY nama ASC");
        $stmtSiswa->execute([$kelasId]);
        $siswaList = $stmtSiswa->fetchAll(\PDO::FETCH_ASSOC);

        $activeMenu = 'absen_izin_piket';
        $nama = $_SESSION['nama'] ?? 'Admin';
        $initial = strtoupper(substr($nama, 0, 1));
        $institusi = $branding;

        ob_start();
        include __DIR__ . '/../../resources/views/absen_v2/eksekusi_inval.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }
}
