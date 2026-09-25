<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\AcademicYear;

class AdminTabunganController {
    
    // Guard access
    private static function guard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $role_id = $_SESSION['role_id'];
        $user_id = $_SESSION['user_id'];
        
        if (in_array($role_id, [1, 99])) return;
        
        $db = Database::connect('core');
        $check = $db->query("SELECT id FROM app_admins WHERE user_id = $user_id AND app_code = 'tabungan'")->fetch();
        if (!$check) {
            header('Location: /login');
            exit;
        }
    }

    // Render View with parameters
    private static function render($view, $data = array(), $required_menu = '') {
        self::guard($required_menu);
        $data['adminNama']    = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Administrator';
        $data['adminInitial'] = strtoupper(substr($data['adminNama'], 0, 2));
        $data['active_module'] = 'finance';
        
        $allowed_menus = ['all']; // Default for superadmin
        if (isset($_SESSION['role_id']) && !in_array($_SESSION['role_id'], [1, 99]) && isset($_SESSION['user_id'])) {
            $db = Database::connect('core');
            $uid = $_SESSION['user_id'];
            $guru = $db->query("SELECT id FROM guru WHERE user_id = $uid")->fetch();
            if ($guru) {
                $akses = $db->query("SELECT akses_menu FROM keuangan_akses WHERE guru_id = {$guru['id']}")->fetch();
                if ($akses && !empty($akses['akses_menu'])) {
                    $allowed_menus = json_decode($akses['akses_menu'], true) ?: [];
                } else {
                    $allowed_menus = [];
                }
            } else {
                $allowed_menus = [];
            }
        }
        $data['allowed_menus'] = $allowed_menus;
        
        extract($data);
        ob_start();
        include __DIR__ . '/../../resources/views/keuangan/' . $view . '.php';
        $content = ob_get_clean();
        
        $activeMenu = isset($active_page) ? $active_page : $view;
        // Fix active menu string to match layout.php sidebar matches
        if (strpos($activeMenu, 'keuangan_') !== 0 && strpos($activeMenu, 'tabungan_') !== 0 && strpos($activeMenu, 'absen_') !== 0 && strpos($activeMenu, 'siakad_') !== 0) {
            $activeMenu = 'keuangan_' . $activeMenu;
        }
        
        $title = isset($page_title) ? $page_title : 'Tabungan';
        require __DIR__ . '/../../resources/views/layout.php';
    }

    public static function overview() {
        self::guard();
        $db = Database::connect('core');

        $petugas_id = isset($_GET['petugas_id']) ? intval($_GET['petugas_id']) : 0;
        
        // Petugas list for filter form
        $petugasList = [];
        if (in_array($_SESSION['role_id'] ?? 0, [1, 99])) {
            $petugasList = $db->query("
                SELECT u.id, COALESCE(g.nama, u.username) as nama 
                FROM users u 
                JOIN app_admins aa ON aa.user_id = u.id 
                LEFT JOIN guru g ON g.user_id = u.id 
                WHERE aa.app_code = 'tabungan'
            ")->fetchAll();
        } else {
            $petugas_id = $_SESSION['user_id'];
        }

        $today = date('Y-m-d');
        
        if ($petugas_id > 0) {
            // Filtered by Petugas
            $totalSaldo = $db->query("SELECT SUM(CASE WHEN jenis_mutasi = 'Setor' THEN jumlah ELSE -jumlah END) as total FROM keuangan_tabungan_mutasi WHERE petugas_id = $petugas_id")->fetch()['total'];
            
            $setorHariIni = $db->query("SELECT SUM(jumlah) as total FROM keuangan_tabungan_mutasi WHERE jenis_mutasi = 'Setor' AND DATE(tanggal) = '$today' AND petugas_id = $petugas_id")->fetch()['total'];
            $tarikHariIni = $db->query("SELECT SUM(jumlah) as total FROM keuangan_tabungan_mutasi WHERE jenis_mutasi = 'Tarik' AND DATE(tanggal) = '$today' AND petugas_id = $petugas_id")->fetch()['total'];
            
            $totalNasabah = $db->query("SELECT COUNT(DISTINCT siswa_id) as total FROM keuangan_tabungan_mutasi WHERE petugas_id = $petugas_id")->fetch()['total'];
            $totalSiswa = $db->query("SELECT COUNT(id) as total FROM siswa WHERE status = 'Aktif'")->fetch()['total'];

            $riwayat = $db->query("
                SELECT m.*, s.nama as nama_siswa, s.nis 
                FROM keuangan_tabungan_mutasi m
                JOIN siswa s ON m.siswa_id = s.id
                WHERE m.petugas_id = $petugas_id
                ORDER BY m.tanggal DESC LIMIT 5
            ")->fetchAll();
        } else {
            // Statistik All
            $totalSaldo = $db->query("SELECT SUM(saldo) as total FROM keuangan_tabungan")->fetch()['total'];
            
            $setorHariIni = $db->query("SELECT SUM(jumlah) as total FROM keuangan_tabungan_mutasi WHERE jenis_mutasi = 'Setor' AND DATE(tanggal) = '$today'")->fetch()['total'];
            $tarikHariIni = $db->query("SELECT SUM(jumlah) as total FROM keuangan_tabungan_mutasi WHERE jenis_mutasi = 'Tarik' AND DATE(tanggal) = '$today'")->fetch()['total'];

            $totalNasabah = $db->query("SELECT COUNT(id) as total FROM keuangan_tabungan")->fetch()['total'];
            $totalSiswa = $db->query("SELECT COUNT(id) as total FROM siswa WHERE status = 'Aktif'")->fetch()['total'];

            $riwayat = $db->query("
                SELECT m.*, s.nama as nama_siswa, s.nis 
                FROM keuangan_tabungan_mutasi m
                JOIN siswa s ON m.siswa_id = s.id
                ORDER BY m.tanggal DESC LIMIT 5
            ")->fetchAll();
        }

        self::render('tabungan_dashboard', [
            'active_module' => 'tabungan',
            'active_page' => 'overview',
            'totalSaldo' => $totalSaldo ? $totalSaldo : 0,
            'setorHariIni' => $setorHariIni ? $setorHariIni : 0,
            'tarikHariIni' => $tarikHariIni ? $tarikHariIni : 0,
            'totalNasabah' => $totalNasabah ? $totalNasabah : 0,
            'totalSiswa' => $totalSiswa ? $totalSiswa : 0,
            'riwayat' => $riwayat,
            'petugasList' => $petugasList,
            'filter_petugas' => $petugas_id
        ]);
    }

    public static function siswa() {
        self::guard();
        $db = Database::connect('core');

        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $role_id = $_SESSION['role_id'];
        $uid = $_SESSION['user_id'];
        
        if (in_array($role_id, [1, 99])) {
            $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
        } else {
            $kelasList = $db->query("SELECT c.id, c.nama_kelas FROM kelas c JOIN admin_tabungan_akses a ON a.kelas_id = c.id WHERE a.user_id = $uid ORDER BY c.tingkat, c.nama_kelas")->fetchAll();
            $allowed_kelas = array_column($kelasList, 'id');
            if ($kelas_id > 0 && !in_array($kelas_id, $allowed_kelas)) {
                $kelas_id = 0; // Prevent access to unauthorized class
            }
        }

        $siswaList = [];
        if ($kelas_id > 0) {
            // Ambil Data Siswa beserta saldo tabungannya
            $sql = "
                SELECT s.id as siswa_id, s.nama, s.nis, c.nama_kelas, t.id as tabungan_id, IFNULL(t.saldo, 0) as saldo
                FROM siswa s
                LEFT JOIN kelas c ON s.kelas_id = c.id
                LEFT JOIN keuangan_tabungan t ON s.id = t.siswa_id
                WHERE s.status = 'Aktif' AND s.kelas_id = $kelas_id
                ORDER BY c.nama_kelas, s.nama
            ";
            $siswaList = $db->query($sql)->fetchAll();
        }

        self::render('tabungan_siswa', [
            'active_module' => 'tabungan',
            'active_page' => 'siswa',
            'kelasList' => $kelasList,
            'siswaList' => $siswaList,
            'filter_kelas' => $kelas_id
        ]);
    }

    public static function transaksi() {
        self::guard();
        $db = Database::connect('core');

        $siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : 0;
        $jenis_mutasi = isset($_POST['jenis_mutasi']) ? $_POST['jenis_mutasi'] : ''; // Setor or Tarik
        $jumlah = isset($_POST['jumlah']) ? floatval($_POST['jumlah']) : 0;
        $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';
        $petugas_id = $_SESSION['user_id'];
        $kelas_id = isset($_POST['kelas_id']) ? intval($_POST['kelas_id']) : 0;

        if ($siswa_id > 0 && $jumlah > 0 && in_array($jenis_mutasi, ['Setor', 'Tarik'])) {
            try {
                $db->beginTransaction();

                // Cek saldo saat ini
                $stmt = $db->prepare("SELECT id, saldo, no_rekening FROM keuangan_tabungan WHERE siswa_id = ?");
                $stmt->execute([$siswa_id]);
                $tabungan = $stmt->fetch();

                $saldo_sebelum = 0;
                $tabungan_id = 0;

                if ($tabungan) {
                    $saldo_sebelum = floatval($tabungan['saldo']);
                    $tabungan_id = $tabungan['id'];
                } else {
                    // Buat rekening baru jika belum ada
                    // no_rekening kita set = NIS
                    $nis = $db->query("SELECT nis FROM siswa WHERE id = $siswa_id")->fetch()['nis'];
                    $stmt_new = $db->prepare("INSERT INTO keuangan_tabungan (siswa_id, no_rekening, saldo) VALUES (?, ?, 0)");
                    $stmt_new->execute([$siswa_id, $nis]);
                    $tabungan_id = $db->lastInsertId();
                }

                $saldo_sesudah = $saldo_sebelum;

                if ($jenis_mutasi === 'Setor') {
                    $saldo_sesudah = $saldo_sebelum + $jumlah;
                } else {
                    // Tarik
                    if ($jumlah > $saldo_sebelum) {
                        // Saldo tidak cukup!
                        $db->rollBack();
                        header('Location: /admin/tabungan/siswa?kelas_id=' . $kelas_id . '&msg=insufficient_balance');
                        exit;
                    }
                    $saldo_sesudah = $saldo_sebelum - $jumlah;
                }

                // Update saldo rekening
                $stmt_upd = $db->prepare("UPDATE keuangan_tabungan SET saldo = ? WHERE id = ?");
                $stmt_upd->execute([$saldo_sesudah, $tabungan_id]);

                // Insert mutasi
                $stmt_mut = $db->prepare("INSERT INTO keuangan_tabungan_mutasi (siswa_id, jenis_mutasi, jumlah, saldo_sebelum, saldo_sesudah, keterangan, petugas_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_mut->execute([$siswa_id, $jenis_mutasi, $jumlah, $saldo_sebelum, $saldo_sesudah, $keterangan, $petugas_id]);

                $db->commit();

                // --- Kirim Notifikasi Personal ---
                require_once __DIR__ . '/../Services/OneSignalService.php';
                $rp_jumlah = 'Rp ' . number_format($jumlah, 0, ',', '.');
                $rp_saldo = 'Rp ' . number_format($saldo_sesudah, 0, ',', '.');
                $judul_notif = "Informasi Tabungan";
                $pesan_notif = "Mutasi: " . $jenis_mutasi . " sebesar " . $rp_jumlah . " berhasil. Saldo akhir: " . $rp_saldo;
                \App\Services\OneSignalService::sendNotification($judul_notif, $pesan_notif, null, $siswa_id);
                // ---------------------------------

                header('Location: /admin/tabungan/siswa?kelas_id=' . $kelas_id . '&msg=success');
                exit;

            } catch (\Exception $e) {
                $db->rollBack();
                header('Location: /admin/tabungan/siswa?kelas_id=' . $kelas_id . '&msg=error');
                exit;
            }
        }

        header('Location: /admin/tabungan/siswa?kelas_id=' . $kelas_id . '&msg=error');
        exit;
    }

    public static function mutasi() {
        self::guard();
        $db = Database::connect('core');

        $tanggal_mulai = isset($_GET['mulai']) ? $_GET['mulai'] : date('Y-m-d', strtotime('-30 days'));
        $tanggal_akhir = isset($_GET['akhir']) ? $_GET['akhir'] : date('Y-m-d');

        $sql = "
            SELECT m.*, s.nama as nama_siswa, s.nis, c.nama_kelas 
            FROM keuangan_tabungan_mutasi m
            JOIN siswa s ON m.siswa_id = s.id
            LEFT JOIN kelas c ON s.kelas_id = c.id
            WHERE DATE(m.tanggal) >= ? AND DATE(m.tanggal) <= ?
            ORDER BY m.tanggal DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$tanggal_mulai, $tanggal_akhir]);
        $riwayat = $stmt->fetchAll();

        self::render('tabungan_mutasi', [
            'active_module' => 'tabungan',
            'active_page' => 'mutasi',
            'riwayat' => $riwayat,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir
        ]);
    }

    public static function rekap() {
        self::guard();
        $db = Database::connect('core');

        $tanggal_mulai = isset($_GET['mulai']) ? $_GET['mulai'] : date('Y-m-01');
        $tanggal_akhir = isset($_GET['akhir']) ? $_GET['akhir'] : date('Y-m-d');
        $petugas_id = isset($_GET['petugas_id']) ? intval($_GET['petugas_id']) : 0;
        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        
        $where_clause = "";
        $params = [$tanggal_mulai, $tanggal_akhir];
        
        // If not super admin, restrict to own data
        if (!in_array($_SESSION['role_id'], [1, 99])) {
            $petugas_id = $_SESSION['user_id'];
        }

        if ($petugas_id > 0) {
            $where_clause .= " AND m.petugas_id = ? ";
            $params[] = $petugas_id;
        }
        
        if ($kelas_id > 0) {
            $where_clause .= " AND s.kelas_id = ? ";
            $params[] = $kelas_id;
        }

        $sql = "
            SELECT 
                DATE(m.tanggal) as tgl,
                u.id as petugas_id,
                COALESCE(g.nama, u.username, 'Sistem / Tidak Diketahui') as nama_petugas,
                s.nama as nama_siswa,
                c.nama_kelas,
                SUM(CASE WHEN m.jenis_mutasi = 'Setor' THEN m.jumlah ELSE 0 END) as total_setor,
                SUM(CASE WHEN m.jenis_mutasi = 'Tarik' THEN m.jumlah ELSE 0 END) as total_tarik,
                COUNT(m.id) as jumlah_transaksi
            FROM keuangan_tabungan_mutasi m
            LEFT JOIN users u ON m.petugas_id = u.id
            LEFT JOIN guru g ON g.user_id = u.id
            LEFT JOIN siswa s ON m.siswa_id = s.id
            LEFT JOIN kelas c ON s.kelas_id = c.id
            WHERE DATE(m.tanggal) >= ? AND DATE(m.tanggal) <= ?
            $where_clause
            GROUP BY DATE(m.tanggal), m.petugas_id, m.siswa_id
            ORDER BY DATE(m.tanggal) DESC, nama_petugas ASC, c.tingkat ASC, c.nama_kelas ASC, s.nama ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rekap = $stmt->fetchAll();
        
        // Petugas list for dropdown filter
        $petugasList = [];
        if (in_array($_SESSION['role_id'], [1, 99])) {
            $petugasList = $db->query("
                SELECT u.id, COALESCE(g.nama, u.username) as nama 
                FROM users u 
                JOIN app_admins aa ON aa.user_id = u.id 
                LEFT JOIN guru g ON g.user_id = u.id 
                WHERE aa.app_code = 'tabungan'
            ")->fetchAll();
        }
        $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();

        self::render('tabungan_rekap', [
            'active_module' => 'tabungan',
            'active_page' => 'rekap',
            'rekap' => $rekap,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'petugas_id' => $petugas_id,
            'kelas_id' => $kelas_id,
            'petugasList' => $petugasList,
            'kelasList' => $kelasList
        ]);
    }

    public static function rekapCetak() {
        self::guard();
        $db = Database::connect('core');

        $tanggal_mulai = isset($_GET['mulai']) ? $_GET['mulai'] : date('Y-m-01');
        $tanggal_akhir = isset($_GET['akhir']) ? $_GET['akhir'] : date('Y-m-d');
        $petugas_id = isset($_GET['petugas_id']) ? intval($_GET['petugas_id']) : 0;
        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        
        $where_clause = "";
        $params = [$tanggal_mulai, $tanggal_akhir];
        
        if (!in_array($_SESSION['role_id'], [1, 99])) {
            $petugas_id = $_SESSION['user_id'];
        }

        if ($petugas_id > 0) {
            $where_clause .= " AND m.petugas_id = ? ";
            $params[] = $petugas_id;
        }
        
        if ($kelas_id > 0) {
            $where_clause .= " AND s.kelas_id = ? ";
            $params[] = $kelas_id;
        }

        $sql = "
            SELECT 
                DATE(m.tanggal) as tgl,
                u.id as petugas_id,
                COALESCE(g.nama, u.username, 'Sistem / Tidak Diketahui') as nama_petugas,
                s.nama as nama_siswa,
                c.nama_kelas,
                SUM(CASE WHEN m.jenis_mutasi = 'Setor' THEN m.jumlah ELSE 0 END) as total_setor,
                SUM(CASE WHEN m.jenis_mutasi = 'Tarik' THEN m.jumlah ELSE 0 END) as total_tarik,
                COUNT(m.id) as jumlah_transaksi
            FROM keuangan_tabungan_mutasi m
            LEFT JOIN users u ON m.petugas_id = u.id
            LEFT JOIN guru g ON g.user_id = u.id
            LEFT JOIN siswa s ON m.siswa_id = s.id
            LEFT JOIN kelas c ON s.kelas_id = c.id
            WHERE DATE(m.tanggal) >= ? AND DATE(m.tanggal) <= ?
            $where_clause
            GROUP BY DATE(m.tanggal), m.petugas_id, m.siswa_id
            ORDER BY DATE(m.tanggal) DESC, nama_petugas ASC, c.tingkat ASC, c.nama_kelas ASC, s.nama ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rekap = $stmt->fetchAll();

        // Get labels for header
        $label_petugas = "Semua Petugas";
        if ($petugas_id > 0) {
            $p = $db->query("SELECT COALESCE(g.nama, u.username) FROM users u LEFT JOIN guru g ON g.user_id = u.id WHERE u.id = $petugas_id")->fetchColumn();
            if ($p) $label_petugas = $p;
        }

        $label_kelas = "Semua Kelas";
        if ($kelas_id > 0) {
            $k = $db->query("SELECT nama_kelas FROM kelas WHERE id = $kelas_id")->fetchColumn();
            if ($k) $label_kelas = $k;
        }

        // Fetch list for filter form
        $petugasList = [];
        if (in_array($_SESSION['role_id'] ?? 0, [1, 99])) {
            $petugasList = $db->query("
                SELECT u.id, COALESCE(g.nama, u.username) as nama 
                FROM users u 
                JOIN app_admins aa ON aa.user_id = u.id 
                LEFT JOIN guru g ON g.user_id = u.id 
                WHERE aa.app_code = 'tabungan'
            ")->fetchAll();
        }
        $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();

        include __DIR__ . '/../../resources/views/keuangan/tabungan_rekap_cetak.php';
    }

    public static function rekapCetakCover() {
        self::guard();
        $db = Database::connect('core');

        $tanggal_mulai = isset($_GET['mulai']) ? $_GET['mulai'] : date('Y-m-01');
        $tanggal_akhir = isset($_GET['akhir']) ? $_GET['akhir'] : date('Y-m-d');
        $petugas_id = isset($_GET['petugas_id']) ? intval($_GET['petugas_id']) : 0;
        
        $label_petugas = "Semua Petugas";
        if ($petugas_id > 0) {
            $p = $db->query("SELECT COALESCE(g.nama, u.username) FROM users u LEFT JOIN guru g ON g.user_id = u.id WHERE u.id = $petugas_id")->fetchColumn();
            if ($p) $label_petugas = $p;
        }

        // Fetch list for filter form
        $petugasList = [];
        if (in_array($_SESSION['role_id'] ?? 0, [1, 99])) {
            $petugasList = $db->query("
                SELECT u.id, COALESCE(g.nama, u.username) as nama 
                FROM users u 
                JOIN app_admins aa ON aa.user_id = u.id 
                LEFT JOIN guru g ON g.user_id = u.id 
                WHERE aa.app_code = 'tabungan'
            ")->fetchAll();
        }

        $ay = AcademicYear::current();
        $active_year_name = $ay ? $ay['name'] : '2025/2026';

        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        include __DIR__ . '/../../resources/views/keuangan/tabungan_rekap_cetak_cover.php';
    }

    public static function rekapCetakMatriks() {
        self::guard();
        $db = Database::connect('core');

        $tanggal_mulai = isset($_GET['mulai']) ? $_GET['mulai'] : date('Y-m-01');
        $tanggal_akhir = isset($_GET['akhir']) ? $_GET['akhir'] : date('Y-m-d');
        $petugas_id = isset($_GET['petugas_id']) ? intval($_GET['petugas_id']) : 0;
        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        
        $where_clause = "";
        $params = [$tanggal_mulai, $tanggal_akhir];
        
        if (!in_array($_SESSION['role_id'], [1, 99])) {
            $petugas_id = $_SESSION['user_id'];
        }

        if ($petugas_id > 0) {
            $where_clause .= " AND m.petugas_id = ? ";
            $params[] = $petugas_id;
        }
        
        if ($kelas_id > 0) {
            $where_clause .= " AND s.kelas_id = ? ";
            $params[] = $kelas_id;
        }

        $tampil_semua = isset($_GET['tampil_semua']) && $_GET['tampil_semua'] == '1';

        // Get all transactions matching the filter
        $sql = "
            SELECT 
                DATE(m.tanggal) as tgl,
                m.siswa_id,
                s.nama as nama_siswa,
                c.nama_kelas,
                c.tingkat,
                m.jenis_mutasi,
                m.jumlah
            FROM keuangan_tabungan_mutasi m
            LEFT JOIN siswa s ON m.siswa_id = s.id
            LEFT JOIN kelas c ON s.kelas_id = c.id
            WHERE DATE(m.tanggal) >= ? AND DATE(m.tanggal) <= ?
            $where_clause
            ORDER BY c.tingkat ASC, c.nama_kelas ASC, s.nama ASC, m.tanggal ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $raw_data = $stmt->fetchAll();

        // Build date range array (Only include dates with transactions)
        $activeDates = [];
        foreach ($raw_data as $row) {
            $activeDates[] = $row['tgl'];
        }
        $activeDates = array_unique($activeDates);

        $dateRangeList = [];
        $startDate = new \DateTime($tanggal_mulai);
        $endDate = new \DateTime($tanggal_akhir);
        $endDate->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $interval, $endDate);
        foreach($dateRange as $dt) {
            $ymd = $dt->format('Y-m-d');
            if (in_array($ymd, $activeDates)) {
                $dateRangeList[] = $ymd;
            }
        }
        // Get Saldo Awal before tanggal_mulai for all students
        $sql_saldo = "
            SELECT 
                siswa_id,
                SUM(CASE WHEN jenis_mutasi = 'Setor' THEN jumlah ELSE -jumlah END) as saldo_awal
            FROM keuangan_tabungan_mutasi
            WHERE DATE(tanggal) < ?
            GROUP BY siswa_id
        ";
        $stmt_saldo = $db->prepare($sql_saldo);
        $stmt_saldo->execute([$tanggal_mulai]);
        $saldo_awal_data = [];
        foreach($stmt_saldo->fetchAll() as $s_row) {
            $saldo_awal_data[$s_row['siswa_id']] = $s_row['saldo_awal'];
        }

        // Pivot structure
        $siswaData = [];
        
        if ($tampil_semua) {
            $wKelas = $kelas_id > 0 ? "AND s.kelas_id = $kelas_id" : "";
            $allSiswa = $db->query("SELECT s.id, s.nama, c.nama_kelas, c.tingkat FROM siswa s LEFT JOIN kelas c ON s.kelas_id = c.id WHERE s.status = 'Aktif' $wKelas ORDER BY c.tingkat ASC, c.nama_kelas ASC, s.nama ASC")->fetchAll();
            foreach ($allSiswa as $s) {
                $siswaData[$s['id']] = [
                    'nama_siswa' => $s['nama'],
                    'nama_kelas' => $s['nama_kelas'],
                    'tingkat' => $s['tingkat'],
                    'saldo_awal' => $saldo_awal_data[$s['id']] ?? 0,
                    'total_setor_all' => 0,
                    'total_tarik_all' => 0,
                    'dates' => []
                ];
            }
        }

        foreach($raw_data as $row) {
            $sid = $row['siswa_id'];
            if (!isset($siswaData[$sid])) {
                $siswaData[$sid] = [
                    'nama_siswa' => $row['nama_siswa'],
                    'nama_kelas' => $row['nama_kelas'],
                    'tingkat' => $row['tingkat'],
                    'saldo_awal' => $saldo_awal_data[$sid] ?? 0,
                    'total_setor_all' => 0,
                    'total_tarik_all' => 0,
                    'dates' => []
                ];
            }
            if (!isset($siswaData[$sid]['dates'][$row['tgl']])) {
                $siswaData[$sid]['dates'][$row['tgl']] = [];
            }
            $siswaData[$sid]['dates'][$row['tgl']][] = [
                'jenis' => $row['jenis_mutasi'],
                'jumlah' => $row['jumlah']
            ];
            
            if ($row['jenis_mutasi'] == 'Setor') {
                $siswaData[$sid]['total_setor_all'] += $row['jumlah'];
            } else {
                $siswaData[$sid]['total_tarik_all'] += $row['jumlah'];
            }
        }

        // Sort if tampil_semua was true (to ensure the newly merged data remains sorted)
        usort($siswaData, function($a, $b) {
            if ($a['tingkat'] != $b['tingkat']) return $a['tingkat'] <=> $b['tingkat'];
            if ($a['nama_kelas'] != $b['nama_kelas']) return strcmp($a['nama_kelas'], $b['nama_kelas']);
            return strcmp($a['nama_siswa'], $b['nama_siswa']);
        });

        // Get labels for header
        $label_petugas = "Semua Petugas";
        if ($petugas_id > 0) {
            $p = $db->query("SELECT COALESCE(g.nama, u.username) FROM users u LEFT JOIN guru g ON g.user_id = u.id WHERE u.id = $petugas_id")->fetchColumn();
            if ($p) $label_petugas = $p;
        }

        $label_kelas = "Semua Kelas";
        if ($kelas_id > 0) {
            $k = $db->query("SELECT nama_kelas FROM kelas WHERE id = $kelas_id")->fetchColumn();
            if ($k) $label_kelas = $k;
        }

        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        // Fetch list for filter form
        $petugasList = [];
        if (in_array($_SESSION['role_id'] ?? 0, [1, 99])) {
            $petugasList = $db->query("
                SELECT u.id, COALESCE(g.nama, u.username) as nama 
                FROM users u 
                JOIN app_admins aa ON aa.user_id = u.id 
                LEFT JOIN guru g ON g.user_id = u.id 
                WHERE aa.app_code = 'tabungan'
            ")->fetchAll();
        }
        $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();

        include __DIR__ . '/../../resources/views/keuangan/tabungan_rekap_cetak_matriks.php';
    }

    public static function cetak() {
        self::guard();
        $db = Database::connect('core');
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        $mutasi = $db->query("
            SELECT m.*, s.nama as nama_siswa, s.nis, c.nama_kelas, p.nama as nama_petugas
            FROM keuangan_tabungan_mutasi m
            JOIN siswa s ON m.siswa_id = s.id
            LEFT JOIN kelas c ON s.kelas_id = c.id
            LEFT JOIN users p ON m.petugas_id = p.id
            WHERE m.id = $id
        ")->fetch();

        if (!$mutasi) {
            echo "Data tidak ditemukan.";
            exit;
        }

        include __DIR__ . '/../../resources/views/keuangan/tabungan_cetak.php';
    }

    public static function bukaRekening() {
        self::guard();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/tabungan/siswa');
            exit;
        }

        $db = Database::connect('core');
        $siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : 0;
        $kelas_id = isset($_POST['kelas_id']) ? intval($_POST['kelas_id']) : 0;
        $is_ajax = isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

        if ($siswa_id > 0) {
            // Check if already exists
            $check = $db->query("SELECT id FROM keuangan_tabungan WHERE siswa_id = $siswa_id")->fetch();
            if (!$check) {
                $no_rekening = 'TAB-' . date('ym') . '-' . str_pad($siswa_id, 4, '0', STR_PAD_LEFT);
                $stmt = $db->prepare("INSERT INTO keuangan_tabungan (siswa_id, no_rekening, saldo) VALUES (?, ?, 0)");
                $stmt->execute([$siswa_id, $no_rekening]);
            }
        }
        
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
            exit;
        }

        header("Location: /admin/tabungan/siswa?kelas_id=$kelas_id&msg=success_buka");
        exit;
    }

    public static function kasir() {
        self::guard();
        
        self::render('tabungan_kasir', [
            'active_module' => 'tabungan',
            'active_page' => 'kasir'
        ]);
    }

    public static function apiSiswa() {
        self::guard();
        header('Content-Type: application/json');
        
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        if (strlen($q) < 2) {
            echo json_encode([]);
            exit;
        }

        $db = Database::connect('core');
        $ay = AcademicYear::current();
        $ayId = $ay ? $ay['id'] : 0;

        $role_id = $_SESSION['role_id'];
        $uid = $_SESSION['user_id'];

        $likeStr = '%' . $q . '%';
        $params = [$likeStr, $likeStr];
        
        $join_akses = "";
        if (!in_array($role_id, [1, 99])) {
            $join_akses = " INNER JOIN admin_tabungan_akses ata ON ata.kelas_id = s.kelas_id AND ata.user_id = " . intval($uid) . " ";
        }

        $query = "
            SELECT s.id as siswa_id, s.nis, s.nama, s.foto,
                   COALESCE(c.nama_kelas, '') as nama_kelas,
                   COALESCE(kt.saldo, 0) as saldo
            FROM siswa s
            LEFT JOIN kelas c ON s.kelas_id = c.id
            $join_akses
            INNER JOIN keuangan_tabungan kt ON s.id = kt.siswa_id
            WHERE (s.nama LIKE ? OR s.nis LIKE ?) AND s.status = 'Aktif'
            LIMIT 10
        ";

        $stmt = $db->prepare($query);
        $stmt->execute([$likeStr, $likeStr]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Format the foto URL
        foreach ($data as &$row) {
            if ($row['foto']) {
                $row['foto_url'] = '/uploads/siswa/' . $row['foto'];
            } else {
                $row['foto_url'] = 'https://ui-avatars.com/api/?name=' . urlencode($row['nama']) . '&background=eff6ff&color=3b82f6';
            }
        }
        
        echo json_encode($data);
        exit;
    }
    
    public static function akses() {
        self::guard();
        if (!in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /admin/tabungan');
            exit;
        }
        
        $db = Database::connect('core');
        
        // Dapatkan semua user yang memiliki akses tabungan
        $admins = $db->query("
            SELECT u.id, u.username,
            COALESCE(g.nama, 'Admin') as nama,
            COALESCE(ats.can_delete, 0) as can_delete,
            (SELECT GROUP_CONCAT(k.nama_kelas SEPARATOR ', ') FROM admin_tabungan_akses a JOIN kelas k ON k.id = a.kelas_id WHERE a.user_id = u.id) as kelas_dipegang
            FROM users u
            JOIN app_admins aa ON aa.user_id = u.id
            LEFT JOIN guru g ON g.user_id = u.id
            LEFT JOIN admin_tabungan_settings ats ON ats.user_id = u.id
            WHERE aa.app_code = 'tabungan'
        ")->fetchAll();
        
        // Data untuk dropdown tambah
        $gurus = $db->query("
            SELECT u.id, u.username, COALESCE(g.nama, u.username) as nama 
            FROM users u 
            LEFT JOIN guru g ON g.user_id = u.id 
            WHERE u.role_id = 2 
            ORDER BY nama
        ")->fetchAll();
        $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
        
        self::render('tabungan_akses', [
            'active_module' => 'tabungan',
            'active_page' => 'akses',
            'admins' => $admins,
            'gurus' => $gurus,
            'kelasList' => $kelasList
        ]);
    }
    
    public static function simpanAkses() {
        self::guard();
        if (!in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /admin/tabungan');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['user_id']);
            $kelas_ids = isset($_POST['kelas_ids']) && is_array($_POST['kelas_ids']) ? $_POST['kelas_ids'] : [];
            
            $db = Database::connect('core');
            try {
                $db->beginTransaction();
                // Pastikan jadi app_admin tabungan
                $db->prepare("INSERT IGNORE INTO app_admins (user_id, app_code) VALUES (?, 'tabungan')")->execute([$user_id]);
                
                // Hapus akses lama
                $db->prepare("DELETE FROM admin_tabungan_akses WHERE user_id = ?")->execute([$user_id]);
                
                // Insert akses baru
                $stmt = $db->prepare("INSERT INTO admin_tabungan_akses (user_id, kelas_id) VALUES (?, ?)");
                foreach ($kelas_ids as $kid) {
                    $stmt->execute([$user_id, intval($kid)]);
                }
                
                // Simpan pengaturan akses hapus
                $can_delete = isset($_POST['can_delete']) ? 1 : 0;
                $stmt_setting = $db->prepare("INSERT INTO admin_tabungan_settings (user_id, can_delete) VALUES (?, ?) ON DUPLICATE KEY UPDATE can_delete = ?");
                $stmt_setting->execute([$user_id, $can_delete, $can_delete]);
                
                $db->commit();
                header('Location: /admin/tabungan/akses?msg=success');
            } catch (\Exception $e) {
                $db->rollBack();
                header('Location: /admin/tabungan/akses?msg=error');
            }
        }
    }
    
    public static function hapusAkses() {
        self::guard();
        if (!in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /admin/tabungan');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['user_id']);
            $db = Database::connect('core');
            $db->prepare("DELETE FROM app_admins WHERE user_id = ? AND app_code = 'tabungan'")->execute([$user_id]);
            $db->prepare("DELETE FROM admin_tabungan_akses WHERE user_id = ?")->execute([$user_id]);
            $db->prepare("DELETE FROM admin_tabungan_settings WHERE user_id = ?")->execute([$user_id]);
            header('Location: /admin/tabungan/akses?msg=success');
        }
    }

    public static function toggleAksesHapus() {
        self::guard();
        if (!in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /admin/tabungan');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['user_id']);
            $db = Database::connect('core');
            $stmt = $db->prepare("SELECT can_delete FROM admin_tabungan_settings WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $current = $stmt->fetchColumn();
            
            $new_val = ($current == 1) ? 0 : 1;
            
            $stmt_update = $db->prepare("INSERT INTO admin_tabungan_settings (user_id, can_delete) VALUES (?, ?) ON DUPLICATE KEY UPDATE can_delete = ?");
            $stmt_update->execute([$user_id, $new_val, $new_val]);
            
            header('Location: /admin/tabungan/akses?msg=success');
        }
    }

}
