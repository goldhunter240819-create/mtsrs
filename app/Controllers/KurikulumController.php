<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Branding;
use App\Core\AcademicYear;

class KurikulumController {
    
    // Pastikan user login dan memiliki akses ke modul kurikulum
    private static function checkAccess() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        return true;
    }
    
    public static function dashboard() {
        self::checkAccess();
        
        $title = "Dashboard Kurikulum";
        $activeMenu = "kurikulum_dashboard";
        
        $db = Database::connect();
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        
        // Stats
        $stmtGuru = $db->query("SELECT COUNT(*) FROM guru");
        $statTotalGuru = $stmtGuru->fetchColumn();
        
        $statAdminLengkap = 0;
        $statSupervisiKelas = 0;
        $recentSupervisi = [];
        
        if ($tahun_ajaran_id) {
            $stmtAdmin = $db->prepare("SELECT COUNT(DISTINCT guru_id) FROM kurikulum_supervisi_administrasi WHERE tahun_ajaran_id = ?");
            $stmtAdmin->execute([$tahun_ajaran_id]);
            $statAdminLengkap = $stmtAdmin->fetchColumn();
            
            $stmtKelas = $db->prepare("SELECT COUNT(DISTINCT guru_id) FROM kurikulum_supervisi_kelas WHERE tahun_ajaran_id = ?");
            $stmtKelas->execute([$tahun_ajaran_id]);
            $statSupervisiKelas = $stmtKelas->fetchColumn();
            
            // Get recent supervisi kelas
            $stmtRecent = $db->prepare("
                SELECT ksk.tanggal_supervisi, ksk.mata_pelajaran, ksk.kelas, g.nama as nama_guru
                FROM kurikulum_supervisi_kelas ksk
                JOIN guru g ON ksk.guru_id = g.id
                WHERE ksk.tahun_ajaran_id = ?
                ORDER BY ksk.created_at DESC
                LIMIT 5
            ");
            $stmtRecent->execute([$tahun_ajaran_id]);
            $recentSupervisi = $stmtRecent->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/dashboard.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }
    
    public static function supervisiAdministrasi() {
        self::checkAccess();
        $db = Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        
        // Ambil daftar guru dan status supervisi mereka tahun ini
        $guru_list = [];
        if ($tahun_ajaran_id) {
            $stmt = $db->prepare("
                SELECT g.id, g.nama, g.nip,
                       s.id as supervisi_id, s.tanggal_supervisi, s.nilai_akhir
                FROM guru g
                LEFT JOIN kurikulum_supervisi_administrasi s 
                  ON g.id = s.guru_id AND s.tahun_ajaran_id = ?
                ORDER BY g.nama ASC
            ");
            $stmt->execute([$tahun_ajaran_id]);
            $guru_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        $title = "Supervisi Administrasi";
        $activeMenu = "kurikulum_supervisi_administrasi";
        
        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/supervisi_administrasi.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function apiSupervisiAdministrasiDetail() {
        self::checkAccess();
        $db = Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        $guru_id = $_GET['guru_id'] ?? null;
        
        if (!$tahun_ajaran_id || !$guru_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM kurikulum_supervisi_administrasi WHERE tahun_ajaran_id = ? AND guru_id = ?");
        $stmt->execute([$tahun_ajaran_id, $guru_id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($data) {
            $data['instrumen'] = json_decode($data['instrumen_json'], true);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    public static function supervisiAdministrasiEvaluasi() {
        self::checkAccess();
        $db = Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        $guru_id = $_GET['guru_id'] ?? null;
        
        if (!$guru_id) {
            header("Location: " . \App\Core\Helper::url('/kurikulum/supervisi-administrasi'));
            exit;
        }

        // Get guru data
        $stmtGuru = $db->prepare("SELECT * FROM guru WHERE id = ?");
        $stmtGuru->execute([$guru_id]);
        $guru = $stmtGuru->fetch(\PDO::FETCH_ASSOC);

        if (!$guru) {
            header("Location: " . \App\Core\Helper::url('/kurikulum/supervisi-administrasi'));
            exit;
        }

        // Get existing supervisi data
        $supervisi = null;
        if ($tahun_ajaran_id) {
            $stmtSup = $db->prepare("SELECT * FROM kurikulum_supervisi_administrasi WHERE tahun_ajaran_id = ? AND guru_id = ?");
            $stmtSup->execute([$tahun_ajaran_id, $guru_id]);
            $supervisi = $stmtSup->fetch(\PDO::FETCH_ASSOC);
            if ($supervisi) {
                $supervisi['instrumen'] = json_decode($supervisi['instrumen_json'], true);
            }
        }

        // Ambil data perangkat pembelajaran guru (yang sudah diupload)
        $berkas_guru = [];
        if ($tahun_ajaran_id) {
            $stmtBerkas = $db->prepare("SELECT * FROM perangkat_pembelajaran WHERE tahun_ajaran_id = ? AND guru_id = ? ORDER BY tanggal_upload DESC");
            $stmtBerkas->execute([$tahun_ajaran_id, $guru_id]);
            $berkasData = $stmtBerkas->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($berkasData as $b) {
                $jenis = $b['jenis_berkas'];
                if (!isset($berkas_guru[$jenis])) {
                    $berkas_guru[$jenis] = [];
                }
                $berkas_guru[$jenis][] = $b;
            }
        }

        $title = "Form Supervisi Administrasi";
        $activeMenu = "kurikulum_supervisi_administrasi";
        
        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/supervisi_administrasi_evaluasi.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function supervisiAdministrasiSave() {
        self::checkAccess();
        $db = Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
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
        
        // Kalkulasi nilai kasar
        $total_items = 8;
        $score = 0;
        foreach ($instrumen as $val) {
            if ($val === 'Ada') $score += 2;
            elseif ($val === 'Tidak Lengkap') $score += 1;
        }
        $nilai_akhir = round(($score / ($total_items * 2)) * 100);

        $instrumen_json = json_encode($instrumen);

        if (!$tahun_ajaran_id || !$guru_id) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit;
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO kurikulum_supervisi_administrasi 
                (tahun_ajaran_id, guru_id, tanggal_supervisi, instrumen_json, nilai_akhir, catatan, tindak_lanjut) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                tanggal_supervisi = VALUES(tanggal_supervisi),
                instrumen_json = VALUES(instrumen_json),
                nilai_akhir = VALUES(nilai_akhir),
                catatan = VALUES(catatan),
                tindak_lanjut = VALUES(tindak_lanjut)
            ");
            $stmt->execute([$tahun_ajaran_id, $guru_id, $tanggal_supervisi, $instrumen_json, $nilai_akhir, $catatan, $tindak_lanjut]);
            
            echo json_encode(['success' => true, 'message' => 'Data supervisi berhasil disimpan']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public static function supervisiKelas() {
        self::checkAccess();
        $db = Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        
        // Ambil daftar guru dan status supervisi mereka tahun ini
        $guru_list = [];
        if ($tahun_ajaran_id) {
            $stmt = $db->prepare("
                SELECT g.id, g.nama, g.nip,
                       s.id as supervisi_id, s.tanggal_supervisi
                FROM guru g
                LEFT JOIN kurikulum_supervisi_kelas s 
                  ON g.id = s.guru_id AND s.tahun_ajaran_id = ?
                ORDER BY g.nama ASC
            ");
            $stmt->execute([$tahun_ajaran_id]);
            $guru_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        $title = "Supervisi Pelaksanaan Pembelajaran";
        $activeMenu = "kurikulum_supervisi_kelas";
        
        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/supervisi_kelas.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function supervisiKelasEvaluasi() {
        self::checkAccess();
        $db = Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        $guru_id = $_GET['guru_id'] ?? null;
        
        if (!$guru_id) {
            header("Location: " . \App\Core\Helper::url('/kurikulum/supervisi-kelas'));
            exit;
        }

        // Get guru data
        $stmtGuru = $db->prepare("SELECT * FROM guru WHERE id = ?");
        $stmtGuru->execute([$guru_id]);
        $guru = $stmtGuru->fetch(\PDO::FETCH_ASSOC);

        if (!$guru) {
            header("Location: " . \App\Core\Helper::url('/kurikulum/supervisi-kelas'));
            exit;
        }

        // Get existing supervisi data
        $supervisi = null;
        if ($tahun_ajaran_id) {
            $stmtSup = $db->prepare("SELECT * FROM kurikulum_supervisi_kelas WHERE tahun_ajaran_id = ? AND guru_id = ?");
            $stmtSup->execute([$tahun_ajaran_id, $guru_id]);
            $supervisi = $stmtSup->fetch(\PDO::FETCH_ASSOC);
            if ($supervisi) {
                $supervisi['instrumen'] = json_decode($supervisi['instrumen_json'], true);
            }
        }

        $title = "Instrumen Supervisi Kelas";
        $activeMenu = "kurikulum_supervisi_kelas";
        
        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/supervisi_kelas_evaluasi.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function supervisiKelasSave() {
        self::checkAccess();
        $db = Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
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

        if (!$tahun_ajaran_id || !$guru_id) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit;
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO kurikulum_supervisi_kelas 
                (tahun_ajaran_id, guru_id, tanggal_supervisi, mata_pelajaran, kelas, jam_ke, materi_pokok, instrumen_json, catatan, tindak_lanjut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                tanggal_supervisi = VALUES(tanggal_supervisi),
                mata_pelajaran = VALUES(mata_pelajaran),
                kelas = VALUES(kelas),
                jam_ke = VALUES(jam_ke),
                materi_pokok = VALUES(materi_pokok),
                instrumen_json = VALUES(instrumen_json),
                catatan = VALUES(catatan),
                tindak_lanjut = VALUES(tindak_lanjut)
            ");
            $stmt->execute([$tahun_ajaran_id, $guru_id, $tanggal_supervisi, $mata_pelajaran, $kelas, $jam_ke, $materi_pokok, $instrumen_json, $catatan, $tindak_lanjut]);
            
            echo json_encode(['success' => true, 'message' => 'Data supervisi kelas berhasil disimpan']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public static function monitoringJurnal() {
        self::checkAccess();
        
        $db = Database::connect();
        
        $filter_tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-d');
        $filter_tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');
        $filter_kelas = $_GET['kelas_id'] ?? '';
        $filter_guru = $_GET['guru_id'] ?? '';
        $filter_mapel = $_GET['mapel_id'] ?? '';
        
        $where = ["j.tanggal >= '$filter_tanggal_mulai' AND j.tanggal <= '$filter_tanggal_akhir'"];
        if (!empty($filter_kelas)) {
            $where[] = "j.kelas_id = " . intval($filter_kelas);
        }
        if (!empty($filter_guru)) {
            $where[] = "j.guru_id = " . intval($filter_guru);
        }
        if (!empty($filter_mapel)) {
            $where[] = "j.mapel_id = " . intval($filter_mapel);
        }
        $whereClause = implode(" AND ", $where);

        $jurnalList = $db->query("SELECT j.*, g.nama as nama_guru, m.nama_mapel, k.nama_kelas 
            FROM jurnal_guru j 
            JOIN guru g ON j.guru_id = g.id 
            JOIN mapel m ON j.mapel_id = m.id 
            JOIN kelas k ON j.kelas_id = k.id 
            WHERE $whereClause
            ORDER BY j.tanggal DESC, j.id DESC")->fetchAll();
            
        $kelasList = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        $guruList = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll();
        $mapelList = $db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        // Fetch attendance stats for each journal
        foreach ($jurnalList as &$r) {
            $jurnal_id = $r['id'];
            
            // Hitung status absen
            $stmt_absen = $db->prepare("SELECT status, COUNT(*) as jml FROM absensi_permapel WHERE jurnal_id = ? GROUP BY status");
            $stmt_absen->execute([$jurnal_id]);
            $counts = $stmt_absen->fetchAll(\PDO::FETCH_KEY_PAIR);
            
            $r['hadir'] = $counts['Hadir'] ?? 0;
            $r['sakit'] = $counts['Sakit'] ?? 0;
            $r['izin']  = $counts['Izin'] ?? 0;
            $r['alpha'] = ($counts['Alpha'] ?? 0) + ($counts['Alpa'] ?? 0) + ($counts['Bolos'] ?? 0);
            
            // Ambil daftar siswa yang tidak hadir
            $stmt_absent = $db->prepare("
                SELECT ap.status, ap.siswa_id, s.nama 
                FROM absensi_permapel ap
                JOIN siswa s ON ap.siswa_id = s.id
                WHERE ap.jurnal_id = ? AND ap.status != 'Hadir'
            ");
            $stmt_absent->execute([$jurnal_id]);
            $r['absent_list'] = $stmt_absent->fetchAll(\PDO::FETCH_ASSOC);
        }
        unset($r);

        $title = "Monitoring Jurnal Kelas";
        $activeMenu = "kurikulum_monitoring_jurnal";
        
        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/monitoring_jurnal.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }
    
    public static function supervisiPenilaian() {
        self::checkAccess();
        
        $db = Database::connect();
        $institusi = \App\Core\Branding::getInstitusi();
        
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        $semester = $institusi['semester'] ?? 'Ganjil';
        
        $kelas_id = $_GET['kelas_id'] ?? null;
        $guru_id = $_GET['guru_id'] ?? null;

        $kelas_list = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $guru_list = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

        $monitoring_data = [];

        if ($kelas_id || $guru_id) {
            $query = "SELECT DISTINCT j.mapel_id, j.guru_id, j.kelas_id, m.nama_mapel, g.nama as nama_guru, k.nama_kelas 
                      FROM jadwal_pelajaran j 
                      JOIN mapel m ON m.id = j.mapel_id 
                      JOIN kelas k ON k.id = j.kelas_id
                      LEFT JOIN guru g ON g.id = j.guru_id WHERE 1=1 ";
            
            $params = [];
            if ($kelas_id) {
                $query .= " AND j.kelas_id = ?";
                $params[] = $kelas_id;
            }
            if ($guru_id) {
                $query .= " AND j.guru_id = ?";
                $params[] = $guru_id;
            }
            $query .= " ORDER BY k.tingkat ASC, k.nama_kelas ASC, m.nama_mapel ASC";

            $stmt_jadwal = $db->prepare($query);
            $stmt_jadwal->execute($params);
            $jadwals = $stmt_jadwal->fetchAll(\PDO::FETCH_ASSOC);

            // Fetch total siswa for all relevant classes
            $kelas_totals = [];
            $stmt_siswa = $db->prepare("SELECT kelas_id, COUNT(*) as total FROM siswa WHERE status = 'Aktif' GROUP BY kelas_id");
            $stmt_siswa->execute();
            foreach ($stmt_siswa->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $kelas_totals[$row['kelas_id']] = $row['total'];
            }

            // Get all nilai_harian for relevant TA, semester
            $stmt_nilai = $db->prepare("SELECT mapel_id, kelas_id, jenis_evaluasi, COUNT(DISTINCT siswa_id) as total_dinilai, MAX(materi) as materi
                FROM nilai_harian 
                WHERE tahun_ajaran_id = ? AND semester = ? AND nilai IS NOT NULL
                GROUP BY mapel_id, kelas_id, jenis_evaluasi");
            $stmt_nilai->execute([$tahun_ajaran_id, $semester]);
            $nilai_raw = $stmt_nilai->fetchAll(\PDO::FETCH_ASSOC);

            $nilai_grouped = [];
            foreach ($nilai_raw as $nr) {
                $nilai_grouped[$nr['kelas_id']][$nr['mapel_id']][$nr['jenis_evaluasi']] = [
                    'total_dinilai' => $nr['total_dinilai'],
                    'materi' => $nr['materi']
                ];
            }
            
            // Get supervision data
            $stmt_supervisi = $db->prepare("SELECT * FROM kurikulum_supervisi_nilai WHERE tahun_ajaran_id = ? AND semester = ?");
            $stmt_supervisi->execute([$tahun_ajaran_id, $semester]);
            $supervisi_raw = $stmt_supervisi->fetchAll(\PDO::FETCH_ASSOC);
            
            $supervisi_grouped = [];
            foreach ($supervisi_raw as $sr) {
                $supervisi_grouped[$sr['kelas_id']][$sr['mapel_id']][$sr['jenis_evaluasi']] = $sr;
            }

            foreach ($jadwals as $j) {
                $k_id = $j['kelas_id'];
                $m_id = $j['mapel_id'];
                $total_siswa = $kelas_totals[$k_id] ?? 0;
                
                $evaluasi_list = [];
                if (isset($nilai_grouped[$k_id][$m_id])) {
                    foreach ($nilai_grouped[$k_id][$m_id] as $jenis => $data) {
                        $sup = $supervisi_grouped[$k_id][$m_id][$jenis] ?? null;
                        
                        $evaluasi_list[] = [
                            'jenis' => $jenis,
                            'total_dinilai' => $data['total_dinilai'],
                            'materi' => $data['materi'],
                            'supervisi' => $sup
                        ];
                    }
                }
                
                $monitoring_data[] = [
                    'kelas_id' => $k_id,
                    'mapel_id' => $m_id,
                    'guru_id' => $j['guru_id'],
                    'kelas' => $j['nama_kelas'],
                    'mapel' => $j['nama_mapel'],
                    'guru' => $j['nama_guru'],
                    'evaluasi' => $evaluasi_list,
                    'total_siswa' => $total_siswa
                ];
            }
        }

        $title = "Supervisi Penilaian";
        $activeMenu = "kurikulum_supervisi_penilaian";
        
        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/supervisi_penilaian.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function supervisiPenilaianSave() {
        self::checkAccess();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $db = Database::connect();
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        $semester = $institusi['semester'] ?? 'Ganjil';
        
        $guru_id = $_POST['guru_id'] ?? 0;
        $kelas_id = $_POST['kelas_id'] ?? 0;
        $mapel_id = $_POST['mapel_id'] ?? 0;
        $jenis_evaluasi = $_POST['jenis_evaluasi'] ?? '';
        
        $ada_kisi = isset($_POST['ada_kisi']) ? 1 : 0;
        $ada_analisis = isset($_POST['ada_analisis']) ? 1 : 0;
        $ada_remedial = isset($_POST['ada_remedial']) ? 1 : 0;
        $catatan = $_POST['catatan'] ?? '';
        $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
        $user_id = $_SESSION['user_id'] ?? 0;

        try {
            $stmt = $db->prepare("
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
                $ada_kisi, $ada_analisis, $ada_remedial, $catatan, $tindak_lanjut, $user_id
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Data supervisi berhasil disimpan']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public static function jadwalPelajaran() {
        self::checkAccess();
        $db = \App\Core\Database::connect();
        $activeYear = \App\Core\AcademicYear::current();

        $kelas = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        $kelasList = $kelas;
        $mapelList = $db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();
        $guruList = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll();

        // Ambil semua jadwal tahun ajaran aktif
        $stmt_jadwal = $db->prepare("
            SELECT j.*, m.nama_mapel, g.nama as nama_guru 
            FROM jadwal_pelajaran j
            JOIN mapel m ON j.mapel_id = m.id
            JOIN guru g ON j.guru_id = g.id
            WHERE j.tahun_ajaran_id = ? 
            ORDER BY j.jam_mulai ASC
        ");
        $stmt_jadwal->execute([$activeYear['id']]);
        $jadwal_raw = $stmt_jadwal->fetchAll();

        // Susun jadwal: $jadwal_master[hari][jam_mulai][kelas_id] = [data...]
        $jadwal_master = [];
        $jam_list = [];
        $hari_list = ['Sabtu', 'Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis'];
        
        foreach ($jadwal_raw as $j) {
            $h = $j['hari'];
            if ($h == "Jum'at" || $h == 'Jum`at') $h = 'Jumat'; // Normalize
            $jam = substr($j['jam_mulai'], 0, 5) . ' - ' . substr($j['jam_selesai'], 0, 5);
            $jadwal_master[$h][$jam][$j['kelas_id']] = $j;
            
            if (!in_array($jam, $jam_list)) {
                $jam_list[] = $jam;
            }
        }
        
        // Sort jam_list
        sort($jam_list);

        $title = "Monitoring Jadwal Pelajaran";
        $activeMenu = 'kurikulum_jadwal';

        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/jadwal.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function monitoringKkm() {
        self::checkAccess();
        $db = \App\Core\Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        
        if (!$tahun_ajaran_id) {
            echo "Tahun Ajaran aktif belum diatur di menu Institusi.";
            return;
        }

        // Get all mapel
        $stmt_mapel = $db->query("SELECT * FROM mapel ORDER BY kelompok ASC, nama_mapel ASC");
        $mapels = $stmt_mapel->fetchAll(\PDO::FETCH_ASSOC);

        // Get KKM data
        $stmt_kkm = $db->prepare("SELECT mapel_id, tingkat, nilai_kkm FROM kurikulum_kkm WHERE tahun_ajaran_id = ?");
        $stmt_kkm->execute([$tahun_ajaran_id]);
        $kkm_raw = $stmt_kkm->fetchAll(\PDO::FETCH_ASSOC);

        $kkm_data = [];
        foreach ($kkm_raw as $kr) {
            $kkm_data[$kr['mapel_id']][$kr['tingkat']] = $kr['nilai_kkm'];
        }

        $title = "Monitoring KKM - Waka Kurikulum";
        $activeMenu = 'kurikulum_kkm';
        
        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/monitoring_kkm.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function monitoringKkmSave() {
        self::checkAccess();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $db = \App\Core\Database::connect();
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;

        if (!$tahun_ajaran_id) {
            echo json_encode(['success' => false, 'message' => 'Tahun ajaran tidak valid']);
            return;
        }

        $kkm_input = $_POST['kkm'] ?? [];
        $bobot_harian = isset($_POST['bobot_harian']) ? intval($_POST['bobot_harian']) : 50;
        $bobot_pas = isset($_POST['bobot_pas']) ? intval($_POST['bobot_pas']) : 50;
        
        try {
            $db->beginTransaction();

            $stmt_bobot = $db->prepare("UPDATE institusi SET bobot_harian = ?, bobot_pas = ?");
            $stmt_bobot->execute([$bobot_harian, $bobot_pas]);
            
            $stmt = $db->prepare("
                INSERT INTO kurikulum_kkm (tahun_ajaran_id, mapel_id, tingkat, nilai_kkm) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE nilai_kkm = VALUES(nilai_kkm)
            ");
            
            foreach ($kkm_input as $mapel_id => $tingkat_data) {
                foreach ($tingkat_data as $tingkat => $nilai_kkm) {
                    if ($nilai_kkm !== '' && is_numeric($nilai_kkm)) {
                        $stmt->execute([$tahun_ajaran_id, $mapel_id, $tingkat, $nilai_kkm]);
                    }
                }
            }
            
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Data KKM berhasil disimpan']);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan KKM: ' . $e->getMessage()]);
        }
        exit;
    }

    public static function rekapKetuntasan() {
        self::checkAccess();
        $db = \App\Core\Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        
        if (!$tahun_ajaran_id) {
            echo "Tahun Ajaran aktif belum diatur di menu Institusi.";
            return;
        }

        $kelas_list = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $mapel_list = $db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll(\PDO::FETCH_ASSOC);
        
        $kelas_id = $_GET['kelas_id'] ?? null;
        $mapel_id = $_GET['mapel_id'] ?? null;
        $guru_id = $_GET['guru_id'] ?? null;
        $semester = $_GET['semester'] ?? 'Ganjil';
        $jenis_evaluasi = $_GET['jenis_evaluasi'] ?? null;

        $guru_list = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

        $nilai_data = [];
        $kkm_nilai = null; // Default to null if not found
        $summary = ['total' => 0, 'tuntas' => 0, 'remedial' => 0];

        if ($kelas_id && $mapel_id && $jenis_evaluasi) {
            // Get tingkat of the class to find KKM
            $stmt_kelas = $db->prepare("SELECT tingkat FROM kelas WHERE id = ?");
            $stmt_kelas->execute([$kelas_id]);
            $tingkat = $stmt_kelas->fetchColumn();

            // Get KKM for this mapel and tingkat
            $stmt_kkm = $db->prepare("SELECT nilai_kkm FROM kurikulum_kkm WHERE tahun_ajaran_id = ? AND mapel_id = ? AND tingkat = ?");
            $stmt_kkm->execute([$tahun_ajaran_id, $mapel_id, $tingkat]);
            $kkm_res = $stmt_kkm->fetchColumn();
            if ($kkm_res !== false) {
                $kkm_nilai = $kkm_res;
            }

            // Get list of all students in this class
            $stmt_siswa = $db->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? ORDER BY nama ASC");
            $stmt_siswa->execute([$kelas_id]);
            $siswa_list = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);

            // Get their grades
            $stmt_nilai = $db->prepare("
                SELECT siswa_id, nilai 
                FROM nilai_harian 
                WHERE tahun_ajaran_id = ? AND kelas_id = ? AND mapel_id = ? AND semester = ? AND jenis_evaluasi = ?
            ");
            $stmt_nilai->execute([$tahun_ajaran_id, $kelas_id, $mapel_id, $semester, $jenis_evaluasi]);
            $nilai_raw = $stmt_nilai->fetchAll(\PDO::FETCH_ASSOC);

            $nilai_map = [];
            foreach ($nilai_raw as $nr) {
                $nilai_map[$nr['siswa_id']] = $nr['nilai'];
            }

            foreach ($siswa_list as $s) {
                $nilai_siswa = $nilai_map[$s['id']] ?? null;
                $is_tuntas = false;
                
                if ($nilai_siswa !== null) {
                    $summary['total']++;
                    if ($kkm_nilai !== null) {
                        if ($nilai_siswa >= $kkm_nilai) {
                            $is_tuntas = true;
                            $summary['tuntas']++;
                        } else {
                            $summary['remedial']++;
                        }
                    }
                }

                $nilai_data[] = [
                    'nis' => $s['nis'],
                    'nama' => $s['nama'],
                    'nilai' => $nilai_siswa,
                    'is_tuntas' => $kkm_nilai !== null ? $is_tuntas : null
                ];
            }
        }

        $title = "Rekap Ketuntasan Nilai - Waka Kurikulum";
        $activeMenu = 'kurikulum_rekap_ketuntasan';
        
        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/rekap_ketuntasan.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public static function apiGuruMengajar() {
        self::checkAccess();
        $db = \App\Core\Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        
        $guru_id = $_GET['guru_id'] ?? null;
        
        if (!$guru_id) {
            echo json_encode(['kelas' => [], 'mapel' => []]);
            return;
        }

        // Ambil kelas yang diampu
        $stmt_kelas = $db->prepare("
            SELECT DISTINCT k.id, k.nama_kelas 
            FROM jadwal_pelajaran j 
            JOIN kelas k ON j.kelas_id = k.id 
            WHERE j.guru_id = ? AND j.tahun_ajaran_id = ?
            ORDER BY k.tingkat ASC, k.nama_kelas ASC
        ");
        $stmt_kelas->execute([$guru_id, $tahun_ajaran_id]);
        $kelas = $stmt_kelas->fetchAll(\PDO::FETCH_ASSOC);

        // Ambil mapel yang diampu
        $stmt_mapel = $db->prepare("
            SELECT DISTINCT m.id, m.nama_mapel 
            FROM jadwal_pelajaran j 
            JOIN mapel m ON j.mapel_id = m.id 
            WHERE j.guru_id = ? AND j.tahun_ajaran_id = ?
            ORDER BY m.nama_mapel ASC
        ");
        $stmt_mapel->execute([$guru_id, $tahun_ajaran_id]);
        $mapel = $stmt_mapel->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode(['kelas' => $kelas, 'mapel' => $mapel]);
        exit;
    }

    public static function apiJenisEvaluasi() {
        self::checkAccess();
        $db = \App\Core\Database::connect();
        
        $institusi = \App\Core\Branding::getInstitusi();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        
        $kelas_id = $_GET['kelas_id'] ?? null;
        $mapel_id = $_GET['mapel_id'] ?? null;
        
        $evaluasi = ['PTS', 'PAS']; // Selalu ada
        $uh_list = [];

        if ($kelas_id && $mapel_id) {
            $stmt = $db->prepare("
                SELECT DISTINCT jenis_evaluasi 
                FROM nilai_harian 
                WHERE tahun_ajaran_id = ? AND kelas_id = ? AND mapel_id = ?
            ");
            $stmt->execute([$tahun_ajaran_id, $kelas_id, $mapel_id]);
            $raw = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            foreach ($raw as $val) {
                if (stripos($val, 'UH') !== false || stripos($val, 'PH') !== false) {
                    $uh_list[] = $val;
                }
            }
        }
        
        // Gabungkan UH list dengan UTS/UAS
        sort($uh_list);
        $final_evaluasi = array_merge($uh_list, $evaluasi);

        header('Content-Type: application/json');
        echo json_encode(['evaluasi' => array_unique($final_evaluasi)]);
        exit;
    }
public function monitoringNilai() {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();
        
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? null;
        $semester = $institusi['semester'] ?? 'Ganjil';
        
        $kelas_id = $_GET['kelas_id'] ?? null;
        $guru_id = $_GET['guru_id'] ?? null;

        $kelas_list = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $guru_list = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

        // Build data structure
        $monitoring_data = [];

        if ($kelas_id || $guru_id) {
            // Retrieve jadwal 
            $query = "SELECT DISTINCT j.mapel_id, j.guru_id, j.kelas_id, m.nama_mapel, g.nama as nama_guru, k.nama_kelas 
                      FROM jadwal_pelajaran j 
                      JOIN mapel m ON m.id = j.mapel_id 
                      JOIN kelas k ON k.id = j.kelas_id
                      LEFT JOIN guru g ON g.id = j.guru_id WHERE 1=1 ";
            
            $params = [];
            if ($kelas_id) {
                $query .= " AND j.kelas_id = ?";
                $params[] = $kelas_id;
            }
            if ($guru_id) {
                $query .= " AND j.guru_id = ?";
                $params[] = $guru_id;
            }
            $query .= " ORDER BY k.tingkat ASC, k.nama_kelas ASC, m.nama_mapel ASC";

            $stmt_jadwal = $db->prepare($query);
            $stmt_jadwal->execute($params);
            $jadwals = $stmt_jadwal->fetchAll(\PDO::FETCH_ASSOC);

            // Fetch total siswa for all relevant classes
            $kelas_totals = [];
            $stmt_siswa = $db->prepare("SELECT kelas_id, COUNT(*) as total FROM siswa WHERE status = 'Aktif' GROUP BY kelas_id");
            $stmt_siswa->execute();
            foreach ($stmt_siswa->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $kelas_totals[$row['kelas_id']] = $row['total'];
            }

            // Get all nilai_harian for relevant TA, semester
            $stmt_nilai = $db->prepare("SELECT mapel_id, kelas_id, jenis_evaluasi, COUNT(DISTINCT siswa_id) as total_dinilai, MAX(materi) as materi
                FROM nilai_harian 
                WHERE tahun_ajaran_id = ? AND semester = ? AND nilai IS NOT NULL
                GROUP BY mapel_id, kelas_id, jenis_evaluasi");
            $stmt_nilai->execute([$tahun_ajaran_id, $semester]);
            $nilai_raw = $stmt_nilai->fetchAll(\PDO::FETCH_ASSOC);

            $nilai_grouped = [];
            foreach ($nilai_raw as $nr) {
                $nilai_grouped[$nr['kelas_id']][$nr['mapel_id']][$nr['jenis_evaluasi']] = [
                    'total_dinilai' => $nr['total_dinilai'],
                    'materi' => $nr['materi']
                ];
            }

            foreach ($jadwals as $j) {
                $k_id = $j['kelas_id'];
                $m_id = $j['mapel_id'];
                $total_siswa = $kelas_totals[$k_id] ?? 0;
                
                $evaluasi_list = [];
                if (isset($nilai_grouped[$k_id][$m_id])) {
                    foreach ($nilai_grouped[$k_id][$m_id] as $jenis => $data) {
                        $evaluasi_list[] = [
                            'jenis' => $jenis,
                            'total_dinilai' => $data['total_dinilai'],
                            'materi' => $data['materi']
                        ];
                    }
                }
                
                $monitoring_data[] = [
                    'kelas_id' => $k_id,
                    'mapel_id' => $m_id,
                    'kelas' => $j['nama_kelas'],
                    'mapel' => $j['nama_mapel'],
                    'guru' => $j['nama_guru'],
                    'evaluasi' => $evaluasi_list,
                    'total_siswa' => $total_siswa
                ];
            }
        }

        $title = "Monitoring Nilai Mapel";
        $activeMenu = "kurikulum_monitoring_nilai";

        ob_start();
        include __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function simpanBobotNilai() {
        self::checkAccess();
        $db = Database::connect();
        $bobot_harian = isset($_POST['bobot_harian']) ? intval($_POST['bobot_harian']) : 50;
        $bobot_pas = isset($_POST['bobot_pas']) ? intval($_POST['bobot_pas']) : 50;
        
        $stmt = $db->prepare("UPDATE institusi SET bobot_harian = ?, bobot_pas = ?");
        $stmt->execute([$bobot_harian, $bobot_pas]);
        
        echo json_encode(['status' => 'success', 'message' => 'Bobot berhasil disimpan']);
    }

    public function monitoringNilaiDetail() {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();
        $activeYear = AcademicYear::current();
        
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? $activeYear['id'];
        $semester = $institusi['semester'] ?? 'Ganjil';
        
        $kelas_id = $_GET['kelas_id'] ?? 0;
        $mapel_id = $_GET['mapel_id'] ?? 0;

        if (!$kelas_id || !$mapel_id) {
            die("Kelas dan Mapel harus dipilih.");
        }

        // Ambil data Kelas
        $stmt = $db->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->execute([$kelas_id]);
        $kelas = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Ambil Wali Kelas
        $stmt_wali = $db->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.keterangan = ? AND gt.tahun_ajaran = ?");
        $stmt_wali->execute(['Kelas ' . $kelas['nama_kelas'], $activeYear['name'] ?? $institusi['tahun_ajaran_id']]);
        $wali = $stmt_wali->fetchColumn();
        $kelas['wali_kelas'] = $wali ?: '-';

        // Ambil data Mapel & Guru Pengampu
        $stmt = $db->prepare("SELECT m.nama_mapel, g.nama as guru_pengampu FROM jadwal_pelajaran j 
            JOIN mapel m ON m.id = j.mapel_id 
            LEFT JOIN guru g ON g.id = j.guru_id 
            WHERE j.kelas_id = ? AND j.mapel_id = ? LIMIT 1");
        $stmt->execute([$kelas_id, $mapel_id]);
        $mapel_info = $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['nama_mapel' => 'Tidak Diketahui', 'guru_pengampu' => '-'];

        // Ambil KKM
        $tingkat = (int) substr($kelas['nama_kelas'] ?? '7', 0, 1);
        if (!in_array($tingkat, [7, 8, 9])) $tingkat = 7;
        $stmt_kkm = $db->prepare("SELECT nilai_kkm FROM kurikulum_kkm WHERE tahun_ajaran_id = ? AND mapel_id = ? AND tingkat = ?");
        $stmt_kkm->execute([$tahun_ajaran_id, $mapel_id, $tingkat]);
        $kkm = $stmt_kkm->fetchColumn();
        if (!$kkm) $kkm = 70;

        // Ambil Siswa
        $stmt = $db->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt->execute([$kelas_id]);
        $siswa_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Ambil jenis PH apa saja yang sudah diinput untuk kelas dan mapel ini
        $stmt = $db->prepare("SELECT jenis_evaluasi, MAX(materi) as materi FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi LIKE 'PH %' GROUP BY jenis_evaluasi ORDER BY jenis_evaluasi ASC");
        $stmt->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester]);
        $ph_list_raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $ph_list = [];
        $ph_materi = [];
        foreach ($ph_list_raw as $ph) {
            $ph_list[] = $ph['jenis_evaluasi'];
            if (!empty($ph['materi'])) {
                $ph_materi[$ph['jenis_evaluasi']] = $ph['materi'];
            }
        }
        if (empty($ph_list)) {
            $ph_list = ['PH 1'];
        }

        // Ambil Semua Nilai
        $stmt = $db->prepare("SELECT siswa_id, jenis_evaluasi, nilai FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ?");
        $stmt->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester]);
        $nilai_raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $nilai_grouped = [];
        foreach ($nilai_raw as $n) {
            $nilai_grouped[$n['siswa_id']][$n['jenis_evaluasi']] = $n['nilai'];
        }

        $title = "Detail Nilai Harian - " . htmlspecialchars($mapel_info['nama_mapel'] ?? '');
        $activeMenu = "kurikulum_monitoring_nilai";

        ob_start();
        require __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai_detail.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../resources/views/layout.php';
    }
    public function monitoringNilaiCetak() {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();
        $activeYear = AcademicYear::current();
        
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? $activeYear['id'];
        $semester = $institusi['semester'] ?? 'Ganjil';
        
        $kelas_id = $_GET['kelas_id'] ?? 0;
        $mapel_id = $_GET['mapel_id'] ?? 0;

        if (!$kelas_id || !$mapel_id) {
            die("Kelas dan Mapel harus dipilih.");
        }

        // Ambil data Kelas
        $stmt = $db->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->execute([$kelas_id]);
        $kelas = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Ambil Wali Kelas
        $stmt_wali = $db->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.keterangan = ? AND gt.tahun_ajaran = ?");
        $stmt_wali->execute(['Kelas ' . $kelas['nama_kelas'], $activeYear['name'] ?? $institusi['tahun_ajaran_id']]);
        $wali = $stmt_wali->fetchColumn();
        $kelas['wali_kelas'] = $wali ?: '-';

        // Ambil data Mapel & Guru Pengampu
        $stmt = $db->prepare("SELECT m.nama_mapel, g.nama as guru_pengampu FROM jadwal_pelajaran j 
            JOIN mapel m ON m.id = j.mapel_id 
            LEFT JOIN guru g ON g.id = j.guru_id 
            WHERE j.kelas_id = ? AND j.mapel_id = ? LIMIT 1");
        $stmt->execute([$kelas_id, $mapel_id]);
        $mapel_info = $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['nama_mapel' => 'Tidak Diketahui', 'guru_pengampu' => '-'];

        // Ambil KKM
        $tingkat = (int) substr($kelas['nama_kelas'] ?? '7', 0, 1);
        if (!in_array($tingkat, [7, 8, 9])) $tingkat = 7;
        $stmt_kkm = $db->prepare("SELECT nilai_kkm FROM kurikulum_kkm WHERE tahun_ajaran_id = ? AND mapel_id = ? AND tingkat = ?");
        $stmt_kkm->execute([$tahun_ajaran_id, $mapel_id, $tingkat]);
        $kkm = $stmt_kkm->fetchColumn();
        if (!$kkm) $kkm = 70;

        // Ambil Siswa
        $stmt = $db->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt->execute([$kelas_id]);
        $siswa_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Ambil jenis PH apa saja yang sudah diinput untuk kelas dan mapel ini
        $stmt = $db->prepare("SELECT jenis_evaluasi, MAX(materi) as materi FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi LIKE 'PH %' GROUP BY jenis_evaluasi ORDER BY jenis_evaluasi ASC");
        $stmt->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester]);
        $ph_list_raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $ph_list = [];
        $ph_materi = [];
        foreach ($ph_list_raw as $ph) {
            $ph_list[] = $ph['jenis_evaluasi'];
            if (!empty($ph['materi'])) {
                $ph_materi[$ph['jenis_evaluasi']] = $ph['materi'];
            }
        }
        if (empty($ph_list)) {
            $ph_list = ['PH 1'];
        }

        // Ambil Semua Nilai
        $stmt = $db->prepare("SELECT siswa_id, jenis_evaluasi, nilai FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ?");
        $stmt->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester]);
        $nilai_raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $nilai_grouped = [];
        foreach ($nilai_raw as $n) {
            $nilai_grouped[$n['siswa_id']][$n['jenis_evaluasi']] = $n['nilai'];
        }

        require __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai_cetak.php';
    }

    public function monitoringNilaiGlobal() {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();
        $activeYear = AcademicYear::current();
        
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? $activeYear['id'];
        $semester = $institusi['semester'] ?? 'Ganjil';
        
        // 1. Ambil KKM
        $stmt_kkm = $db->prepare("SELECT mapel_id, tingkat, nilai_kkm FROM kurikulum_kkm WHERE tahun_ajaran_id = ?");
        $stmt_kkm->execute([$tahun_ajaran_id]);
        $kkm_raw = $stmt_kkm->fetchAll(\PDO::FETCH_ASSOC);
        $kkm_map = [];
        foreach ($kkm_raw as $k) {
            $kkm_map[$k['mapel_id'] . '_' . $k['tingkat']] = $k['nilai_kkm'];
        }

        // 2. Ambil Kelas
        $stmt_kelas = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
        $kelas_list = $stmt_kelas->fetchAll(\PDO::FETCH_ASSOC);

        // 3. Ambil Mapel per Kelas
        $stmt_mapel = $db->query("SELECT DISTINCT j.kelas_id, j.mapel_id, m.nama_mapel FROM jadwal_pelajaran j JOIN mapel m ON m.id = j.mapel_id ORDER BY m.nama_mapel ASC");
        $mapel_raw = $stmt_mapel->fetchAll(\PDO::FETCH_ASSOC);
        $mapel_kelas = [];
        foreach ($mapel_raw as $m) {
            $mapel_kelas[$m['kelas_id']][] = [
                'id' => $m['mapel_id'],
                'nama' => $m['nama_mapel']
            ];
        }

        // 4. Ambil Siswa
        $stmt_siswa = $db->query("SELECT id, kelas_id, nis, nama FROM siswa WHERE status = 'Aktif' ORDER BY nama ASC");
        $siswa_raw = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);
        $siswa_kelas = [];
        foreach ($siswa_raw as $s) {
            $siswa_kelas[$s['kelas_id']][] = $s;
        }

        // 5. Ambil Semua Nilai
        $stmt_nilai = $db->prepare("SELECT siswa_id, kelas_id, mapel_id, jenis_evaluasi, nilai FROM nilai_harian WHERE tahun_ajaran_id = ? AND semester = ?");
        $stmt_nilai->execute([$tahun_ajaran_id, $semester]);
        $nilai_raw = $stmt_nilai->fetchAll(\PDO::FETCH_ASSOC);
        
        $nilai_map = [];
        $ph_counts = []; // Untuk mengetahui max PH per kelas per mapel
        
        foreach ($nilai_raw as $n) {
            $nilai_map[$n['siswa_id']][$n['mapel_id']][$n['jenis_evaluasi']] = $n['nilai'];
            
            if (strpos($n['jenis_evaluasi'], 'PH ') === 0) {
                // Ekstrak angka PH
                $num = (int) str_replace('PH ', '', $n['jenis_evaluasi']);
                $key = $n['kelas_id'] . '_' . $n['mapel_id'];
                if (!isset($ph_counts[$key]) || $num > $ph_counts[$key]) {
                    $ph_counts[$key] = $num;
                }
            }
        }

        require __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai_global.php';
    }
}