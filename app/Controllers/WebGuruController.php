<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;

class WebGuruController {
    public function dashboard() {
        $activeMenu = 'guru_dashboard';
        $title = 'Dashboard Guru | MTs RS';
        $db = Database::connect('core');

        $guru_id = $_SESSION['guru_id'] ?? null;
        
        // Fetch specific data for this guru
        $guru_info = null;
        if ($guru_id) {
            $stmt = $db->prepare("SELECT * FROM guru WHERE id = ?");
            $stmt->execute([$guru_id]);
            $guru_info = $stmt->fetch();
        }

        ob_start();
        include __DIR__ . '/../../resources/views/guru/dashboard.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function jadwal() {
        $activeMenu = 'guru_jadwal';
        $title = 'Jadwal Mengajar | MTs RS';
        $db = Database::connect('core');

        $guru_id = $_SESSION['guru_id'] ?? null;
        if (!$guru_id) { Helper::redirect('/guru'); }

        $active_year = \App\Core\AcademicYear::current();
        $tahun_ajaran_id = $active_year ? $active_year['id'] : 1;

        $jadwal_raw = $db->query("SELECT jp.*, k.nama_kelas, m.nama_mapel 
                                  FROM jadwal_pelajaran jp 
                                  LEFT JOIN kelas k ON jp.kelas_id = k.id 
                                  LEFT JOIN mapel m ON jp.mapel_id = m.id 
                                  WHERE jp.guru_id = $guru_id AND jp.tahun_ajaran_id = $tahun_ajaran_id 
                                  ORDER BY FIELD(jp.hari, 'Sabtu', 'Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis'), jp.jam_mulai ASC")->fetchAll();

        // Group by Hari
        $jadwal = [
            'Sabtu' => [], 'Ahad' => [], 'Senin' => [], 'Selasa' => [], 'Rabu' => [], 'Kamis' => []
        ];
        foreach ($jadwal_raw as $j) {
            $jadwal[$j['hari']][] = $j;
        }

        ob_start();
        include __DIR__ . '/../../resources/views/guru/jadwal.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function jurnal() {
        $activeMenu = 'guru_jurnal';
        $title = 'Jurnal & Absensi Kelas | MTs RS';
        $db = Database::connect('core');

        $guru_id = $_SESSION['guru_id'] ?? null;
        if (!$guru_id) { Helper::redirect('/guru'); }

        $active_year = \App\Core\AcademicYear::current();
        $tahun_ajaran_id = $active_year ? $active_year['id'] : 1;

        $filter_kelas_id = (int)($_GET['kelas_id'] ?? 0);
        $filter_mapel_id = (int)($_GET['mapel_id'] ?? 0);

        // Fetch classes taught by this guru for the dropdown
        $kelas_mengajar = $db->query("SELECT DISTINCT k.id, k.nama_kelas 
                                      FROM jadwal_pelajaran jp 
                                      JOIN kelas k ON jp.kelas_id = k.id 
                                      WHERE jp.guru_id = $guru_id AND jp.tahun_ajaran_id = $tahun_ajaran_id
                                      ORDER BY k.tingkat ASC, k.nama_kelas ASC")->fetchAll();

        // Fetch subjects taught by this teacher FOR THE SELECTED CLASS
        $mapel_mengajar = [];
        if ($filter_kelas_id > 0) {
            $stmt = $db->prepare("SELECT DISTINCT m.id, m.nama_mapel 
                                          FROM jadwal_pelajaran jp 
                                          JOIN mapel m ON jp.mapel_id = m.id 
                                          WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.kelas_id = ?
                                          ORDER BY m.nama_mapel ASC");
            $stmt->execute([$guru_id, $tahun_ajaran_id, $filter_kelas_id]);
            $mapel_mengajar = $stmt->fetchAll();
        }

        // Fetch logs (jurnals) made by this guru if filtered
        $jurnals = [];
        if ($filter_kelas_id > 0 && $filter_mapel_id > 0) {
            $stmt = $db->prepare("SELECT j.*, k.nama_kelas, m.nama_mapel 
                                   FROM jurnal_guru j 
                                   LEFT JOIN kelas k ON j.kelas_id = k.id 
                                   LEFT JOIN mapel m ON j.mapel_id = m.id 
                                   WHERE j.guru_id = ? AND j.kelas_id = ? AND j.mapel_id = ?
                                   ORDER BY j.tanggal DESC, j.id DESC LIMIT 100");
            $stmt->execute([$guru_id, $filter_kelas_id, $filter_mapel_id]);
            $jurnals = $stmt->fetchAll();
        }

        ob_start();
        include __DIR__ . '/../../resources/views/guru/jurnal.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function jurnalSave() {
        $db = Database::connect();
        $guru_id = $_SESSION['guru_id'] ?? null;
        
        if (!$guru_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('/guru/jurnal');
        }

        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        $mapel_id = intval($_POST['mapel_id'] ?? 0);
        $materi = trim($_POST['materi'] ?? '');
        $siswa_absen = trim($_POST['siswa_absen'] ?? '');

        if ($kelas_id > 0 && $mapel_id > 0 && !empty($materi)) {
            $stmt = $db->prepare("INSERT INTO jurnal_guru (tanggal, guru_id, kelas_id, mapel_id, materi, siswa_absen) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal, $guru_id, $kelas_id, $mapel_id, $materi, $siswa_absen]);
            Helper::logActivity('GURU_PORTAL', 'INSERT', "Mengisi Jurnal Harian (Mapel ID: $mapel_id, Kelas ID: $kelas_id)");
        }

        Helper::redirect('/guru/jurnal');
    }

    public function nilaiHarian() {
        $activeMenu = 'guru_nilai_harian';
        $title = 'Nilai Harian | MTs RS';
        $db = Database::connect('core');
        $siakad_db = Database::connect('siakad');

        $guru_id = $_SESSION['guru_id'] ?? null;
        if (!$guru_id) { Helper::redirect('/guru'); }

        $active_year = \App\Core\AcademicYear::current();
        $tahun_ajaran_id = $active_year ? $active_year['id'] : 1;
        $semester = $active_year ? $active_year['semester'] : 'Ganjil';

        $filter_kelas_id = (int)($_GET['kelas_id'] ?? 0);
        $filter_mapel_id = (int)($_GET['mapel_id'] ?? 0);

        // Fetch classes taught by this teacher
        $kelas_mengajar = $db->query("SELECT DISTINCT k.id, k.nama_kelas 
                                      FROM jadwal_pelajaran jp 
                                      JOIN kelas k ON jp.kelas_id = k.id 
                                      WHERE jp.guru_id = $guru_id AND jp.tahun_ajaran_id = $tahun_ajaran_id
                                      ORDER BY k.tingkat ASC, k.nama_kelas ASC")->fetchAll();

        // Fetch subjects taught by this teacher FOR THE SELECTED CLASS
        $mapel_mengajar = [];
        if ($filter_kelas_id > 0) {
            $stmt = $db->prepare("SELECT DISTINCT m.id, m.nama_mapel 
                                          FROM jadwal_pelajaran jp 
                                          JOIN mapel m ON jp.mapel_id = m.id 
                                          WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.kelas_id = ?
                                          ORDER BY m.nama_mapel ASC");
            $stmt->execute([$guru_id, $tahun_ajaran_id, $filter_kelas_id]);
            $mapel_mengajar = $stmt->fetchAll();
        }

        $siswa_list = [];
        $ph_list = [];
        $ph_materi = [];
        $nilai_grouped = [];

        if ($filter_kelas_id > 0 && $filter_mapel_id > 0) {
            // Ambil Siswa
            $stmt = $siakad_db->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
            $stmt->execute([$filter_kelas_id]);
            $siswa_list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Ambil jenis PH
            $stmt = $siakad_db->prepare("SELECT jenis_evaluasi, MAX(materi) as materi FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi LIKE 'PH %' GROUP BY jenis_evaluasi ORDER BY jenis_evaluasi ASC");
            $stmt->execute([$filter_kelas_id, $filter_mapel_id, $tahun_ajaran_id, $semester]);
            $ph_list_raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
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
            $stmt = $siakad_db->prepare("SELECT siswa_id, jenis_evaluasi, nilai FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ?");
            $stmt->execute([$filter_kelas_id, $filter_mapel_id, $tahun_ajaran_id, $semester]);
            $nilai_raw = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($nilai_raw as $n) {
                $nilai_grouped[$n['siswa_id']][$n['jenis_evaluasi']] = $n['nilai'];
            }
        }

        ob_start();
        include __DIR__ . '/../../resources/views/guru/nilai_harian_index.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function nilaiHarianInput() {
        $activeMenu = 'guru_nilai_harian';
        $title = 'Input Nilai Harian | MTs RS';
        $db = Database::connect('core');
        $siakad_db = Database::connect('siakad');

        $guru_id = $_SESSION['guru_id'] ?? null;
        if (!$guru_id) { Helper::redirect('/guru'); }

        $kelas_id = intval($_GET['kelas_id'] ?? 0);
        $mapel_id = intval($_GET['mapel_id'] ?? 0);
        $jenis_evaluasi = $_GET['jenis_evaluasi'] ?? '';
        
        $active_year = \App\Core\AcademicYear::current();
        $tahun_ajaran_id = $active_year ? $active_year['id'] : 1;
        $semester = $active_year ? $active_year['semester'] : 'Ganjil';

        if ($jenis_evaluasi == 'PH') {
            // Find max PH
            $stmt_ph = $siakad_db->prepare("SELECT MAX(CAST(SUBSTRING_INDEX(jenis_evaluasi, ' ', -1) AS UNSIGNED)) as max_ph FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi LIKE 'PH %'");
            $stmt_ph->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester]);
            $max_ph = $stmt_ph->fetchColumn();
            $next_ph = $max_ph ? ($max_ph + 1) : 1;
            $jenis_evaluasi = "PH $next_ph";
        }

        $stmt_kelas = $db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
        $stmt_kelas->execute([$kelas_id]);
        $nama_kelas = $stmt_kelas->fetchColumn();

        $stmt_mapel = $db->prepare("SELECT nama_mapel FROM mapel WHERE id = ?");
        $stmt_mapel->execute([$mapel_id]);
        $nama_mapel = $stmt_mapel->fetchColumn();

        $stmt_siswa = $db->prepare("SELECT id, nis, nama, jenis_kelamin FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt_siswa->execute([$kelas_id]);
        $siswa_list = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_nilai = $siakad_db->prepare("SELECT siswa_id, nilai, keterangan, materi FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi = ?");
        $stmt_nilai->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester, $jenis_evaluasi]);
        $nilai_exist_raw = $stmt_nilai->fetchAll(\PDO::FETCH_ASSOC);
        
        $nilai_exist = [];
        $keterangan_exist = [];
        $materi_exist = $nilai_exist_raw[0]['materi'] ?? '';
        foreach ($nilai_exist_raw as $n) {
            $nilai_exist[$n['siswa_id']] = $n['nilai'];
            $keterangan_exist[$n['siswa_id']] = $n['keterangan'];
        }

        ob_start();
        include __DIR__ . '/../../resources/views/guru/nilai_harian_input.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function nilaiHarianSave() {
        $siakad_db = Database::connect('siakad');
        $guru_id = $_SESSION['guru_id'] ?? null;
        
        if (!$guru_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('/guru/nilai-harian');
        }

        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        $mapel_id = intval($_POST['mapel_id'] ?? 0);
        $jenis_evaluasi = $_POST['jenis_evaluasi'] ?? '';
        $materi = trim($_POST['materi'] ?? '');
        $nilai_arr = $_POST['nilai'] ?? [];
        $keterangan_arr = $_POST['keterangan'] ?? [];

        $active_year = \App\Core\AcademicYear::current();
        $tahun_ajaran_id = $active_year ? $active_year['id'] : 1;
        $semester = $active_year ? $active_year['semester'] : 'Ganjil';

        try {
            $siakad_db->beginTransaction();

            $stmt_del = $siakad_db->prepare("DELETE FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi = ?");
            $stmt_del->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester, $jenis_evaluasi]);

            $stmt_ins = $siakad_db->prepare("INSERT INTO nilai_harian (siswa_id, mapel_id, kelas_id, tahun_ajaran_id, semester, jenis_evaluasi, materi, nilai, keterangan, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            foreach ($nilai_arr as $siswa_id => $nilai) {
                if ($nilai !== '' && $nilai !== null) {
                    $ket = $keterangan_arr[$siswa_id] ?? '';
                    $stmt_ins->execute([$siswa_id, $mapel_id, $kelas_id, $tahun_ajaran_id, $semester, $jenis_evaluasi, $materi, $nilai, $ket]);
                }
            }

            $siakad_db->commit();
            $_SESSION['guru_msg'] = "Nilai $jenis_evaluasi berhasil disimpan!";
            $_SESSION['guru_msg_type'] = "success";
        } catch (\Exception $e) {
            $siakad_db->rollBack();
            $_SESSION['guru_msg'] = "Gagal menyimpan: " . $e->getMessage();
            $_SESSION['guru_msg_type'] = "error";
        }

        Helper::redirect('/guru/nilai-harian');
    }

    public function nilaiHarianDelete() {
        $siakad_db = Database::connect('siakad');
        $guru_id = $_SESSION['guru_id'] ?? null;
        
        if (!$guru_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('/guru/nilai-harian');
        }

        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        $mapel_id = intval($_POST['mapel_id'] ?? 0);
        $jenis_evaluasi = $_POST['jenis_evaluasi'] ?? '';

        $active_year = \App\Core\AcademicYear::current();
        $tahun_ajaran_id = $active_year ? $active_year['id'] : 1;
        $semester = $active_year ? $active_year['semester'] : 'Ganjil';

        $stmt_del = $siakad_db->prepare("DELETE FROM nilai_harian WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ? AND jenis_evaluasi = ?");
        $stmt_del->execute([$kelas_id, $mapel_id, $tahun_ajaran_id, $semester, $jenis_evaluasi]);

        $_SESSION['guru_msg'] = "Data $jenis_evaluasi berhasil dihapus.";
        $_SESSION['guru_msg_type'] = "success";
        
        Helper::redirect('/guru/nilai-harian');
    }

    public function berkas() {
        $activeMenu = 'guru_berkas';
        $title = 'Arsip Berkas | MTs RS';
        $db = Database::connect('core');
        $siakad_db = Database::connect('siakad');

        $guru_id = $_SESSION['guru_id'] ?? null;
        if (!$guru_id) { Helper::redirect('/guru'); }

        $active_year = \App\Core\AcademicYear::current();
        $ta_id = $active_year ? $active_year['id'] : 1;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tab_type = $_POST['tab_type'] ?? 'perangkat';

            if ($tab_type === 'pribadi') {
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
                                $stmt = $db->prepare("INSERT INTO berkas_pribadi (guru_id, jenis_berkas, judul_berkas, file_nama) VALUES (?, ?, ?, ?)");
                                $stmt->execute([$guru_id, $jenis_berkas, $judul_berkas, $new_filename]);
                                $_SESSION['guru_msg'] = 'Berkas pribadi berhasil diunggah.';
                                $_SESSION['guru_msg_type'] = 'success';
                                $_SESSION['active_tab'] = 'pribadi';
                                Helper::redirect('/guru/berkas');
                            } catch (\Exception $e) {
                                $_SESSION['guru_msg'] = 'Gagal menyimpan ke database.';
                                $_SESSION['guru_msg_type'] = 'error';
                            }
                        } else {
                            $_SESSION['guru_msg'] = 'Gagal memindahkan file yang diunggah.';
                            $_SESSION['guru_msg_type'] = 'error';
                        }
                    } else {
                        $_SESSION['guru_msg'] = 'Format file tidak didukung.';
                        $_SESSION['guru_msg_type'] = 'error';
                    }
                } else {
                    $_SESSION['guru_msg'] = 'Harap isi semua data dan pilih file.';
                    $_SESSION['guru_msg_type'] = 'warning';
                }
            } else {
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
                                $stmt = $db->prepare("INSERT INTO perangkat_pembelajaran (guru_id, tahun_ajaran_id, jenis_berkas, judul_berkas, file_nama, kelas_id, mapel_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                $stmt->execute([$guru_id, $ta_id, $jenis_berkas, $judul_berkas, $new_filename, $kelas_id, $mapel_id]);
                                $_SESSION['guru_msg'] = 'Berkas berhasil diunggah dan menunggu validasi.';
                                $_SESSION['guru_msg_type'] = 'success';
                                Helper::redirect('/guru/berkas');
                            } catch (\Exception $e) {
                                $_SESSION['guru_msg'] = 'Gagal menyimpan ke database.';
                                $_SESSION['guru_msg_type'] = 'error';
                            }
                        } else {
                            $_SESSION['guru_msg'] = 'Gagal memindahkan file yang diunggah.';
                            $_SESSION['guru_msg_type'] = 'error';
                        }
                    } else {
                        $_SESSION['guru_msg'] = 'Format file tidak didukung. Harap unggah dokumen berformat PDF saja.';
                        $_SESSION['guru_msg_type'] = 'error';
                    }
                } else {
                    $_SESSION['guru_msg'] = 'Harap isi semua data dan pilih file.';
                    $_SESSION['guru_msg_type'] = 'warning';
                }
            }
        }

        $active_tab = 'perangkat';
        if (isset($_SESSION['active_tab'])) {
            $active_tab = $_SESSION['active_tab'];
            unset($_SESSION['active_tab']);
        }

        $berkasList = [];
        try {
            $stmt = $db->prepare("SELECT p.*, k.nama_kelas, m.nama_mapel FROM perangkat_pembelajaran p LEFT JOIN kelas k ON p.kelas_id = k.id LEFT JOIN mapel m ON p.mapel_id = m.id WHERE p.guru_id = ? AND p.tahun_ajaran_id = ? ORDER BY p.tanggal_upload DESC");
            $stmt->execute([$guru_id, $ta_id]);
            $berkasList = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $berkasPribadiList = [];
        try {
            $stmt = $db->prepare("SELECT * FROM berkas_pribadi WHERE guru_id = ? ORDER BY tanggal_upload DESC");
            $stmt->execute([$guru_id]);
            $berkasPribadiList = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $stmt_kelas = $siakad_db->prepare("SELECT DISTINCT k.id, k.nama_kelas FROM jadwal_pelajaran jp JOIN kelas k ON jp.kelas_id = k.id WHERE jp.guru_id = ?");
        $stmt_kelas->execute([$guru_id]);
        $kelas_mengajar = $stmt_kelas->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_mapel = $siakad_db->prepare("SELECT DISTINCT m.id, m.nama_mapel FROM jadwal_pelajaran jp JOIN mapel m ON jp.mapel_id = m.id WHERE jp.guru_id = ?");
        $stmt_mapel->execute([$guru_id]);
        $mapel_mengajar = $stmt_mapel->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_kombinasi = $siakad_db->prepare("
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

        ob_start();
        include __DIR__ . '/../../resources/views/guru/berkas.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function hapusBerkas($id)
    {
        if (!isset($_SESSION['guru_id'])) { Helper::redirect('/guru'); }
        $guru_id = $_SESSION['guru_id'];
        $db = Database::connect('core');
        try {
            $stmt = $db->prepare("SELECT file_nama FROM perangkat_pembelajaran WHERE id = ? AND guru_id = ?");
            $stmt->execute([$id, $guru_id]);
            $berkas = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($berkas) {
                $filepath = __DIR__ . '/../../public/uploads/berkas/' . $berkas['file_nama'];
                if (file_exists($filepath)) unlink($filepath);
                $stmt = $db->prepare("DELETE FROM perangkat_pembelajaran WHERE id = ? AND guru_id = ?");
                $stmt->execute([$id, $guru_id]);
                $_SESSION['guru_msg'] = 'Berkas berhasil dihapus.';
                $_SESSION['guru_msg_type'] = 'success';
            }
        } catch (\Exception $e) {}
        Helper::redirect('/guru/berkas');
    }

    public function hapusBerkasPribadi($id)
    {
        if (!isset($_SESSION['guru_id'])) { Helper::redirect('/guru'); }
        $guru_id = $_SESSION['guru_id'];
        $db = Database::connect('core');
        try {
            $stmt = $db->prepare("SELECT file_nama FROM berkas_pribadi WHERE id = ? AND guru_id = ?");
            $stmt->execute([$id, $guru_id]);
            $berkas = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($berkas) {
                $filepath = __DIR__ . '/../../public/uploads/berkas/' . $berkas['file_nama'];
                if (file_exists($filepath)) unlink($filepath);
                $stmt = $db->prepare("DELETE FROM berkas_pribadi WHERE id = ? AND guru_id = ?");
                $stmt->execute([$id, $guru_id]);
                $_SESSION['guru_msg'] = 'Berkas pribadi berhasil dihapus.';
                $_SESSION['guru_msg_type'] = 'success';
                $_SESSION['active_tab'] = 'pribadi';
            }
        } catch (\Exception $e) {}
        Helper::redirect('/guru/berkas');
    }

    public function berkasSalinParalel()
    {
        if (!isset($_SESSION['guru_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('/guru');
        }
        $guru_id = $_SESSION['guru_id'];
        $kelas_id = $_POST['kelas_id'] ?? null;
        $mapel_id = $_POST['mapel_id'] ?? null;
        
        if (!$kelas_id || !$mapel_id) {
            $_SESSION['guru_msg'] = 'Data tidak lengkap.';
            $_SESSION['guru_msg_type'] = 'error';
            Helper::redirect('/guru/berkas');
        }

        $active_year = \App\Core\AcademicYear::current();
        $ta_id = $active_year ? $active_year['id'] : 1;

        $db = Database::connect('core');
        $siakad_db = Database::connect('siakad');

        try {
            // 1. Get source class tingkat
            $stmt = $siakad_db->prepare("SELECT tingkat FROM kelas WHERE id = ?");
            $stmt->execute([$kelas_id]);
            $tingkat = $stmt->fetchColumn();

            if (!$tingkat) {
                throw new \Exception("Kelas asal tidak ditemukan.");
            }

            // 2. Find target classes (same teacher, same mapel, same tingkat, different class)
            $stmt = $siakad_db->prepare("
                SELECT DISTINCT k.id 
                FROM jadwal_pelajaran jp 
                JOIN kelas k ON jp.kelas_id = k.id 
                WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? AND jp.mapel_id = ? AND k.tingkat = ? AND k.id != ?
            ");
            $stmt->execute([$guru_id, $ta_id, $mapel_id, $tingkat, $kelas_id]);
            $target_classes = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (empty($target_classes)) {
                $_SESSION['guru_msg'] = 'Tidak ada kelas paralel lain (tingkat & mapel sama) yang Anda ajar.';
                $_SESSION['guru_msg_type'] = 'warning';
                Helper::redirect('/guru/berkas');
                exit;
            }

            // 3. Get all uploaded documents for the source class
            $stmt = $db->prepare("SELECT * FROM perangkat_pembelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND kelas_id = ? AND mapel_id = ?");
            $stmt->execute([$guru_id, $ta_id, $kelas_id, $mapel_id]);
            $source_docs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($source_docs)) {
                $_SESSION['guru_msg'] = 'Tidak ada berkas di kelas ini yang bisa disalin.';
                $_SESSION['guru_msg_type'] = 'warning';
                Helper::redirect('/guru/berkas');
                exit;
            }

            $copied_count = 0;

            // 4. Copy each document to target classes if not exists
            foreach ($target_classes as $target_kelas_id) {
                foreach ($source_docs as $doc) {
                    // Check if already exists in target class
                    $stmt_check = $db->prepare("SELECT id FROM perangkat_pembelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND kelas_id = ? AND mapel_id = ? AND jenis_berkas = ?");
                    $stmt_check->execute([$guru_id, $ta_id, $target_kelas_id, $mapel_id, $doc['jenis_berkas']]);
                    if (!$stmt_check->fetch()) {
                        // Copy the file physically to ensure independent deletion
                        $ext = strtolower(pathinfo($doc['file_nama'], PATHINFO_EXTENSION));
                        $new_filename = uniqid('berkas_copy_') . '.' . $ext;
                        $old_path = __DIR__ . '/../../public/uploads/berkas/' . $doc['file_nama'];
                        $new_path = __DIR__ . '/../../public/uploads/berkas/' . $new_filename;
                        
                        if (file_exists($old_path) && copy($old_path, $new_path)) {
                            // Insert db record
                            $stmt_insert = $db->prepare("INSERT INTO perangkat_pembelajaran (guru_id, tahun_ajaran_id, jenis_berkas, judul_berkas, file_nama, kelas_id, mapel_id, status_validasi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt_insert->execute([$guru_id, $ta_id, $doc['jenis_berkas'], $doc['judul_berkas'], $new_filename, $target_kelas_id, $mapel_id, 'Menunggu']);
                            $copied_count++;
                        }
                    }
                }
            }

            if ($copied_count > 0) {
                $_SESSION['guru_msg'] = "Berhasil menyalin $copied_count berkas ke kelas paralel.";
                $_SESSION['guru_msg_type'] = 'success';
            } else {
                $_SESSION['guru_msg'] = 'Semua kelas paralel sudah memiliki berkas tersebut.';
                $_SESSION['guru_msg_type'] = 'warning';
            }

        } catch (\Exception $e) {
            $_SESSION['guru_msg'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
            $_SESSION['guru_msg_type'] = 'error';
        }

        Helper::redirect('/guru/berkas');
    }
}
