<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;
use App\Core\Branding;
use App\Core\AcademicYear;

class ApkController
{
    private $core_db;
    private $siakad_db;

    public function __construct()
    {
        $this->core_db = Database::connect('core');
        $this->siakad_db = Database::connect('siakad');
    }

    public function index()
    {
        // Auto-redirect based on session
        if (isset($_SESSION['siswa_id'])) {
            Helper::redirect('/apk/siswa/dashboard');
        } elseif (isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/guru/dashboard');
        } else {
            Helper::redirect('/apk/login');
        }
    }

    public function login()
    {
        if (isset($_SESSION['siswa_id'])) {
            Helper::redirect('/apk/siswa/dashboard');
        } elseif (isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/guru/dashboard');
        }

        $error_msg = $_SESSION['apk_error'] ?? '';
        unset($_SESSION['apk_error']);

        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;

        $title = "Login APK MTs RS";
        ob_start();
        include __DIR__ . '/../../resources/views/apk/login.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function loginProcess()
    {
        if (isset($_SESSION['siswa_id'])) {
            Helper::redirect('/apk/siswa/dashboard');
        } elseif (isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/guru/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $qr_data = trim($_POST['qr_data'] ?? '');
            
            if (!empty($qr_data)) {
                $identifier = $qr_data;
                $qr_password = null;
                $is_uuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $qr_data);
                
                if (!$is_uuid && preg_match('/USERNAME:\s*([^\r\n]+?)\s*PASSWORD:\s*([A-Z0-9a-z]+)/i', $qr_data, $matchUP)) {
                    $identifier = trim($matchUP[1]);
                    $qr_password = trim($matchUP[2]);
                } elseif (preg_match('/NIS\/NISN:\s*([A-Z0-9]+(?:\/[A-Z0-9]+)?)/i', $qr_data, $m)) {
                    $parts = array_filter(explode('/', $m[1]));
                    $identifier = trim(end($parts));
                }
                
                $user = null;
                $pass_ok = false;
                
                if ($qr_password !== null) {
                    $stmt = $this->core_db->prepare("SELECT * FROM users WHERE username = :uname LIMIT 1");
                    $stmt->execute(['uname' => $identifier]);
                    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($user) {
                        if (password_verify($qr_password, $user['password_hash']) || password_verify(strtoupper($qr_password), $user['password_hash'])) {
                            $pass_ok = true;
                        }
                    }
                } else {
                    $stmt = $this->core_db->prepare("SELECT u.* FROM users u LEFT JOIN siswa s ON u.id = s.user_id LEFT JOIN guru g ON u.id = g.user_id WHERE u.username = ? OR u.qr_token = ? OR s.nis = ? OR s.nisn = ? OR g.nip = ? LIMIT 1");
                    $stmt->execute([$identifier, $identifier, $identifier, $identifier, $identifier]);
                    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($user) $pass_ok = true;
                }
                
                if ($user && $pass_ok) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role_id'] = $user['role_id'];
                    
                    if ($user['role_id'] == 3 || $user['role_id'] == 6) { 
                        $stmtS = $this->core_db->prepare("SELECT * FROM siswa WHERE user_id = ? LIMIT 1");
                        $stmtS->execute([$user['id']]);
                        $siswa = $stmtS->fetch(\PDO::FETCH_ASSOC);
                        
                        if ($siswa) {
                            $_SESSION['siswa_id'] = $siswa['id'];
                            $_SESSION['nama'] = $siswa['nama'];
                            Helper::logActivity('AUTH', 'LOGIN', "Siswa {$siswa['nama']} berhasil login via APK.");
                            Helper::redirect('/apk/siswa/dashboard');
                        }
                    } elseif ($user['role_id'] == 2 || $user['role_id'] == 4) { 
                        $stmtG = $this->core_db->prepare("SELECT * FROM guru WHERE user_id = ? LIMIT 1");
                        $stmtG->execute([$user['id']]);
                        $guru = $stmtG->fetch(\PDO::FETCH_ASSOC);
                        
                        if ($guru) {
                            $_SESSION['guru_id'] = $guru['id'];
                            $_SESSION['nama'] = $guru['nama'];
                            $_SESSION['is_kamad'] = $guru['is_kamad'];
                            Helper::logActivity('AUTH', 'LOGIN', "Guru {$guru['nama']} berhasil login via APK.");
                            Helper::redirect('/apk/guru/dashboard');
                        }
                    } else {
                        Helper::redirect('/portal');
                    }
                } else {
                    $_SESSION['apk_error'] = "Kredensial atau QR tidak valid.";
                }
            } else {
                $_SESSION['apk_error'] = "Data QR Kosong.";
            }
        }
        
        Helper::redirect('/apk/login');
    }

    public function dashboardSiswa()
    {
        if (!isset($_SESSION['siswa_id'])) {
            Helper::redirect('/apk/login');
        }

        $siswa_id = $_SESSION['siswa_id'];
        
        // Fetch Siswa Data
        $stmt = $this->core_db->prepare("SELECT * FROM siswa WHERE id = ? LIMIT 1");
        $stmt->execute([$siswa_id]);
        $siswa = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$siswa) {
            unset($_SESSION['siswa_id']);
            Helper::redirect('/apk/login');
        }

        // Fetch Tagihan
        $stmt_t = $this->core_db->prepare("SELECT t.*, (SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as jumlah_terbayar FROM keuangan_tagihan t WHERE t.siswa_id = ?");
        $stmt_t->execute([$siswa_id]);
        $tagihans = $stmt_t->fetchAll(\PDO::FETCH_ASSOC);
        
        $total_tunggakan = 0;
        foreach ($tagihans as $t) {
            if ($t['status'] != 'Lunas') {
                $total_tunggakan += ($t['jumlah_tagihan'] - $t['jumlah_terbayar']);
            }
        }

        // Fetch Absen Hari Ini
        $absen_hari_ini = ['status' => 'Belum Absen', 'kode' => '-'];
        try {
            $today = date('Y-m-d');
            $stmt_a = $this->siakad_db->prepare("SELECT * FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ? LIMIT 1");
            $stmt_a->execute([$siswa_id, $today]);
            $absen = $stmt_a->fetch(\PDO::FETCH_ASSOC);
            if ($absen) {
                $stat = $absen['status'];
                if (in_array(strtolower($stat), ['hadir', 'sakit', 'izin', 'alpa'])) {
                    $absen_hari_ini['status'] = ucfirst(strtolower($stat));
                    $absen_hari_ini['kode'] = substr(ucfirst(strtolower($stat)), 0, 1);
                } else {
                    $absen_hari_ini['kode'] = $stat;
                    $absen_hari_ini['status'] = ($stat=='H'?'Hadir':($stat=='S'?'Sakit':($stat=='I'?'Izin':'Alpa')));
                }
            }
        } catch (\Exception $e) {}
        
        $aktif = AcademicYear::current();
        $tahun_ajaran_aktif = ($aktif['name'] ?? '-') . ' - Semester ' . ucfirst($aktif['semester'] ?? '-');
        
        // Fetch Broadcast Notifikasi
        $pengumumans = [];
        try {
            $stmt_p = $this->core_db->query("SELECT judul, pesan, created_at as tanggal_dibuat, NULL as url FROM broadcast_notifikasi WHERE target_role = 'Semua' OR target_role = 'Siswa' ORDER BY created_at DESC LIMIT 5");
            $pengumumans = $stmt_p->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        
        $title = "Dashboard Siswa";
        $activeNav = "home";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/siswa_dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function dashboardGuru()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        
        // Fetch Guru Data
        $stmt = $this->core_db->prepare("SELECT * FROM guru WHERE id = ? LIMIT 1");
        $stmt->execute([$guru_id]);
        $guru = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$guru) {
            unset($_SESSION['guru_id']);
            Helper::redirect('/apk/login');
        }

        $user_id = $guru['user_id'];
        $is_kamad = (isset($guru['is_kamad']) && $guru['is_kamad'] == 1);
        $is_wali_kelas = false;
        $nama_kelas_wali = '';
        $wali_stats = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];

        $today = date('Y-m-d');
        $days = ['Sunday' => 'Ahad', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $hari_ini = $days[date('l')] ?? date('l');
        $besok = $days[date('l', strtotime('+1 day'))] ?? date('l', strtotime('+1 day'));

        // Cek Kalender Pendidikan
        $stmt_kalender = $this->core_db->prepare("SELECT * FROM kalender_pendidikan WHERE tanggal_mulai <= ? AND tanggal_selesai >= ? ORDER BY tanggal_mulai DESC LIMIT 1");
        $stmt_kalender->execute([$today, $today]);
        $kalender_today = $stmt_kalender->fetch(\PDO::FETCH_ASSOC);
        
        $is_libur = ($kalender_today && $kalender_today['kategori'] === 'Libur');
        $is_kegiatan = ($kalender_today && $kalender_today['kategori'] === 'Kegiatan');
        $nama_kegiatan = $kalender_today ? $kalender_today['kegiatan'] : '';

        // 1. Absensi Guru Hari Ini
        $stmt_abs = $this->siakad_db->prepare("SELECT * FROM absensi_guru WHERE guru_id = ? AND tanggal = ? LIMIT 1");
        $stmt_abs->execute([$guru_id, $today]);
        $absensi_hari_ini = $stmt_abs->fetch(\PDO::FETCH_ASSOC);

        // 2. Rekap Bulanan
        $jml_jurnal = 0;
        try {
            $stmt_j = $this->siakad_db->prepare("SELECT COUNT(*) FROM jurnal_guru WHERE guru_id = ? AND MONTH(tanggal) = MONTH(?) AND YEAR(tanggal) = YEAR(?)");
            $stmt_j->execute([$guru_id, $today, $today]);
            $jml_jurnal = $stmt_j->fetchColumn();
        } catch (\Exception $e) {}

        $jml_hadir = 0;
        try {
            $stmt_h = $this->siakad_db->prepare("SELECT COUNT(*) FROM absensi_guru WHERE guru_id = ? AND MONTH(tanggal) = MONTH(?) AND YEAR(tanggal) = YEAR(?) AND status IN ('Hadir', 'Terlambat')");
            $stmt_h->execute([$guru_id, $today, $today]);
            $jml_hadir = $stmt_h->fetchColumn();
        } catch (\Exception $e) {}

        // 3. Pengumuman
        $pengumumans = [];
        try {
            $stmt_p = $this->core_db->query("SELECT judul, pesan, created_at as tanggal_dibuat, NULL as url FROM broadcast_notifikasi WHERE target_role = 'Semua' OR target_role = 'Guru' ORDER BY created_at DESC LIMIT 5");
            $pengumumans = $stmt_p->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 4. Jadwal Mengajar
        $jadwal_hari_ini = [];
        $jadwal_besok = [];
        try {
            $aktif = AcademicYear::current();
            $ta_id = $aktif['id'] ?? 1;
            // Semester in jadwal is usually 'Ganjil' or 'Genap'
            $semester_name = strtolower($aktif['semester'] ?? 'Ganjil');
            $semester = ($semester_name === 'genap' || $semester_name === '2') ? 'Genap' : 'Ganjil';

            $stmt_jdw = $this->siakad_db->prepare("
                SELECT jp.*, m.nama_mapel, k.nama_kelas 
                FROM jadwal_pelajaran jp
                JOIN mapel m ON jp.mapel_id = m.id
                JOIN kelas k ON jp.kelas_id = k.id
                WHERE jp.guru_id = ? AND jp.hari = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
                ORDER BY jp.jam_mulai ASC
            ");

            $stmt_jdw->execute([$guru_id, $hari_ini, $ta_id, $semester]);
            $jadwal_hari_ini_raw = $stmt_jdw->fetchAll(\PDO::FETCH_ASSOC);

            $stmt_jdw->execute([$guru_id, $besok, $ta_id, $semester]);
            $jadwal_besok_raw = $stmt_jdw->fetchAll(\PDO::FETCH_ASSOC);

            // Group Jadwal
            $groupJadwal = function($jdw) {
                if (empty($jdw)) return [];
                $res = [];
                $cur = $jdw[0];
                for ($i=1; $i<count($jdw); $i++) {
                    $next = $jdw[$i];
                    if ($cur['nama_mapel'] === $next['nama_mapel'] && $cur['nama_kelas'] === $next['nama_kelas'] && $cur['jam_selesai'] === $next['jam_mulai']) {
                        $cur['jam_selesai'] = $next['jam_selesai'];
                    } else {
                        $res[] = $cur;
                        $cur = $next;
                    }
                }
                $res[] = $cur;
                return $res;
            };

            $jadwal_hari_ini = $groupJadwal($jadwal_hari_ini_raw);
            $jadwal_besok = $groupJadwal($jadwal_besok_raw);
        } catch (\Exception $e) {}

        // 5. Wali Kelas Monitoring
        try {
            $stmt_wk = $this->core_db->prepare("SELECT k.id, k.nama_kelas FROM guru_tugas gt JOIN kelas k ON gt.keterangan = CONCAT('Kelas ', k.nama_kelas) WHERE gt.guru_id = ? AND gt.tugas = 'Wali Kelas' LIMIT 1");
            $stmt_wk->execute([$guru_id]);
            $wk = $stmt_wk->fetch(\PDO::FETCH_ASSOC);
            if ($wk) {
                $is_wali_kelas = true;
                $nama_kelas_wali = $wk['nama_kelas'];
                
                // Stat
                $stmt_siswa = $this->core_db->prepare("SELECT id, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif'");
                $stmt_siswa->execute([$wk['id']]);
                $siswa_raw = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);
                
                $siswa_map = [];
                $siswa_ids = [];
                foreach ($siswa_raw as $s) {
                    $siswa_map[$s['id']] = $s['nama'];
                    $siswa_ids[] = $s['id'];
                }

                $absen_raw = [];
                if (!empty($siswa_ids)) {
                    $inQuery = implode(',', array_fill(0, count($siswa_ids), '?'));
                    $params = array_merge($siswa_ids, [$today]);
                    $stmt_absen = $this->siakad_db->prepare("SELECT siswa_id, status FROM absensi_siswa WHERE siswa_id IN ($inQuery) AND tanggal = ?");
                    $stmt_absen->execute($params);
                    $absen_raw = $stmt_absen->fetchAll(\PDO::FETCH_ASSOC);
                }

                $wali_stats = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0, 'Bolos' => 0];
                $wali_names = ['Hadir' => [], 'Sakit' => [], 'Izin' => [], 'Alpa' => [], 'Bolos' => []];

                $absen_map = [];
                foreach ($absen_raw as $a) {
                    $absen_map[$a['siswa_id']] = $a['status'];
                }

                foreach ($siswa_map as $sid => $nama) {
                    $st = $absen_map[$sid] ?? '';
                    if ($st == 'Terlambat') $st = 'Hadir';

                    if (isset($wali_stats[$st])) {
                        $wali_stats[$st]++;
                        $wali_names[$st][] = $nama;
                    }
                }
            }
        } catch (\Exception $e) {}

        // Kamad
        $siswa_aktif_count = 0;
        $siswa_hadir_count = 0;
        $guru_aktif_count = 0;
        $guru_hadir_count = 0;
        if ($is_kamad) {
            try {
                $siswa_aktif_count = $this->core_db->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();
                $siswa_hadir_count = $this->siakad_db->prepare("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = ? AND status IN ('Hadir', 'Terlambat')");
                $siswa_hadir_count->execute([$today]);
                $siswa_hadir_count = $siswa_hadir_count->fetchColumn();

                $guru_aktif_count = $this->core_db->query("SELECT COUNT(*) FROM guru")->fetchColumn();
                $guru_hadir_count = $this->siakad_db->prepare("SELECT COUNT(*) FROM absensi_guru WHERE tanggal = ? AND status IN ('Hadir', 'Terlambat')");
                $guru_hadir_count->execute([$today]);
                $guru_hadir_count = $guru_hadir_count->fetchColumn();
            } catch (\Exception $e) {}
        }

        $aktif = AcademicYear::current();
        $tahun_ajaran_aktif = ($aktif['name'] ?? '-') . ' - Semester ' . ucfirst($aktif['semester'] ?? '-');

        $title = "Dashboard Guru";
        $activeNav = "home";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/guru_dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function jurnal()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $message = '';
        $status = '';

        $aktif = AcademicYear::current();
        $ta_id = $aktif['id'] ?? 1;
        $semester_name = strtolower($aktif['semester'] ?? 'Ganjil');
        $ta_sem = ($semester_name === 'genap' || $semester_name === '2') ? '2' : '1';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kelas_id = intval($_POST['kelas_id'] ?? 0);
            $mapel_id = intval($_POST['mapel_id'] ?? 0);
            $tanggal = $_POST['tanggal'] ?? '';
            $materi = $_POST['materi'] ?? '';
            $keterangan = $_POST['keterangan'] ?? '';
            
            if ($kelas_id > 0 && $mapel_id > 0 && !empty($tanggal) && !empty($materi)) {
                $cek = $this->siakad_db->prepare("
                    SELECT 1 FROM penugasan_mengajar WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ?
                    UNION 
                    SELECT 1 FROM jadwal_pelajaran WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ?
                    LIMIT 1
                ");
                $cek->execute([$guru_id, $kelas_id, $mapel_id, $guru_id, $kelas_id, $mapel_id, $ta_id, $ta_sem]);
                
                if (!$cek->fetch()) {
                    $message = 'Akses ditolak: Anda tidak mengampu kelas/mapel tersebut.';
                    $status = 'error';
                } else {
                    // Cek Strict Mode Jurnal
                    $institusi = $this->siakad_db->query("SELECT strict_jurnal_mode, jurnal_grace_period FROM institusi LIMIT 1")->fetch();
                    $strict_mode = $institusi['strict_jurnal_mode'] ?? 0;
                    $grace_period = $institusi['jurnal_grace_period'] ?? 30;

                    $is_locked = false;
                    $today_date = date('Y-m-d');
                    
                    if ($strict_mode == 1) {
                        if ($tanggal != $today_date) {
                            $is_locked = true;
                            $message = 'Gagal (Strict Mode): Anda tidak dapat mengisi/mengubah jurnal untuk tanggal yang sudah lewat atau di masa depan.';
                        } else {
                            $hari_ini = date('N'); 
                            $hari_indo = ['1'=>'Senin', '2'=>'Selasa', '3'=>'Rabu', '4'=>'Kamis', '5'=>'Jumat', '6'=>'Sabtu', '7'=>'Ahad'];
                            $nama_hari = $hari_indo[$hari_ini] ?? '';
                            
                            $stmt_jadwal = $this->siakad_db->prepare("
                                SELECT jam_mulai, jam_selesai 
                                FROM jadwal_pelajaran 
                                WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND hari = ? 
                                ORDER BY jam_selesai DESC LIMIT 1
                            ");
                            $stmt_jadwal->execute([$guru_id, $kelas_id, $mapel_id, $nama_hari]);
                            $jadwal = $stmt_jadwal->fetch();
                            
                            if ($jadwal && !empty($jadwal['jam_selesai']) && !empty($jadwal['jam_mulai'])) {
                                $jam_mulai = $jadwal['jam_mulai'];
                                $jam_selesai = $jadwal['jam_selesai'];
                                
                                $waktu_mulai = strtotime($today_date . ' ' . $jam_mulai);
                                $waktu_batas = strtotime("+$grace_period minutes", strtotime($today_date . ' ' . $jam_selesai));
                                
                                if (time() < $waktu_mulai) {
                                    $is_locked = true;
                                    $message = 'Gagal (Strict Mode): Jam pelajaran belum dimulai. Jurnal hanya bisa diisi saat atau setelah jam pelajaran.';
                                } elseif (time() > $waktu_batas) {
                                    $is_locked = true;
                                    $message = 'Gagal (Strict Mode): Waktu pengisian jurnal untuk jadwal ini telah ditutup karena melewati batas toleransi (' . $grace_period . ' menit dari jam selesai).';
                                }
                            } else {
                                $is_locked = true;
                                $message = 'Gagal (Strict Mode): Anda tidak memiliki jadwal untuk kelas dan mapel ini pada hari ini.';
                            }
                        }
                    }

                    if ($is_locked) {
                        $message = 'Gagal: Batas waktu pengisian jurnal untuk jadwal ini telah habis.';
                        $status = 'error';
                    } else {
                        try {
                            $this->siakad_db->beginTransaction();
                        
                        $edit_id_post = intval($_POST['jurnal_id'] ?? 0);
                        if ($edit_id_post > 0) {
                            $stmt_jurnal = $this->siakad_db->prepare("UPDATE jurnal_guru SET kelas_id=?, mapel_id=?, tanggal=?, materi=?, hambatan=? WHERE id=? AND guru_id=?");
                            $stmt_jurnal->execute([$kelas_id, $mapel_id, $tanggal, $materi, $keterangan, $edit_id_post, $guru_id]);
                            $jurnal_id = $edit_id_post;
                            $this->siakad_db->prepare("DELETE FROM absensi_permapel WHERE jurnal_id=?")->execute([$jurnal_id]);
                        } else {
                            $stmt_jurnal = $this->siakad_db->prepare("INSERT INTO jurnal_guru (guru_id, kelas_id, mapel_id, tanggal, materi, hambatan) VALUES (?, ?, ?, ?, ?, ?)");
                            $stmt_jurnal->execute([$guru_id, $kelas_id, $mapel_id, $tanggal, $materi, $keterangan]);
                            $jurnal_id = $this->siakad_db->lastInsertId();
                        }
                        
                        if (isset($_POST['absensi']) && is_array($_POST['absensi'])) {
                            $stmt_absen = $this->siakad_db->prepare("
                                INSERT INTO absensi_permapel 
                                    (jurnal_id, guru_id, siswa_id, kelas_id, mapel_id, tanggal, status)
                                VALUES (?, ?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE status = VALUES(status)
                            ");
                            
                            // Siapkan pengecekan untuk deteksi "Bolos" otomatis
                            $cek_hadir_gate = $this->core_db->prepare("SELECT 1 FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ? AND status IN ('Hadir', 'Terlambat') LIMIT 1");
                            $cek_hadir_jurnal = $this->siakad_db->prepare("SELECT 1 FROM absensi_permapel WHERE siswa_id = ? AND tanggal = ? AND status = 'Hadir' AND jurnal_id != ? LIMIT 1");
                            
                            // Pengecekan status mutlak (Sakit/Izin/Alpa) dari admin
                            $cek_global_status = $this->siakad_db->prepare("SELECT status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ? AND status IN ('Sakit', 'Izin', 'Alpa') LIMIT 1");

                            foreach ($_POST['absensi'] as $siswa_id_absen => $status_absen) {
                                $siswa_id_absen = intval($siswa_id_absen);
                                
                                // Auto Bolos Logic
                                if (in_array(strtolower($status_absen), ['alpha', 'alpa'])) {
                                    $is_bolos = false;
                                    
                                    // 1. Cek di gerbang (tabel core)
                                    $cek_hadir_gate->execute([$siswa_id_absen, $tanggal]);
                                    if ($cek_hadir_gate->fetch()) {
                                        $is_bolos = true;
                                    } else {
                                        // 2. Cek di jurnal lain hari ini (tabel siakad)
                                        $cek_hadir_jurnal->execute([$siswa_id_absen, $tanggal, $jurnal_id]);
                                        if ($cek_hadir_jurnal->fetch()) {
                                            $is_bolos = true;
                                        }
                                    }
                                    
                                    if ($is_bolos) {
                                        $status_absen = 'Bolos';
                                    }
                                }
                                
                                // Override dengan status mutlak jika ada
                                $cek_global_status->execute([$siswa_id_absen, $tanggal]);
                                $global_status = $cek_global_status->fetchColumn();
                                if ($global_status) {
                                    $status_absen = $global_status;
                                }
                                
                                $stmt_absen->execute([$jurnal_id, $guru_id, $siswa_id_absen, $kelas_id, $mapel_id, $tanggal, $status_absen]);
                            }
                        }
                        
                        $this->siakad_db->commit();
                        
                        // Sinkronkan status harian (Status Pusat) berdasarkan absensi guru
                        \App\Controllers\AbsenV2Controller::syncStatusPusat($tanggal, $kelas_id);
                        
                        $namaUser = $_SESSION['nama'] ?? 'Guru';
                        if ($edit_id_post > 0) {
                            Helper::logActivity('SIAKAD', 'UPDATE', "Update jurnal mengajar oleh Guru: $namaUser via APK.");
                        } else {
                            Helper::logActivity('SIAKAD', 'CREATE', "Input jurnal mengajar oleh Guru: $namaUser via APK.");
                        }
                        $message = 'Jurnal dan absensi berhasil disimpan!';
                        $status = 'success';
                    } catch (\PDOException $e) {
                        $this->siakad_db->rollBack();
                        $message = 'Gagal menyimpan data: ' . $e->getMessage();
                        $status = 'error';
                    }
                    }
                }
            } else {
                $message = 'Harap isi semua kolom wajib!';
                $status = 'warning';
            }
        }

        $kelasList = [];
        $mengajarData = [];

        try {
            $stmt_k = $this->siakad_db->prepare("
                SELECT DISTINCT id, nama_kelas, tingkat FROM (
                    SELECT k.id, k.nama_kelas, k.tingkat 
                    FROM penugasan_mengajar m
                    JOIN kelas k ON k.id = m.kelas_id
                    WHERE m.guru_id = ?
                    UNION
                    SELECT k.id, k.nama_kelas, k.tingkat 
                    FROM jadwal_pelajaran jp 
                    JOIN kelas k ON k.id = jp.kelas_id 
                    WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
                ) AS combined
                ORDER BY tingkat, nama_kelas
            ");
            $stmt_k->execute([$guru_id, $guru_id, $ta_id, $ta_sem]);
            $kelasList = $stmt_k->fetchAll(\PDO::FETCH_ASSOC);

            $stmt_m = $this->siakad_db->prepare("
                SELECT DISTINCT kelas_id, mapel_id, nama_mapel FROM (
                    SELECT m.kelas_id, mp.id as mapel_id, mp.nama_mapel
                    FROM penugasan_mengajar m
                    JOIN mapel mp ON mp.id = m.mapel_id
                    WHERE m.guru_id = ?
                    UNION
                    SELECT jp.kelas_id, mp.id as mapel_id, mp.nama_mapel 
                    FROM jadwal_pelajaran jp 
                    JOIN mapel mp ON mp.id = jp.mapel_id 
                    WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
                ) AS combined
                ORDER BY nama_mapel
            ");
            $stmt_m->execute([$guru_id, $guru_id, $ta_id, $ta_sem]);
            $mengajarData = $stmt_m->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $edit_id = intval($_GET['edit_id'] ?? 0);
        $jurnal_edit = null;
        $absensi_edit = [];
        if ($edit_id > 0) {
            $stmt_edit = $this->siakad_db->prepare("SELECT * FROM jurnal_guru WHERE id = ? AND guru_id = ?");
            $stmt_edit->execute([$edit_id, $guru_id]);
            $jurnal_edit = $stmt_edit->fetch(\PDO::FETCH_ASSOC);
            if ($jurnal_edit) {
                $stmt_absen_edit = $this->siakad_db->prepare("SELECT siswa_id, status FROM absensi_permapel WHERE jurnal_id = ?");
                $stmt_absen_edit->execute([$edit_id]);
                while ($ab = $stmt_absen_edit->fetch(\PDO::FETCH_ASSOC)) {
                    $absensi_edit[$ab['siswa_id']] = $ab['status'];
                }
            }
        }

        $title = "Isi Jurnal Mengajar";
        $activeNav = "jurnal";
        
        // Cek apakah form harus dikunci total di UI (karena tidak ada jadwal aktif)
        $institusi_settings = $this->siakad_db->query("SELECT strict_jurnal_mode, jurnal_grace_period FROM institusi LIMIT 1")->fetch();
        $is_form_locked_global = false;
        
        if (!empty($institusi_settings['strict_jurnal_mode']) && $institusi_settings['strict_jurnal_mode'] == 1) {
            $hari_ini = date('N'); 
            $hari_indo = ['1'=>'Senin', '2'=>'Selasa', '3'=>'Rabu', '4'=>'Kamis', '5'=>'Jumat', '6'=>'Sabtu', '7'=>'Ahad'];
            $nama_hari = $hari_indo[$hari_ini] ?? '';
            $today_date = date('Y-m-d');
            $waktu_sekarang = time();
            $grace_period = $institusi_settings['jurnal_grace_period'] ?? 30;

            if ($edit_id > 0 && $jurnal_edit) {
                if ($jurnal_edit['tanggal'] != $today_date) {
                    $is_form_locked_global = true;
                } else {
                    $stmt_cek = $this->siakad_db->prepare("SELECT jam_mulai, jam_selesai FROM jadwal_pelajaran WHERE guru_id=? AND kelas_id=? AND mapel_id=? AND hari=? ORDER BY jam_selesai DESC LIMIT 1");
                    $stmt_cek->execute([$guru_id, $jurnal_edit['kelas_id'], $jurnal_edit['mapel_id'], $nama_hari]);
                    $jadwal_cek = $stmt_cek->fetch();
                    if ($jadwal_cek && !empty($jadwal_cek['jam_selesai']) && !empty($jadwal_cek['jam_mulai'])) {
                        $waktu_mulai = strtotime($today_date . ' ' . $jadwal_cek['jam_mulai']);
                        $waktu_batas = strtotime("+$grace_period minutes", strtotime($today_date . ' ' . $jadwal_cek['jam_selesai']));
                        if ($waktu_sekarang < $waktu_mulai || $waktu_sekarang > $waktu_batas) {
                            $is_form_locked_global = true;
                        }
                    } else {
                        $is_form_locked_global = true;
                    }
                }
            } else {
                $stmt_cek_jadwal = $this->siakad_db->prepare("SELECT jam_mulai, jam_selesai FROM jadwal_pelajaran WHERE guru_id = ? AND hari = ?");
                $stmt_cek_jadwal->execute([$guru_id, $nama_hari]);
                $jadwal_hari_ini = $stmt_cek_jadwal->fetchAll(\PDO::FETCH_ASSOC);
                
                $has_open_schedule = false;
                foreach ($jadwal_hari_ini as $jdw) {
                    if (!empty($jdw['jam_selesai']) && !empty($jdw['jam_mulai'])) {
                        $waktu_mulai = strtotime($today_date . ' ' . $jdw['jam_mulai']);
                        $waktu_batas = strtotime("+$grace_period minutes", strtotime($today_date . ' ' . $jdw['jam_selesai']));
                        if ($waktu_sekarang >= $waktu_mulai && $waktu_sekarang <= $waktu_batas) {
                            $has_open_schedule = true;
                            break;
                        }
                    }
                }
                
                if (!$has_open_schedule) {
                    $is_form_locked_global = true;
                }
            }
        }
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/jurnal.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function rekapJurnal()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        
        $aktif = AcademicYear::current();
        $ta_id = $aktif['id'] ?? 1;
        $semester_name = strtolower($aktif['semester'] ?? 'Ganjil');
        $ta_sem = ($semester_name === 'genap' || $semester_name === '2') ? '2' : '1';

        $kelasList = [];
        $mengajarData = [];

        // Same filter logic as jurnal()
        try {
            $stmt_k = $this->siakad_db->prepare("
                SELECT DISTINCT id, nama_kelas, tingkat FROM (
                    SELECT k.id, k.nama_kelas, k.tingkat 
                    FROM penugasan_mengajar m
                    JOIN kelas k ON k.id = m.kelas_id
                    WHERE m.guru_id = ?
                    UNION
                    SELECT k.id, k.nama_kelas, k.tingkat 
                    FROM jadwal_pelajaran jp 
                    JOIN kelas k ON k.id = jp.kelas_id 
                    WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
                ) AS combined
                ORDER BY tingkat, nama_kelas
            ");
            $stmt_k->execute([$guru_id, $guru_id, $ta_id, $ta_sem]);
            $kelasList = $stmt_k->fetchAll(\PDO::FETCH_ASSOC);

            $stmt_m = $this->siakad_db->prepare("
                SELECT DISTINCT kelas_id, mapel_id, nama_mapel FROM (
                    SELECT m.kelas_id, mp.id as mapel_id, mp.nama_mapel
                    FROM penugasan_mengajar m
                    JOIN mapel mp ON mp.id = m.mapel_id
                    WHERE m.guru_id = ?
                    UNION
                    SELECT jp.kelas_id, mp.id as mapel_id, mp.nama_mapel 
                    FROM jadwal_pelajaran jp 
                    JOIN mapel mp ON mp.id = jp.mapel_id 
                    WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
                ) AS combined
                ORDER BY nama_mapel
            ");
            $stmt_m->execute([$guru_id, $guru_id, $ta_id, $ta_sem]);
            $mengajarData = $stmt_m->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $filter_kelas = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $filter_mapel = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
        
        $jurnalList = [];
        if ($filter_kelas > 0 && $filter_mapel > 0) {
            try {
                $stmt = $this->siakad_db->prepare("
                    SELECT j.*, m.nama_mapel, k.nama_kelas 
                    FROM jurnal_guru j 
                    JOIN mapel m ON j.mapel_id = m.id 
                    JOIN kelas k ON j.kelas_id = k.id 
                    WHERE j.guru_id = ? AND j.kelas_id = ? AND j.mapel_id = ?
                    ORDER BY j.tanggal DESC, j.id DESC
                ");
                $stmt->execute([$guru_id, $filter_kelas, $filter_mapel]);
                $jurnalList = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                // Fetch attendance stats for each journal
                foreach ($jurnalList as &$r) {
                    $jurnal_id = $r['id'];
                    $stmt_absen = $this->siakad_db->prepare("
                        SELECT a.status, COUNT(a.id) as jml, GROUP_CONCAT(s.nama SEPARATOR ', ') as names 
                        FROM absensi_permapel a
                        JOIN siswa s ON a.siswa_id = s.id
                        WHERE a.jurnal_id = ? 
                        GROUP BY a.status
                    ");
                    $stmt_absen->execute([$jurnal_id]);
                    $details = $stmt_absen->fetchAll(\PDO::FETCH_ASSOC);
                    
                    $counts = [];
                    $names = [];
                    foreach($details as $d) {
                        $counts[$d['status']] = $d['jml'];
                        $names[$d['status']] = $d['names'];
                    }
                    
                    $r['hadir'] = $counts['Hadir'] ?? 0;
                    $r['sakit'] = $counts['Sakit'] ?? 0;
                    $r['izin']  = $counts['Izin'] ?? 0;
                    $r['alpha'] = ($counts['Alpha'] ?? 0) + ($counts['Alpa'] ?? 0);
                    
                    $r['names_hadir'] = $names['Hadir'] ?? '-';
                    $r['names_sakit'] = $names['Sakit'] ?? '-';
                    $r['names_izin']  = $names['Izin'] ?? '-';
                    $r['names_alpha'] = trim(($names['Alpha'] ?? '') . ', ' . ($names['Alpa'] ?? ''), ', ');
                    if (empty($r['names_alpha'])) $r['names_alpha'] = '-';
                }
                unset($r);

                // Cek duplikat berdasarkan tanggal
                $tanggal_counts = [];
                foreach ($jurnalList as $j) {
                    $tanggal = $j['tanggal'];
                    if (!isset($tanggal_counts[$tanggal])) {
                        $tanggal_counts[$tanggal] = 0;
                    }
                    $tanggal_counts[$tanggal]++;
                }
                foreach ($jurnalList as &$r) {
                    $r['is_duplicate'] = $tanggal_counts[$r['tanggal']] > 1;
                }
                unset($r);
                
            } catch (\Exception $e) {}
        }

        $title = "Rekap Absen & Jurnal";
        $activeNav = "profile";

        ob_start();
        include __DIR__ . '/../../resources/views/apk/rekap_jurnal.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function deleteJurnal()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }
        $guru_id = $_SESSION['guru_id'];
        $jurnal_id = intval($_GET['id'] ?? 0);
        $kelas_id = intval($_GET['kelas_id'] ?? 0);
        $mapel_id = intval($_GET['mapel_id'] ?? 0);

        if ($jurnal_id > 0) {
            $cek = $this->siakad_db->prepare("SELECT id FROM jurnal_guru WHERE id = ? AND guru_id = ?");
            $cek->execute([$jurnal_id, $guru_id]);
            if ($cek->fetch()) {
                $this->siakad_db->prepare("DELETE FROM jurnal_guru WHERE id = ?")->execute([$jurnal_id]);
                $this->siakad_db->prepare("DELETE FROM absensi_permapel WHERE jurnal_id = ?")->execute([$jurnal_id]);
                $_SESSION['apk_jurnal_msg'] = "Jurnal berhasil dihapus.";
                $_SESSION['apk_jurnal_status'] = "success";
            }
        }
        Helper::redirect('/apk/guru/rekap-jurnal?kelas_id=' . $kelas_id . '&mapel_id=' . $mapel_id);
    }

    public function rekapAbsensi()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        
        $aktif = AcademicYear::current();
        $ta_id = $aktif['id'] ?? 1;
        $semester_name = strtolower($aktif['semester'] ?? 'Ganjil');
        $ta_sem = ($semester_name === 'genap' || $semester_name === '2') ? '2' : '1';

        $kelasList = [];
        $mengajarData = [];

        try {
            $stmt_k = $this->siakad_db->prepare("
                SELECT DISTINCT id, nama_kelas, tingkat FROM (
                    SELECT k.id, k.nama_kelas, k.tingkat 
                    FROM penugasan_mengajar m
                    JOIN kelas k ON k.id = m.kelas_id
                    WHERE m.guru_id = ?
                    UNION
                    SELECT k.id, k.nama_kelas, k.tingkat 
                    FROM jadwal_pelajaran jp 
                    JOIN kelas k ON k.id = jp.kelas_id 
                    WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
                ) AS combined
                ORDER BY tingkat, nama_kelas
            ");
            $stmt_k->execute([$guru_id, $guru_id, $ta_id, $ta_sem]);
            $kelasList = $stmt_k->fetchAll(\PDO::FETCH_ASSOC);

            $stmt_m = $this->siakad_db->prepare("
                SELECT DISTINCT kelas_id, mapel_id, nama_mapel FROM (
                    SELECT m.kelas_id, mp.id as mapel_id, mp.nama_mapel
                    FROM penugasan_mengajar m
                    JOIN mapel mp ON mp.id = m.mapel_id
                    WHERE m.guru_id = ?
                    UNION
                    SELECT jp.kelas_id, mp.id as mapel_id, mp.nama_mapel 
                    FROM jadwal_pelajaran jp 
                    JOIN mapel mp ON mp.id = jp.mapel_id 
                    WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
                ) AS combined
                ORDER BY nama_mapel
            ");
            $stmt_m->execute([$guru_id, $guru_id, $ta_id, $ta_sem]);
            $mengajarData = $stmt_m->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $filter_kelas = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $filter_mapel = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
        
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        
        $studentStats = [];
        $totalPertemuan = 0;

        if ($filter_kelas > 0 && $filter_mapel > 0) {
            try {
                // Get Total Meetings
                $stmt_j = $this->siakad_db->prepare("SELECT COUNT(id) FROM jurnal_guru WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tanggal BETWEEN ? AND ?");
                $stmt_j->execute([$guru_id, $filter_kelas, $filter_mapel, $start_date, $end_date]);
                $totalPertemuan = (int) $stmt_j->fetchColumn();

                // Get All Active Students in Class
                $stmt_s = $this->core_db->prepare("SELECT id, nama, nis FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
                $stmt_s->execute([$filter_kelas]);
                $siswaList = $stmt_s->fetchAll(\PDO::FETCH_ASSOC);

                // Get Attendance Stats
                $stmt_a = $this->siakad_db->prepare("
                    SELECT siswa_id, status, COUNT(id) as jml 
                    FROM absensi_permapel 
                    WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tanggal BETWEEN ? AND ?
                    GROUP BY siswa_id, status
                ");
                $stmt_a->execute([$guru_id, $filter_kelas, $filter_mapel, $start_date, $end_date]);
                $absenRaw = $stmt_a->fetchAll(\PDO::FETCH_ASSOC);
                
                $statsMap = [];
                foreach ($absenRaw as $a) {
                    $sid = $a['siswa_id'];
                    $st = $a['status'];
                    if (!isset($statsMap[$sid])) {
                        $statsMap[$sid] = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
                    }
                    if ($st == 'Alpha') $st = 'Alpa';
                    if (isset($statsMap[$sid][$st])) {
                        $statsMap[$sid][$st] += $a['jml'];
                    }
                }

                foreach ($siswaList as $s) {
                    $sid = $s['id'];
                    $hadir = $statsMap[$sid]['Hadir'] ?? 0;
                    $sakit = $statsMap[$sid]['Sakit'] ?? 0;
                    $izin = $statsMap[$sid]['Izin'] ?? 0;
                    $alpa = $statsMap[$sid]['Alpa'] ?? 0;
                    
                    $persentase = $totalPertemuan > 0 ? round(($hadir / $totalPertemuan) * 100) : 0;
                    
                    $studentStats[] = [
                        'nama' => $s['nama'],
                        'nis' => $s['nis'],
                        'hadir' => $hadir,
                        'sakit' => $sakit,
                        'izin' => $izin,
                        'alpa' => $alpa,
                        'persentase' => $persentase
                    ];
                }
                
            } catch (\Exception $e) {}
        }

        $title = "Rekap Absensi Persentase";
        $activeNav = "profile";

        ob_start();
        include __DIR__ . '/../../resources/views/apk/rekap_absensi.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function supervisiAdministrasi()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $isKamad = false;
        $isWakaKurik = false;

        try {
            $stmt = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ?");
            $stmt->execute([$guru_id]);
            $g = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($g) {
                if ($g['is_kamad'] == 1 || stripos($g['jabatan'], 'Kepala') !== false || stripos($g['jabatan'], 'Kamad') !== false) {
                    $isKamad = true;
                }
                if (stripos($g['jabatan'], 'Kurikulum') !== false || stripos($g['jabatan'], 'Waka Kurikulum') !== false) {
                    $isWakaKurik = true;
                }
            }
            $stmtT = $this->core_db->prepare("SELECT tugas FROM guru_tugas WHERE guru_id = ?");
            $stmtT->execute([$guru_id]);
            $tgs = $stmtT->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tgs as $t) {
                if (stripos($t, 'Kurikulum') !== false) $isWakaKurik = true;
            }
        } catch (\Exception $e) {}

        if (!$isKamad && !$isWakaKurik) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $aktif = AcademicYear::current();
        $ta_id = $aktif['id'] ?? 1;

        $guru_list = [];
        try {
            $stmt = $this->core_db->prepare("
                SELECT g.id, g.nama, g.nip,
                       s.id as supervisi_id, s.tanggal_supervisi, s.nilai_akhir
                FROM guru g
                LEFT JOIN kurikulum_supervisi_administrasi s 
                  ON g.id = s.guru_id AND s.tahun_ajaran_id = ?
                ORDER BY g.nama ASC
            ");
            $stmt->execute([$ta_id]);
            $guru_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $title = "Supervisi Administrasi";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/supervisi_administrasi.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function supervisiAdministrasiEvaluasi()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id_admin = $_SESSION['guru_id'];
        $isKamad = false;
        $isWakaKurik = false;

        try {
            $stmt = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ?");
            $stmt->execute([$guru_id_admin]);
            $g = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($g) {
                if ($g['is_kamad'] == 1 || stripos($g['jabatan'], 'Kepala') !== false || stripos($g['jabatan'], 'Kamad') !== false) {
                    $isKamad = true;
                }
                if (stripos($g['jabatan'], 'Kurikulum') !== false || stripos($g['jabatan'], 'Waka Kurikulum') !== false) {
                    $isWakaKurik = true;
                }
            }
            $stmtT = $this->core_db->prepare("SELECT tugas FROM guru_tugas WHERE guru_id = ?");
            $stmtT->execute([$guru_id_admin]);
            $tgs = $stmtT->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tgs as $t) {
                if (stripos($t, 'Kurikulum') !== false) $isWakaKurik = true;
            }
        } catch (\Exception $e) {}

        if (!$isKamad && !$isWakaKurik) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $guru_id = $_GET['guru_id'] ?? null;
        if (!$guru_id) {
            Helper::redirect('/apk/supervisi-administrasi');
        }

        $aktif = AcademicYear::current();
        $ta_id = $aktif['id'] ?? 1;

        $stmtGuru = $this->core_db->prepare("SELECT * FROM guru WHERE id = ?");
        $stmtGuru->execute([$guru_id]);
        $guru = $stmtGuru->fetch(\PDO::FETCH_ASSOC);

        if (!$guru) {
            Helper::redirect('/apk/supervisi-administrasi');
        }

        $supervisi = null;
        $stmtSup = $this->core_db->prepare("SELECT * FROM kurikulum_supervisi_administrasi WHERE tahun_ajaran_id = ? AND guru_id = ?");
        $stmtSup->execute([$ta_id, $guru_id]);
        $supervisi = $stmtSup->fetch(\PDO::FETCH_ASSOC);
        if ($supervisi) {
            $supervisi['instrumen'] = json_decode($supervisi['instrumen_json'], true);
        }

        $berkas_guru = [];
        $stmtBerkas = $this->core_db->prepare("SELECT * FROM perangkat_pembelajaran WHERE tahun_ajaran_id = ? AND guru_id = ? ORDER BY tanggal_upload DESC");
        $stmtBerkas->execute([$ta_id, $guru_id]);
        $berkasData = $stmtBerkas->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($berkasData as $b) {
            $jenis = $b['jenis_berkas'];
            if (!isset($berkas_guru[$jenis])) {
                $berkas_guru[$jenis] = [];
            }
            $berkas_guru[$jenis][] = $b;
        }

        $title = "Evaluasi Supervisi";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/supervisi_administrasi_evaluasi.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function supervisiAdministrasiSave()
    {
        if (!isset($_SESSION['guru_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthenticated']);
            exit;
        }

        $aktif = AcademicYear::current();
        $tahun_ajaran_id = $aktif['id'] ?? 1;

        $guru_id = $_POST['guru_id'] ?? null;
        $tanggal_supervisi = $_POST['tanggal_supervisi'] ?? date('Y-m-d');
        $catatan = $_POST['catatan'] ?? '';
        $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
        
        $instrumen = [
            'silabus' => $_POST['instrumen']['silabus'] ?? '',
            'rpp' => $_POST['instrumen']['rpp'] ?? '',
            'prota' => $_POST['instrumen']['prota'] ?? '',
            'promes' => $_POST['instrumen']['promes'] ?? '',
            'kkm' => $_POST['instrumen']['kkm'] ?? '',
            'jurnal' => $_POST['instrumen']['jurnal'] ?? '',
            'absen' => $_POST['instrumen']['absen'] ?? '',
            'nilai' => $_POST['instrumen']['nilai'] ?? ''
        ];
        
        $total_items = 8;
        $score = 0;
        foreach ($instrumen as $val) {
            if ($val === 'Ada') $score += 2;
            elseif ($val === 'Tidak Lengkap') $score += 1;
        }
        $nilai_akhir = round(($score / ($total_items * 2)) * 100);
        $instrumen_json = json_encode($instrumen);

        try {
            $stmtCek = $this->core_db->prepare("SELECT id FROM kurikulum_supervisi_administrasi WHERE tahun_ajaran_id = ? AND guru_id = ?");
            $stmtCek->execute([$tahun_ajaran_id, $guru_id]);
            $cek = $stmtCek->fetch(\PDO::FETCH_ASSOC);

            if ($cek) {
                $stmtUpdate = $this->core_db->prepare("UPDATE kurikulum_supervisi_administrasi SET tanggal_supervisi = ?, instrumen_json = ?, catatan = ?, tindak_lanjut = ?, nilai_akhir = ?, updated_at = NOW() WHERE id = ?");
                $stmtUpdate->execute([$tanggal_supervisi, $instrumen_json, $catatan, $tindak_lanjut, $nilai_akhir, $cek['id']]);
            } else {
                $stmtInsert = $this->core_db->prepare("INSERT INTO kurikulum_supervisi_administrasi (tahun_ajaran_id, guru_id, tanggal_supervisi, instrumen_json, catatan, tindak_lanjut, nilai_akhir) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmtInsert->execute([$tahun_ajaran_id, $guru_id, $tanggal_supervisi, $instrumen_json, $catatan, $tindak_lanjut, $nilai_akhir]);
            }

            echo json_encode(['status' => 'success', 'message' => 'Data supervisi berhasil disimpan. Nilai Akhir: ' . $nilai_akhir]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()]);
        }
        exit;
    }

    public function supervisiKelas()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $isKamad = false;
        $isWakaKurik = false;

        try {
            $stmt = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ?");
            $stmt->execute([$guru_id]);
            $g = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($g) {
                if ($g['is_kamad'] == 1 || stripos($g['jabatan'], 'Kepala') !== false || stripos($g['jabatan'], 'Kamad') !== false) {
                    $isKamad = true;
                }
                if (stripos($g['jabatan'], 'Kurikulum') !== false || stripos($g['jabatan'], 'Waka Kurikulum') !== false) {
                    $isWakaKurik = true;
                }
            }
            $stmtT = $this->core_db->prepare("SELECT tugas FROM guru_tugas WHERE guru_id = ?");
            $stmtT->execute([$guru_id]);
            $tgs = $stmtT->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tgs as $t) {
                if (stripos($t, 'Kurikulum') !== false) $isWakaKurik = true;
            }
        } catch (\Exception $e) {}

        if (!$isKamad && !$isWakaKurik) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $aktif = AcademicYear::current();
        $ta_id = $aktif['id'] ?? 1;

        $guru_list = [];
        try {
            $stmt = $this->core_db->prepare("
                SELECT g.id, g.nama, g.nip,
                       s.id as supervisi_id, s.tanggal_supervisi
                FROM guru g
                LEFT JOIN kurikulum_supervisi_kelas s 
                  ON g.id = s.guru_id AND s.tahun_ajaran_id = ?
                ORDER BY g.nama ASC
            ");
            $stmt->execute([$ta_id]);
            $guru_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $title = "Supervisi Kelas";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/supervisi_kelas.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function supervisiKelasEvaluasi()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id_admin = $_SESSION['guru_id'];
        $isKamad = false;
        $isWakaKurik = false;

        try {
            $stmt = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ?");
            $stmt->execute([$guru_id_admin]);
            $g = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($g) {
                if ($g['is_kamad'] == 1 || stripos($g['jabatan'], 'Kepala') !== false || stripos($g['jabatan'], 'Kamad') !== false) {
                    $isKamad = true;
                }
                if (stripos($g['jabatan'], 'Kurikulum') !== false || stripos($g['jabatan'], 'Waka Kurikulum') !== false) {
                    $isWakaKurik = true;
                }
            }
            $stmtT = $this->core_db->prepare("SELECT tugas FROM guru_tugas WHERE guru_id = ?");
            $stmtT->execute([$guru_id_admin]);
            $tgs = $stmtT->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tgs as $t) {
                if (stripos($t, 'Kurikulum') !== false) $isWakaKurik = true;
            }
        } catch (\Exception $e) {}

        if (!$isKamad && !$isWakaKurik) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $guru_id = $_GET['guru_id'] ?? null;
        if (!$guru_id) {
            Helper::redirect('/apk/supervisi-kelas');
        }

        $aktif = AcademicYear::current();
        $ta_id = $aktif['id'] ?? 1;

        $stmtGuru = $this->core_db->prepare("SELECT * FROM guru WHERE id = ?");
        $stmtGuru->execute([$guru_id]);
        $guru = $stmtGuru->fetch(\PDO::FETCH_ASSOC);

        if (!$guru) {
            Helper::redirect('/apk/supervisi-kelas');
        }

        $supervisi = null;
        $stmtSup = $this->core_db->prepare("SELECT * FROM kurikulum_supervisi_kelas WHERE tahun_ajaran_id = ? AND guru_id = ?");
        $stmtSup->execute([$ta_id, $guru_id]);
        $supervisi = $stmtSup->fetch(\PDO::FETCH_ASSOC);
        if ($supervisi) {
            $supervisi['instrumen'] = json_decode($supervisi['instrumen_json'], true);
        }

        $title = "Evaluasi Supervisi Kelas";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/supervisi_kelas_evaluasi.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function supervisiKelasSave()
    {
        if (!isset($_SESSION['guru_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthenticated']);
            exit;
        }

        $aktif = AcademicYear::current();
        $tahun_ajaran_id = $aktif['id'] ?? 1;

        $guru_id = $_POST['guru_id'] ?? null;
        $tanggal_supervisi = $_POST['tanggal_supervisi'] ?? date('Y-m-d');
        $mata_pelajaran = $_POST['mata_pelajaran'] ?? '';
        $kelas = $_POST['kelas'] ?? '';
        $jam_ke = $_POST['jam_ke'] ?? '';
        $materi_pokok = $_POST['materi_pokok'] ?? '';
        $catatan = $_POST['catatan'] ?? '';
        $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
        
        $instrumen = $_POST['instrumen'] ?? [];
        $instrumen_json = json_encode($instrumen);

        try {
            $stmtCek = $this->core_db->prepare("SELECT id FROM kurikulum_supervisi_kelas WHERE tahun_ajaran_id = ? AND guru_id = ?");
            $stmtCek->execute([$tahun_ajaran_id, $guru_id]);
            $cek = $stmtCek->fetch(\PDO::FETCH_ASSOC);

            if ($cek) {
                $stmtUpdate = $this->core_db->prepare("UPDATE kurikulum_supervisi_kelas SET tanggal_supervisi = ?, mata_pelajaran = ?, kelas = ?, jam_ke = ?, materi_pokok = ?, instrumen_json = ?, catatan = ?, tindak_lanjut = ? WHERE id = ?");
                $stmtUpdate->execute([$tanggal_supervisi, $mata_pelajaran, $kelas, $jam_ke, $materi_pokok, $instrumen_json, $catatan, $tindak_lanjut, $cek['id']]);
            } else {
                $stmtInsert = $this->core_db->prepare("INSERT INTO kurikulum_supervisi_kelas (tahun_ajaran_id, guru_id, tanggal_supervisi, mata_pelajaran, kelas, jam_ke, materi_pokok, instrumen_json, catatan, tindak_lanjut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtInsert->execute([$tahun_ajaran_id, $guru_id, $tanggal_supervisi, $mata_pelajaran, $kelas, $jam_ke, $materi_pokok, $instrumen_json, $catatan, $tindak_lanjut]);
            }

            echo json_encode(['status' => 'success', 'message' => 'Data supervisi kelas berhasil disimpan.']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()]);
        }
        exit;
    }

    public function monitorAbsen()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $isKamad = false;
        $isWakaKurik = false;
        $isWakaKesiswaan = false;

        try {
            $stmt = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ?");
            $stmt->execute([$guru_id]);
            $g = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($g) {
                if ($g['is_kamad'] == 1 || stripos($g['jabatan'], 'Kepala') !== false) $isKamad = true;
                if (stripos($g['jabatan'], 'Kurikulum') !== false) $isWakaKurik = true;
                if (stripos($g['jabatan'], 'Kesiswaan') !== false) $isWakaKesiswaan = true;
            }
            $stmtT = $this->core_db->prepare("SELECT tugas FROM guru_tugas WHERE guru_id = ?");
            $stmtT->execute([$guru_id]);
            $tgs = $stmtT->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tgs as $t) {
                if (stripos($t, 'Kurikulum') !== false) $isWakaKurik = true;
                if (stripos($t, 'Kesiswaan') !== false) $isWakaKesiswaan = true;
            }
            
            $stmt_tambahan = $this->core_db->prepare("SELECT jabatan_tugas FROM guru_tugas_tambahan WHERE guru_id = ?");
            $stmt_tambahan->execute([$guru_id]);
            $tugas_tambahan = $stmt_tambahan->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tugas_tambahan as $tt) {
                if (stripos($tt, 'Kurikulum') !== false) $isWakaKurik = true;
                if (stripos($tt, 'Kesiswaan') !== false) $isWakaKesiswaan = true;
            }
        } catch (\Exception $e) {}

        if (!$isKamad && !$isWakaKurik && !$isWakaKesiswaan) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $today = date('Y-m-d');
        
        // Statistik Siswa
        $siswa_aktif = $this->siakad_db->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();
        
        $stmt_s = $this->siakad_db->prepare("SELECT status, COUNT(*) as jml FROM absensi_siswa WHERE tanggal = ? GROUP BY status");
        $stmt_s->execute([$today]);
        $absen_s = $stmt_s->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        $s_hadir = ($absen_s['Hadir'] ?? 0) + ($absen_s['Terlambat'] ?? 0);
        $s_izin = ($absen_s['Izin'] ?? 0) + ($absen_s['Sakit'] ?? 0);
        $s_alpa = $absen_s['Alpa'] ?? 0;
        $s_belum = max(0, $siswa_aktif - ($s_hadir + $s_izin + $s_alpa));

        // Statistik Guru
        $guru_aktif = $this->core_db->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        
        $stmt_g = $this->core_db->prepare("SELECT status, COUNT(*) as jml FROM absensi_guru WHERE tanggal = ? GROUP BY status");
        $stmt_g->execute([$today]);
        $absen_g = $stmt_g->fetchAll(\PDO::FETCH_KEY_PAIR);

        $g_hadir = ($absen_g['Hadir'] ?? 0) + ($absen_g['Terlambat'] ?? 0);
        $g_izin = ($absen_g['Izin'] ?? 0) + ($absen_g['Sakit'] ?? 0);
        $g_alpa = $absen_g['Alpa'] ?? 0;
        $g_belum = max(0, $guru_aktif - ($g_hadir + $g_izin + $g_alpa));

        $title = "Monitor Absensi";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/monitor_absen.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function monitorKeuangan()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $isKamad = false;

        try {
            $stmt = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ?");
            $stmt->execute([$guru_id]);
            $g = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($g && ($g['is_kamad'] == 1 || stripos($g['jabatan'], 'Kepala') !== false)) {
                $isKamad = true;
            }
        } catch (\Exception $e) {}

        if (!$isKamad) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        // 1. Total Saldo Kas Keseluruhan
        $totalMasukAll = 0;
        $totalKeluarAll = 0;
        try {
            $sumPembayaran = (float)$this->core_db->query("SELECT SUM(jumlah) FROM keuangan_komite_pembayaran")->fetchColumn();
            $sumMasukManual = (float)$this->core_db->query("SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pemasukan'")->fetchColumn();
            $sumKeluarManual = (float)$this->core_db->query("SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pengeluaran'")->fetchColumn();
            $totalMasukAll = $sumPembayaran + $sumMasukManual;
            $totalKeluarAll = $sumKeluarManual;
        } catch (\Exception $e) {}
        $totalSaldoKas = $totalMasukAll - $totalKeluarAll;

        // 2. Count Tagihan Belum Lunas
        $tagihanBelumLunas = 0;
        try {
            $tagihanBelumLunas = (int)$this->core_db->query("SELECT COUNT(*) FROM keuangan_tagihan WHERE status != 'Lunas'")->fetchColumn();
        } catch (\Exception $e) {}

        // 3. Data Saldo Per Kategori
        $kategoriData = [];
        try {
            $allGurus = $this->core_db->query("SELECT id, nama FROM guru")->fetchAll(\PDO::FETCH_KEY_PAIR);

            $kategoris = $this->core_db->query("
                SELECT k.* 
                FROM keuangan_komite_kategori k
                ORDER BY k.nama_kategori ASC
            ")->fetchAll(\PDO::FETCH_ASSOC);

            // Fetch mappings for tagihan to kategori
            $jenisTagihan = $this->core_db->query("SELECT nama_tagihan, kategori FROM keuangan_komite_jenis")->fetchAll(\PDO::FETCH_ASSOC);
            $tagihanToKategori = [];
            foreach ($jenisTagihan as $jt) {
                $tagihanToKategori[trim($jt['nama_tagihan'])] = trim($jt['kategori']);
            }

            // Fetch all payments
            $allPembayaran = $this->core_db->query("SELECT TRIM(jenis_pembayaran) as jp, SUM(jumlah) as jml FROM keuangan_komite_pembayaran GROUP BY TRIM(jenis_pembayaran)")->fetchAll(\PDO::FETCH_ASSOC);
            $pemasukanKategori = [];
            foreach ($allPembayaran as $p) {
                $kat = $tagihanToKategori[$p['jp']] ?? 'Lainnya';
                if (!isset($pemasukanKategori[$kat])) $pemasukanKategori[$kat] = 0;
                $pemasukanKategori[$kat] += (float)$p['jml'];
            }

            // Fetch all manual transactions
            $allManual = $this->core_db->query("SELECT TRIM(kategori) as kat, jenis, SUM(jumlah) as jml FROM keuangan_komite_transaksi GROUP BY TRIM(kategori), jenis")->fetchAll(\PDO::FETCH_ASSOC);
            $pengeluaranKategori = [];
            foreach ($allManual as $m) {
                $kat = $m['kat'];
                // Jika kat adalah nama tagihan, resolve ke kategori sebenarnya
                if (isset($tagihanToKategori[$kat])) {
                    $kat = $tagihanToKategori[$kat];
                }
                
                if ($m['jenis'] == 'Pemasukan') {
                    if (!isset($pemasukanKategori[$kat])) $pemasukanKategori[$kat] = 0;
                    $pemasukanKategori[$kat] += (float)$m['jml'];
                } else {
                    if (!isset($pengeluaranKategori[$kat])) $pengeluaranKategori[$kat] = 0;
                    $pengeluaranKategori[$kat] += (float)$m['jml'];
                }
            }

            foreach ($kategoris as $k) {
                $nama_kat = trim($k['nama_kategori']);
                $masuk = $pemasukanKategori[$nama_kat] ?? 0;
                $keluar = $pengeluaranKategori[$nama_kat] ?? 0;
                
                $petugasNames = [];
                $ids = [];
                if (!empty($k['guru_ids'])) {
                    $decoded = json_decode($k['guru_ids'], true);
                    if (is_array($decoded)) {
                        $ids = $decoded;
                    }
                } elseif (!empty($k['guru_id'])) {
                    $ids[] = $k['guru_id'];
                }

                foreach ($ids as $gid) {
                    if (isset($allGurus[$gid])) {
                        $petugasNames[] = $allGurus[$gid];
                    }
                }
                $kategoriData[] = [
                    'nama_kategori' => $nama_kat,
                    'petugas' => !empty($petugasNames) ? $petugasNames : ['Admin'],
                    'masuk' => $masuk,
                    'keluar' => $keluar,
                    'saldo' => $masuk - $keluar
                ];
            }
        } catch (\Exception $e) {}

        $title = "Monitor Keuangan";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/monitor_keuangan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function monitorBos()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $isKamad = false;

        try {
            $stmt = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ?");
            $stmt->execute([$guru_id]);
            $g = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($g && ($g['is_kamad'] == 1 || stripos($g['jabatan'], 'Kepala') !== false)) {
                $isKamad = true;
            }
        } catch (\Exception $e) {}

        if (!$isKamad) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $title = "Monitor BOS";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/monitor_bos.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function waliKelasSiswa()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        
        $aktif = AcademicYear::current();
        $ta_id = $aktif['id'] ?? 1;
        $active_year_name = $aktif['name'] ?? '2025/2026';

        // Cari tahu guru ini wali kelas berapa
        $stmt_tugas = $this->core_db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru_id, $active_year_name]);
        $keterangan = $stmt_tugas->fetchColumn();

        if (!$keterangan) {
            // Bukan wali kelas, atau tidak diset
            Helper::redirect('/apk/aplikasi-guru');
        }

        $nama_kelas_raw = str_ireplace('Kelas ', '', $keterangan);
        $nama_kelas_raw = trim($nama_kelas_raw);

        // Cari kelas_id di siakad_db
        $stmt_k = $this->siakad_db->prepare("SELECT id, tingkat, nama_kelas FROM kelas WHERE nama_kelas = ?");
        $stmt_k->execute([$nama_kelas_raw]);
        $kelasInfo = $stmt_k->fetch(\PDO::FETCH_ASSOC);

        $siswas = [];
        if ($kelasInfo) {
            $stmt_s = $this->siakad_db->prepare("
                SELECT * 
                FROM siswa 
                WHERE kelas_id = ?
                ORDER BY nama ASC
            ");
            $stmt_s->execute([$kelasInfo['id']]);
            $siswas = $stmt_s->fetchAll(\PDO::FETCH_ASSOC);
        }

        $title = "Data Siswa Wali Kelas";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/wali_kelas_siswa.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function waliKelasSiswaProfil($id) {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        
        $aktif = AcademicYear::current();
        $active_year_name = $aktif['name'] ?? '2025/2026';

        // Check if teacher is wali kelas
        $stmt_tugas = $this->core_db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru_id, $active_year_name]);
        $keterangan = $stmt_tugas->fetchColumn();

        if (!$keterangan) {
            Helper::redirect('/apk/aplikasi-guru');
        }

        $stmt_s = $this->siakad_db->prepare("SELECT * FROM siswa WHERE id = ?");
        $stmt_s->execute([$id]);
        $s = $stmt_s->fetch(\PDO::FETCH_ASSOC);

        if (!$s) {
            Helper::redirect('/apk/wali-kelas/siswa');
        }

        $title = "Profil " . $s['nama'];
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/wali_kelas_siswa_profil.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function waliKelasJurnal() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        
        $aktif = AcademicYear::current();
        $active_year_name = $aktif['name'] ?? '2025/2026';

        // Cari tahu guru ini wali kelas berapa
        $stmt_tugas = $this->core_db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru_id, $active_year_name]);
        $keterangan = $stmt_tugas->fetchColumn();

        if (!$keterangan) {
            Helper::redirect('/apk/aplikasi-guru');
        }

        $nama_kelas_raw = str_ireplace('Kelas ', '', $keterangan);
        $nama_kelas_raw = trim($nama_kelas_raw);

        // Cari kelas_id di siakad_db
        $stmt_k = $this->siakad_db->prepare("SELECT id FROM kelas WHERE nama_kelas = ?");
        $stmt_k->execute([$nama_kelas_raw]);
        $kelas_id = $stmt_k->fetchColumn();

        $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
        $days_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $months_indo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];

        $final_display = [];

        if ($kelas_id) {
            // 1. Ambil Jadwal Pelajaran Kelas Ini
            $stmt_jadwal = $this->siakad_db->prepare("
                SELECT jp.hari, jp.jam_ke, jp.jam_mulai, jp.jam_selesai, m.nama_mapel, g.nama as nama_guru, jp.mapel_id, jp.guru_id
                FROM jadwal_pelajaran jp
                JOIN mapel m ON m.id = jp.mapel_id
                LEFT JOIN guru g ON g.id = jp.guru_id
                WHERE jp.kelas_id = ?
                ORDER BY FIELD(jp.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jp.jam_mulai
            ");
            $stmt_jadwal->execute([$kelas_id]);
            $jadwal_list = $stmt_jadwal->fetchAll(\PDO::FETCH_ASSOC);

            $jadwal_per_hari = [];
            foreach ($jadwal_list as $jdwl) {
                // Filter out Pembiasaan, Ekstrakurikuler dll
                $nama_mapel = strtolower($jdwl['nama_mapel']);
                $exclude_words = ['pembiasaan', 'ekstra', 'senam', 'upacara', 'sholat', 'pramuka', 'juz amma', 'asmaul husna', 'istirahat'];
                $is_excluded = false;
                foreach ($exclude_words as $word) {
                    if (strpos($nama_mapel, $word) !== false) {
                        $is_excluded = true;
                        break;
                    }
                }
                
                if ($is_excluded) {
                    continue;
                }

                $hari = ucfirst(strtolower(trim($jdwl['hari'])));
                if ($hari === 'Ahad') {
                    $hari = 'Minggu';
                }
                $mapel_id = $jdwl['mapel_id'];
                
                // Group per hari per mapel (jadikan 1 row saja dari jam mulai s/d jam selesai)
                if (!isset($jadwal_per_hari[$hari][$mapel_id])) {
                    $jadwal_per_hari[$hari][$mapel_id] = $jdwl;
                } else {
                    if (strtotime($jdwl['jam_selesai']) > strtotime($jadwal_per_hari[$hari][$mapel_id]['jam_selesai'])) {
                        $jadwal_per_hari[$hari][$mapel_id]['jam_selesai'] = $jdwl['jam_selesai'];
                    }
                }
            }

            // 2. Ambil Jurnal Guru untuk bulan ini
            $stmt_jurnal = $this->siakad_db->prepare("
                SELECT jg.id, jg.tanggal, jg.mapel_id, jg.materi, jg.hambatan, 
                       g.nama as nama_guru_jurnal, m.nama_mapel
                FROM jurnal_guru jg
                JOIN guru g ON g.id = jg.guru_id
                JOIN mapel m ON m.id = jg.mapel_id
                WHERE jg.kelas_id = ? AND DATE_FORMAT(jg.tanggal, '%Y-%m') = ?
            ");
            $stmt_jurnal->execute([$kelas_id, $bulan]);
            $jurnals_raw = $stmt_jurnal->fetchAll(\PDO::FETCH_ASSOC);

            $jurnal_map = [];
            foreach($jurnals_raw as $jr) {
                $jurnal_map[$jr['tanggal']][$jr['mapel_id']] = $jr;
            }

            // 3. Bangun Rekap Berdasarkan Hari di Bulan Ini (Hanya dari hari ini ke belakang)
            $start_date = $bulan . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            if ($bulan == date('Y-m')) {
                $end_date = date('Y-m-d'); // limit to today if current month
            }

            // Fetch Libur
            $stmt_libur = $this->core_db->prepare("SELECT tanggal_mulai, tanggal_selesai, kegiatan FROM kalender_pendidikan WHERE kategori = 'Libur' AND tanggal_mulai <= ? AND tanggal_selesai >= ?");
            $stmt_libur->execute([$end_date, $start_date]);
            $libur_raw = $stmt_libur->fetchAll(\PDO::FETCH_ASSOC);
            $libur_map = [];
            foreach ($libur_raw as $lib) {
                $curr_lib = strtotime($lib['tanggal_mulai']);
                $end_lib = strtotime($lib['tanggal_selesai']);
                while ($curr_lib <= $end_lib) {
                    $libur_map[date('Y-m-d', $curr_lib)] = $lib['kegiatan'];
                    $curr_lib += 86400;
                }
            }

            $current = strtotime($end_date);
            $start = strtotime($start_date);

            while ($current >= $start) {
                $tgl = date('Y-m-d', $current);
                $hari_inggris = date('l', $current);
                $hari_indo = $days_indo[$hari_inggris];
                
                if (isset($libur_map[$tgl]) && isset($jadwal_per_hari[$hari_indo])) {
                    $final_display[] = [
                        'tanggal' => $tgl,
                        'hari_indo' => $hari_indo,
                        'is_libur' => true,
                        'kegiatan_libur' => $libur_map[$tgl],
                        'items' => []
                    ];
                } elseif (isset($jadwal_per_hari[$hari_indo])) {
                    $day_data = [
                        'tanggal' => $tgl,
                        'hari_indo' => $hari_indo,
                        'is_libur' => false,
                        'items' => []
                    ];
                    
                    foreach ($jadwal_per_hari[$hari_indo] as $jdwl) {
                        $mapel_id = $jdwl['mapel_id'];
                        $jurnal = $jurnal_map[$tgl][$mapel_id] ?? null;
                        
                        // Ambil absensi jika ada jurnal
                        $hadir = 0; $sakit = 0; $izin = 0; $alpha = 0;
                        if ($jurnal) {
                            $stmt_absen = $this->siakad_db->prepare("SELECT status, COUNT(*) as jml FROM absensi_permapel WHERE jurnal_id = ? GROUP BY status");
                            $stmt_absen->execute([$jurnal['id']]);
                            $counts = $stmt_absen->fetchAll(\PDO::FETCH_KEY_PAIR);
                            $hadir = $counts['Hadir'] ?? 0;
                            $sakit = $counts['Sakit'] ?? 0;
                            $izin = $counts['Izin'] ?? 0;
                            $alpha = ($counts['Alpha'] ?? 0) + ($counts['Alpa'] ?? 0);
                        }
                        
                        $day_data['items'][] = [
                            'jadwal' => $jdwl,
                            'jurnal' => $jurnal,
                            'absen' => [
                                'hadir' => $hadir,
                                'sakit' => $sakit,
                                'izin' => $izin,
                                'alpha' => $alpha
                            ]
                        ];
                    }
                    $final_display[] = $day_data;
                }
                
                $current -= 86400; // mundur 1 hari
            }
        }
        
        $title = "Jurnal Kelas - Wali Kelas";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/wali_kelas_jurnal.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function kamadMonitoringJurnal() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];

        // Verify if user is KAMAD
        $isKamad = false;
        $isWakaKurikulum = false;
        $stmt_wk = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ? LIMIT 1");
        $stmt_wk->execute([$guru_id]);
        $g_data = $stmt_wk->fetch(\PDO::FETCH_ASSOC);
        if ($g_data) {
            if ($g_data['is_kamad'] == 1 || (stripos($g_data['jabatan'], 'Kepala') !== false || stripos($g_data['jabatan'], 'Kamad') !== false)) {
                $isKamad = true;
            }
            if (stripos($g_data['jabatan'], 'Kurikulum') !== false || stripos($g_data['jabatan'], 'Waka Kurikulum') !== false) {
                $isWakaKurikulum = true;
            }
        }
        if (!$isKamad && !$isWakaKurikulum) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        // Fetch all active classes
        $stmt_classes = $this->siakad_db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
        $classes = $stmt_classes->fetchAll(\PDO::FETCH_ASSOC);

        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : ($classes[0]['id'] ?? 0);
        $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
        
        $days_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $months_indo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];

        $final_display = [];
        $selected_class_name = "";

        if ($kelas_id) {
            // Find selected class name
            foreach ($classes as $c) {
                if ($c['id'] == $kelas_id) {
                    $selected_class_name = $c['nama_kelas'];
                    break;
                }
            }

            // 1. Ambil Jadwal Pelajaran Kelas Ini
            $stmt_jadwal = $this->siakad_db->prepare("
                SELECT jp.hari, jp.jam_ke, jp.jam_mulai, jp.jam_selesai, m.nama_mapel, g.nama as nama_guru, jp.mapel_id, jp.guru_id
                FROM jadwal_pelajaran jp
                JOIN mapel m ON m.id = jp.mapel_id
                LEFT JOIN guru g ON g.id = jp.guru_id
                WHERE jp.kelas_id = ?
                ORDER BY FIELD(jp.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jp.jam_mulai
            ");
            $stmt_jadwal->execute([$kelas_id]);
            $jadwal_list = $stmt_jadwal->fetchAll(\PDO::FETCH_ASSOC);

            $jadwal_per_hari = [];
            foreach ($jadwal_list as $jdwl) {
                $nama_mapel = strtolower($jdwl['nama_mapel']);
                $exclude_words = ['pembiasaan', 'ekstra', 'senam', 'upacara', 'sholat', 'pramuka', 'juz amma', 'asmaul husna', 'istirahat'];
                $is_excluded = false;
                foreach ($exclude_words as $word) {
                    if (strpos($nama_mapel, $word) !== false) {
                        $is_excluded = true;
                        break;
                    }
                }
                
                if ($is_excluded) {
                    continue;
                }

                $hari = ucfirst(strtolower(trim($jdwl['hari'])));
                if ($hari === 'Ahad') {
                    $hari = 'Minggu';
                }
                $mapel_id = $jdwl['mapel_id'];
                
                if (!isset($jadwal_per_hari[$hari][$mapel_id])) {
                    $jadwal_per_hari[$hari][$mapel_id] = $jdwl;
                } else {
                    if (strtotime($jdwl['jam_selesai']) > strtotime($jadwal_per_hari[$hari][$mapel_id]['jam_selesai'])) {
                        $jadwal_per_hari[$hari][$mapel_id]['jam_selesai'] = $jdwl['jam_selesai'];
                    }
                }
            }

            // 2. Ambil Jurnal Guru untuk bulan ini
            $stmt_jurnal = $this->siakad_db->prepare("
                SELECT jg.id, jg.tanggal, jg.mapel_id, jg.materi, jg.hambatan, 
                       g.nama as nama_guru_jurnal, m.nama_mapel
                FROM jurnal_guru jg
                JOIN guru g ON g.id = jg.guru_id
                JOIN mapel m ON m.id = jg.mapel_id
                WHERE jg.kelas_id = ? AND DATE_FORMAT(jg.tanggal, '%Y-%m') = ?
            ");
            $stmt_jurnal->execute([$kelas_id, $bulan]);
            $jurnals_raw = $stmt_jurnal->fetchAll(\PDO::FETCH_ASSOC);

            $jurnal_map = [];
            foreach($jurnals_raw as $jr) {
                $jurnal_map[$jr['tanggal']][$jr['mapel_id']] = $jr;
            }

            // 3. Bangun Rekap Berdasarkan Hari di Bulan Ini
            $start_date = $bulan . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            if ($bulan == date('Y-m')) {
                $end_date = date('Y-m-d');
            }

            // Fetch dates that are holidays or kegiatans
            $stmt_libur_dates = $this->core_db->prepare("SELECT tanggal_mulai, tanggal_selesai, kategori, kegiatan, jam_pulang FROM kalender_pendidikan WHERE kategori IN ('Libur', 'Kegiatan', 'Pulang Dipercepat') AND tanggal_selesai >= ? AND tanggal_mulai <= ?");
            $stmt_libur_dates->execute([$start_date, $end_date]);
            $libur_periods = $stmt_libur_dates->fetchAll(\PDO::FETCH_ASSOC);
            
            $special_dates = [];
            foreach ($libur_periods as $lp) {
                $c = strtotime($lp['tanggal_mulai']);
                $e = strtotime($lp['tanggal_selesai'] ?? $lp['tanggal_mulai']);
                while ($c <= $e) {
                    $dt = date('Y-m-d', $c);
                    if (!isset($special_dates[$dt])) {
                        $special_dates[$dt] = [];
                    }
                    if ($lp['kategori'] === 'Pulang Dipercepat') {
                        $special_dates[$dt]['pulang_dipercepat'] = [
                            'jam_pulang' => $lp['jam_pulang'],
                            'kegiatan' => $lp['kegiatan'] ?? 'Pulang Dipercepat'
                        ];
                    } else {
                        $special_dates[$dt]['is_full_day'] = true;
                        $special_dates[$dt]['kategori'] = $lp['kategori'];
                        $special_dates[$dt]['kegiatan'] = $lp['kegiatan'];
                    }
                    $c = strtotime('+1 day', $c);
                }
            }

            $current = strtotime($end_date);
            $start = strtotime($start_date);

            while ($current >= $start) {
                $tgl = date('Y-m-d', $current);
                $hari_inggris = date('l', $current);
                $hari_indo = $days_indo[$hari_inggris];
                if (isset($jadwal_per_hari[$hari_indo])) {
                    $sd = isset($special_dates[$tgl]) ? $special_dates[$tgl] : [];
                    $is_full_day_special = isset($sd['is_full_day']) ? $sd : false;
                    
                    $day_data = [
                        'tanggal' => $tgl,
                        'hari_indo' => $hari_indo,
                        'is_special' => $is_full_day_special,
                        'pulang_dipercepat' => $sd['pulang_dipercepat'] ?? null,
                        'items' => []
                    ];
                    
                    if (!$day_data['is_special']) {
                        foreach ($jadwal_per_hari[$hari_indo] as $jdwl) {
                            $mapel_id = $jdwl['mapel_id'];
                            $jurnal = $jurnal_map[$tgl][$mapel_id] ?? null;
                            
                            $hadir = 0; $sakit = 0; $izin = 0; $alpha = 0;
                            if ($jurnal) {
                                $stmt_absen = $this->siakad_db->prepare("SELECT status, COUNT(*) as jml FROM absensi_permapel WHERE jurnal_id = ? GROUP BY status");
                                $stmt_absen->execute([$jurnal['id']]);
                                $counts = $stmt_absen->fetchAll(\PDO::FETCH_KEY_PAIR);
                                $hadir = $counts['Hadir'] ?? 0;
                                $sakit = $counts['Sakit'] ?? 0;
                                $izin = $counts['Izin'] ?? 0;
                                $alpha = ($counts['Alpha'] ?? 0) + ($counts['Alpa'] ?? 0);
                            }
                            
                            $day_data['items'][] = [
                                'jadwal' => $jdwl,
                                'jurnal' => $jurnal,
                                'absen' => [
                                    'hadir' => $hadir,
                                    'sakit' => $sakit,
                                    'izin' => $izin,
                                    'alpha' => $alpha
                                ]
                            ];
                        }
                    }
                    $final_display[] = $day_data;
                }
                $current -= 86400; // mundur 1 hari
            }
        }
        
        $title = "Monitoring Jurnal";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/kamad_monitoring_jurnal.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function kamadRekapAbsen() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];

        // Verify if user is KAMAD
        $isKamad = false;
        $isWakaKesiswaan = false;
        $isBK = false;
        $stmt_wk = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ? LIMIT 1");
        $stmt_wk->execute([$guru_id]);
        $g_data = $stmt_wk->fetch(\PDO::FETCH_ASSOC);
        if ($g_data) {
            if ($g_data['is_kamad'] == 1 || (stripos($g_data['jabatan'], 'Kepala') !== false || stripos($g_data['jabatan'], 'Kamad') !== false)) {
                $isKamad = true;
            }
            if (stripos($g_data['jabatan'], 'Kesiswaan') !== false || stripos($g_data['jabatan'], 'Waka Kesiswaan') !== false) {
                $isWakaKesiswaan = true;
            }
            if (stripos($g_data['jabatan'], 'BK') !== false || stripos($g_data['jabatan'], 'Bimbingan') !== false) {
                $isBK = true;
            }
        }
        if (!$isKamad && !$isWakaKesiswaan && !$isBK) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        // Fetch all active classes
        $stmt_classes = $this->siakad_db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
        $classes = $stmt_classes->fetchAll(\PDO::FETCH_ASSOC);

        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : ($classes[0]['id'] ?? 0);
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');

        $absen_list = [];
        $selected_class_name = "";
        
        if ($kelas_id) {
            // Find selected class name
            foreach ($classes as $c) {
                if ($c['id'] == $kelas_id) {
                    $selected_class_name = $c['nama_kelas'];
                    break;
                }
            }

            $stmt_a = $this->siakad_db->prepare("
                SELECT s.id, s.nama, s.nis, s.foto,
                    SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN a.status = 'Alpa' OR a.status = 'Alpha' THEN 1 ELSE 0 END) as alpa
                FROM siswa s 
                LEFT JOIN absensi_permapel a ON s.id = a.siswa_id AND a.tanggal >= ? AND a.tanggal <= ?
                WHERE s.kelas_id = ? AND s.status = 'Aktif'
                GROUP BY s.id, s.nama, s.nis, s.foto
                ORDER BY s.nama ASC
            ");
            $stmt_a->execute([$start_date, $end_date, $kelas_id]);
            $absen_list = $stmt_a->fetchAll(\PDO::FETCH_ASSOC);
        }

        $title = "Rekap Absen Kelas";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/kamad_rekap_absen.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function kamadMonitorQRSiswa() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $isKamad = false;
        $isWakaKesiswaan = false;
        $isBK = false;
        $stmt_wk = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ? LIMIT 1");
        $stmt_wk->execute([$guru_id]);
        $g_data = $stmt_wk->fetch(\PDO::FETCH_ASSOC);
        if ($g_data) {
            if ($g_data['is_kamad'] == 1 || (stripos($g_data['jabatan'], 'Kepala') !== false || stripos($g_data['jabatan'], 'Kamad') !== false)) {
                $isKamad = true;
            }
            if (stripos($g_data['jabatan'], 'Kesiswaan') !== false || stripos($g_data['jabatan'], 'Waka Kesiswaan') !== false) {
                $isWakaKesiswaan = true;
            }
            if (stripos($g_data['jabatan'], 'BK') !== false || stripos($g_data['jabatan'], 'Bimbingan') !== false) {
                $isBK = true;
            }
        }
        if (!$isKamad && !$isWakaKesiswaan && !$isBK) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $today = date('Y-m-d');
        $title = "Monitor QR Siswa";
        $activeNav = "apps";
        
        $kelasList = $this->core_db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : ($kelasList[0]['id'] ?? 0);

        $absen_list = [];
        if ($kelas_id) {
            $stmt = $this->siakad_db->prepare("
                SELECT s.id, s.nama, s.nis, s.foto, a.status, a.created_at
                FROM " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s
                LEFT JOIN absensi_siswa a ON s.id = a.siswa_id AND a.tanggal = ?
                WHERE s.kelas_id = ? AND s.status = 'Aktif'
                ORDER BY a.created_at DESC, s.nama ASC
            ");
            $stmt->execute([$today, $kelas_id]);
            $absen_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/kamad_monitor_qr_siswa.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function kamadRekapQRSiswa() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $isKamad = false;
        $isWakaKesiswaan = false;
        $isBK = false;
        $stmt_wk = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ? LIMIT 1");
        $stmt_wk->execute([$guru_id]);
        $g_data = $stmt_wk->fetch(\PDO::FETCH_ASSOC);
        if ($g_data) {
            if ($g_data['is_kamad'] == 1 || (stripos($g_data['jabatan'], 'Kepala') !== false || stripos($g_data['jabatan'], 'Kamad') !== false)) {
                $isKamad = true;
            }
            if (stripos($g_data['jabatan'], 'Kesiswaan') !== false || stripos($g_data['jabatan'], 'Waka Kesiswaan') !== false) {
                $isWakaKesiswaan = true;
            }
            if (stripos($g_data['jabatan'], 'BK') !== false || stripos($g_data['jabatan'], 'Bimbingan') !== false) {
                $isBK = true;
            }
        }
        if (!$isKamad && !$isWakaKesiswaan && !$isBK) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $title = "Rekap QR Siswa";
        $activeNav = "apps";
        
        $classes = $this->siakad_db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : ($classes[0]['id'] ?? 0);
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');

        $selected_class_name = "";
        $absen_list = [];
        if ($kelas_id) {
            foreach ($classes as $c) {
                if ($c['id'] == $kelas_id) {
                    $selected_class_name = $c['nama_kelas'];
                    break;
                }
            }

            $stmt_a = $this->siakad_db->prepare("
                SELECT s.id, s.nama, s.nis, s.foto,
                    SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN a.status = 'Alpa' OR a.status = 'Alpha' THEN 1 ELSE 0 END) as alpa,
                    SUM(CASE WHEN a.status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat
                FROM " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s 
                LEFT JOIN absensi_siswa a ON s.id = a.siswa_id AND a.tanggal >= ? AND a.tanggal <= ?
                WHERE s.kelas_id = ? AND s.status = 'Aktif'
                GROUP BY s.id, s.nama, s.nis, s.foto
                ORDER BY s.nama ASC
            ");
            $stmt_a->execute([$start_date, $end_date, $kelas_id]);
            $absen_list = $stmt_a->fetchAll(\PDO::FETCH_ASSOC);
        }

        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/kamad_rekap_qr_siswa.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function kamadMonitorPenilaian() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $isKamad = false;
        $isWakaKurik = false;
        $stmt_wk = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ? LIMIT 1");
        $stmt_wk->execute([$guru_id]);
        $g_data = $stmt_wk->fetch(\PDO::FETCH_ASSOC);
        if ($g_data) {
            if ($g_data['is_kamad'] == 1 || (stripos($g_data['jabatan'], 'Kepala') !== false || stripos($g_data['jabatan'], 'Kamad') !== false)) {
                $isKamad = true;
            }
            if (stripos($g_data['jabatan'], 'Kurikulum') !== false || stripos($g_data['jabatan'], 'Waka Kurikulum') !== false) {
                $isWakaKurik = true;
            }
        }
        
        $stmtT = $this->core_db->prepare("SELECT tugas FROM guru_tugas WHERE guru_id = ?");
        $stmtT->execute([$guru_id]);
        $tgs = $stmtT->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tgs as $t) {
            if (stripos($t, 'Kurikulum') !== false) $isWakaKurik = true;
        }

        if (!$isKamad && !$isWakaKurik) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $title = "Monitor Penilaian";
        $activeNav = "apps";
        
        $list_guru = $this->core_db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $selected_guru_id = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;

        $stmt_ta = $this->core_db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC);
        $tahun_ajaran_id = $ta ? $ta['id'] : 0;
        $semester = $ta ? $ta['semester'] : 0;

        $data_mengajar = [];
        if ($selected_guru_id && $tahun_ajaran_id) {
            // Find all classes & mapels this teacher teaches
            $stmt_jp = $this->siakad_db->prepare("
                SELECT combined.kelas_id, combined.mapel_id, k.nama_kelas, m.nama_mapel 
                FROM (
                    SELECT kelas_id, mapel_id FROM penugasan_mengajar WHERE guru_id = ?
                    UNION
                    SELECT kelas_id, mapel_id FROM jadwal_pelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
                ) AS combined
                JOIN kelas k ON combined.kelas_id = k.id
                JOIN mapel m ON combined.mapel_id = m.id
                ORDER BY k.tingkat ASC, k.nama_kelas ASC, m.nama_mapel ASC
            ");
            $stmt_jp->execute([$selected_guru_id, $selected_guru_id, $tahun_ajaran_id, $semester]);
            $mengajar = $stmt_jp->fetchAll(\PDO::FETCH_ASSOC);

            // Get active students count per class
            $stmt_siswa = $this->core_db->query("SELECT kelas_id, COUNT(id) as total FROM siswa WHERE status = 'Aktif' GROUP BY kelas_id");
            $siswaCount = $stmt_siswa->fetchAll(\PDO::FETCH_KEY_PAIR);

            // Get supervision data
            $stmt_supervisi = $this->core_db->prepare("SELECT * FROM kurikulum_supervisi_nilai WHERE tahun_ajaran_id = ? AND semester = ? AND guru_id = ?");
            $stmt_supervisi->execute([$tahun_ajaran_id, $semester, $selected_guru_id]);
            $supervisi_raw = $stmt_supervisi->fetchAll(\PDO::FETCH_ASSOC);
            
            $supervisi_grouped = [];
            foreach ($supervisi_raw as $sr) {
                $supervisi_grouped[$sr['kelas_id']][$sr['mapel_id']][$sr['jenis_evaluasi']] = $sr;
            }

            foreach ($mengajar as $m) {
                $c_id = $m['kelas_id'];
                $mp_id = $m['mapel_id'];
                $total_siswa = $siswaCount[$c_id] ?? 0;

                // Find distinct jenis_evaluasi entered by this teacher for this class and mapel
                $stmt_eval = $this->siakad_db->prepare("
                    SELECT jenis_evaluasi, COUNT(siswa_id) as jumlah_terisi, MAX(materi) as materi
                    FROM nilai_harian
                    WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ?
                    GROUP BY jenis_evaluasi
                    ORDER BY jenis_evaluasi ASC
                ");
                $stmt_eval->execute([$c_id, $mp_id, $tahun_ajaran_id, $semester]);
                $eval_list = $stmt_eval->fetchAll(\PDO::FETCH_ASSOC);

                $evaluasi_data = [];
                foreach ($eval_list as $e) {
                    // Fetch detail student scores for this evaluasi, including empty ones
                    $stmt_detail = $this->siakad_db->prepare("
                        SELECT s.nama, n.nilai 
                        FROM " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s
                        LEFT JOIN nilai_harian n ON s.id = n.siswa_id 
                            AND n.kelas_id = ? AND n.mapel_id = ? AND n.tahun_ajaran_id = ? AND n.semester = ? AND n.jenis_evaluasi = ?
                        WHERE s.kelas_id = ? AND s.status = 'Aktif'
                        ORDER BY s.nama ASC
                    ");
                    $stmt_detail->execute([$c_id, $mp_id, $tahun_ajaran_id, $semester, $e['jenis_evaluasi'], $c_id]);
                    $details = $stmt_detail->fetchAll(\PDO::FETCH_ASSOC);
                    
                    $sup = $supervisi_grouped[$c_id][$mp_id][$e['jenis_evaluasi']] ?? null;

                    $evaluasi_data[] = [
                        'nama_evaluasi' => $e['jenis_evaluasi'],
                        'terisi' => $e['jumlah_terisi'],
                        'total' => $total_siswa,
                        'persen' => $total_siswa > 0 ? round(($e['jumlah_terisi'] / $total_siswa) * 100) : 0,
                        'materi' => $e['materi'],
                        'details' => $details,
                        'supervisi' => $sup
                    ];
                }

                $data_mengajar[] = [
                    'kelas_id' => $c_id,
                    'mapel_id' => $mp_id,
                    'nama_kelas' => $m['nama_kelas'],
                    'nama_mapel' => $m['nama_mapel'],
                    'evaluasi' => $evaluasi_data
                ];
            }
        }

        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/kamad_monitor_penilaian.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function kamadSupervisiPenilaianEvaluasi() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id_admin = $_SESSION['guru_id'];
        $isKamad = false;
        $isWakaKurik = false;

        $stmt_wk = $this->core_db->prepare("SELECT jabatan, is_kamad FROM guru WHERE id = ? LIMIT 1");
        $stmt_wk->execute([$guru_id_admin]);
        $g_data = $stmt_wk->fetch(\PDO::FETCH_ASSOC);
        if ($g_data) {
            if ($g_data['is_kamad'] == 1 || (stripos($g_data['jabatan'], 'Kepala') !== false || stripos($g_data['jabatan'], 'Kamad') !== false)) {
                $isKamad = true;
            }
            if (stripos($g_data['jabatan'], 'Kurikulum') !== false || stripos($g_data['jabatan'], 'Waka Kurikulum') !== false) {
                $isWakaKurik = true;
            }
        }
        
        $stmtT = $this->core_db->prepare("SELECT tugas FROM guru_tugas WHERE guru_id = ?");
        $stmtT->execute([$guru_id_admin]);
        $tgs = $stmtT->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tgs as $t) {
            if (stripos($t, 'Kurikulum') !== false) $isWakaKurik = true;
        }

        if (!$isKamad && !$isWakaKurik) {
            // Helper::redirect('/apk/aplikasi-guru');
        }

        $guru_id = $_GET['guru_id'] ?? 0;
        $kelas_id = $_GET['kelas_id'] ?? 0;
        $mapel_id = $_GET['mapel_id'] ?? 0;
        $jenis_evaluasi = $_GET['jenis_evaluasi'] ?? '';

        if (!$guru_id || !$kelas_id || !$mapel_id || !$jenis_evaluasi) {
            Helper::redirect('/apk/kamad/monitor-penilaian');
        }

        $stmtGuru = $this->core_db->prepare("SELECT id, nama, nip FROM guru WHERE id = ?");
        $stmtGuru->execute([$guru_id]);
        $guru = $stmtGuru->fetch(\PDO::FETCH_ASSOC);

        $stmtKelas = $this->siakad_db->prepare("SELECT id, nama_kelas FROM kelas WHERE id = ?");
        $stmtKelas->execute([$kelas_id]);
        $kelas = $stmtKelas->fetch(\PDO::FETCH_ASSOC);

        $stmtMapel = $this->siakad_db->prepare("SELECT id, nama_mapel FROM mapel WHERE id = ?");
        $stmtMapel->execute([$mapel_id]);
        $mapel = $stmtMapel->fetch(\PDO::FETCH_ASSOC);

        if (!$guru || !$kelas || !$mapel) {
            Helper::redirect('/apk/kamad/monitor-penilaian');
        }

        $stmt_ta = $this->core_db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC);
        $tahun_ajaran_id = $ta ? $ta['id'] : 0;
        $semester = $ta ? $ta['semester'] : 0;

        $supervisi = null;
        $stmtSup = $this->core_db->prepare("SELECT * FROM kurikulum_supervisi_nilai WHERE tahun_ajaran_id = ? AND semester = ? AND guru_id = ? AND kelas_id = ? AND mapel_id = ? AND jenis_evaluasi = ?");
        $stmtSup->execute([$tahun_ajaran_id, $semester, $guru_id, $kelas_id, $mapel_id, $jenis_evaluasi]);
        $supervisi = $stmtSup->fetch(\PDO::FETCH_ASSOC);

        $title = "Evaluasi Supervisi Penilaian";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/kamad_supervisi_penilaian_evaluasi.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function kamadSupervisiPenilaianSave() {
        if (!isset($_SESSION['guru_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthenticated']);
            exit;
        }

        $guru_id_admin = $_SESSION['guru_id'];
        
        $stmt_ta = $this->core_db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC);
        $tahun_ajaran_id = $ta ? $ta['id'] : 0;
        $semester = $ta ? $ta['semester'] : 0;

        $guru_id = $_POST['guru_id'] ?? 0;
        $kelas_id = $_POST['kelas_id'] ?? 0;
        $mapel_id = $_POST['mapel_id'] ?? 0;
        $jenis_evaluasi = $_POST['jenis_evaluasi'] ?? '';
        
        $ada_kisi = isset($_POST['ada_kisi']) ? 1 : 0;
        $ada_analisis = isset($_POST['ada_analisis']) ? 1 : 0;
        $ada_remedial = isset($_POST['ada_remedial']) ? 1 : 0;
        $catatan = $_POST['catatan'] ?? '';
        $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';

        try {
            $stmt = $this->core_db->prepare("
                INSERT INTO kurikulum_supervisi_nilai 
                (tahun_ajaran_id, semester, guru_id, kelas_id, mapel_id, jenis_evaluasi, ada_kisi, ada_analisis, ada_remedial, catatan, tindak_lanjut, disupervisi_oleh) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                ada_kisi = VALUES(ada_kisi),
                ada_analisis = VALUES(ada_analisis),
                ada_remedial = VALUES(ada_remedial),
                catatan = VALUES(catatan),
                tindak_lanjut = VALUES(tindak_lanjut),
                disupervisi_oleh = VALUES(disupervisi_oleh)
            ");
            $stmt->execute([
                $tahun_ajaran_id, $semester, $guru_id, $kelas_id, $mapel_id, $jenis_evaluasi,
                $ada_kisi, $ada_analisis, $ada_remedial, $catatan, $tindak_lanjut, $guru_id_admin
            ]);
            
            echo json_encode(['status' => 'success', 'message' => 'Data supervisi berhasil disimpan.']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
        }
        exit;
    }

    public function waliKelasMonitorAbsen() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $aktif = AcademicYear::current();
        $active_year_name = $aktif['name'] ?? '2025/2026';

        $stmt_tugas = $this->core_db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru_id, $active_year_name]);
        $keterangan = $stmt_tugas->fetchColumn();

        if (!$keterangan) {
            Helper::redirect('/apk/aplikasi-guru');
        }

        $nama_kelas_raw = str_ireplace('Kelas ', '', $keterangan);
        $nama_kelas_raw = trim($nama_kelas_raw);

        $stmt_k = $this->siakad_db->prepare("SELECT id FROM kelas WHERE nama_kelas = ?");
        $stmt_k->execute([$nama_kelas_raw]);
        $kelas_id = $stmt_k->fetchColumn();

        if (!$kelas_id) {
            Helper::redirect('/apk/aplikasi-guru');
        }

        $today = $_GET['tgl'] ?? date('Y-m-d');
        $title = "Absensi Kelas";

        $sql = "
            SELECT a.siswa_id, a.status, m.nama_mapel, s.nama, s.kelas_id, k.nama_kelas, k.tingkat, asis.jam_masuk, asis.status as asis_status
            FROM " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_permapel a
            JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s ON a.siswa_id = s.id
            JOIN " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".jurnal_guru j ON a.jurnal_id = j.id
            LEFT JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".mapel m ON j.mapel_id = m.id
            LEFT JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".kelas k ON s.kelas_id = k.id
            LEFT JOIN " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_siswa asis ON asis.siswa_id = s.id AND asis.tanggal = a.tanggal
            WHERE a.tanggal = ? AND s.status = 'Aktif' AND k.id = ?
            ORDER BY k.tingkat ASC, k.nama_kelas ASC, s.nama ASC, j.id ASC
        ";
        $stmt = $this->siakad_db->prepare($sql);
        $stmt->execute([$today, $kelas_id]);
        $raw_data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $kelasData = [];
        $studentData = [];
        
        foreach ($raw_data as $r) {
            $k_id = $r['kelas_id'];
            $s_id = $r['siswa_id'];
            
            if (!isset($kelasData[$k_id])) {
                $tot = $this->core_db->prepare("SELECT COUNT(*) FROM siswa WHERE kelas_id = ? AND status='Aktif'");
                $tot->execute([$k_id]);
                $total_siswa = $tot->fetchColumn();
                
                $kelasData[$k_id] = [
                    'id' => $k_id,
                    'nama' => $r['nama_kelas'],
                    'tingkat' => $r['tingkat'],
                    'total' => $total_siswa,
                    'hadir' => 0,
                    'bolos' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpha' => 0,
                    'students' => []
                ];
            }
            
            if (!isset($studentData[$k_id][$s_id])) {
                $studentData[$k_id][$s_id] = [
                    'siswa_id' => $s_id,
                    'nama' => $r['nama'],
                    'mapel_dict' => [],
                    'masuk' => empty($r['jam_masuk']) ? '-' : $r['jam_masuk'],
                    'final_status' => empty($r['asis_status']) ? '-' : $r['asis_status']
                ];
            }
            
            $mapel_name = $r['nama_mapel'] ?? 'Mapel';
            $st = $r['status'];
            if ($st === 'Bolos' || $st === 'Alpa') $st = 'Alpha';
            $studentData[$k_id][$s_id]['mapel_dict'][$mapel_name] = $st;
        }

        foreach ($studentData as $k_id => $students) {
            foreach ($students as $s_id => $s_data) {
                $statuses = array_values($s_data['mapel_dict']);
                $masuk = $s_data['masuk'];
                
                $status_pusat = $s_data['final_status'];
                
                if (!in_array($status_pusat, ['Sakit', 'Izin', 'Alpa', 'Alpha'])) {
                    if ($masuk === '-' && in_array('Hadir', $statuses)) {
                        $status_pusat = 'Bolos';
                    } elseif ($masuk !== '-') {
                        $status_pusat = 'Hadir';
                    }
                }
                
                $studentData[$k_id][$s_id]['final_status'] = $status_pusat;
                
                if ($status_pusat == 'Hadir') $kelasData[$k_id]['hadir']++;
                elseif ($status_pusat == 'Bolos') $kelasData[$k_id]['bolos']++;
                elseif ($status_pusat == 'Sakit') $kelasData[$k_id]['sakit']++;
                elseif ($status_pusat == 'Izin') $kelasData[$k_id]['izin']++;
                elseif ($status_pusat == 'Alpha' || $status_pusat == 'Alpa') $kelasData[$k_id]['alpha']++;
                
                $studentData[$k_id][$s_id]['mapels'] = [];
                foreach ($s_data['mapel_dict'] as $mName => $mStatus) {
                    $studentData[$k_id][$s_id]['mapels'][] = [
                        'mapel' => $mName,
                        'status' => $mStatus
                    ];
                }
                
                $kelasData[$k_id]['students'][] = $studentData[$k_id][$s_id];
            }
        }

        if (empty($kelasData) && $kelas_id) {
            $tot = $this->core_db->prepare("SELECT COUNT(*) FROM siswa WHERE kelas_id = ? AND status='Aktif'");
            $tot->execute([$kelas_id]);
            $total_siswa = $tot->fetchColumn();
            $kelasData[$kelas_id] = [
                'id' => $kelas_id,
                'nama' => $nama_kelas_raw,
                'tingkat' => '',
                'total' => $total_siswa,
                'hadir' => 0, 'bolos' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0,
                'students' => []
            ];
        }

        ob_start();
        include __DIR__ . '/../../resources/views/apk/wali_kelas_monitor_absen.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function waliKelasAbsen() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        
        $aktif = AcademicYear::current();
        $active_year_name = $aktif['name'] ?? '2025/2026';

        // Cari tahu guru ini wali kelas berapa
        $stmt_tugas = $this->core_db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru_id, $active_year_name]);
        $keterangan = $stmt_tugas->fetchColumn();

        if (!$keterangan) {
            Helper::redirect('/apk/aplikasi-guru');
        }

        $nama_kelas_raw = str_ireplace('Kelas ', '', $keterangan);
        $nama_kelas_raw = trim($nama_kelas_raw);

        // Cari kelas_id di siakad_db
        $stmt_k = $this->siakad_db->prepare("SELECT id, tingkat, nama_kelas FROM kelas WHERE nama_kelas = ?");
        $stmt_k->execute([$nama_kelas_raw]);
        $kelasInfo = $stmt_k->fetch(\PDO::FETCH_ASSOC);

        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');

        $absen_list = [];
        if ($kelasInfo) {
            $filter_kelas_id = $kelasInfo['id'];
            $sql = "
                SELECT a.siswa_id, a.tanggal, a.status, s.nama, s.nis, s.foto, k.nama_kelas, m.nama_mapel, asis.jam_masuk, asis.status as asis_status
                FROM " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_permapel a
                JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s ON a.siswa_id = s.id
                JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".kelas k ON s.kelas_id = k.id
                JOIN " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".jurnal_guru j ON a.jurnal_id = j.id
                LEFT JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".mapel m ON j.mapel_id = m.id
                LEFT JOIN " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_siswa asis ON asis.siswa_id = s.id AND asis.tanggal = a.tanggal
                WHERE a.tanggal >= ? AND a.tanggal <= ? AND s.kelas_id = ? AND s.status = 'Aktif'
                ORDER BY a.tanggal ASC, s.nama ASC, j.id ASC
            ";
            $stmt = $this->siakad_db->prepare($sql);
            $stmt->execute([$start_date, $end_date, $filter_kelas_id]);
            $raw_data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $temp = [];
            foreach ($raw_data as $r) {
                $sid = $r['siswa_id'];
                $tgl = $r['tanggal'];
                
                if (!isset($temp[$sid])) {
                    $temp[$sid] = [
                        'siswa_id' => $sid,
                        'nama' => $r['nama'],
                        'nis' => $r['nis'],
                        'foto' => $r['foto'],
                        'nama_kelas' => $r['nama_kelas'],
                        'dates' => []
                    ];
                }
                
                if (!isset($temp[$sid]['dates'][$tgl])) {
                    $temp[$sid]['dates'][$tgl] = [
                        'mapel_dict' => [],
                        'masuk' => empty($r['jam_masuk']) ? '-' : $r['jam_masuk'],
                        'final_status' => empty($r['asis_status']) ? '-' : $r['asis_status']
                    ];
                }
                
                $st = $r['status'];
                if ($st === 'Bolos' || $st === 'Alpa') $st = 'Alpha';
                
                $temp[$sid]['dates'][$tgl]['mapel_dict'][$r['nama_mapel'] ?? 'Mapel'] = $st;
            }
            
            foreach ($temp as $sid => $sData) {
                $rekap_list[$sid] = [
                    'siswa_id' => $sid,
                    'nama' => $sData['nama'],
                    'nis' => $sData['nis'],
                    'foto' => $sData['foto'],
                    'nama_kelas' => $sData['nama_kelas'],
                    'hadir' => 0,
                    'bolos' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpa' => 0
                ];
                
                foreach ($sData['dates'] as $tgl => $dData) {
                    
                    $status_pusat = $dData['final_status'];
                    $masuk = $dData['masuk'];
                    
                    if (!in_array($status_pusat, ['Sakit', 'Izin', 'Alpa', 'Alpha'])) {
                        $statuses = array_values($dData['mapel_dict']);
                        if ($masuk === '-' && in_array('Hadir', $statuses)) {
                            $status_pusat = 'Bolos';
                        } elseif ($masuk !== '-') {
                            $status_pusat = 'Hadir';
                        }
                    }
                    
                    if ($status_pusat == 'Hadir') $rekap_list[$sid]['hadir']++;
                    elseif ($status_pusat == 'Bolos') $rekap_list[$sid]['bolos']++;
                    elseif ($status_pusat == 'Sakit') $rekap_list[$sid]['sakit']++;
                    elseif ($status_pusat == 'Izin') $rekap_list[$sid]['izin']++;
                    elseif ($status_pusat == 'Alpha' || $status_pusat == 'Alpa') $rekap_list[$sid]['alpa']++;
                }
                $absen_list[] = $rekap_list[$sid];
            }
            
            // Urutkan absen_list berdasarkan nama ASC
            usort($absen_list, function($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
        }

        $title = "Rekap Kehadiran";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/wali_kelas_absen.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }
    public function waliKelasBukuKerja() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        
        $aktif = AcademicYear::current();
        $active_year_name = $aktif['name'] ?? '2025/2026';
        $active_year_id = $aktif['id'] ?? 1;

        // Cari tahu guru ini wali kelas berapa
        $stmt_tugas = $this->core_db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru_id, $active_year_name]);
        $keterangan = $stmt_tugas->fetchColumn();

        if (!$keterangan) {
            Helper::redirect('/apk/aplikasi-guru');
        }

        $nama_kelas_raw = str_ireplace('Kelas ', '', $keterangan);
        $nama_kelas_raw = trim($nama_kelas_raw);
        
        // Cari kelas_id di siakad_db
        $stmt_k = $this->siakad_db->prepare("SELECT id, tingkat, nama_kelas FROM kelas WHERE nama_kelas = ?");
        $stmt_k->execute([$nama_kelas_raw]);
        $kelasInfo = $stmt_k->fetch(\PDO::FETCH_ASSOC);
        $kelas_id = $kelasInfo ? $kelasInfo['id'] : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $kelas_id > 0) {
            $keys = [
                'ketua_kelas', 'wakil_ketua', 'sekretaris', 'bendahara',
                'piket_sabtu', 'piket_minggu', 'piket_senin', 'piket_selasa', 'piket_rabu', 'piket_kamis'
            ];

            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    $val = is_array($_POST[$key]) ? implode(',', $_POST[$key]) : $_POST[$key];
                    
                    // Check if exists
                    $stmt_cek = $this->core_db->prepare("SELECT id FROM kelas_meta WHERE kelas_id = ? AND tahun_ajaran_id = ? AND meta_key = ?");
                    $stmt_cek->execute([$kelas_id, $active_year_id, $key]);
                    if ($stmt_cek->fetchColumn()) {
                        $stmt_upd = $this->core_db->prepare("UPDATE kelas_meta SET meta_value = ? WHERE kelas_id = ? AND tahun_ajaran_id = ? AND meta_key = ?");
                        $stmt_upd->execute([$val, $kelas_id, $active_year_id, $key]);
                    } else {
                        $stmt_ins = $this->core_db->prepare("INSERT INTO kelas_meta (kelas_id, tahun_ajaran_id, meta_key, meta_value) VALUES (?, ?, ?, ?)");
                        $stmt_ins->execute([$kelas_id, $active_year_id, $key, $val]);
                    }
                }
            }
            $_SESSION['swal_success'] = 'Program Wali Kelas berhasil disimpan.';
            Helper::redirect('/apk/wali-kelas/buku-kerja');
        }

        // Fetch students
        $siswa_list = [];
        if ($kelas_id > 0) {
            $stmt_s = $this->siakad_db->prepare("SELECT id, nama, nis FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
            $stmt_s->execute([$kelas_id]);
            $siswa_list = $stmt_s->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Fetch existing meta
        $meta = [];
        if ($kelas_id > 0) {
            $stmt_m = $this->core_db->prepare("SELECT meta_key, meta_value FROM kelas_meta WHERE kelas_id = ? AND tahun_ajaran_id = ?");
            $stmt_m->execute([$kelas_id, $active_year_id]);
            while ($row = $stmt_m->fetch(\PDO::FETCH_ASSOC)) {
                $meta[$row['meta_key']] = $row['meta_value'];
            }
        }

        $title = "Program Wali Kelas";
        $activeNav = "apps";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/wali_kelas_buku_kerja.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function waliKelasPoin() {
        if (!isset($_SESSION['guru_id'])) \App\Core\Helper::redirect('/apk/login');
        $guru_id = $_SESSION['guru_id'];
        
        $aktif = \App\Core\AcademicYear::current();
        $active_year_name = $aktif['name'] ?? '2025/2026';

        // Cari tahu guru ini wali kelas berapa
        $stmt_tugas = $this->core_db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru_id, $active_year_name]);
        $keterangan = $stmt_tugas->fetchColumn();

        if (!$keterangan) \App\Core\Helper::redirect('/apk/aplikasi-guru');

        $nama_kelas_raw = trim(str_ireplace('Kelas ', '', $keterangan));
        $stmt_k = $this->siakad_db->prepare("SELECT id, tingkat, nama_kelas FROM kelas WHERE nama_kelas = ?");
        $stmt_k->execute([$nama_kelas_raw]);
        $kelasInfo = $stmt_k->fetch(\PDO::FETCH_ASSOC);
        $kelas_id = $kelasInfo ? $kelasInfo['id'] : 0;

        $poinList = [];
        $topSiswa = [];
        if ($kelas_id > 0) {
            $poinList = $this->core_db->query("SELECT p.*, s.nama as nama_siswa, k.nama_kelas, kt.nama_kategori, kt.tipe, g.nama as nama_guru FROM bk_poin p JOIN siswa s ON p.siswa_id = s.id LEFT JOIN kelas k ON s.kelas_id = k.id JOIN bk_kategori kt ON p.kategori_id = kt.id LEFT JOIN guru g ON p.guru_id = g.id WHERE s.kelas_id = $kelas_id ORDER BY p.tanggal DESC")->fetchAll();
            $topSiswa = $this->core_db->query("SELECT bp.siswa_id, s.nama as nama_siswa, k.nama_kelas, SUM(bp.poin) as total_poin FROM bk_poin bp LEFT JOIN siswa s ON bp.siswa_id = s.id LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.kelas_id = $kelas_id GROUP BY bp.siswa_id, s.nama, k.nama_kelas ORDER BY total_poin DESC")->fetchAll();
        }

        $title = "Log Kedisiplinan Kelas";
        $activeNav = "apps";
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/wali_kelas_poin.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function eRapor() {
        if (!isset($_SESSION['guru_id'])) {
            \App\Core\Helper::redirect('/apk/login');
        }
        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'guru') {
            \App\Core\Helper::redirect('/apk/profile');
        }
        $guru_id = $_SESSION['guru_id'];
        $ta = \App\Core\AcademicYear::current();
        $ta_id = $ta ? $ta['id'] : 0;

        // Ambil kombinasi mengajar
        $stmt_kombinasi = $this->siakad_db->prepare("
            SELECT DISTINCT jp.kelas_id, jp.mapel_id, k.nama_kelas, m.nama_mapel 
            FROM jadwal_pelajaran jp 
            JOIN kelas k ON jp.kelas_id = k.id 
            JOIN mapel m ON jp.mapel_id = m.id 
            WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ?
            ORDER BY k.tingkat, k.nama_kelas, m.nama_mapel
        ");
        $stmt_kombinasi->execute([$guru_id, $ta_id]);
        $kombinasi_mengajar = $stmt_kombinasi->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Nilai Rapor";
        $activeNav = "aplikasi";

        $institusi = \App\Core\Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;

        ob_start();
        include __DIR__ . '/../../resources/views/apk/e_rapor_index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function eRaporDetail() {
        if (!isset($_SESSION['guru_id'])) {
            \App\Core\Helper::redirect('/apk/login');
        }
        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'guru') {
            \App\Core\Helper::redirect('/apk/profile');
        }
        
        $kelas_id = $_GET['kelas_id'] ?? 0;
        $mapel_id = $_GET['mapel_id'] ?? 0;
        
        if(!$kelas_id || !$mapel_id) {
            \App\Core\Helper::redirect('/apk/e-rapor');
        }

        $guru_id = $_SESSION['guru_id'];
        $ta = \App\Core\AcademicYear::current();
        $ta_id = $ta ? $ta['id'] : 0;

        // Ambil informasi kelas dan mapel
        $stmt_k = $this->siakad_db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
        $stmt_k->execute([$kelas_id]);
        $nama_kelas = $stmt_k->fetchColumn();

        $stmt_m = $this->siakad_db->prepare("SELECT nama_mapel FROM mapel WHERE id = ?");
        $stmt_m->execute([$mapel_id]);
        $nama_mapel = $stmt_m->fetchColumn();

        // Ambil daftar siswa
        $stmt_siswa = $this->siakad_db->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? ORDER BY nama ASC");
        $stmt_siswa->execute([$kelas_id]);
        $siswaList = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);

        // Ambil nilai harian & ujian
        $stmt_nilai = $this->siakad_db->prepare("
            SELECT siswa_id, jenis_evaluasi, nilai 
            FROM nilai_harian 
            WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ?
        ");
        $stmt_nilai->execute([$kelas_id, $mapel_id, $ta_id]);
        $nilaiRaw = $stmt_nilai->fetchAll(\PDO::FETCH_ASSOC);

        $nilaiSiswa = [];
        $max_ph_class = 0;
        foreach ($nilaiRaw as $n) {
            $sid = $n['siswa_id'];
            $je = $n['jenis_evaluasi'];
            $val = floatval($n['nilai']);

            if (!isset($nilaiSiswa[$sid])) {
                $nilaiSiswa[$sid] = ['PH' => [], 'PTS' => null, 'PAS' => null];
            }

            if ($je === 'PTS') {
                $nilaiSiswa[$sid]['PTS'] = $val;
            } elseif ($je === 'PAS') {
                $nilaiSiswa[$sid]['PAS'] = $val;
            } elseif (strpos($je, 'PH ') === 0) {
                $num = (int) str_replace('PH ', '', $je);
                if ($num > 0) {
                    $nilaiSiswa[$sid]['PH'][$num] = $val;
                    if ($num > $max_ph_class) {
                        $max_ph_class = $num;
                    }
                }
            } else {
                // Untuk penamaan selain PH X, tambahkan ke array biasa (index = max+1)
                $next_idx = count($nilaiSiswa[$sid]['PH']) + 1;
                while(isset($nilaiSiswa[$sid]['PH'][$next_idx])) {
                    $next_idx++;
                }
                $nilaiSiswa[$sid]['PH'][$next_idx] = $val;
                if ($next_idx > $max_ph_class) {
                    $max_ph_class = $next_idx;
                }
            }
        }

        // Bobot Rapor SIAP
        $bobot_harian = 60; // (Rata-rata PH + PTS)
        $bobot_pas = 40;

        $rekapSiswa = [];
        foreach ($siswaList as $s) {
            $sid = $s['id'];
            $ns = $nilaiSiswa[$sid] ?? ['PH' => [], 'PTS' => null, 'PAS' => null];
            
            $ph_arr = $ns['PH'];
            $sum_ph = array_sum($ph_arr);
            $count_ph = count($ph_arr);
            
            $pts = $ns['PTS'];
            $pas = $ns['PAS'];
            
            $total_elemen_harian = $count_ph + ($pts !== null ? 1 : 0);
            $sum_harian = $sum_ph + ($pts !== null ? $pts : 0);
            
            $rata_harian = $total_elemen_harian > 0 ? ($sum_harian / $total_elemen_harian) : 0;
            
            $nilai_rapor = 0;
            if ($total_elemen_harian > 0 || $pas !== null) {
                $pas_val = $pas !== null ? $pas : 0;
                $nilai_rapor = round((($rata_harian * $bobot_harian) + ($pas_val * $bobot_pas)) / 100);
            }

            $rekapSiswa[] = [
                'siswa' => $s,
                'PH' => $ph_arr,
                'PTS' => $pts,
                'PAS' => $pas,
                'RAPOR' => $nilai_rapor
            ];
        }

        $title = "Monitor E-Rapor";
        $activeNav = "aplikasi";

        $institusi = \App\Core\Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;

        ob_start();
        include __DIR__ . '/../../resources/views/apk/e_rapor_detail.php';
        $content = ob_get_clean();
        
        $hideNav = true; // hide bottom nav in detail view
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function berkas()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login'); // Hanya untuk guru
        }

        $guru_id = $_SESSION['guru_id'];
        $message = '';
        $status = '';

        $aktif = AcademicYear::current();
        $ta_id = $aktif['id'] ?? 1;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tab_type = $_POST['tab_type'] ?? 'perangkat';

            if ($tab_type === 'pribadi') {
                // Handle upload berkas pribadi
                $jenis_berkas = trim($_POST['jenis_berkas'] ?? '');
                $judul_berkas = trim($_POST['judul_berkas'] ?? '');

                if (!empty($jenis_berkas) && !empty($judul_berkas) && isset($_FILES['file_berkas']) && $_FILES['file_berkas']['error'] == 0) {
                    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
                    $filename = $_FILES['file_berkas']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid('pribadi_') . '.' . $ext;
                        $upload_dir = __DIR__ . '/../../public/uploads/berkas/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                        if (move_uploaded_file($_FILES['file_berkas']['tmp_name'], $upload_dir . $new_filename)) {
                            try {
                                $stmt = $this->core_db->prepare("INSERT INTO berkas_pribadi (guru_id, jenis_berkas, judul_berkas, file_nama) VALUES (?, ?, ?, ?)");
                                $stmt->execute([$guru_id, $jenis_berkas, $judul_berkas, $new_filename]);
                                $_SESSION['swal_success'] = 'Berkas pribadi berhasil diunggah.';
                                $_SESSION['active_tab'] = 'pribadi';
                                Helper::redirect('/apk/berkas');
                            } catch (\Exception $e) {
                                $message = 'Gagal menyimpan ke database.';
                                $status = 'error';
                            }
                        } else {
                            $message = 'Gagal memindahkan file yang diunggah.';
                            $status = 'error';
                        }
                    } else {
                        $message = 'Format file tidak didukung. Harap unggah dokumen berformat PDF saja.';
                        $status = 'error';
                    }
                } else {
                    $message = 'Harap isi semua data dan pilih file.';
                    $status = 'warning';
                }
            } else {
                // Handle upload perangkat mengajar (existing)
                $jenis_berkas = trim($_POST['jenis_berkas'] ?? '');
                $judul_berkas = trim($_POST['judul_berkas'] ?? '');
                $kelas_id = !empty($_POST['kelas_id']) ? $_POST['kelas_id'] : null;
                $mapel_id = !empty($_POST['mapel_id']) ? $_POST['mapel_id'] : null;

                if (!empty($jenis_berkas) && !empty($judul_berkas) && !empty($kelas_id) && !empty($mapel_id) && isset($_FILES['file_berkas']) && $_FILES['file_berkas']['error'] == 0) {
                    $allowed = ['pdf'];
                    $filename = $_FILES['file_berkas']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid('berkas_') . '.' . $ext;
                        $upload_dir = __DIR__ . '/../../public/uploads/berkas/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                        if (move_uploaded_file($_FILES['file_berkas']['tmp_name'], $upload_dir . $new_filename)) {
                            try {
                                $stmt = $this->core_db->prepare("INSERT INTO perangkat_pembelajaran (guru_id, tahun_ajaran_id, jenis_berkas, judul_berkas, file_nama, kelas_id, mapel_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                $stmt->execute([$guru_id, $ta_id, $jenis_berkas, $judul_berkas, $new_filename, $kelas_id, $mapel_id]);
                                $_SESSION['swal_success'] = 'Berkas berhasil diunggah dan menunggu validasi.';
                                Helper::redirect('/apk/berkas');
                            } catch (\Exception $e) {
                                $message = 'Gagal menyimpan ke database.';
                                $status = 'error';
                            }
                        } else {
                            $message = 'Gagal memindahkan file yang diunggah.';
                            $status = 'error';
                        }
                    } else {
                        $message = 'Format file tidak didukung. Harap unggah dokumen berformat PDF saja.';
                        $status = 'error';
                    }
                } else {
                    $message = 'Harap isi semua data dan pilih file.';
                    $status = 'warning';
                }
            }
        }

        // Active tab (from session after redirect, or default)
        $active_tab = 'perangkat';
        if (isset($_SESSION['active_tab'])) {
            $active_tab = $_SESSION['active_tab'];
            unset($_SESSION['active_tab']);
        }

        // Fetch Berkas List
        $berkasList = [];
        try {
            $stmt = $this->core_db->prepare("SELECT p.*, k.nama_kelas, m.nama_mapel FROM perangkat_pembelajaran p LEFT JOIN kelas k ON p.kelas_id = k.id LEFT JOIN mapel m ON p.mapel_id = m.id WHERE p.guru_id = ? AND p.tahun_ajaran_id = ? ORDER BY p.tanggal_upload DESC");
            $stmt->execute([$guru_id, $ta_id]);
            $berkasList = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Fetch Berkas Pribadi List
        $berkasPribadiList = [];
        try {
            $stmt = $this->core_db->prepare("SELECT * FROM berkas_pribadi WHERE guru_id = ? ORDER BY tanggal_upload DESC");
            $stmt->execute([$guru_id]);
            $berkasPribadiList = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Fetch Kelas & Mapel yang diajar
        $stmt_kelas = $this->siakad_db->prepare("SELECT DISTINCT k.id, k.nama_kelas FROM jadwal_pelajaran jp JOIN kelas k ON jp.kelas_id = k.id WHERE jp.guru_id = ?");
        $stmt_kelas->execute([$guru_id]);
        $kelas_mengajar = $stmt_kelas->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_mapel = $this->siakad_db->prepare("SELECT DISTINCT m.id, m.nama_mapel FROM jadwal_pelajaran jp JOIN mapel m ON jp.mapel_id = m.id WHERE jp.guru_id = ?");
        $stmt_mapel->execute([$guru_id]);
        $mapel_mengajar = $stmt_mapel->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch kombinasi kelas-mapel yang diajar untuk grouping UI
        $stmt_kombinasi = $this->siakad_db->prepare("
            SELECT DISTINCT jp.kelas_id, jp.mapel_id, k.nama_kelas, m.nama_mapel 
            FROM jadwal_pelajaran jp 
            JOIN kelas k ON jp.kelas_id = k.id 
            JOIN mapel m ON jp.mapel_id = m.id 
            WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ?
            ORDER BY k.tingkat, k.nama_kelas, m.nama_mapel
        ");
        $stmt_kombinasi->execute([$guru_id, $ta_id]);
        $kombinasi_mengajar = $stmt_kombinasi->fetchAll(\PDO::FETCH_ASSOC);

        $dokumen_wajib = [
            'Capaian Pembelajaran (CP) / KI-KD',
            'Silabus / ATP',
            'Program Tahunan (Prota)',
            'Program Semester (Promes)',
            'RPP / Modul Ajar',
            'KKM / KKTP',
            'Bank Soal / Evaluasi'
        ];

        $dokumen_pribadi_wajib = [
            'KTP',
            'Ijazah Terakhir',
            'Sertifikat Pendidik',
            'SK Pengangkatan',
            'NUPTK / NRG',
            'Kartu BPJS',
            'Surat Lamaran / CV'
        ];

        $title = "Berkas Saya";
        $activeNav = "berkas";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/berkas.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function berkasSalinParalel()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('/apk/berkas');
        }

        $guru_id = $_SESSION['guru_id'];
        $kelas_id = trim($_POST['kelas_id'] ?? '');
        $mapel_id = trim($_POST['mapel_id'] ?? '');

        if (empty($kelas_id) || empty($mapel_id)) {
            $_SESSION['swal_error'] = 'Data kelas atau mapel tidak valid.';
            Helper::redirect('/apk/berkas');
            exit;
        }

        $active_year = AcademicYear::current();
        $ta_id = $active_year ? $active_year['id'] : 1;

        try {
            // 1. Get source class tingkat
            $stmt = $this->siakad_db->prepare("SELECT tingkat FROM kelas WHERE id = ?");
            $stmt->execute([$kelas_id]);
            $tingkat = $stmt->fetchColumn();

            if (!$tingkat) {
                throw new \Exception("Kelas asal tidak ditemukan.");
            }

            // 2. Find target classes
            $stmt = $this->siakad_db->prepare("
                SELECT DISTINCT k.id 
                FROM jadwal_pelajaran jp 
                JOIN kelas k ON jp.kelas_id = k.id 
                WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.mapel_id = ? AND k.tingkat = ? AND k.id != ?
            ");
            $stmt->execute([$guru_id, $ta_id, $mapel_id, $tingkat, $kelas_id]);
            $target_classes = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (empty($target_classes)) {
                $_SESSION['swal_error'] = 'Tidak ada kelas paralel lain (tingkat & mapel sama) yang Anda ajar.';
                Helper::redirect('/apk/berkas');
                exit;
            }

            // 3. Get all uploaded documents
            $stmt = $this->core_db->prepare("SELECT * FROM perangkat_pembelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND kelas_id = ? AND mapel_id = ?");
            $stmt->execute([$guru_id, $ta_id, $kelas_id, $mapel_id]);
            $source_docs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($source_docs)) {
                $_SESSION['swal_error'] = 'Tidak ada berkas di kelas ini yang bisa disalin.';
                Helper::redirect('/apk/berkas');
                exit;
            }

            $copied_count = 0;

            // 4. Copy each document
            foreach ($target_classes as $target_kelas_id) {
                foreach ($source_docs as $doc) {
                    $stmt_check = $this->core_db->prepare("SELECT id FROM perangkat_pembelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND kelas_id = ? AND mapel_id = ? AND jenis_berkas = ?");
                    $stmt_check->execute([$guru_id, $ta_id, $target_kelas_id, $mapel_id, $doc['jenis_berkas']]);
                    if (!$stmt_check->fetch()) {
                        $ext = strtolower(pathinfo($doc['file_nama'], PATHINFO_EXTENSION));
                        $new_filename = uniqid('berkas_copy_') . '.' . $ext;
                        $old_path = __DIR__ . '/../../public/uploads/berkas/' . $doc['file_nama'];
                        $new_path = __DIR__ . '/../../public/uploads/berkas/' . $new_filename;
                        
                        if (file_exists($old_path) && copy($old_path, $new_path)) {
                            $stmt_insert = $this->core_db->prepare("INSERT INTO perangkat_pembelajaran (guru_id, tahun_ajaran_id, jenis_berkas, judul_berkas, file_nama, kelas_id, mapel_id, status_validasi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt_insert->execute([$guru_id, $ta_id, $doc['jenis_berkas'], $doc['judul_berkas'], $new_filename, $target_kelas_id, $mapel_id, 'Menunggu']);
                            $copied_count++;
                        }
                    }
                }
            }

            if ($copied_count > 0) {
                $_SESSION['swal_success'] = "Berhasil menyalin $copied_count berkas ke kelas paralel.";
            } else {
                $_SESSION['swal_error'] = 'Semua kelas paralel sudah memiliki berkas tersebut.';
            }

        } catch (\Exception $e) {
            $_SESSION['swal_error'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }

        Helper::redirect('/apk/berkas');
    }

    public function hapusBerkas($id)
    {
        if (!isset($_SESSION['guru_id'])) { Helper::redirect('/apk/login'); }
        $guru_id = $_SESSION['guru_id'];
        try {
            $stmt = $this->core_db->prepare("SELECT file_nama FROM perangkat_pembelajaran WHERE id = ? AND guru_id = ?");
            $stmt->execute([$id, $guru_id]);
            $berkas = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($berkas) {
                $filepath = __DIR__ . '/../../public/uploads/berkas/' . $berkas['file_nama'];
                if (file_exists($filepath)) unlink($filepath);
                $stmt = $this->core_db->prepare("DELETE FROM perangkat_pembelajaran WHERE id = ? AND guru_id = ?");
                $stmt->execute([$id, $guru_id]);
                $_SESSION['swal_success'] = 'Berkas berhasil dihapus.';
            }
        } catch (\Exception $e) {}
        Helper::redirect('/apk/berkas');
    }

    public function hapusBerkasPribadi($id)
    {
        if (!isset($_SESSION['guru_id'])) { Helper::redirect('/apk/login'); }
        $guru_id = $_SESSION['guru_id'];
        try {
            $stmt = $this->core_db->prepare("SELECT file_nama FROM berkas_pribadi WHERE id = ? AND guru_id = ?");
            $stmt->execute([$id, $guru_id]);
            $berkas = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($berkas) {
                $filepath = __DIR__ . '/../../public/uploads/berkas/' . $berkas['file_nama'];
                if (file_exists($filepath)) unlink($filepath);
                $stmt = $this->core_db->prepare("DELETE FROM berkas_pribadi WHERE id = ? AND guru_id = ?");
                $stmt->execute([$id, $guru_id]);
                $_SESSION['swal_success'] = 'Berkas pribadi berhasil dihapus.';
                $_SESSION['active_tab'] = 'pribadi';
            }
        } catch (\Exception $e) {}
        Helper::redirect('/apk/berkas');
    }

    public function viewer()
    {
        if (!isset($_SESSION['guru_id']) && !isset($_SESSION['siswa_id']) && !isset($_SESSION['role_id'])) {
            Helper::redirect('/apk/login');
        }

        $type = $_GET['type'] ?? 'perangkat';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        $doc_url = "";
        $absolute_doc_url = "";
        $page_title = "";
        $doc_ext = "";

        if ($type === 'perangkat') {
            $stmt = $this->core_db->prepare("SELECT judul_berkas, file_nama FROM perangkat_pembelajaran WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $berkas = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$berkas) {
                echo "<script>alert('Perangkat tidak ditemukan!'); window.history.back();</script>";
                exit;
            }
            $doc_url = "/public/uploads/berkas/" . $berkas['file_nama'];
            $absolute_doc_url = "http://" . $_SERVER['HTTP_HOST'] . "/public/uploads/berkas/" . $berkas['file_nama'];
            $page_title = $berkas['judul_berkas'];
            $doc_ext = strtolower(pathinfo($berkas['file_nama'], PATHINFO_EXTENSION));
        } else if ($type === 'pribadi') {
            $stmt = $this->core_db->prepare("SELECT judul_berkas, file_nama FROM berkas_pribadi WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $berkas = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$berkas) {
                echo "<script>alert('Berkas tidak ditemukan!'); window.history.back();</script>";
                exit;
            }
            $doc_url = "/public/uploads/berkas/" . $berkas['file_nama'];
            $absolute_doc_url = "http://" . $_SERVER['HTTP_HOST'] . "/public/uploads/berkas/" . $berkas['file_nama'];
            $page_title = $berkas['judul_berkas'];
            $doc_ext = strtolower(pathinfo($berkas['file_nama'], PATHINFO_EXTENSION));
        } else {
            echo "<script>alert('Tipe dokumen tidak valid!'); window.history.back();</script>";
            exit;
        }

        // We will render it using a dedicated view with the main layout but hidden bottom nav
        $hideNav = true;
        ob_start();
        include __DIR__ . '/../../resources/views/apk/viewer.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function apiGetSiswaKelas()
    {
        if (!isset($_SESSION['guru_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $kelas_id = intval($_GET['kelas_id'] ?? 0);
        header('Content-Type: application/json');
        
        if ($kelas_id <= 0) {
            echo json_encode([]);
            exit;
        }

        try {
            $stmt = $this->core_db->prepare("
                SELECT id, nama, nis, foto 
                FROM siswa 
                WHERE kelas_id = ? AND status = 'Aktif'
                ORDER BY nama ASC
            ");
            $stmt->execute([$kelas_id]);
            $siswa = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode($siswa);
        } catch (\PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function apiScan()
    {
        header('Content-Type: application/json');
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!$data || empty($data['nis'])) {
            echo json_encode(['success' => false, 'message' => 'Token tidak valid!']);
            exit;
        }

        $token = $data['nis']; // Can be nis, nip, or username
        $mode = $data['mode'] ?? 'siswa'; // 'guru' or 'siswa'

        $today = date('Y-m-d');
        $time = date('H:i:s');

        try {
            // Setup DB and timezone
            date_default_timezone_set('Asia/Jakarta');

            $aktif = AcademicYear::current();
            $ta_id = $aktif['id'] ?? 1;

            if ($mode === 'guru') {
                // Cari Guru
                $stmt = $this->core_db->prepare("SELECT * FROM guru WHERE nip = ? OR qr_token = ? LIMIT 1");
                $stmt->execute([$token, $token]);
                $guru = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$guru) {
                    echo json_encode(['success' => false, 'message' => 'Data guru tidak ditemukan!']);
                    exit;
                }

                $guru_id = $guru['id'];
                
                // Panggil Bapak / Ibu berdasarkan jenis kelamin (Untuk tampilan)
                if ($guru['jenis_kelamin'] === 'P') {
                    $nama = 'Ibu ' . $guru['nama'];
                } else {
                    $nama = 'Bapak ' . $guru['nama'];
                }

                // Bersihkan nama untuk TTS agar gelar dieja dengan benar
                $gelar_map = [
                    'S.Pd.I' => 'Sarjana Pendidikan Islam',
                    'S.Pd' => 'Sarjana Pendidikan',
                    'M.Pd' => 'Magister Pendidikan',
                    'S.Ag' => 'Sarjana Agama',
                    'M.Ag' => 'Magister Agama',
                    'S.E' => 'Sarjana Ekonomi',
                    'M.E' => 'Magister Ekonomi',
                    'S.Kom' => 'Sarjana Komputer',
                    'M.Kom' => 'Magister Komputer',
                    'S.H' => 'Sarjana Hukum',
                    'M.H' => 'Magister Hukum',
                    'Lc' => 'Elsi'
                ];
                $nama_tts = $guru['nama'];
                foreach ($gelar_map as $singkatan => $panjang) {
                    $nama_tts = str_ireplace($singkatan, $panjang, $nama_tts);
                }
                // Hapus titik koma agar tidak ada jeda aneh
                $nama_tts = str_replace(['.', ',', '-'], ' ', $nama_tts);
                // Tambahkan sapaan
                $nama_tts = ($guru['jenis_kelamin'] === 'P' ? 'Ibu ' : 'Bapak ') . $nama_tts;

                // Get Konfigurasi Jam Guru
                $jam_q = $this->siakad_db->query("SELECT * FROM jam_absen_guru LIMIT 1");
                $konfig = $jam_q->fetch(\PDO::FETCH_ASSOC);

                if (!$konfig) {
                    echo json_encode(['success' => false, 'message' => 'Konfigurasi jam absen belum diatur Admin.']);
                    exit;
                }

                $stmt_cek = $this->siakad_db->prepare("SELECT * FROM absensi_guru WHERE guru_id = ? AND tanggal = ?");
                $stmt_cek->execute([$guru_id, $today]);
                $absen_hari_ini = $stmt_cek->fetch(\PDO::FETCH_ASSOC);

                if (!$absen_hari_ini) {
                    // Masuk
                    if ($time >= $konfig['jam_masuk_mulai'] && $time <= $konfig['jam_masuk_akhir']) {
                        $status = ($time > $konfig['jam_masuk_batas']) ? 'Terlambat' : 'Hadir';
                        $ins = $this->siakad_db->prepare("INSERT INTO absensi_guru (guru_id, tanggal, jam_masuk, status, tahun_ajaran_id) VALUES (?, ?, ?, ?, ?)");
                        $ins->execute([$guru_id, $today, $time, $status, $ta_id]);

                        require_once __DIR__ . '/../Services/OneSignalService.php';
                        \App\Services\OneSignalService::sendNotification("Absen Masuk", "Bapak/Ibu " . $guru['nama'] . ", absen masuk Anda berhasil tercatat pada pukul " . substr($time, 0, 5), null, "guru_" . $guru['id']);

                        echo json_encode(['success' => true, 'type' => 'guru', 'data' => ['nama' => $nama, 'nama_tts' => $nama_tts, 'status' => 'Masuk ' . $status]]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Di luar jam absen masuk!']);
                    }
                } else {
                    // Pulang
                    if (empty($absen_hari_ini['jam_pulang'])) {
                        if ($time >= $konfig['jam_pulang_mulai'] && $time <= $konfig['jam_pulang_akhir']) {
                            $upd = $this->siakad_db->prepare("UPDATE absensi_guru SET jam_pulang = ? WHERE id = ?");
                            $upd->execute([$time, $absen_hari_ini['id']]);

                            require_once __DIR__ . '/../Services/OneSignalService.php';
                            \App\Services\OneSignalService::sendNotification("Absen Pulang", "Bapak/Ibu " . $guru['nama'] . ", absen pulang Anda berhasil tercatat pada pukul " . substr($time, 0, 5) . ". Hati-hati di jalan!", null, "guru_" . $guru['id']);

                            echo json_encode(['success' => true, 'type' => 'guru', 'data' => ['nama' => $nama, 'nama_tts' => $nama_tts, 'status' => 'Pulang']]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Di luar jam absen pulang!']);
                        }
                    } else {
                        echo json_encode(['success' => true, 'type' => 'guru', 'data' => ['nama' => $nama, 'nama_tts' => $nama_tts, 'status' => 'Sudah Absen Pulang']]);
                    }
                }

            } else {
                // Cari Siswa
                $stmt = $this->core_db->prepare("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.nis = ? OR s.nisn = ? OR s.qr_token = ? LIMIT 1");
                $stmt->execute([$token, $token, $token]);
                $siswa = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$siswa) {
                    echo json_encode(['success' => false, 'message' => 'Data siswa tidak ditemukan!']);
                    exit;
                }

                $siswa_id = $siswa['id'];
                $nama = $siswa['nama'];
                $kelas = $siswa['nama_kelas'];
                $kelas_id = $siswa['kelas_id'];

                $jam_q = $this->siakad_db->query("SELECT * FROM jam_absen LIMIT 1");
                $konfig = $jam_q->fetch(\PDO::FETCH_ASSOC);

                if (!$konfig) {
                    echo json_encode(['success' => false, 'message' => 'Konfigurasi jam absen siswa belum diatur.']);
                    exit;
                }

                $stmt_cek = $this->siakad_db->prepare("SELECT * FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                $stmt_cek->execute([$siswa_id, $today]);
                $absen_hari_ini = $stmt_cek->fetch(\PDO::FETCH_ASSOC);

                if (!$absen_hari_ini) {
                    // Masuk
                    if ($time >= $konfig['jam_masuk_mulai'] && $time <= $konfig['jam_masuk_akhir']) {
                        $status = ($time > $konfig['jam_masuk_batas']) ? 'Terlambat' : 'Hadir';
                        $ins = $this->siakad_db->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, jam_masuk, status, tahun_ajaran_id) VALUES (?, ?, ?, ?, ?, ?)");
                        $ins->execute([$siswa_id, $kelas_id, $today, $time, $status, $ta_id]);

                        require_once __DIR__ . '/../Services/OneSignalService.php';
                        \App\Services\OneSignalService::sendNotification("Absensi Kehadiran", "Ananda " . $siswa['nama'] . " telah Hadir di madrasah pada pukul " . substr($time, 0, 5), null, "siswa_" . $siswa['id']);

                        echo json_encode(['success' => true, 'type' => 'siswa', 'data' => ['nama' => $nama, 'kelas' => $kelas, 'status' => 'Masuk ' . $status]]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Di luar jam absen masuk!']);
                    }
                } else {
                    // Pulang
                    if (empty($absen_hari_ini['jam_pulang'])) {
                        if ($time >= $konfig['jam_pulang_mulai'] && $time <= $konfig['jam_pulang_akhir']) {
                            $upd = $this->siakad_db->prepare("UPDATE absensi_siswa SET jam_pulang = ? WHERE id = ?");
                            $upd->execute([$time, $absen_hari_ini['id']]);

                            require_once __DIR__ . '/../Services/OneSignalService.php';
                            \App\Services\OneSignalService::sendNotification("Absensi Pulang", "Ananda " . $siswa['nama'] . " telah absen pulang pada pukul " . substr($time, 0, 5), null, "siswa_" . $siswa['id']);

                            echo json_encode(['success' => true, 'type' => 'siswa', 'data' => ['nama' => $nama, 'kelas' => $kelas, 'status' => 'Pulang']]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Di luar jam absen pulang!']);
                        }
                    } else {
                        echo json_encode(['success' => true, 'type' => 'siswa', 'data' => ['nama' => $nama, 'kelas' => $kelas, 'status' => 'Sudah Absen Pulang']]);
                    }
                }
            }
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function scanner()
    {
        if (!isset($_SESSION['guru_id']) && !isset($_SESSION['siswa_id'])) {
            Helper::redirect('/apk/login');
        }

        $mode = isset($_GET['mode']) && $_GET['mode'] === 'guru' ? 'guru' : 'siswa';
        $title = $mode === 'guru' ? 'Scanner Guru' : 'Scanner Siswa';
        $activeNav = "absen";
        
        $settings = [];
        $settings_path = __DIR__ . '/../../apk_settings.json';
        if (file_exists($settings_path)) {
            $settings = json_decode(file_get_contents($settings_path), true);
        }
        
        try {
            // Gabungkan setting dari database (ini yang disetting dari web Admin V2)
            $db_set = $this->core_db->query("SELECT kunci, nilai FROM absensi_pengaturan")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($db_set as $row) {
                if (!empty($row['nilai'])) {
                    $settings[$row['kunci']] = $row['nilai'];
                }
            }
        } catch (\Exception $e) {}

        $ai_voice_enabled = isset($settings['ai_voice_enabled']) ? $settings['ai_voice_enabled'] : true;
        $ai_voice_pitch = isset($settings['ai_voice_pitch']) ? floatval($settings['ai_voice_pitch']) : 1.0;
        $ai_voice_rate = isset($settings['ai_voice_rate']) ? floatval($settings['ai_voice_rate']) : 1.0;

        $getTtsArray = function($key, $defaultText) use ($settings) {
            $text = isset($settings[$key]) && trim($settings[$key]) !== '' ? $settings[$key] : $defaultText;
            return array_values(array_filter(array_map('trim', explode("\n", $text))));
        };

        $tts_guru_masuk = $getTtsArray('tts_guru_masuk', "Selamat pagi Bapak Ibu [nama], kehadiran Anda telah tercatat.\nKehadiran berhasil. Selamat bertugas, Bapak Ibu [nama].\nAbsen masuk sukses. Semangat mengajar, Bapak Ibu [nama].");
        $tts_guru_pulang = $getTtsArray('tts_guru_pulang', "Terima kasih Bapak Ibu [nama], selamat beristirahat.\nAbsen pulang berhasil. Hati-hati di jalan, [nama].\nSampai jumpa Bapak Ibu [nama], terima kasih atas dedikasinya hari ini.");
        $tts_guru_telat = $getTtsArray('tts_guru_telat', "Kehadiran Bapak Ibu [nama] tercatat. Anda terlambat.\nAbsen berhasil, namun Bapak Ibu [nama] terlambat masuk.");
        $tts_guru_sudah = $getTtsArray('tts_guru_sudah', "Mohon maaf, Bapak Ibu [nama] sudah absen sebelumnya.\nKehadiran Bapak Ibu [nama] sudah tercatat hari ini.\nBapak Ibu [nama] sudah melakukan absensi.");
        $tts_siswa_masuk = $getTtsArray('tts_siswa_masuk', "Mantap! Kehadiran [nama] sudah tercatat.\nHalo [nama], selamat datang di sekolah!\nSip, [nama] hadir! Semangat belajarnya!\nWah, [nama] rajin sekali hari ini!");
        $tts_siswa_pulang = $getTtsArray('tts_siswa_pulang', "Sampai jumpa [nama], hati-hati di jalan ya!\nAbsen pulang sukses. Selamat beristirahat [nama]!\nTerima kasih [nama], sampai bertemu besok!\nDadah [nama], semoga harimu menyenangkan!");
        $tts_siswa_telat = $getTtsArray('tts_siswa_telat', "Yah, [nama] terlambat. Besok lebih pagi ya!\nWaduh [nama], kok telat sih?\n[nama] hadir, tapi jangan kesiangan lagi ya.");
        $tts_siswa_sudah = $getTtsArray('tts_siswa_sudah', "Hehe, [nama] kan sudah absen.\nLoh, [nama] sudah absen tadi.\nKehadiran [nama] sudah tercatat kok.");
        $tts_gagal = $getTtsArray('tts_gagal', "Aduh, gagal. [desc]\nScan ditolak. [desc]\nMohon maaf, [desc]");

        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/scanner.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function aplikasiSaya()
    {
        if (!isset($_SESSION['guru_id']) && !isset($_SESSION['siswa_id'])) {
            Helper::redirect('/apk/login');
        }

        $title = "Aplikasi Saya";
        $activeNav = "apps";

        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;

        if (isset($_SESSION['guru_id'])) {
            // Logika Guru
            $is_admin_tabungan = false;
            $is_admin_keuangan = false;
            
            $user_id = $_SESSION['user_id'] ?? 0;
            if ($user_id == 0) {
                $user_id = $this->core_db->query("SELECT user_id FROM guru WHERE id = " . intval($_SESSION['guru_id']))->fetchColumn();
                $_SESSION['user_id'] = $user_id;
            }

            try {
                // Ignore app_admins if table doesn't exist yet, we will wrap in try-catch
                $stmt_cek = $this->core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = ? AND app_code IN ('tabungan', 'keuangan')");
                $stmt_cek->execute([$user_id]);
                $akses = $stmt_cek->fetchAll(\PDO::FETCH_COLUMN);
                
                if (in_array('tabungan', $akses)) $is_admin_tabungan = true;
                if (in_array('keuangan', $akses)) $is_admin_keuangan = true;
            } catch (\Exception $e) {}

            $is_wali_kelas = false;
            $isKamad = false;
            try {
                $stmt_wk = $this->core_db->prepare("SELECT id, jabatan, is_kamad FROM guru WHERE id = ? LIMIT 1");
                $stmt_wk->execute([$_SESSION['guru_id']]);
                $g_data = $stmt_wk->fetch(\PDO::FETCH_ASSOC);
                if ($g_data) {
                    if ($g_data['is_kamad'] == 1 || (stripos($g_data['jabatan'], 'Kepala') !== false || stripos($g_data['jabatan'], 'Kamad') !== false)) {
                        $isKamad = true;
                    }
                }
                $stmt_tgs = $this->core_db->prepare("SELECT tugas FROM guru_tugas WHERE guru_id = ?");
                $stmt_tgs->execute([$_SESSION['guru_id']]);
                $tugas_list = $stmt_tgs->fetchAll(\PDO::FETCH_COLUMN);
                
                $is_waka_kurikulum = false;
                $is_waka_kesiswaan = false;
                $is_bk = false;
                
                // Check in guru_tugas
                foreach ($tugas_list as $t) {
                    if (stripos($t, 'Wali Kelas') !== false) $is_wali_kelas = true;
                    if (stripos($t, 'Kurikulum') !== false) $is_waka_kurikulum = true;
                    if (stripos($t, 'Kesiswaan') !== false) $is_waka_kesiswaan = true;
                    if (stripos($t, 'Konseling') !== false || stripos($t, 'BK') !== false) $is_bk = true;
                }
                
                // Check in jabatan utama
                if ($g_data) {
                    $j = $g_data['jabatan'];
                    if (stripos($j, 'Kurikulum') !== false) $is_waka_kurikulum = true;
                    if (stripos($j, 'Kesiswaan') !== false) $is_waka_kesiswaan = true;
                }
                
                // Check in guru_tugas_tambahan
                $stmt_tambahan = $this->core_db->prepare("SELECT jabatan_tugas FROM guru_tugas_tambahan WHERE guru_id = ?");
                $stmt_tambahan->execute([$_SESSION['guru_id']]);
                $tugas_tambahan = $stmt_tambahan->fetchAll(\PDO::FETCH_COLUMN);
                foreach ($tugas_tambahan as $tt) {
                    if (stripos($tt, 'Kurikulum') !== false) $is_waka_kurikulum = true;
                    if (stripos($tt, 'Kesiswaan') !== false) $is_waka_kesiswaan = true;
                    if (stripos($tt, 'Konseling') !== false || stripos($tt, 'BK') !== false) $is_bk = true;
                }
            } catch (\Exception $e) {}

            $settings = [];
            try {
                $res = $this->core_db->query("SELECT kunci, nilai FROM absensi_pengaturan WHERE kunci IN ('admin_absen_guru', 'admin_absen_siswa')")->fetchAll(\PDO::FETCH_KEY_PAIR);
                $settings = $res;
            } catch (\Exception $e) {}

            $adminIdsSiswa = isset($settings['admin_absen_siswa']) ? explode(',', $settings['admin_absen_siswa']) : [];
            $adminIdsGuru = isset($settings['admin_absen_guru']) ? explode(',', $settings['admin_absen_guru']) : [];

            // Get custom APK access
            $custom_apk_access = [];
            try {
                $stmt_custom = $this->core_db->prepare("SELECT app_code FROM apk_akses_guru WHERE guru_id = ?");
                $stmt_custom->execute([$_SESSION['guru_id']]);
                $custom_apk_access = $stmt_custom->fetchAll(\PDO::FETCH_COLUMN);
            } catch (\Exception $e) {}

            $isSuperAdmin = in_array($_SESSION['role_id'] ?? 0, [1, 99]);
            $canScanSiswa = $isSuperAdmin || in_array($user_id, $adminIdsSiswa);
            $canScanGuru = $isSuperAdmin || $isKamad || in_array($user_id, $adminIdsGuru);
            
            $adminIdsIzin = isset($settings['admin_izin_piket']) ? explode(',', $settings['admin_izin_piket']) : [];
            if ($isSuperAdmin || in_array($user_id, $adminIdsIzin)) {
                $custom_apk_access[] = 'persetujuan_izin';
            }
            if ($canScanSiswa && !in_array('scan_siswa', $custom_apk_access)) $custom_apk_access[] = 'scan_siswa';
            if ($canScanGuru && !in_array('scan_guru', $custom_apk_access)) $custom_apk_access[] = 'scan_guru';
            if ($is_wali_kelas) {
                $custom_apk_access = array_merge($custom_apk_access, ['wali_siswa', 'wali_jurnal', 'wali_absen', 'wali_buku', 'wali_poin']);
            }

            ob_start();
            include __DIR__ . '/../../resources/views/apk/aplikasi_guru.php';
            $content = ob_get_clean();
        } else {
            // Logika Siswa
            ob_start();
            include __DIR__ . '/../../resources/views/apk/aplikasi_siswa.php';
            $content = ob_get_clean();
        }

        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function tagihan()
    {
        if (!isset($_SESSION['siswa_id'])) {
            Helper::redirect('/apk/login');
        }

        $siswa_id = $_SESSION['siswa_id'];
        
        $tagihan_per_tp = [];
        $total_tunggakan = 0;
        
        try {
            $stmt_ta = $this->core_db->prepare("
                SELECT t.*, ta.name as nama_tahun, k.nama_komponen as nama_tagihan
                FROM keuangan_tagihan t 
                JOIN keuangan_komponen k ON t.komponen_id = k.id
                LEFT JOIN tahun_ajaran ta ON t.tahun_ajaran_id = ta.id 
                WHERE t.siswa_id = ? 
                ORDER BY ta.name DESC, t.id ASC
            ");
            $stmt_ta->execute([$siswa_id]);
            $semua_tagihan = $stmt_ta->fetchAll(\PDO::FETCH_ASSOC);

            foreach($semua_tagihan as $t) {
                $tp = $t['nama_tahun'] ?: 'Tagihan Lainnya';
                if (!isset($tagihan_per_tp[$tp])) {
                    $tagihan_per_tp[$tp] = [];
                }
                $tagihan_per_tp[$tp][] = $t;
                
                if ($t['status'] != 'Lunas') {
                    $total_tunggakan += ($t['nominal'] - $t['terbayar']);
                }
            }
        } catch (\PDOException $e) {}

        $riwayat_pembayaran = [];
        try {
            $stmt_ri = $this->core_db->prepare("
                SELECT t.*, k.nama_komponen 
                FROM keuangan_transaksi t
                JOIN keuangan_tagihan tg ON t.tagihan_id = tg.id
                JOIN keuangan_komponen k ON tg.komponen_id = k.id
                WHERE t.siswa_id = ? 
                ORDER BY t.tanggal_bayar DESC 
                LIMIT 10
            ");
            $stmt_ri->execute([$siswa_id]);
            $riwayat_pembayaran = $stmt_ri->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {}

        $title = "Keuangan & Tagihan";
        $activeNav = "tagihan";

        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/tagihan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function rekapKehadiranSaya()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }
        
        $db = \App\Core\Database::connect();
        $guru_id = $_SESSION['guru_id'];
        
        $sql = "SELECT tanggal, status, jam_masuk, jam_pulang, keterangan FROM absensi_guru WHERE guru_id = ? ORDER BY tanggal DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$guru_id]);
        $absensiRaw = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $absensiGrouped = [];
        foreach ($absensiRaw as $a) {
            $month = date('Y-m', strtotime($a['tanggal']));
            if (!isset($absensiGrouped[$month])) {
                $absensiGrouped[$month] = [];
            }
            $absensiGrouped[$month][] = $a;
        }
        
        $stmt = $db->prepare("SELECT nama FROM guru WHERE id = ?");
        $stmt->execute([$guru_id]);
        $guru = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $title = "Rekap Absensi Saya";
        $activeNav = "profile";
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/rekap_absensi_guru.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function izinGuru()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }
        
        $title = "Ajukan Izin";
        $activeNav = "profile";
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/izin_guru.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }
    public function izinSiswa() {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        
        // Authorization Check
        $user_id = $_SESSION['user_id'] ?? 0;
        if ($user_id == 0) {
            $user_id = $this->core_db->query("SELECT user_id FROM guru WHERE id = " . intval($_SESSION['guru_id']))->fetchColumn();
            $_SESSION['user_id'] = $user_id;
        }
        
        $has_access = false;
        $stmt_cek = $this->core_db->prepare("SELECT COUNT(*) FROM apk_akses_guru WHERE guru_id = ? AND app_code = 'izin_siswa'");
        $stmt_cek->execute([$_SESSION['guru_id']]);
        if ($stmt_cek->fetchColumn() > 0) $has_access = true;
        
        // Check if Super Admin
        $role_id = $_SESSION['role_id'] ?? 0;
        if (in_array($role_id, [1, 99])) $has_access = true;
        
        if (!$has_access) {
            Helper::redirect('/apk/aplikasi-saya');
        }

        $title = "Izin Siswa";
        $activeNav = "apps";
        
        $db = \App\Core\Database::connect();
        $stmt_kelas = $db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
        $kelasList = $stmt_kelas->fetchAll(\PDO::FETCH_ASSOC);

        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $kelas_id = $_GET['kelas_id'] ?? '';
        
        $siswaList = [];
        $statusAbsen = [];
        
        if ($kelas_id) {
            $active_year = \App\Core\AcademicYear::current();
            $active_year_name = $active_year['name'] ?? '2025/2026';
            
            $stmtSiswa = $db->prepare("
                SELECT s.id, s.nama, s.nisn 
                FROM siswa s 
                LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                    AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = ?)
                WHERE s.status IN ('Aktif', 'Alumni') 
                  AND COALESCE(rks.kelas_id, s.kelas_id) = ?
                ORDER BY s.nama ASC
            ");
            $stmtSiswa->execute([$active_year_name, $kelas_id]);
            $siswaList = $stmtSiswa->fetchAll(\PDO::FETCH_ASSOC);
            
            $db_siakad = \App\Core\Database::connect();
            $stmtAbsen = $db_siakad->prepare("SELECT siswa_id, status FROM absensi_siswa WHERE tanggal = ? AND kelas_id = ?");
            $stmtAbsen->execute([$tanggal, $kelas_id]);
            $statusAbsen = $stmtAbsen->fetchAll(\PDO::FETCH_KEY_PAIR);
        }

        ob_start();
        include __DIR__ . '/../../resources/views/apk/izin_siswa.php';
        $content = ob_get_clean();
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function izinSiswaSave() {
        if (!isset($_SESSION['guru_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $siswa_id = $_POST['siswa_id'] ?? 0;
        $tanggal = $_POST['tanggal'] ?? '';
        $status = $_POST['status'] ?? '';
        
        if (!$siswa_id || !$tanggal || !in_array($status, ['Sakit', 'Izin', 'Alpa'])) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap atau status tidak valid.']);
            exit;
        }
        
        $db_core = \App\Core\Database::connect();
        $db_siakad = \App\Core\Database::connect();
        
        // Get kelas_id
        $stmt_kelas = $db_core->prepare("SELECT kelas_id FROM siswa WHERE id = ?");
        $stmt_kelas->execute([$siswa_id]);
        $kelas_id = $stmt_kelas->fetchColumn();
        
        $stmtCheck = $db_siakad->prepare("SELECT id FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
        $stmtCheck->execute([$siswa_id, $tanggal]);
        
        if ($stmtCheck->fetch()) {
            $stmtUpdate = $db_siakad->prepare("UPDATE absensi_siswa SET status = ? WHERE siswa_id = ? AND tanggal = ?");
            $stmtUpdate->execute([$status, $siswa_id, $tanggal]);
        } else {
            $stmtInsert = $db_siakad->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status) VALUES (?, ?, ?, ?)");
            $stmtInsert->execute([$siswa_id, $kelas_id, $tanggal, $status]);
        }
        
        // Ensure all teacher mapel attendance for this student on this date is also updated to the new status
        $stmtUpdateMapel = $db_siakad->prepare("UPDATE absensi_permapel SET status = ? WHERE siswa_id = ? AND tanggal = ?");
        $stmtUpdateMapel->execute([$status, $siswa_id, $tanggal]);
        
        echo json_encode(['success' => true, 'message' => 'Status absensi berhasil diperbarui.']);
        exit;
    }
    public function adminIzinPiket() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }
        
        $db = \App\Core\Database::connect('siakad');
        $today = date('Y-m-d');
        
        $isRiwayat = isset($_GET['riwayat']) && $_GET['riwayat'] == '1';
        $bulanRiwayat = $_GET['bulan'] ?? date('Y-m');
        
        if ($isRiwayat) {
            $stmt = $db->prepare("
                SELECT p.*, g.nama as nama_guru 
                FROM pengajuan_izin_guru p 
                JOIN guru g ON p.guru_id = g.id 
                WHERE DATE_FORMAT(p.tanggal, '%Y-%m') = ?
                ORDER BY p.tanggal DESC, p.created_at DESC
            ");
            $stmt->execute([$bulanRiwayat]);
        } else {
            $stmt = $db->prepare("
                SELECT p.*, g.nama as nama_guru 
                FROM pengajuan_izin_guru p 
                JOIN guru g ON p.guru_id = g.id 
                WHERE p.tanggal >= ? OR p.status_approval = 'Pending'
                ORDER BY 
                    CASE WHEN p.status_approval = 'Pending' THEN 0 ELSE 1 END,
                    p.tanggal DESC, p.created_at DESC
                LIMIT 50
            ");
            $stmt->execute([$today]);
        }
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

        // Build Tugas Inval list for selected date (default today)
        $tanggal_inval = $_GET['tanggal_inval'] ?? date('Y-m-d');
        $tugasInvalGrouped = [];
        $stmtApproved = $db->prepare("
            SELECT p.*, g.nama as nama_guru 
            FROM pengajuan_izin_guru p 
            JOIN guru g ON p.guru_id = g.id 
            WHERE p.tanggal = ? AND p.status_approval = 'Disetujui'
        ");
        $stmtApproved->execute([$tanggal_inval]);
        $approvedToday = $stmtApproved->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($approvedToday as $ap) {
            $tugasData = json_decode($ap['tugas_inval'], true) ?: [];
            if (!empty($tugasData)) {
                if (!isset($tugasInvalGrouped[$ap['guru_id']])) {
                    $tugasInvalGrouped[$ap['guru_id']] = [
                        'nama_guru' => $ap['nama_guru'],
                        'sumber' => $ap['jenis_izin'],
                        'tugas' => []
                    ];
                }

                foreach ($tugasData as $t) {
                    $stK = $db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
                    $stK->execute([$t['kelas_id'] ?? 0]);
                    $namaKelas = $stK->fetchColumn() ?: '?';
                    
                    $stM = $db->prepare("SELECT nama_mapel FROM mapel WHERE id = ?");
                    $stM->execute([$t['mapel_id'] ?? 0]);
                    $namaMapel = $stM->fetchColumn() ?: '?';

                    $stJ = $db->prepare("SELECT jam_mulai, jam_selesai FROM jadwal_pelajaran WHERE id = ?");
                    $stJ->execute([$t['jadwal_id'] ?? 0]);
                    $jdw = $stJ->fetch(\PDO::FETCH_ASSOC);

                    $checkInval = $db->prepare("SELECT id, keterangan FROM jurnal_guru WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tanggal = ? AND keterangan LIKE '%[INVAL%'");
                    $checkInval->execute([$ap['guru_id'], $t['kelas_id'], $t['mapel_id'], $tanggal_inval]);
                    $invalRecord = $checkInval->fetch(\PDO::FETCH_ASSOC);
                    
                    $petugasInval = '';
                    if ($invalRecord) {
                        if (preg_match('/\[INVAL oleh (.*?)\]/', $invalRecord['keterangan'], $matches)) {
                            $petugasInval = $matches[1];
                        }
                    }

                    $tugasInvalGrouped[$ap['guru_id']]['tugas'][] = [
                        'pengajuan_id' => $ap['id'],
                        'kelas_id' => $t['kelas_id'],
                        'mapel_id' => $t['mapel_id'],
                        'nama_kelas' => $namaKelas,
                        'nama_mapel' => $namaMapel,
                        'jam_mulai' => isset($jdw['jam_mulai']) ? substr($jdw['jam_mulai'], 0, 5) : '--:--',
                        'jam_selesai' => isset($jdw['jam_selesai']) ? substr($jdw['jam_selesai'], 0, 5) : '--:--',
                        'materi' => $t['materi'] ?? '',
                        'tanggal' => $tanggal_inval,
                        'sudah_inval' => $invalRecord ? true : false,
                        'petugas_inval' => $petugasInval,
                    ];
                }
            }
        }

        $guruList = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);
        
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'inval') {
            include __DIR__ . '/../../resources/views/apk/admin_izin_piket_inval_list.php';
            exit;
        }

        $title = "Izin Guru";
        $activeNav = "profile"; // or "apps"
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/admin_izin_piket.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function approveIzinGuru() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $db = \App\Core\Database::connect('siakad');
        $id = $_POST['id'] ?? 0;
        $action = $_POST['action'] ?? '';
        $approvedBy = $_SESSION['guru_id'];

        if ($action === 'approve') {
            $stmt = $db->prepare("UPDATE pengajuan_izin_guru SET status_approval = 'Disetujui', approved_by = ? WHERE id = ?");
            $stmt->execute([$approvedBy, $id]);

            $pengajuan = $db->prepare("SELECT * FROM pengajuan_izin_guru WHERE id = ?");
            $pengajuan->execute([$id]);
            $p = $pengajuan->fetch(\PDO::FETCH_ASSOC);

            if ($p) {
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

        Helper::redirect('/apk/admin-izin-piket');
    }

    public function laporAlpa() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $db = \App\Core\Database::connect('siakad');
        $guru_id = $_POST['guru_id'] ?? 0;
        $today = date('Y-m-d');
        $approvedBy = $_SESSION['guru_id'];

        if (empty($guru_id)) {
            Helper::redirect('/apk/admin-izin-piket');
        }

        $check = $db->prepare("SELECT id FROM absensi_guru WHERE guru_id = ? AND tanggal = ?");
        $check->execute([$guru_id, $today]);
        if (!$check->fetch()) {
            $ins = $db->prepare("INSERT INTO absensi_guru (tanggal, guru_id, status, keterangan) VALUES (?, ?, 'Alpa', 'Dilaporkan oleh Guru Piket')");
            $ins->execute([$today, $guru_id]);
        }

        $hariArr = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $hari = $hariArr[date('l')] ?? 'Senin';
        $stmt_ta = $db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
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
            $ins = $db->prepare("INSERT INTO pengajuan_izin_guru (guru_id, tanggal, jenis_izin, tugas_inval, status_approval, approved_by) VALUES (?, ?, 'Alpa', ?, 'Disetujui', ?)");
            $ins->execute([$guru_id, $today, json_encode($tugasInval), $approvedBy]);
        }

        Helper::redirect('/apk/admin-izin-piket');
    }

    public function eksekusiInval() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $db = \App\Core\Database::connect('siakad');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pengajuanId = $_POST['pengajuan_id'] ?? 0;
            $kelasId = $_POST['kelas_id'] ?? 0;
            $mapelId = $_POST['mapel_id'] ?? 0;
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $guruIzinId = $_POST['guru_izin_id'] ?? 0;
            $jurnalMateri = $_POST['jurnal_materi'] ?? '';
            $absen = $_POST['absen'] ?? [];
            $piketGuruId = $_SESSION['guru_id'];

            $stmtPiket = $db->prepare("SELECT nama FROM guru WHERE id = ?");
            $stmtPiket->execute([$piketGuruId]);
            $piketName = $stmtPiket->fetchColumn() ?: 'Guru Piket';

            $stmtIzin = $db->prepare("SELECT nama FROM guru WHERE id = ?");
            $stmtIzin->execute([$guruIzinId]);
            $izinName = $stmtIzin->fetchColumn() ?: 'Guru';

            $keteranganJurnal = "[INVAL oleh " . $piketName . "] " . $jurnalMateri;
            $stmtJurnal = $db->prepare("INSERT INTO jurnal_guru (guru_id, kelas_id, mapel_id, tanggal, materi, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtJurnal->execute([$guruIzinId, $kelasId, $mapelId, $tanggal, $jurnalMateri, $keteranganJurnal]);
            $jurnalId = $db->lastInsertId();

            $cek_global = $db->prepare("SELECT status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ? AND status IN ('Sakit', 'Izin', 'Alpa') LIMIT 1");

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

            $stmtUpd = $db->prepare("UPDATE pengajuan_izin_guru SET inval_by = ? WHERE id = ?");
            $stmtUpd->execute([$piketGuruId, $pengajuanId]);

            Helper::redirect('/apk/admin-izin-piket?success=1');
        }

        $pengajuanId = $_GET['id'] ?? 0;
        $kelasId = $_GET['kelas_id'] ?? 0;
        $mapelId = $_GET['mapel_id'] ?? 0;
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

        $stmtP = $db->prepare("SELECT p.*, g.nama as nama_guru FROM pengajuan_izin_guru p JOIN guru g ON p.guru_id = g.id WHERE p.id = ?");
        $stmtP->execute([$pengajuanId]);
        $pengajuan = $stmtP->fetch(\PDO::FETCH_ASSOC);

        $namaGuruIzin = $pengajuan['nama_guru'] ?? 'Guru';
        $guruIzinId = $pengajuan['guru_id'] ?? 0;
        $sumberIzin = $pengajuan['jenis_izin'] ?? 'Izin';

        $namaKelas = $db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
        $namaKelas->execute([$kelasId]);
        $namaKelas = $namaKelas->fetchColumn() ?: 'Kelas ?';

        $namaMapel = $db->prepare("SELECT nama_mapel FROM mapel WHERE id = ?");
        $namaMapel->execute([$mapelId]);
        $namaMapel = $namaMapel->fetchColumn() ?: 'Mapel ?';

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

        $stmtSiswa = $db->prepare("SELECT id, nama, nis FROM siswa WHERE kelas_id = ? ORDER BY nama ASC");
        $stmtSiswa->execute([$kelasId]);
        $siswaList = $stmtSiswa->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Eksekusi Inval";
        $activeNav = "profile";

        ob_start();
        include __DIR__ . '/../../resources/views/apk/eksekusi_inval.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function getJadwalHarian()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['guru_id'])) {
            echo json_encode([]);
            exit;
        }

        $guru_id = $_SESSION['guru_id'];
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        
        $hariList = ['Minggu' => 'Minggu', 'Senin' => 'Senin', 'Selasa' => 'Selasa', 'Rabu' => 'Rabu', 'Kamis' => 'Kamis', 'Jumat' => 'Jumat', 'Sabtu' => 'Sabtu'];
        $dayOfWeek = date('l', strtotime($tanggal));
        $dayMap = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $hari = $dayMap[$dayOfWeek] ?? 'Senin';

        try {
            $db = \App\Core\Database::connect('siakad');
            
            $stmt_ta = $this->core_db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
            $ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC);
            $ta_id = $ta ? $ta['id'] : 1;
            $semester = $ta ? $ta['semester'] : 'Ganjil';

            $stmt = $db->prepare("
                SELECT jp.id, jp.kelas_id, jp.mapel_id as mapel_id, jp.jam_mulai, jp.jam_selesai,
                       k.nama_kelas, m.nama_mapel
                FROM jadwal_pelajaran jp
                JOIN kelas k ON jp.kelas_id = k.id
                JOIN mapel m ON jp.mapel_id = m.id
                WHERE jp.guru_id = ? AND jp.hari = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
                ORDER BY jp.jam_mulai ASC
            ");
            $stmt->execute([$guru_id, $hari, $ta_id, $semester]);
            $jadwal = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode($jadwal);
        } catch (\Exception $e) {
            echo json_encode([]);
        }
        exit;
    }

    public function submitIzinGuru()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $tanggal = $_POST['tanggal'] ?? '';
        $jenis_izin = $_POST['jenis_izin'] ?? '';
        $alasan_detail = $_POST['alasan_detail'] ?? '';
        
        if (!empty($alasan_detail)) {
            $jenis_izin .= ' - ' . $alasan_detail;
        }

        $jadwal_ids = $_POST['jadwal_ids'] ?? [];
        $kelas_ids = $_POST['kelas_ids'] ?? [];
        $mapel_ids = $_POST['mapel_ids'] ?? [];
        $tugas = $_POST['tugas'] ?? [];

        if (empty($tanggal) || empty($jenis_izin)) {
            $_SESSION['flash_error'] = 'Data tidak lengkap.';
            Helper::redirect('/apk/izin-guru');
            return;
        }

        // Build tugas_inval JSON
        $tugasInval = [];
        for ($i = 0; $i < count($jadwal_ids); $i++) {
            $tugasInval[] = [
                'jadwal_id' => (int)$jadwal_ids[$i],
                'kelas_id' => (int)$kelas_ids[$i],
                'mapel_id' => (int)$mapel_ids[$i],
                'materi' => $tugas[$i] ?? ''
            ];
        }

        try {
            $db = \App\Core\Database::connect('siakad');
            
            // Check if already submitted for this date
            $check = $db->prepare("SELECT id FROM pengajuan_izin_guru WHERE guru_id = ? AND tanggal = ? AND status_approval != 'Ditolak'");
            $check->execute([$guru_id, $tanggal]);
            if ($check->fetch()) {
                $_SESSION['flash_error'] = 'Anda sudah mengajukan izin untuk tanggal tersebut.';
                Helper::redirect('/apk/izin-guru');
                return;
            }

            $stmt = $db->prepare("INSERT INTO pengajuan_izin_guru (guru_id, tanggal, jenis_izin, tugas_inval) VALUES (?, ?, ?, ?)");
            $stmt->execute([$guru_id, $tanggal, $jenis_izin, json_encode($tugasInval)]);

            // Get Guru Name
            $stmtG = $db->prepare("SELECT nama FROM guru WHERE id = ?");
            $stmtG->execute([$guru_id]);
            $guruNama = $stmtG->fetchColumn() ?: 'Seorang Guru';

            // Fetch admin_izin_piket ids
            $db_core = \App\Core\Database::connect('core');
            $stmtAdmin = $db_core->prepare("SELECT nilai FROM absensi_pengaturan WHERE kunci = 'admin_izin_piket'");
            $stmtAdmin->execute();
            $adminIds = $stmtAdmin->fetchColumn();
            
            if ($adminIds) {
                require_once __DIR__ . '/../Services/OneSignalService.php';
                $adminIdArray = explode(',', $adminIds);
                foreach ($adminIdArray as $a_id) {
                    \App\Services\OneSignalService::sendNotification(
                        "Pengajuan Izin Baru", 
                        "Bapak/Ibu $guruNama mengajukan izin dan meninggalkan tugas. Segera cek dan atur Inval di panel Izin Guru.", 
                        null, 
                        "guru_" . trim($a_id)
                    );
                }
            }

            $_SESSION['flash_success'] = 'Pengajuan izin berhasil dikirim! Menunggu persetujuan Guru Piket.';
            Helper::redirect('/apk/rekap-absensi');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Gagal mengirim pengajuan: ' . $e->getMessage();
            Helper::redirect('/apk/izin-guru');
        }
    }

    public function profile()
    {
        if (!isset($_SESSION['guru_id']) && !isset($_SESSION['siswa_id'])) {
            Helper::redirect('/apk/login');
        }

        $title = "Profil Saya";
        $activeNav = "profile";

        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;

        $userProfile = [];
        $isGuru = isset($_SESSION['guru_id']);
        $is_wali_kelas = false;

        if ($isGuru) {
            $stmt = $this->core_db->prepare("SELECT * FROM guru WHERE id = ?");
            $stmt->execute([$_SESSION['guru_id']]);
            $userProfile = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Cek Wali Kelas di tabel kelas
            try {
                $stmt_wk = $this->core_db->prepare("SELECT id FROM kelas WHERE wali_kelas_id = ? LIMIT 1");
                $stmt_wk->execute([$_SESSION['guru_id']]);
                if ($stmt_wk->fetch()) {
                    $is_wali_kelas = true;
                }
            } catch (\Exception $e) {}

        } else {
            $stmt = $this->core_db->prepare("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.id = ?");
            $stmt->execute([$_SESSION['siswa_id']]);
            $userProfile = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        ob_start();
        include __DIR__ . '/../../resources/views/apk/profile.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function logout()
    {
        unset($_SESSION['guru_id']);
        unset($_SESSION['siswa_id']);
        Helper::redirect('/apk/login');
    }

    public function editProfile()
    {
        if (!isset($_SESSION['guru_id']) && !isset($_SESSION['siswa_id'])) {
            Helper::redirect('/apk/login');
        }

        $isGuru = isset($_SESSION['guru_id']);
        $userId = $isGuru ? $_SESSION['guru_id'] : $_SESSION['siswa_id'];
        $table = $isGuru ? 'guru' : 'siswa';
        
        $errorMsg = null;
        $successMsg = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Update text fields
                $setClause = [];
                $params = [];
                
                $allowedFields = $isGuru 
                    ? ['tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'telepon', 'rt', 'rw', 'desa', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'nik', 'email', 'npk', 'npwp', 'no_rekening', 'nrg', 'no_peserta_sertifikasi', 'no_sertifikat', 'tgl_sertifikat', 'jenjang_sertifikat', 'mapel_sertifikat']
                    : ['tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'alamat', 'no_hp_ortu', 'rt', 'rw', 'desa', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'nama_ayah', 'nama_ibu', 'nik', 'no_kk', 'pekerjaan_ayah', 'pekerjaan_ibu', 'penghasilan_ortu', 'nama_wali', 'pekerjaan_wali', 'no_hp_wali', 'gol_darah', 'anak_ke', 'kewarganegaraan', 'sekolah_asal'];

                foreach ($allowedFields as $field) {
                    if (isset($_POST[$field])) {
                        $setClause[] = "$field = ?";
                        $params[] = trim($_POST[$field]);
                    }
                }
                
                // Handle Photo Upload
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['foto']['tmp_name'];
                    $fileName = $_FILES['foto']['name'];
                    $fileSize = $_FILES['foto']['size'];
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    
                    $allowedExts = ['jpg', 'jpeg', 'png'];
                    if (!in_array($fileExt, $allowedExts)) {
                        throw new \Exception("Ekstensi file tidak diizinkan. Harap upload file JPG atau PNG.");
                    }
                    
                    if ($fileSize > 2 * 1024 * 1024) {
                        throw new \Exception("Ukuran file maksimal 2 MB.");
                    }
                    
                    $newFileName = $table . '_' . time() . '_' . rand(100, 999) . '.' . $fileExt;
                    $uploadDir = __DIR__ . '/../../public/uploads/' . $table . '/';
                    
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Ambil nama file lama
                    $stmtOld = $this->core_db->prepare("SELECT foto FROM $table WHERE id = ?");
                    $stmtOld->execute([$userId]);
                    $oldFoto = $stmtOld->fetchColumn();
                    
                    if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
                        $setClause[] = "foto = ?";
                        $params[] = $newFileName;
                        
                        // Hapus foto lama jika ada
                        if ($oldFoto && file_exists($uploadDir . $oldFoto)) {
                            @unlink($uploadDir . $oldFoto);
                        }
                    } else {
                        throw new \Exception("Gagal mengupload foto.");
                    }
                }

                if (!empty($setClause)) {
                    $params[] = $userId;
                    $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE id = ?";
                    $stmt = $this->core_db->prepare($sql);
                    $stmt->execute($params);
                    
                    $namaUser = $_SESSION['nama'] ?? 'User';
                    $tipeUser = $isGuru ? 'Guru' : 'Siswa';
                    Helper::logActivity('SIAKAD', 'UPDATE', "Update data profil $tipeUser: $namaUser via APK.");
                    
                    $successMsg = "Profil berhasil diperbarui!";
                }

            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
            }
        }

        // Ambil data terbaru
        $stmt = $this->core_db->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$userId]);
        $userProfile = $stmt->fetch(\PDO::FETCH_ASSOC);

        $title = "Edit Profil";
        
        $institusi = Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;

        ob_start();
        include __DIR__ . '/../../resources/views/apk/edit_profile.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function wakaKurikulum()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        
        $title = "Waka Kurikulum";
        $activeNav = "apps";
        
        $db = \App\Core\Database::connect();
        $today = date('Y-m-d');
        $hari_ini = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][date('w')];
        
        // Guru hadir hari ini
        $stmt = $db->prepare("SELECT COUNT(DISTINCT guru_id) as total FROM absensi_guru WHERE tanggal = ?");
        $stmt->execute([$today]);
        $guruHadir = $stmt->fetch()['total'] ?? 0;
        
        // Kelas kosong (estimasi: jadwal total - jurnal terisi)
        $stmt = $db->prepare("SELECT COUNT(id) as total FROM jadwal_pelajaran WHERE hari = ?");
        $stmt->execute([$hari_ini]);
        $totalJadwal = $stmt->fetch()['total'] ?? 0;
        
        $stmt = $db->prepare("SELECT COUNT(id) as total FROM jurnal_guru WHERE tanggal = ?");
        $stmt->execute([$today]);
        $jurnalTerisi = $stmt->fetch()['total'] ?? 0;
        
        $kelasKosong = max(0, $totalJadwal - $jurnalTerisi);

        ob_start();
        include __DIR__ . '/../../resources/views/apk/waka_kurikulum.php';
        $content = ob_get_clean();
        
        $hideNav = true; // Sembunyikan bottom nav di halaman khusus agar fokus
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function wakaKesiswaan()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        
        $title = "Waka Kesiswaan";
        $activeNav = "apps";
        
        $db = \App\Core\Database::connect();
        $today = date('Y-m-d');
        
        // Siswa hadir hari ini (asumsi status 'H' atau 'Hadir')
        $stmt = $db->prepare("SELECT COUNT(DISTINCT siswa_id) as total FROM absensi_siswa WHERE tanggal = ? AND status IN ('H', 'Hadir')");
        $stmt->execute([$today]);
        $siswaHadir = $stmt->fetch()['total'] ?? 0;
        
        // Pelanggaran baru hari ini
        $stmt = $db->prepare("SELECT COUNT(id) as total FROM bk_poin WHERE tanggal = ?");
        $stmt->execute([$today]);
        $pelanggaranBaru = $stmt->fetch()['total'] ?? 0;

        ob_start();
        include __DIR__ . '/../../resources/views/apk/waka_kesiswaan.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function wakaKurikulumJadwal()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        $title = "Jadwal KBM Global";
        $activeNav = "apps";
        
        $db = \App\Core\Database::connect();
        $hari_ini = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][date('w')];
        $jadwal = $db->query("SELECT j.*, m.nama_mapel, k.nama_kelas, g.nama as nama_guru FROM jadwal_pelajaran j LEFT JOIN mapel m ON j.mapel_id = m.id LEFT JOIN kelas k ON j.kelas_id = k.id LEFT JOIN guru g ON j.guru_id = g.id WHERE j.hari = '$hari_ini' ORDER BY j.jam_mulai ASC")->fetchAll();

        ob_start();
        include __DIR__ . '/../../resources/views/apk/waka_kurikulum_jadwal.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function wakaKurikulumPresensi()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        
        $db = \App\Core\Database::connect();
        
        // Filter tanggal, default ke hari ini
        $tanggal = $_GET['tgl'] ?? date('Y-m-d');
        
        // Ambil data semua guru dan absennya pada tanggal terpilih
        // Kita join left dari tabel guru ke absensi_guru
        $sql = "SELECT g.id as guru_id, g.nama,
                a.status, a.jam_masuk, a.jam_pulang, a.keterangan 
                FROM guru g 
                LEFT JOIN absensi_guru a ON g.id = a.guru_id AND a.tanggal = ?
                ORDER BY g.nama ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$tanggal]);
        $rekap = $stmt->fetchAll();
        
        // Statistik hari itu
        $hadir = 0; $terlambat = 0; $sakit = 0; $izin = 0; $alpa = 0; $belum_absen = 0;
        foreach ($rekap as $r) {
            if (!$r['status']) $belum_absen++;
            elseif ($r['status'] == 'Hadir') $hadir++;
            elseif ($r['status'] == 'Terlambat') $terlambat++;
            elseif ($r['status'] == 'Sakit') $sakit++;
            elseif ($r['status'] == 'Izin') $izin++;
            elseif ($r['status'] == 'Alpa') $alpa++;
        }
        
        $title = "QR Absen Guru";
        $activeNav = "apps";
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/waka_kurikulum_presensi.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function kamadRekapAbsenGuru()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        
        $db = \App\Core\Database::connect();
        
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');
        
        // Ambil data semua guru dan hitung statistik absennya dalam rentang tanggal
        $sql = "SELECT g.id, g.nama,
                SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.status = 'Alpa' OR a.status = 'Alpha' THEN 1 ELSE 0 END) as alpa
                FROM guru g 
                LEFT JOIN absensi_guru a ON g.id = a.guru_id AND a.tanggal >= ? AND a.tanggal <= ?
                GROUP BY g.id, g.nama
                ORDER BY g.nama ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$start_date, $end_date]);
        $rekap_list = $stmt->fetchAll();
        
        $title = "Rekap Absen Guru";
        $activeNav = "apps";
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/kamad_rekap_absen_guru.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function wakaKesiswaanPoin()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        $title = "Poin Pelanggaran";
        $activeNav = "apps";
        
        $db = \App\Core\Database::connect();
        $pelanggaran = $db->query("SELECT bp.*, s.nama as nama_siswa, k.nama_kelas, bk.nama_kategori, bp.poin FROM bk_poin bp LEFT JOIN siswa s ON bp.siswa_id = s.id LEFT JOIN kelas k ON s.kelas_id = k.id LEFT JOIN bk_kategori bk ON bp.kategori_id = bk.id ORDER BY bp.tanggal DESC LIMIT 100")->fetchAll();
        $topSiswa = $db->query("SELECT bp.siswa_id, s.nama as nama_siswa, k.nama_kelas, SUM(bp.poin) as total_poin FROM bk_poin bp LEFT JOIN siswa s ON bp.siswa_id = s.id LEFT JOIN kelas k ON s.kelas_id = k.id GROUP BY bp.siswa_id, s.nama, k.nama_kelas ORDER BY total_poin DESC")->fetchAll();

        ob_start();
        include __DIR__ . '/../../resources/views/apk/waka_kesiswaan_poin.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function wakaKesiswaanMutasi()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        $title = "Mutasi Siswa";
        $activeNav = "apps";
        
        $db = \App\Core\Database::connect();
        $mutasi = $db->query("SELECT m.*, s.nama as nama_siswa, s.nisn FROM mutasi_siswa m LEFT JOIN siswa s ON m.siswa_id = s.id ORDER BY m.tanggal_mutasi DESC LIMIT 50")->fetchAll();

        ob_start();
        include __DIR__ . '/../../resources/views/apk/waka_kesiswaan_mutasi.php';
        $content = ob_get_clean();
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function wakaKesiswaanAbsensi()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        $title = "Statistik Kehadiran";
        $activeNav = "apps";
        
        $db = \App\Core\Database::connect();
        $today = date('Y-m-d');
        
        // Total Siswa
        $stmt_total = $db->query("SELECT COUNT(id) as total FROM siswa WHERE status = 'Aktif'");
        $totalSiswa = $stmt_total->fetch()['total'] ?? 0;
        
        // Kehadiran Hari ini (Hadir, Izin, Sakit, Alpha)
        $stmt_hadir = $db->prepare("SELECT status, COUNT(*) as jumlah FROM absensi_siswa WHERE tanggal = ? GROUP BY status");
        $stmt_hadir->execute([$today]);
        $absenData = $stmt_hadir->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        $hadir = $absenData['Hadir'] ?? 0;
        $izin = $absenData['Izin'] ?? 0;
        $sakit = $absenData['Sakit'] ?? 0;
        $alpha = ($absenData['Alpha'] ?? 0) + ($absenData['Alpa'] ?? 0);
        $bolos = $absenData['Bolos'] ?? 0;
        
        $persentase = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100, 1) : 0;
        
        $c1 = $totalSiswa > 0 ? ($hadir / $totalSiswa * 100) : 0;
        $c2 = $c1 + ($totalSiswa > 0 ? ($sakit / $totalSiswa * 100) : 0);
        $c3 = $c2 + ($totalSiswa > 0 ? ($izin / $totalSiswa * 100) : 0);
        $c4 = $c3 + ($totalSiswa > 0 ? ($alpha / $totalSiswa * 100) : 0);
        $c5 = $c4 + ($totalSiswa > 0 ? ($bolos / $totalSiswa * 100) : 0);
        
        $conicGradient = "conic-gradient(#22c55e 0% {$c1}%, #f59e0b {$c1}% {$c2}%, #3b82f6 {$c2}% {$c3}%, #ef4444 {$c3}% {$c4}%, #d97706 {$c4}% {$c5}%, #f1f5f9 {$c5}% 100%)";
        
        // Data Grafik 7 hari terakhir (dummy or basic)
        $grafik = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $stmt = $db->prepare("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = ? AND status = 'Hadir'");
            $stmt->execute([$date]);
            $grafik[date('d M', strtotime($date))] = $stmt->fetchColumn();
        }

        ob_start();
        include __DIR__ . '/../../resources/views/apk/waka_kesiswaan_absensi.php';
        $content = ob_get_clean();
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function bk()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        
        $title = "Bimbingan Konseling";
        $activeNav = "apps";
        
        $db = \App\Core\Database::connect();
        $today = date('Y-m-d');
        
        // Dummy data for Sesi Konseling if no actual table exists, else 0
        $sesiKonseling = 0; 
        
        // Siswa perlu perhatian (minus poin >= 50 for example)
        // We will just do a dummy count for now or query bk_poin joining with bk_kategori
        $stmt_perhatian = $db->query("SELECT COUNT(DISTINCT p.siswa_id) FROM bk_poin p JOIN bk_kategori k ON p.kategori_id = k.id WHERE k.tipe = 'pelanggaran'");
        $perluPerhatian = $stmt_perhatian->fetchColumn() ?: 0;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/bk.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function bkJurnal()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        $db = \App\Core\Database::connect();
        
        $guru_id = $_SESSION['guru_id'];
        $jurnalList = $db->query("SELECT j.*, s.nama as nama_siswa, k.nama_kelas FROM bk_konseling j JOIN siswa s ON j.siswa_id = s.id LEFT JOIN kelas k ON s.kelas_id = k.id WHERE j.guru_id = $guru_id ORDER BY j.tanggal DESC")->fetchAll();
        $siswaList = $db->query("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.status = 'Aktif' ORDER BY k.tingkat ASC, k.nama_kelas ASC, s.nama ASC")->fetchAll();
        
        $title = "Jurnal Konseling";
        $activeNav = "apps";
        ob_start();
        include __DIR__ . '/../../resources/views/apk/bk_jurnal.php';
        $content = ob_get_clean();
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function bkJurnalSave()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = \App\Core\Database::connect();
            $guru_id = $_SESSION['guru_id'];
            $siswa_id = $_POST['siswa_id'] ?? '';
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $masalah = $_POST['masalah'] ?? '';
            $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
            
            if ($siswa_id && $masalah) {
                $stmt = $db->prepare("INSERT INTO bk_konseling (siswa_id, guru_id, tanggal, masalah, tindak_lanjut) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$siswa_id, $guru_id, $tanggal, $masalah, $tindak_lanjut]);
            }
        }
        Helper::redirect('/apk/bk/jurnal');
    }

    public function bkPoin()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        $db = \App\Core\Database::connect();
        
        $poinList = $db->query("SELECT p.*, s.nama as nama_siswa, k.nama_kelas, kt.nama_kategori, kt.tipe, g.nama as nama_guru FROM bk_poin p JOIN siswa s ON p.siswa_id = s.id LEFT JOIN kelas k ON s.kelas_id = k.id JOIN bk_kategori kt ON p.kategori_id = kt.id LEFT JOIN guru g ON p.guru_id = g.id ORDER BY p.tanggal DESC LIMIT 100")->fetchAll();
        $kelasList = $db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        $kategoriList = $db->query("SELECT * FROM bk_kategori WHERE tipe = 'pelanggaran' ORDER BY tingkat ASC, nama_kategori ASC")->fetchAll();
        
        // Buat mapping siswa per kelas untuk kebutuhan JS di frontend
        $siswaRaw = $db->query("SELECT id, nama, kelas_id FROM siswa WHERE status = 'Aktif' ORDER BY nama ASC")->fetchAll();
        $siswaByKelas = [];
        foreach ($siswaRaw as $s) {
            $siswaByKelas[$s['kelas_id']][] = $s;
        }
        
        // Cek tahun ajaran aktif
        $ta = $db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1")->fetch();
        $ta_id = $ta ? $ta['id'] : 0;
        
        $title = "Poin Kedisiplinan";
        $activeNav = "apps";
        ob_start();
        include __DIR__ . '/../../resources/views/apk/bk_poin.php';
        $content = ob_get_clean();
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function bkPoinSave()
    {
        if (!isset($_SESSION['guru_id'])) Helper::redirect('/apk/login');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = \App\Core\Database::connect();
            $guru_id = $_SESSION['guru_id'];
            $siswa_id = $_POST['siswa_id'] ?? '';
            $kategori_id = $_POST['kategori_id'] ?? '';
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $keterangan = $_POST['keterangan'] ?? '';
            $ta_id = $_POST['tahun_ajaran_id'] ?? 0;
            $poin = (int)($_POST['poin'] ?? 0);
            
            if ($siswa_id && $kategori_id && $poin > 0) {
                $stmt = $db->prepare("INSERT INTO bk_poin (siswa_id, kategori_id, poin, tahun_ajaran_id, guru_id, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$siswa_id, $kategori_id, $poin, $ta_id, $guru_id, $tanggal, $keterangan]);
            }
        }
        Helper::redirect('/apk/bk/poin');
    }

    public function nilaiHarianIndex() {
        if (!isset($_SESSION['guru_id'])) {
            \App\Core\Helper::redirect('/apk/login');
        }
        $guru_id = $_SESSION['guru_id'];
        
        // Fetch active tahun ajaran
        $stmt_ta = $this->core_db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC);
        $tahun_ajaran_id = $ta ? $ta['id'] : null;
        $semester = $ta ? $ta['semester'] : null;

        $stmt_k = $this->siakad_db->prepare("
            SELECT DISTINCT id, nama_kelas, tingkat FROM (
                SELECT k.id, k.nama_kelas, k.tingkat 
                FROM penugasan_mengajar m
                JOIN kelas k ON k.id = m.kelas_id
                WHERE m.guru_id = ?
                UNION
                SELECT k.id, k.nama_kelas, k.tingkat 
                FROM jadwal_pelajaran jp 
                JOIN kelas k ON k.id = jp.kelas_id 
                WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
            ) AS combined
            ORDER BY tingkat, nama_kelas
        ");
        $stmt_k->execute([$guru_id, $guru_id, $tahun_ajaran_id, $semester]);
        $kelasList = $stmt_k->fetchAll(\PDO::FETCH_ASSOC);
        
        $stmt_m = $this->siakad_db->prepare("
            SELECT DISTINCT kelas_id, mapel_id, nama_mapel FROM (
                SELECT m.kelas_id, mp.id as mapel_id, mp.nama_mapel
                FROM penugasan_mengajar m
                JOIN mapel mp ON mp.id = m.mapel_id
                WHERE m.guru_id = ?
                UNION
                SELECT jp.kelas_id, mp.id as mapel_id, mp.nama_mapel 
                FROM jadwal_pelajaran jp 
                JOIN mapel mp ON mp.id = jp.mapel_id 
                WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.semester = ?
            ) AS combined
            ORDER BY nama_mapel
        ");
        $stmt_m->execute([$guru_id, $guru_id, $tahun_ajaran_id, $semester]);
        $mengajarData = $stmt_m->fetchAll(\PDO::FETCH_ASSOC);

        $mapelList = [];
        $addedMapels = [];
        foreach($mengajarData as $m) {
            if(!isset($addedMapels[$m['mapel_id']])) {
                $mapelList[] = ['id' => $m['mapel_id'], 'nama_mapel' => $m['nama_mapel']];
                $addedMapels[$m['mapel_id']] = true;
            }
        }
        
        // Fetch all students for the teacher's classes first to get $kelasIds
        $kelasIds = array_column($kelasList, 'id');
        $siswaMap = [];
        if (!empty($kelasIds)) {
            $placeholders = implode(',', array_fill(0, count($kelasIds), '?'));
            $stmt_s = $this->core_db->prepare("SELECT id, kelas_id, nama FROM siswa WHERE kelas_id IN ($placeholders) AND status = 'Aktif' ORDER BY nama ASC");
            $stmt_s->execute($kelasIds);
            foreach ($stmt_s->fetchAll(\PDO::FETCH_ASSOC) as $s) {
                $siswaMap[$s['kelas_id']][] = $s;
            }
        }

        // Fetch history of existing PH/PTS/PAS grades to allow editing (Optimized to filter by guru's classes)
        $historyListRaw = [];
        if (!empty($kelasIds)) {
            $placeholders = implode(',', array_fill(0, count($kelasIds), '?'));
            // Urutkan berdasarkan Mapel, lalu jenis evaluasi (PH 1, PH 2.. PTS, PAS)
            $stmt_history = $this->siakad_db->prepare("SELECT kelas_id, mapel_id, jenis_evaluasi, COUNT(siswa_id) as jml_siswa, MAX(created_at) as last_updated, MAX(keterangan) as keterangan FROM nilai_harian WHERE tahun_ajaran_id = ? AND semester = ? AND kelas_id IN ($placeholders) GROUP BY kelas_id, mapel_id, jenis_evaluasi ORDER BY mapel_id ASC, (CASE WHEN jenis_evaluasi LIKE 'PH %' THEN CAST(SUBSTRING(jenis_evaluasi, 4) AS UNSIGNED) WHEN jenis_evaluasi = 'PTS' THEN 998 WHEN jenis_evaluasi = 'PAS' THEN 999 ELSE 1000 END) ASC");
            $params = array_merge([$tahun_ajaran_id, $semester], $kelasIds);
            $stmt_history->execute($params);
            $historyListRaw = $stmt_history->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Fetch all grades for the teacher's classes
        $nilaiMap = [];
        if (!empty($kelasIds)) {
            $placeholders = implode(',', array_fill(0, count($kelasIds), '?'));
            $stmt_n = $this->siakad_db->prepare("SELECT siswa_id, kelas_id, mapel_id, jenis_evaluasi, nilai FROM nilai_harian WHERE tahun_ajaran_id = ? AND semester = ? AND kelas_id IN ($placeholders)");
            $params = array_merge([$tahun_ajaran_id, $semester], $kelasIds);
            $stmt_n->execute($params);
            foreach ($stmt_n->fetchAll(\PDO::FETCH_ASSOC) as $g) {
                $nilaiMap[$g['kelas_id']][$g['mapel_id']][$g['jenis_evaluasi']][$g['siswa_id']] = $g['nilai'];
            }
        }
        // Build a strict mapping of what the teacher actually teaches
        $validMengajar = [];
        foreach($mengajarData as $m) {
            $validMengajar[$m['kelas_id'] . '_' . $m['mapel_id']] = true;
        }

        // Map history to include class and mapel names, and group by class then mapel
        $historyGrouped = [];
        foreach($historyListRaw as $h) {
            $nama_kelas = 'Unknown';
            foreach($kelasList as $k) {
                if($k['id'] == $h['kelas_id']) { $nama_kelas = $k['nama_kelas']; break; }
            }
            $nama_mapel = 'Unknown';
            foreach($mapelList as $m) {
                if($m['id'] == $h['mapel_id']) { $nama_mapel = $m['nama_mapel']; break; }
            }
            
            $isValid = isset($validMengajar[$h['kelas_id'] . '_' . $h['mapel_id']]);
            
            if($isValid && $nama_kelas != 'Unknown' && $nama_mapel != 'Unknown') {
                $k_id = $h['kelas_id'];
                $m_id = $h['mapel_id'];
                $eval = $h['jenis_evaluasi'];
                
                $siswaOfClass = $siswaMap[$k_id] ?? [];
                $totalSiswa = count($siswaOfClass);
                
                $participated = 0;
                $details = [];
                foreach ($siswaOfClass as $siswa) {
                    $nilai = $nilaiMap[$k_id][$m_id][$eval][$siswa['id']] ?? 0;
                    $nilai = $nilai !== "" && $nilai !== null ? (int)$nilai : 0;
                    if ($nilai > 0) {
                        $participated++;
                    }
                    $details[] = [
                        'nama' => $siswa['nama'],
                        'nilai' => $nilai,
                        'tuntas' => $nilai >= 75 // Assuming KKM 75
                    ];
                }
                
                $persen = $totalSiswa > 0 ? round(($participated / $totalSiswa) * 100) : 0;
                
                if (!isset($historyGrouped[$nama_kelas])) {
                    $historyGrouped[$nama_kelas] = [];
                }
                if (!isset($historyGrouped[$nama_kelas][$nama_mapel])) {
                    $historyGrouped[$nama_kelas][$nama_mapel] = [];
                }
                
                $historyGrouped[$nama_kelas][$nama_mapel][] = [
                    'kelas_id' => $k_id,
                    'mapel_id' => $m_id,
                    'nama_kelas' => $nama_kelas,
                    'nama_mapel' => $nama_mapel,
                    'jenis_evaluasi' => $eval,
                    'jml_siswa' => $totalSiswa,
                    'keterangan' => $h['keterangan'] ?? '',
                    'persen_ikut' => $persen,
                    'details' => $details
                ];
            }
        }
        
        // Sort grouped classes by keys
        ksort($historyGrouped);

        $title = "Nilai Harian";
        $activeNav = "profile"; // Keeping it on profile tab
        
        $institusi = \App\Core\Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/nilai_harian_index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function nilaiHarianInput() {
        if (!isset($_SESSION['guru_id'])) {
            \App\Core\Helper::redirect('/apk/login');
        }
        $guru_id = $_SESSION['guru_id'];
        
        $kelas_id = $_GET['kelas_id'] ?? null;
        $mapel_id = $_GET['mapel_id'] ?? null;
        $jenis_evaluasi_tipe = $_GET['jenis_evaluasi_tipe'] ?? 'PH'; // PH, PTS, PAS
        
        if (!$kelas_id || !$mapel_id) {
            \App\Core\Helper::redirect('/apk/nilai-harian');
        }
        
        // Fetch active tahun ajaran
        $stmt_ta = $this->core_db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC);
        $tahun_ajaran_id = $ta ? $ta['id'] : null;
        $semester = $ta ? $ta['semester'] : null;

        // Fetch students in this class
        $stmt_siswa = $this->core_db->prepare("SELECT id, nama, nis, nisn, foto FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt_siswa->execute([$kelas_id]);
        $siswaList = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch class and mapel details
        $kelas = $this->core_db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
        $kelas->execute([$kelas_id]);
        $nama_kelas = $kelas->fetchColumn();

        $mapel = $this->siakad_db->prepare("SELECT nama_mapel FROM mapel WHERE id = ?");
        $mapel->execute([$mapel_id]);
        $nama_mapel = $mapel->fetchColumn();
        
        // Determine exact jenis_evaluasi (e.g. "PH 1", "PTS", "PAS")
        $jenis_evaluasi_final = $jenis_evaluasi_tipe;
        if ($jenis_evaluasi_tipe === 'PH') {
            // Check if there's an exact PH requested for editing
            if (isset($_GET['ph_nomor']) && !empty($_GET['ph_nomor'])) {
                $jenis_evaluasi_final = "PH " . (int)$_GET['ph_nomor'];
            } else {
                // Auto-increment logic for new entry
                $stmt_ph = $this->siakad_db->prepare("SELECT MAX(CAST(SUBSTRING_INDEX(jenis_evaluasi, ' ', -1) AS UNSIGNED)) as max_ph FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi LIKE 'PH %'");
                $stmt_ph->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester]);
                $max_ph = $stmt_ph->fetchColumn();
                $next_ph = $max_ph ? $max_ph + 1 : 1;
                $jenis_evaluasi_final = "PH " . $next_ph;
            }
        } else if ($jenis_evaluasi_tipe === 'EXACT') {
             $jenis_evaluasi_final = $_GET['jenis_evaluasi_exact'] ?? 'PH 1';
        }
        
        // Fetch existing grades if any
        $stmt_nilai = $this->siakad_db->prepare("SELECT siswa_id, nilai, materi FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi = ?");
        $stmt_nilai->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester, $jenis_evaluasi_final]);
        $existingNilaiRaw = $stmt_nilai->fetchAll(\PDO::FETCH_ASSOC);
        $existingNilai = [];
        $existingMateri = '';
        foreach($existingNilaiRaw as $n) {
            $existingNilai[$n['siswa_id']] = $n['nilai'];
            if(empty($existingMateri) && !empty($n['materi'])) {
                $existingMateri = $n['materi'];
            }
        }

        $title = "Input Nilai " . $jenis_evaluasi_final;
        $activeNav = "profile";
        
        $institusi = \App\Core\Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/nilai_harian_input.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function nilaiHarianSave() {
        if (!isset($_SESSION['guru_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $guru_id = $_SESSION['guru_id'];
        $kelas_id = $_POST['kelas_id'] ?? '';
        $mapel_id = $_POST['mapel_id'] ?? '';
        $jenis_evaluasi = $_POST['jenis_evaluasi'] ?? '';
        $materi = $_POST['materi'] ?? '';
        $nilai_data = $_POST['nilai'] ?? []; // array of siswa_id => nilai
        
        if (empty($kelas_id) || empty($mapel_id) || empty($jenis_evaluasi) || empty(trim($materi))) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap. Pastikan Materi Pembelajaran sudah diisi.']);
            exit;
        }
        
        $stmt_ta = $this->core_db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC);
        $tahun_ajaran_id = $ta ? $ta['id'] : null;
        $semester = $ta ? $ta['semester'] : null;
        
        if (!$tahun_ajaran_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Tahun ajaran aktif tidak ditemukan']);
            exit;
        }

        try {
            $this->siakad_db->beginTransaction();
            
            // Delete existing grades for this specific evaluation so we can insert new ones
            $stmt_del = $this->siakad_db->prepare("DELETE FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi = ?");
            $stmt_del->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester, $jenis_evaluasi]);
            
            $stmt_ins = $this->siakad_db->prepare("INSERT INTO nilai_harian (siswa_id, mapel_id, kelas_id, tahun_ajaran_id, semester, jenis_evaluasi, nilai, materi, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            foreach ($nilai_data as $siswa_id => $nilai) {
                if ($nilai !== '' && $nilai !== null) {
                    $nilai_val = (float)$nilai;
                    // Cap at 100
                    if ($nilai_val > 100) $nilai_val = 100;
                    if ($nilai_val < 0) $nilai_val = 0;
                    $stmt_ins->execute([$siswa_id, $mapel_id, $kelas_id, $tahun_ajaran_id, $semester, $jenis_evaluasi, $nilai_val, $materi]);
                }
            }
            
            $this->siakad_db->commit();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Nilai berhasil disimpan!']);
        } catch (\Exception $e) {
            $this->siakad_db->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan nilai: ' . $e->getMessage()]);
        }
        exit;
    }

    public function nilaiHarianDelete() {
        if (!isset($_SESSION['guru_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $kelas_id = $_POST['kelas_id'] ?? '';
        $mapel_id = $_POST['mapel_id'] ?? '';
        $jenis_evaluasi = $_POST['jenis_evaluasi'] ?? '';

        if (empty($kelas_id) || empty($mapel_id) || empty($jenis_evaluasi)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        $stmt_ta = $this->core_db->query("SELECT * FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
        $ta = $stmt_ta->fetch(\PDO::FETCH_ASSOC);
        $tahun_ajaran_id = $ta ? $ta['id'] : null;
        $semester = $ta ? $ta['semester'] : null;

        try {
            $stmt_del = $this->siakad_db->prepare("DELETE FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi = ?");
            $stmt_del->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester, $jenis_evaluasi]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Riwayat nilai berhasil dihapus!']);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus nilai: ' . $e->getMessage()]);
        }
        exit;
    }

    public function monitorRealtime()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $today = $_GET['tgl'] ?? date('Y-m-d');
        
        // Cek Kalender Pendidikan (Hari Libur / Kegiatan)
        $stmt_kalender = $this->core_db->prepare("SELECT * FROM kalender_pendidikan WHERE tanggal_mulai <= ? AND tanggal_selesai >= ? ORDER BY tanggal_mulai DESC LIMIT 1");
        $stmt_kalender->execute([$today, $today]);
        $kalender_today = $stmt_kalender->fetch(\PDO::FETCH_ASSOC);
        
        $is_libur = ($kalender_today && $kalender_today['kategori'] === 'Libur');
        $is_kegiatan = ($kalender_today && $kalender_today['kategori'] === 'Kegiatan');
        $nama_kegiatan = $kalender_today ? $kalender_today['kegiatan'] : '';

        $title = "QR Absen Siswa";
        
        $kelasList = $this->core_db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        
        $stmt_absen = $this->siakad_db->prepare("
            SELECT s.kelas_id, s.nama, a.siswa_id, a.status, a.jam_masuk 
            FROM absensi_siswa a
            JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s ON a.siswa_id = s.id
            WHERE a.tanggal = ? AND s.status = 'Aktif'
            ORDER BY s.nama ASC
        ");
        $stmt_absen->execute([$today]);
        $all_absen = $stmt_absen->fetchAll(\PDO::FETCH_ASSOC);
        
        $absen_per_siswa = [];
        foreach ($all_absen as $row) {
            $absen_per_siswa[$row['siswa_id']] = [
                'kelas_id' => $row['kelas_id'],
                'nama' => $row['nama'],
                'status' => $row['status'],
                'jam_masuk' => $row['jam_masuk']
            ];
        }

        $absenStats = [];
        foreach ($absen_per_siswa as $siswa_id => $data) {
            $k_id = $data['kelas_id'];
            $st = $data['status'];
            if (!isset($absenStats[$k_id])) $absenStats[$k_id] = [];
            if (!isset($absenStats[$k_id][$st])) $absenStats[$k_id][$st] = 0;
            $absenStats[$k_id][$st]++;
        }

        $stmt_siswa = $this->core_db->query("SELECT kelas_id, COUNT(*) as total FROM siswa WHERE status = 'Aktif' GROUP BY kelas_id");
        $siswaCount = $stmt_siswa->fetchAll(\PDO::FETCH_KEY_PAIR);

        $kelasData = [];
        foreach ($kelasList as $k) {
            $k_id = $k['id'];
            $kelasData[$k_id] = [
                'nama' => $k['nama_kelas'],
                'total' => $siswaCount[$k_id] ?? 0,
                'hadir' => 0,
                'terlambat' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
                'belum' => 0,
                'students' => []
            ];
            $kelasData[$k_id]['belum'] = $kelasData[$k_id]['total'];
        }

        // Ambil data semua siswa aktif untuk ngisi yang belum absen
        $stmt_all_siswa = $this->core_db->query("SELECT id, kelas_id, nama FROM siswa WHERE status = 'Aktif' ORDER BY nama ASC");
        $all_siswa = $stmt_all_siswa->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($all_siswa as $s) {
            $s_id = $s['id'];
            $k_id = $s['kelas_id'];
            if (!isset($kelasData[$k_id])) continue;
            
            if (isset($absen_per_siswa[$s_id])) {
                $st = $absen_per_siswa[$s_id]['status'];
                $jam = $absen_per_siswa[$s_id]['jam_masuk'];
                $is_qr = !empty($jam) && $jam !== '00:00:00';
                
                // Jika absen manual (bukan QR) dan statusnya Hadir/Terlambat, anggap Belum scan QR.
                if (!$is_qr && !in_array($st, ['Sakit', 'Izin', 'Alpa', 'Alpha', 'Bolos'])) {
                    $st = 'Belum';
                }

                $jam_display = $is_qr ? substr($jam, 0, 5) : '--:--';
                
                $kelasData[$k_id]['students'][] = [
                    'nama' => $s['nama'],
                    'status' => $st,
                    'jam' => $jam_display
                ];
                
                if ($st == 'Hadir') {
                    $kelasData[$k_id]['hadir']++;
                    $kelasData[$k_id]['belum']--;
                } elseif ($st == 'Terlambat') {
                    $kelasData[$k_id]['terlambat']++;
                    $kelasData[$k_id]['belum']--;
                } elseif ($st == 'Sakit') {
                    $kelasData[$k_id]['sakit']++;
                    $kelasData[$k_id]['belum']--;
                } elseif ($st == 'Izin') {
                    $kelasData[$k_id]['izin']++;
                    $kelasData[$k_id]['belum']--;
                } elseif (in_array($st, ['Alpa', 'Alpha', 'Bolos'])) {
                    $kelasData[$k_id]['alpa']++;
                    $kelasData[$k_id]['belum']--;
                }
            } else {
                $kelasData[$k_id]['students'][] = [
                    'nama' => $s['nama'],
                    'status' => 'Belum',
                    'jam' => '--:--'
                ];
            }
        }

        // Guru Stats
        $guru_aktif = $this->core_db->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        
        $stmt_g = $this->core_db->prepare("SELECT status, COUNT(*) as jml FROM absensi_guru WHERE tanggal = ? GROUP BY status");
        $stmt_g->execute([$today]);
        $absen_g = $stmt_g->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        $g_hadir = ($absen_g['Hadir'] ?? 0) + ($absen_g['Terlambat'] ?? 0);
        $g_izin = ($absen_g['Izin'] ?? 0) + ($absen_g['Sakit'] ?? 0);
        $g_alpa = $absen_g['Alpa'] ?? 0;
        $g_belum = max(0, $guru_aktif - ($g_hadir + $g_izin + $g_alpa));
        
        $guruStats = [
            'total' => $guru_aktif,
            'hadir' => $g_hadir,
            'sakit_izin' => $g_izin,
            'alpa' => $g_alpa,
            'belum' => $g_belum
        ];

        // Jika Libur atau Kegiatan, anggap belum diabsen = 0 agar tidak merah
        if ($is_libur || $is_kegiatan) {
            $guruStats['belum'] = 0;
            // Untuk absen siswa, jika Libur, anggap belum diabsen = 0
            if ($is_libur) {
                foreach ($kelasData as $k_id => $kd) {
                    $kelasData[$k_id]['belum'] = 0;
                }
            }
        }

        ob_start();
        include __DIR__ . '/../../resources/views/apk/monitor_realtime.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function monitorRealtimeRekap()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');
        $filter_kelas_id = $_GET['kelas_id'] ?? '';
        
        $title = "Rekap QR Siswa";
        
        // Ambil list kelas untuk dropdown
        $kelasList = $this->core_db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        
        $rekap_list = [];
        
        if (!empty($filter_kelas_id)) {
            // Build the query
            $sql = "
                SELECT s.id as siswa_id, s.nama, k.nama_kelas,
                a.tanggal, a.status, a.jam_masuk
                FROM " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s
                LEFT JOIN kelas k ON s.kelas_id = k.id
                LEFT JOIN absensi_siswa a ON s.id = a.siswa_id AND a.tanggal >= ? AND a.tanggal <= ?
                WHERE s.status = 'Aktif' AND s.kelas_id = ?
                ORDER BY k.tingkat ASC, k.nama_kelas ASC, s.nama ASC, a.tanggal ASC
            ";
            
            $params = [$start_date, $end_date, $filter_kelas_id];
            
            $stmt_rekap = $this->siakad_db->prepare($sql);
            $stmt_rekap->execute($params);
            $raw_data = $stmt_rekap->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($raw_data as $row) {
                $s_id = $row['siswa_id'];
                if (!isset($rekap_list[$s_id])) {
                    $rekap_list[$s_id] = [
                        'siswa_id' => $s_id,
                        'nama' => $row['nama'],
                        'nama_kelas' => $row['nama_kelas'],
                        'hadir' => 0,
                        'terlambat' => 0,
                        'sakit' => 0,
                        'izin' => 0,
                        'belum' => 0,
                        'detail_hadir' => [],
                        'detail_terlambat' => [],
                        'detail_sakit' => [],
                        'detail_izin' => [],
                        'detail_belum' => [],
                        'processed_dates' => []
                    ];
                }
                
                if (!empty($row['tanggal']) && !empty($row['status'])) {
                    $tgl_raw = $row['tanggal'];
                    if (in_array($tgl_raw, $rekap_list[$s_id]['processed_dates'])) {
                        continue;
                    }
                    $rekap_list[$s_id]['processed_dates'][] = $tgl_raw;
                    $st = $row['status'];
                    $jam = $row['jam_masuk'];
                    $is_qr = !empty($jam) && $jam !== '00:00:00';
                    
                    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $day_index = date('w', strtotime($row['tanggal']));
                    $hari = $days[$day_index];
                    $tanggal = $hari . ', ' . date('d/m/Y', strtotime($row['tanggal']));
                    
                    if ($st == 'Hadir' && $is_qr) {
                        $rekap_list[$s_id]['hadir']++;
                        $rekap_list[$s_id]['detail_hadir'][] = ['tanggal' => $tanggal, 'jam' => substr($jam, 0, 5)];
                    } elseif ($st == 'Terlambat' && $is_qr) {
                        $rekap_list[$s_id]['terlambat']++;
                        $rekap_list[$s_id]['detail_terlambat'][] = ['tanggal' => $tanggal, 'jam' => substr($jam, 0, 5)];
                    } elseif ($st == 'Sakit') {
                        $rekap_list[$s_id]['sakit']++;
                        $rekap_list[$s_id]['detail_sakit'][] = ['tanggal' => $tanggal];
                    } elseif ($st == 'Izin') {
                        $rekap_list[$s_id]['izin']++;
                        $rekap_list[$s_id]['detail_izin'][] = ['tanggal' => $tanggal];
                    } elseif (in_array($st, ['Alpa', 'Alpha', 'Bolos']) || $st == 'Belum' || (!$is_qr && !in_array($st, ['Sakit', 'Izin']))) {
                        $rekap_list[$s_id]['belum']++;
                        $rekap_list[$s_id]['detail_belum'][] = ['tanggal' => $tanggal];
                    }
                }
            }
            
            // Ambil semua tanggal aktif untuk kelas ini di range ini (sebagai acuan total hari sekolah)
            $sql_dates = "
                SELECT DISTINCT a.tanggal 
                FROM absensi_siswa a
                JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s ON a.siswa_id = s.id
                WHERE a.tanggal >= ? AND a.tanggal <= ? AND s.kelas_id = ?
                ORDER BY a.tanggal ASC
            ";
            $stmt_dates = $this->siakad_db->prepare($sql_dates);
            $stmt_dates->execute([$start_date, $end_date, $filter_kelas_id]);
            $active_dates = $stmt_dates->fetchAll(\PDO::FETCH_COLUMN);

            // Fetch dates that are holidays
            $stmt_libur_dates = $this->core_db->prepare("SELECT tanggal_mulai, tanggal_selesai FROM kalender_pendidikan WHERE kategori = 'Libur' AND tanggal_selesai >= ? AND tanggal_mulai <= ?");
            $stmt_libur_dates->execute([$start_date, $end_date]);
            $libur_periods = $stmt_libur_dates->fetchAll(\PDO::FETCH_ASSOC);
            
            $libur_dates = [];
            foreach ($libur_periods as $lp) {
                $current = strtotime($lp['tanggal_mulai']);
                $end = strtotime($lp['tanggal_selesai']);
                while ($current <= $end) {
                    $libur_dates[] = date('Y-m-d', $current);
                    $current = strtotime('+1 day', $current);
                }
            }

            // Tambahkan tanggal yang bolong (tidak ada record) sebagai 'Belum'
            foreach ($rekap_list as $s_id => &$rl) {
                foreach ($active_dates as $ad) {
                    // Skip jika hari ini adalah hari libur (jangan dihitung sebagai belum absen)
                    if (in_array($ad, $libur_dates)) {
                        continue;
                    }
                    if (!in_array($ad, $rl['processed_dates'])) {
                        $rl['belum']++;
                        
                        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        $day_index = date('w', strtotime($ad));
                        $hari = $days[$day_index];
                        $tanggal = $hari . ', ' . date('d/m/Y', strtotime($ad));
                        
                        $rl['detail_belum'][] = ['tanggal' => $tanggal];
                        $rl['processed_dates'][] = $ad; // Tandai sudah diproses
                    }
                }
                unset($rl['processed_dates']); // Bersihkan
            }
            
            $rekap_list = array_values($rekap_list);
        }

        ob_start();
        include __DIR__ . '/../../resources/views/apk/monitor_realtime_rekap.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function monitorPermapel()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $today = $_GET['tgl'] ?? date('Y-m-d');
        $title = "Absen Mapel Siswa";
        
        // Ambil data absensi permapel hari ini
        $sql = "
            SELECT a.siswa_id, a.status, m.nama_mapel, s.nama, s.kelas_id, k.nama_kelas, k.tingkat, asis.jam_masuk, asis.status as asis_status
            FROM " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_permapel a
            JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s ON a.siswa_id = s.id
            JOIN " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".jurnal_guru j ON a.jurnal_id = j.id
            LEFT JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".mapel m ON j.mapel_id = m.id
            LEFT JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".kelas k ON s.kelas_id = k.id
            LEFT JOIN " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_siswa asis ON asis.siswa_id = s.id AND asis.tanggal = a.tanggal
            WHERE a.tanggal = ? AND s.status = 'Aktif'
            ORDER BY k.tingkat ASC, k.nama_kelas ASC, s.nama ASC, j.id ASC
        ";
        $stmt = $this->siakad_db->prepare($sql);
        $stmt->execute([$today]);
        $raw_data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Organisasi data
        $kelasData = [];
        $studentData = [];
        
        foreach ($raw_data as $r) {
            $k_id = $r['kelas_id'];
            $s_id = $r['siswa_id'];
            
            if (!isset($kelasData[$k_id])) {
                // Ambil total siswa kelas ini
                $tot = $this->core_db->prepare("SELECT COUNT(*) FROM siswa WHERE kelas_id = ? AND status='Aktif'");
                $tot->execute([$k_id]);
                $total_siswa = $tot->fetchColumn();
                
                $kelasData[$k_id] = [
                    'id' => $k_id,
                    'nama' => $r['nama_kelas'],
                    'tingkat' => $r['tingkat'],
                    'total' => $total_siswa,
                    'hadir' => 0,
                    'bolos' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpha' => 0,
                    'students' => [] // Will populate later
                ];
            }
            
            if (!isset($studentData[$k_id][$s_id])) {
                $studentData[$k_id][$s_id] = [
                    'siswa_id' => $s_id,
                    'nama' => $r['nama'],
                    'mapel_dict' => [],
                    'masuk' => empty($r['jam_masuk']) ? '-' : $r['jam_masuk'],
                    'final_status' => empty($r['asis_status']) ? '-' : $r['asis_status']
                ];
            }
            
            // Tambahkan mapel (deduplikasi per hari)
            $mapel_name = $r['nama_mapel'] ?? 'Mapel';
            $st = $r['status'];
            if ($st === 'Bolos' || $st === 'Alpa') $st = 'Alpha';
            
            $studentData[$k_id][$s_id]['mapel_dict'][$mapel_name] = $st;
        }
        
        // Tentukan final status (Status Pusat) untuk masing-masing siswa dan agregasi ke kelas
        foreach ($studentData as $k_id => $students) {
            foreach ($students as $s_id => $s_data) {
                $status_pusat = $s_data['final_status'];
                
                if (!in_array($status_pusat, ['Sakit', 'Izin', 'Alpa', 'Alpha'])) {
                    $masuk = $s_data['masuk'];
                    $statuses = array_values($s_data['mapel_dict']);
                    if ($masuk === '-' && in_array('Hadir', $statuses)) {
                        $status_pusat = 'Bolos';
                    } elseif ($masuk !== '-') {
                        $status_pusat = 'Hadir';
                    }
                }
                
                $studentData[$k_id][$s_id]['final_status'] = $status_pusat;
                
                // Agregasi
                if ($status_pusat == 'Hadir') $kelasData[$k_id]['hadir']++;
                elseif ($status_pusat == 'Bolos') $kelasData[$k_id]['bolos']++;
                elseif ($status_pusat == 'Sakit') $kelasData[$k_id]['sakit']++;
                elseif ($status_pusat == 'Izin') $kelasData[$k_id]['izin']++;
                elseif ($status_pusat == 'Alpha' || $status_pusat == 'Alpa') $kelasData[$k_id]['alpha']++;
                
                // Siapkan list mapels untuk view
                $studentData[$k_id][$s_id]['mapels'] = [];
                foreach ($s_data['mapel_dict'] as $mName => $mStatus) {
                    $studentData[$k_id][$s_id]['mapels'][] = [
                        'mapel' => $mName,
                        'status' => $mStatus
                    ];
                }
                
                $kelasData[$k_id]['students'][] = $studentData[$k_id][$s_id];
            }
        }
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/monitor_permapel.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function monitorPermapelRekap()
    {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }

        $title = "Rekap Absen Mapel";
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        $filter_kelas_id = $_GET['kelas_id'] ?? '';
        
        // Ambil list kelas untuk dropdown
        $kelasList = $this->core_db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        
        $rekap_list = [];
        
        if (!empty($filter_kelas_id)) {
            // Ambil semua absensi mapel untuk kelas terpilih di rentang tanggal
            $sql = "
                SELECT a.siswa_id, a.tanggal, a.status, s.nama, k.nama_kelas, m.nama_mapel, asis.jam_masuk, asis.status as asis_status
                FROM " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_permapel a
                JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".siswa s ON a.siswa_id = s.id
                JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".kelas k ON s.kelas_id = k.id
                JOIN " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".jurnal_guru j ON a.jurnal_id = j.id
                LEFT JOIN " . $this->core_db->query("SELECT database()")->fetchColumn() . ".mapel m ON j.mapel_id = m.id
                LEFT JOIN " . $this->siakad_db->query("SELECT database()")->fetchColumn() . ".absensi_siswa asis ON asis.siswa_id = s.id AND asis.tanggal = a.tanggal
                WHERE a.tanggal >= ? AND a.tanggal <= ? AND s.kelas_id = ? AND s.status = 'Aktif'
                ORDER BY a.tanggal ASC, s.nama ASC, j.id ASC
            ";
            $stmt = $this->siakad_db->prepare($sql);
            $stmt->execute([$start_date, $end_date, $filter_kelas_id]);
            $raw_data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Proses data mentah per hari, per siswa
            // 1. Kumpulkan data per siswa -> per tanggal -> list mapel
            $temp = [];
            foreach ($raw_data as $r) {
                $sid = $r['siswa_id'];
                $tgl = $r['tanggal'];
                
                if (!isset($temp[$sid])) {
                    $temp[$sid] = [
                        'siswa_id' => $sid,
                        'nama' => $r['nama'],
                        'nama_kelas' => $r['nama_kelas'],
                        'dates' => []
                    ];
                }
                
                if (!isset($temp[$sid]['dates'][$tgl])) {
                    $temp[$sid]['dates'][$tgl] = [
                        'mapel_dict' => [],
                        'masuk' => empty($r['jam_masuk']) ? '-' : $r['jam_masuk'],
                        'final_status' => empty($r['asis_status']) ? '-' : $r['asis_status']
                    ];
                }
                
                $st = $r['status'];
                if ($st === 'Bolos' || $st === 'Alpa') $st = 'Alpha';
                
                $temp[$sid]['dates'][$tgl]['mapel_dict'][$r['nama_mapel'] ?? 'Mapel'] = $st;
            }
            
            // 2. Evaluasi status harian dan masukkan ke rekap_list
            foreach ($temp as $sid => $sData) {
                $rekap_list[$sid] = [
                    'siswa_id' => $sid,
                    'nama' => $sData['nama'],
                    'nama_kelas' => $sData['nama_kelas'],
                    'hadir' => 0,
                    'bolos' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpha' => 0,
                    'detail_hadir' => [],
                    'detail_bolos' => [],
                    'detail_sakit' => [],
                    'detail_izin' => [],
                    'detail_alpha' => []
                ];
                
                foreach ($sData['dates'] as $tgl => $dData) {
                    
                    $status_pusat = $dData['final_status'];
                    $masuk = $dData['masuk'];
                    
                    if (!in_array($status_pusat, ['Sakit', 'Izin', 'Alpa', 'Alpha'])) {
                        $statuses = array_values($dData['mapel_dict']);
                        if ($masuk === '-' && in_array('Hadir', $statuses)) {
                            $status_pusat = 'Bolos';
                        } elseif ($masuk !== '-') {
                            $status_pusat = 'Hadir';
                        }
                    }
                    
                    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $day_index = date('w', strtotime($tgl));
                    $hari = $days[$day_index];
                    $tgl_format = $hari . ', ' . date('d/m/Y', strtotime($tgl));
                    
                    $mapels_list = [];
                    foreach ($dData['mapel_dict'] as $mName => $mStatus) {
                        $mapels_list[] = ['mapel' => $mName, 'status' => $mStatus];
                    }
                    
                    $detail_item = [
                        'tanggal' => $tgl_format,
                        'masuk' => $masuk,
                        'mapels' => $mapels_list
                    ];
                    
                    if ($status_pusat == 'Hadir') {
                        $rekap_list[$sid]['hadir']++;
                        $rekap_list[$sid]['detail_hadir'][] = $detail_item;
                    } elseif ($status_pusat == 'Bolos') {
                        $rekap_list[$sid]['bolos']++;
                        $rekap_list[$sid]['detail_bolos'][] = $detail_item;
                    } elseif ($status_pusat == 'Sakit') {
                        $rekap_list[$sid]['sakit']++;
                        $rekap_list[$sid]['detail_sakit'][] = $detail_item;
                    } elseif ($status_pusat == 'Izin') {
                        $rekap_list[$sid]['izin']++;
                        $rekap_list[$sid]['detail_izin'][] = $detail_item;
                    } elseif ($status_pusat == 'Alpha' || $status_pusat == 'Alpa') {
                        $rekap_list[$sid]['alpha']++;
                        $rekap_list[$sid]['detail_alpha'][] = $detail_item;
                    }
                }
            }
            
            // Re-index array
            $rekap_list = array_values($rekap_list);
            
            // Urutkan berdasarkan nama
            usort($rekap_list, function($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
        }
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/monitor_permapel_rekap.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }
    public function kasirKolektif() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }
        
        $custom_apk_access = $this->getDelegatedApkAccess();
        if (!in_array('kasir_bendahara', $custom_apk_access)) {
            Helper::redirect('/apk/aplikasi-saya');
        }

        $title = "Kasir Bendahara - Kolektif";
        $activeNav = "apps";
        $db = Database::connect();
        
        $siswa = [];
        $selected_siswa = null;
        $tagihan_list = [];
        $sisa_tagihan = 0;
        
        $siswa = $db->query("SELECT s.id, s.nis, s.nama, s.kelas_id, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.status = 'Aktif' ORDER BY s.nama ASC")->fetchAll();
        $kelas = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();        
        $siswa_id = isset($_GET['siswa_id']) ? intval($_GET['siswa_id']) : 0;
        if ($siswa_id > 0) {
            $selected_siswa = $db->query("SELECT * FROM siswa WHERE id = $siswa_id")->fetch();
            $tagihans = $db->query("SELECT t.*, (SELECT SUM(jumlah) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as real_terbayar FROM keuangan_tagihan t WHERE t.siswa_id = $siswa_id ORDER BY CASE WHEN t.status = 'Lunas' THEN 1 ELSE 0 END, t.nama_tagihan ASC")->fetchAll();
            
            $belum_lunas_count = 0;
            foreach ($tagihans as &$t) {
                $terbayar = $t['real_terbayar'] ? floatval($t['real_terbayar']) : 0;
                $t['jumlah_terbayar'] = $terbayar; // Update for the view
                if ($t['status'] !== 'Lunas') {
                    $sisa_tagihan += ($t['jumlah_tagihan'] - $terbayar);
                    $belum_lunas_count++;
                }
                $tagihan_list[] = $t;
            }
            unset($t);
        }
        
        $config_list = $db->query("SELECT * FROM keuangan_kolektif_config ORDER BY nilai ASC")->fetchAll();
        $config_map = [];
        foreach ($config_list as $c) {
            $config_map[$c['nama_tagihan']] = $c;
        }
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/kasir_kolektif.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function kasirManual() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }
        
        $custom_apk_access = $this->getDelegatedApkAccess();
        if (!in_array('kasir_bendahara', $custom_apk_access)) {
            Helper::redirect('/apk/aplikasi-saya');
        }

        $title = "Kasir Bendahara - Manual";
        $activeNav = "apps";
        $db = Database::connect();
        
        $siswa = $db->query("SELECT s.id, s.nis, s.nama, s.kelas_id, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.status = 'Aktif' ORDER BY s.nama ASC")->fetchAll();
        $kelas = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        $siswa_id = isset($_GET['siswa_id']) ? intval($_GET['siswa_id']) : 0;
        $selected_siswa = null;
        $tagihan_list = [];
        
        if ($siswa_id > 0) {
            $selected_siswa = $db->query("SELECT * FROM siswa WHERE id = $siswa_id")->fetch();
            $tagihan_list = $db->query("SELECT * FROM keuangan_tagihan WHERE siswa_id = $siswa_id ORDER BY status ASC, id ASC")->fetchAll();
        }
        
        ob_start();
        include __DIR__ . '/../../resources/views/apk/kasir_manual.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    private function getDelegatedApkAccess() {
        if (!isset($_SESSION['guru_id'])) return [];
        $custom_apk_access = [];
        try {
            $stmt = $this->core_db->prepare("SELECT app_code FROM apk_akses_guru WHERE guru_id = ?");
            $stmt->execute([$_SESSION['guru_id']]);
            $custom_apk_access = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {}
        return $custom_apk_access;
    }

    public function bkuBendahara() {
        if (!isset($_SESSION['guru_id'])) {
            Helper::redirect('/apk/login');
        }
        
        $custom_apk_access = $this->getDelegatedApkAccess();
        if (!in_array('bku_bendahara', $custom_apk_access)) {
            Helper::redirect('/apk/aplikasi-saya');
        }

        $title = "BKU Bendahara";
        $activeNav = "apps";
        $db = Database::connect();
        
        // Hitung total Pemasukan & Pengeluaran secara umum
        $totMasuk = (float)$db->query("SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_transaksi WHERE jenis='Pemasukan'")->fetchColumn();
        $totKeluar = (float)$db->query("SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_transaksi WHERE jenis='Pengeluaran'")->fetchColumn();
        $saldo = $totMasuk - $totKeluar;

        // Ambil riwayat terbaru (semua transaksi: manual + pembayaran siswa)
        $riwayat = $db->query("SELECT * FROM keuangan_transaksi ORDER BY created_at DESC LIMIT 30")->fetchAll(\PDO::FETCH_ASSOC);
        
        // Ambil rincian saldo per kategori
        $rincianKategori = [];
        try {
            $rincianKategori = $db->query("
                SELECT kategori, 
                       SUM(CASE WHEN jenis='Pemasukan' THEN jumlah ELSE 0 END) as masuk,
                       SUM(CASE WHEN jenis='Pengeluaran' THEN jumlah ELSE 0 END) as keluar,
                       SUM(CASE WHEN jenis='Pemasukan' THEN jumlah ELSE -jumlah END) as saldo
                FROM keuangan_transaksi 
                WHERE kategori IS NOT NULL AND TRIM(kategori) != ''
                GROUP BY kategori
                ORDER BY kategori ASC
            ")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        ob_start();
        include __DIR__ . '/../../resources/views/apk/bku_bendahara.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function bkuBendaharaSave() {
        if (!isset($_SESSION['guru_id'])) {
            $_SESSION['flash_message'] = "Sesi habis, silakan login ulang.";
            $_SESSION['flash_type'] = "error";
            Helper::redirect('/apk/login');
            return;
        }
        
        $custom_apk_access = $this->getDelegatedApkAccess();
        if (!in_array('bku_bendahara', $custom_apk_access)) {
            Helper::redirect('/apk/aplikasi-saya');
        }

        $db = Database::connect();
        $jenis = $_POST['jenis'] ?? 'Pemasukan';
        $kategori = $_POST['kategori'] ?? 'Lain-lain';
        $keterangan = $_POST['keterangan'] ?? '';
        $jumlah = str_replace(['Rp', '.', ',', ' '], '', $_POST['jumlah'] ?? '0');
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $guru_id = $_SESSION['guru_id'];

        if (empty($keterangan) || $jumlah <= 0) {
            $_SESSION['flash_message'] = "Data tidak valid. Keterangan dan Jumlah harus diisi.";
            $_SESSION['flash_type'] = "error";
            Helper::redirect('/apk/bku');
            return;
        }

        $bukti = null;
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
            $buktiName = 'bku_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/uploads/keuangan/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (move_uploaded_file($_FILES['bukti']['tmp_name'], $uploadDir . $buktiName)) {
                $bukti = $buktiName;
            }
        }

        try {
            $stmt = $db->prepare("INSERT INTO keuangan_transaksi (tanggal_bayar, kategori, keterangan, jenis, jumlah, guru_id, bukti) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal, $kategori, $keterangan, $jenis, $jumlah, $guru_id, $bukti]);
            
            $_SESSION['flash_message'] = "$jenis berhasil disimpan!";
            $_SESSION['flash_type'] = "success";
        } catch (\Exception $e) {
            $_SESSION['flash_message'] = "Gagal menyimpan data: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
        
        Helper::redirect('/apk/bku');
    }

    public function kasirManualSave() {
        if (!isset($_SESSION['guru_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sesi habis, silakan login ulang.']);
            return;
        }
        
        $custom_apk_access = $this->getDelegatedApkAccess();
        if (!in_array('kasir_bendahara', $custom_apk_access)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Anda tidak punya akses Kasir Bendahara.']);
            return;
        }

        $db = Database::connect();
        $siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : 0;
        $nama_tagihan = isset($_POST['nama_tagihan']) ? trim($_POST['nama_tagihan']) : '';
        $nominal = isset($_POST['nominal']) ? floatval($_POST['nominal']) : 0;
        $tanggal_bayar = date('Y-m-d');

        header('Content-Type: application/json');

        if ($siswa_id <= 0 || $nominal <= 0 || empty($nama_tagihan)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
            return;
        }

        // Cek sisa tagihan
        $tagihanQ = $db->prepare("SELECT id, jumlah_tagihan FROM keuangan_tagihan WHERE siswa_id = ? AND nama_tagihan = ?");
        $tagihanQ->execute([$siswa_id, $nama_tagihan]);
        $tCheck = $tagihanQ->fetch();

        if ($tCheck) {
            $paidQ = $db->prepare("SELECT COALESCE(SUM(jumlah), 0) as total FROM keuangan_komite_pembayaran WHERE siswa_id = ? AND jenis_pembayaran = ?");
            $paidQ->execute([$siswa_id, $nama_tagihan]);
            $sudahBayar = floatval($paidQ->fetch()['total']);
            $sisa = floatval($tCheck['jumlah_tagihan']) - $sudahBayar;
            if ($nominal > $sisa) {
                echo json_encode(['success' => false, 'message' => 'Nominal melebihi sisa tagihan (Rp ' . number_format($sisa, 0, ',', '.') . '). Ditolak.']);
                return;
            }
        }

        // Resolve petugas_id
        $petugas_id = null;
        try {
            $petugas_id = $db->query("SELECT user_id FROM guru WHERE id = " . intval($_SESSION['guru_id']))->fetchColumn();
        } catch (\Exception $e) {}

        // Simpan pembayaran
        $stmt = $db->prepare("INSERT INTO keuangan_komite_pembayaran (siswa_id, jenis_pembayaran, periode, jumlah, tanggal_bayar, petugas_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$siswa_id, $nama_tagihan, '-', $nominal, $tanggal_bayar, $petugas_id]);

        // Update status tagihan
        if ($tCheck) {
            $paid = $db->prepare("SELECT SUM(jumlah) as total FROM keuangan_komite_pembayaran WHERE siswa_id = ? AND jenis_pembayaran = ?");
            $paid->execute([$siswa_id, $nama_tagihan]);
            $totalBayar = floatval($paid->fetch()['total']);
            $statusBaru = ($totalBayar >= floatval($tCheck['jumlah_tagihan'])) ? 'Lunas' : 'Mengangsur';
            $db->prepare("UPDATE keuangan_tagihan SET jumlah_terbayar = ?, status = ? WHERE id = ?")->execute([$totalBayar, $statusBaru, $tCheck['id']]);
        }

        // Insert transaksi
        $stmt2 = $db->prepare("INSERT INTO keuangan_transaksi (tanggal_bayar, keterangan, jenis, jumlah, siswa_id, guru_id) VALUES (?, ?, 'Pemasukan', ?, ?, ?)");
        $stmt2->execute([$tanggal_bayar, "Pembayaran Kas ($nama_tagihan): APK Kasir", $nominal, $siswa_id, $petugas_id]);

        // Kirim Notifikasi
        try {
            require_once __DIR__ . '/../Services/OneSignalService.php';
            $rp = 'Rp ' . number_format($nominal, 0, ',', '.');
            \App\Services\OneSignalService::sendNotification("Pembayaran Berhasil", "Pembayaran $nama_tagihan sebesar $rp telah diterima. Status: " . ($statusBaru ?? '-'), null, $siswa_id);
        } catch (\Exception $e) {}

        echo json_encode(['success' => true, 'message' => 'Pembayaran ' . $nama_tagihan . ' sebesar Rp ' . number_format($nominal, 0, ',', '.') . ' berhasil!']);
    }

    public function kasirKolektifSave() {
        if (!isset($_SESSION['guru_id'])) {
            $_SESSION['flash_message'] = 'Sesi habis, silakan login ulang.';
            $_SESSION['flash_type'] = 'error';
            header('Location: /apk/login');
            exit;
        }

        $custom_apk_access = $this->getDelegatedApkAccess();
        if (!in_array('kasir_bendahara', $custom_apk_access)) {
            $_SESSION['flash_message'] = 'Anda tidak punya akses Kasir Bendahara.';
            $_SESSION['flash_type'] = 'error';
            header('Location: /apk/aplikasi-saya');
            exit;
        }

        // Set session vars for AdminFinanceController guard compatibility
        if (!isset($_SESSION['role_id'])) $_SESSION['role_id'] = 2;
        if (!isset($_SESSION['user_id'])) {
            $uid = $this->core_db->query("SELECT user_id FROM guru WHERE id = " . intval($_SESSION['guru_id']))->fetchColumn();
            if ($uid) $_SESSION['user_id'] = $uid;
        }

        // Validate and clean nominal
        $nominal = (float)str_replace(['Rp', '.', ',', ' '], '', $_POST['nominal'] ?? '0');
        $_POST['nominal'] = $nominal;
        
        $siswa_id = $_POST['siswa_id'] ?? 0;
        $db = \App\Core\Database::connect('siakad');
        $sisa = $db->query("SELECT COALESCE(SUM(jumlah_tagihan - jumlah_terbayar), 0) FROM keuangan_tagihan WHERE siswa_id = " . intval($siswa_id) . " AND status != 'Lunas'")->fetchColumn();

        if ($nominal > $sisa) {
            $_SESSION['flash_message'] = "Nominal melebihi sisa tagihan! (Maks. Rp " . number_format($sisa, 0, ',', '.') . ")";
            $_SESSION['flash_type'] = 'error';
            header("Location: /apk/kasir/kolektif?siswa_id=$siswa_id");
            exit;
        }

        // Forward ke savePembayaranKolektif dengan is_apk flag
        $_POST['is_apk'] = 1;
        \App\Controllers\AdminFinanceController::savePembayaranKolektif();
    }

    public function waliKelasCatatan() {
        if (!isset($_SESSION['guru_id'])) {
            \App\Core\Helper::redirect('/apk/login');
        }

        $guru_id = $_SESSION['guru_id'];
        $ta = \App\Core\AcademicYear::current();
        $ta_id = $ta ? $ta['id'] : 0;
        $semester = $ta ? $ta['semester'] : 'Ganjil';

        // Cari tahu guru ini wali kelas berapa
        $active_year_name = $ta ? $ta['name'] : '2025/2026';
        $stmt_tugas = $this->core_db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru_id, $active_year_name]);
        $keterangan = $stmt_tugas->fetchColumn();

        if (!$keterangan) {
            // Bukan wali kelas, atau tidak diset
            \App\Core\Helper::redirect('/apk/aplikasi-saya');
        }

        $nama_kelas_raw = trim(str_ireplace('Kelas ', '', $keterangan));
        $stmt_k = $this->siakad_db->prepare("SELECT id, tingkat, nama_kelas FROM kelas WHERE nama_kelas = ?");
        $stmt_k->execute([$nama_kelas_raw]);
        $kelas = $stmt_k->fetch(\PDO::FETCH_ASSOC);

        if (!$kelas) {
            \App\Core\Helper::redirect('/apk/aplikasi-saya');
        }

        $kelas_id = $kelas['id'];
        $nama_kelas = $kelas['nama_kelas'];

        // Ambil daftar siswa
        $stmt_siswa = $this->siakad_db->prepare("
            SELECT id, nis, nama 
            FROM siswa 
            WHERE kelas_id = ? 
            ORDER BY nama ASC
        ");
        $stmt_siswa->execute([$kelas_id]);
        $siswaList = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);

        // Ambil riwayat catatan terbaru
        $stmt_riwayat = $this->siakad_db->prepare("
            SELECT c.*, s.nama as nama_siswa 
            FROM catatan_wali_kelas c
            JOIN siswa s ON c.siswa_id = s.id
            WHERE c.kelas_id = ? AND c.tahun_ajaran_id = ? AND c.semester = ?
            ORDER BY c.tanggal DESC, c.id DESC
        ");
        $stmt_riwayat->execute([$kelas_id, $ta_id, $semester]);
        $riwayatList = $stmt_riwayat->fetchAll(\PDO::FETCH_ASSOC);

        $title = "Catatan Wali Kelas";
        $activeNav = "aplikasi";

        $institusi = \App\Core\Branding::getInstitusi();
        $logoSrc = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png');
        $faviconSrc = !empty($institusi['favicon']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;

        ob_start();
        include __DIR__ . '/../../resources/views/apk/wali_catatan.php';
        $content = ob_get_clean();
        
        $hideNav = true;
        include __DIR__ . '/../../resources/views/apk/layout.php';
    }

    public function waliKelasCatatanSave() {
        if (!isset($_SESSION['guru_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $guru_id = $_SESSION['guru_id'];
        $siswa_id = intval($_POST['siswa_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $jenis = $_POST['jenis'] ?? 'Info';
        $catatan = trim($_POST['catatan'] ?? '');
        
        if (!$siswa_id || empty($catatan)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            return;
        }

        $ta = \App\Core\AcademicYear::current();
        $ta_id = $ta ? $ta['id'] : 0;
        $semester = $ta ? $ta['semester'] : 'Ganjil';

        // Cari tahu guru ini wali kelas berapa
        $active_year_name = $ta ? $ta['name'] : '2025/2026';
        $stmt_tugas = $this->core_db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru_id, $active_year_name]);
        $keterangan = $stmt_tugas->fetchColumn();

        if (!$keterangan) {
            echo json_encode(['success' => false, 'message' => 'Anda bukan wali kelas.']);
            return;
        }

        $nama_kelas_raw = trim(str_ireplace('Kelas ', '', $keterangan));
        $stmt_k = $this->siakad_db->prepare("SELECT id FROM kelas WHERE nama_kelas = ?");
        $stmt_k->execute([$nama_kelas_raw]);
        $kelas_id_wali = $stmt_k->fetchColumn();

        // Pastikan siswa ini ada di kelas tersebut
        $stmt_cek = $this->siakad_db->prepare("SELECT id FROM siswa WHERE id = ? AND kelas_id = ?");
        $stmt_cek->execute([$siswa_id, $kelas_id_wali]);
        $valid = $stmt_cek->fetchColumn();

        if (!$valid) {
            echo json_encode(['success' => false, 'message' => 'Siswa ini bukan berada di kelas Anda.']);
            return;
        }
        
        $kelas_id = $kelas_id_wali;

        // Insert catatan baru
        $stmt_insert = $this->siakad_db->prepare("INSERT INTO catatan_wali_kelas (siswa_id, kelas_id, tahun_ajaran_id, semester, tanggal, jenis, catatan) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->execute([$siswa_id, $kelas_id, $ta_id, $semester, $tanggal, $jenis, $catatan]);

        echo json_encode(['success' => true, 'message' => 'Catatan berhasil ditambahkan!']);
    }
}
