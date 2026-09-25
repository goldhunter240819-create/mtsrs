<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Branding;
use App\Core\AcademicYear;
use App\Core\Helper;

class SiakadController {

    private function createThumbnail($source, $destination, $maxWidth = 150, $maxHeight = 150) {
        if (!file_exists($source)) return false;
        $info = getimagesize($source);
        if ($info === false) return false;
        
        $width = $info[0];
        $height = $info[1];
        
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);
        
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        
        switch ($info['mime']) {
            case 'image/jpeg':
                $img = imagecreatefromjpeg($source);
                imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($thumb, $destination, 80);
                break;
            case 'image/png':
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                $img = imagecreatefrompng($source);
                imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagepng($thumb, $destination, 8);
                break;
            case 'image/gif':
                $img = imagecreatefromgif($source);
                imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagegif($thumb, $destination);
                break;
            default:
                return false;
        }
        
        imagedestroy($img);
        imagedestroy($thumb);
        return true;
    }

    // --- 1. Dashboard SIAKAD ---
    public function dashboard() {
        $db = Database::connect();

        $statSiswa = $db->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();
        $statGuru = $db->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        $statKelas = $db->query("SELECT COUNT(*) FROM kelas")->fetchColumn();
        $statMapel = $db->query("SELECT COUNT(*) FROM mapel")->fetchColumn();

        $siswaList = $db->query("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.status = 'Aktif' ORDER BY s.nama ASC LIMIT 5")->fetchAll();
        $kalenderList = $db->query("SELECT * FROM kalender_pendidikan ORDER BY tanggal_mulai ASC LIMIT 5")->fetchAll();

        $title = "Dashboard SIAKAD - MTs Roudlotus Sholihin";
        $activeMenu = 'siakad_dashboard';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/dashboard.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    // --- 2. Profil Institusi ---
    public function institusi() {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();
        $years = $db->query("SELECT * FROM tahun_ajaran ORDER BY name DESC")->fetchAll();

        $title = "Profil Institusi - SIAKAD MTs RS";
        $activeMenu = 'siakad_institusi';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/institusi.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function institusiSave() {
        $db = Database::connect();
        $nama = trim($_POST['nama'] ?? '');
        $singkatan = trim($_POST['singkatan'] ?? '');
        $npsn = trim($_POST['npsn'] ?? '');
        $nsm = trim($_POST['nsm'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $desa = trim($_POST['desa'] ?? '');
        $kecamatan = trim($_POST['kecamatan'] ?? '');
        $kota = trim($_POST['kota'] ?? '');
        $provinsi = trim($_POST['provinsi'] ?? '');
        $kodepos = trim($_POST['kodepos'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $nama_kepala = trim($_POST['nama_kepala'] ?? '');
        $nip_kepala = trim($_POST['nip_kepala'] ?? '');
        $tahun_ajaran_id = intval($_POST['tahun_ajaran_id'] ?? 1);
        $semester = $_POST['semester'] ?? 'Ganjil';

        $logoName = null;
        if (!empty($_FILES['logo']['name'])) {
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $logoName = 'logo_' . time() . '.' . strtolower($ext);
            $uploadDir1 = __DIR__ . '/../../uploads/logo/';
            $uploadDir2 = __DIR__ . '/../../public/uploads/logo/';
            if (!is_dir($uploadDir1)) mkdir($uploadDir1, 0777, true);
            if (!is_dir($uploadDir2)) mkdir($uploadDir2, 0777, true);
            move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir1 . $logoName);
            copy($uploadDir1 . $logoName, $uploadDir2 . $logoName);
        }

        if (!empty($nama)) {
            if ($logoName) {
                $stmt = $db->prepare("UPDATE institusi SET nama = ?, singkatan = ?, npsn = ?, nsm = ?, alamat = ?, desa = ?, kecamatan = ?, kota = ?, provinsi = ?, kodepos = ?, telepon = ?, email = ?, website = ?, nama_kepala = ?, nip_kepala = ?, tahun_ajaran_id = ?, semester = ?, logo = ? WHERE id = 1");
                $stmt->execute([$nama, $singkatan, $npsn, $nsm, $alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos, $telepon, $email, $website, $nama_kepala, $nip_kepala, $tahun_ajaran_id, $semester, $logoName]);
            } else {
                $stmt = $db->prepare("UPDATE institusi SET nama = ?, singkatan = ?, npsn = ?, nsm = ?, alamat = ?, desa = ?, kecamatan = ?, kota = ?, provinsi = ?, kodepos = ?, telepon = ?, email = ?, website = ?, nama_kepala = ?, nip_kepala = ?, tahun_ajaran_id = ?, semester = ? WHERE id = 1");
                $stmt->execute([$nama, $singkatan, $npsn, $nsm, $alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos, $telepon, $email, $website, $nama_kepala, $nip_kepala, $tahun_ajaran_id, $semester]);
            }
        }

        Helper::redirect('/siakad/institusi');
    }

    public function tahunAjaranAdd() {
        $db = Database::connect();
        $name = trim($_POST['name'] ?? '');
        if (!empty($name)) {
            $stmt = $db->prepare("INSERT INTO tahun_ajaran (name, is_active) VALUES (?, 0)");
            $stmt->execute([$name]);
        }
        Helper::redirect('/siakad/institusi');
    }

    public function tahunAjaranActivate($id) {
        $db = Database::connect();
        $id = intval($id);
        if ($id > 0) {
            $db->query("UPDATE tahun_ajaran SET is_active = 0");
            $db->prepare("UPDATE tahun_ajaran SET is_active = 1 WHERE id = ?")->execute([$id]);
            $db->prepare("UPDATE institusi SET tahun_ajaran_id = ? WHERE id = 1")->execute([$id]);
        }
        Helper::redirect('/siakad/institusi');
    }

    // --- 3. Mata Pelajaran ---
    public function mapel() {
        $db = Database::connect();
        $mapelList = $db->query("SELECT * FROM mapel ORDER BY kode_mapel ASC")->fetchAll();

        $title = "Mata Pelajaran - SIAKAD MTs RS";
        $activeMenu = 'siakad_mapel';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/mapel.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function mapelSave() {
        $db = Database::connect();
        $id = intval($_POST['id'] ?? 0);
        $kode_mapel = trim($_POST['kode_mapel'] ?? '');
        $nama_mapel = trim($_POST['nama_mapel'] ?? '');
        $kelompok = $_POST['kelompok'] ?? 'A';

        if (!empty($kode_mapel) && !empty($nama_mapel)) {
            try {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE mapel SET kode_mapel = ?, nama_mapel = ?, kelompok = ? WHERE id = ?");
                    $stmt->execute([$kode_mapel, $nama_mapel, $kelompok, $id]);
                } else {
                    $stmt = $db->prepare("INSERT INTO mapel (kode_mapel, nama_mapel, kelompok) VALUES (?, ?, ?)");
                    $stmt->execute([$kode_mapel, $nama_mapel, $kelompok]);
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = "Gagal menyimpan: Kode Mapel '" . htmlspecialchars($kode_mapel) . "' sudah terdaftar!";
            }
        }
        Helper::redirect('/siakad/mapel');
    }
    public function mapelDelete() {
        $db = Database::connect();
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM mapel WHERE id = ?");
            $stmt->execute([$id]);
        }
        Helper::redirect('/siakad/mapel');
    }

    // --- 4. Data Kelas ---
    public function kelas() {
        $db = Database::connect();
        $kelasList = $db->query("SELECT k.*, (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id = k.id AND s.status = 'Aktif') as total_siswa FROM kelas k ORDER BY k.tingkat ASC, k.nama_kelas ASC")->fetchAll();

        $active_year = AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        $stmt_wali = $db->prepare("SELECT gt.keterangan as kelas_nama, g.id as guru_id, g.nama as guru_nama, g.foto as guru_foto FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.tahun_ajaran = ?");
        $stmt_wali->execute([$active_year_name]);
        $walis = $stmt_wali->fetchAll();
        
        $wali_map = [];
        foreach($walis as $w) {
            $wali_map[$w['kelas_nama']] = $w;
        }

        $gurus = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();

        $title = "Data Rombel Kelas - SIAKAD MTs RS";
        $activeMenu = 'siakad_kelas';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/kelas.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function kelasSave() {
        $db = Database::connect();
        $nama_kelas = trim($_POST['nama_kelas'] ?? '');
        $tingkat = intval($_POST['tingkat'] ?? 7);
        $wali_kelas_id = trim($_POST['wali_kelas_id'] ?? '');

        if (!empty($nama_kelas)) {
            $stmt = $db->prepare("INSERT INTO kelas (nama_kelas, tingkat) VALUES (?, ?)");
            $stmt->execute([$nama_kelas, $tingkat]);

            if (!empty($wali_kelas_id)) {
                $active_year = AcademicYear::current();
                $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
                $kelas_nama_format = "Kelas " . $nama_kelas;

                $stmt = $db->prepare("DELETE FROM guru_tugas WHERE tugas = 'Wali Kelas' AND keterangan = ? AND tahun_ajaran = ?");
                $stmt->execute([$kelas_nama_format, $active_year_name]);

                $stmt = $db->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, 'Wali Kelas', ?, ?)");
                $stmt->execute([$wali_kelas_id, $kelas_nama_format, $active_year_name]);
            }
        }

        Helper::redirect('/siakad/kelas');
    }

    public function kelasUpdate() {
        $db = Database::connect();
        $id = intval($_POST['id'] ?? 0);
        $nama_kelas = trim($_POST['nama_kelas'] ?? '');
        $tingkat = intval($_POST['tingkat'] ?? 7);
        $wali_kelas_id = trim($_POST['wali_kelas_id'] ?? '');

        if ($id > 0 && !empty($nama_kelas)) {
            $stmt = $db->prepare("UPDATE kelas SET nama_kelas = ?, tingkat = ? WHERE id = ?");
            $stmt->execute([$nama_kelas, $tingkat, $id]);

            $active_year = AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            $kelas_nama_format = "Kelas " . $nama_kelas;

            $stmt = $db->prepare("DELETE FROM guru_tugas WHERE tugas = 'Wali Kelas' AND keterangan = ? AND tahun_ajaran = ?");
            $stmt->execute([$kelas_nama_format, $active_year_name]);

            if (!empty($wali_kelas_id)) {
                $stmt = $db->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, 'Wali Kelas', ?, ?)");
                $stmt->execute([$wali_kelas_id, $kelas_nama_format, $active_year_name]);
            }
        }

        Helper::redirect('/siakad/kelas');
    }

    public function kelasDelete($id) {
        $db = Database::connect();
        $id = intval($id);
        if ($id > 0) {
            $db->exec("DELETE FROM kelas WHERE id = $id");
        }
        Helper::redirect('/siakad/kelas');
    }

    public function siswaEdit($id) {
        $db = Database::connect();
        
        $siswa = $db->query("SELECT s.*, u.username FROM siswa s LEFT JOIN users u ON s.user_id = u.id WHERE s.id = " . intval($id))->fetch();
        if (!$siswa) {
            Helper::redirect('/siakad/siswa');
        }

        $kelas = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        
        $selected_kelas = $_GET['kelas_id'] ?? '';
        $sql = "SELECT s.*, k.nama_kelas, u.qr_token FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id LEFT JOIN users u ON s.user_id = u.id WHERE s.status = 'Aktif'";
        if (!empty($selected_kelas) && $selected_kelas !== 'all') {
            $sql .= " AND s.kelas_id = " . intval($selected_kelas);
        }
        $sql .= " ORDER BY k.nama_kelas ASC, s.nama ASC";
        $siswas = $db->query($sql)->fetchAll();
        
        $activeYear = AcademicYear::current();
        $active_year_name = $activeYear ? $activeYear['name'] : '2025/2026';

        $title = "Edit Siswa - SIAKAD MTs RS";
        $activeMenu = 'siakad_siswa';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/siswa_edit.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function siswaDelete($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
        }
        if ($id) {
            $db = Database::connect();
            $siswa = $db->query("SELECT user_id, foto FROM siswa WHERE id = " . intval($id))->fetch();
            if ($siswa) {
                if ($siswa['foto'] && file_exists(__DIR__ . '/../../../public/uploads/siswa/' . $siswa['foto'])) {
                    unlink(__DIR__ . '/../../../public/uploads/siswa/' . $siswa['foto']);
                }
                $db->prepare("DELETE FROM siswa WHERE id = ?")->execute([$id]);
                if ($siswa['user_id']) {
                    $db->prepare("DELETE FROM users WHERE id = ?")->execute([$siswa['user_id']]);
                }
            }
        }
        Helper::redirect('/siakad/siswa');
    }

    public function siswaCetakKartu() {
        $db = Database::connect();
        $query = "SELECT s.*, u.username, u.qr_token, k.nama_kelas FROM siswa s JOIN users u ON s.user_id = u.id LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.status = 'Aktif'";
        if (isset($_GET['kelas_id']) && !empty($_GET['kelas_id'])) {
            $kelas_id = (int)$_GET['kelas_id'];
            $query .= " AND s.kelas_id = $kelas_id";
        }
        $query .= " ORDER BY s.nama ASC";
        $siswas = $db->query($query)->fetchAll();
        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $kelasList = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'horizontal';
        
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/siswa_cetak_kartu_view.php';
        echo ob_get_clean();
        exit;
    }

    public function siswaCetakKartuMassal() {
        $db = Database::connect();
        $siswas = $db->query("SELECT s.*, u.username, u.qr_token FROM siswa s JOIN users u ON s.user_id = u.id WHERE s.status = 'Aktif' ORDER BY s.nama ASC")->fetchAll();
        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'horizontal';
        
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/siswa_cetak_kartu_view.php';
        echo ob_get_clean();
        exit;
    }

    public function siswaCetakKartuBulk() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $db = Database::connect();
            $ids_raw = $_POST['ids'];
            $ids = is_array($ids_raw) ? array_map('intval', $ids_raw) : array_map('intval', explode(',', $ids_raw));
            $ids_str = implode(',', $ids);
            
            $siswas = $db->query("SELECT s.*, u.username, u.qr_token FROM siswa s JOIN users u ON s.user_id = u.id WHERE s.id IN ($ids_str) ORDER BY s.nama ASC")->fetchAll();
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
            $mode = isset($_GET['mode']) ? $_GET['mode'] : 'horizontal';
            
            ob_start();
            include __DIR__ . '/../../resources/views/siakad/siswa_cetak_kartu_view.php';
            echo ob_get_clean();
            exit;
        }
        Helper::redirect('/siakad/siswa');
    }

    public function siswaDeleteBulk() {
        if (!empty($_POST['ids']) && is_array($_POST['ids'])) {
            $db = Database::connect();
            $ids = array_map('intval', $_POST['ids']);
            $idList = implode(',', $ids);
            
            $siswas = $db->query("SELECT user_id, foto FROM siswa WHERE id IN ($idList)")->fetchAll();
            foreach ($siswas as $s) {
                if ($s['foto'] && file_exists(__DIR__ . '/../../../public/uploads/siswa/' . $s['foto'])) {
                    unlink(__DIR__ . '/../../../public/uploads/siswa/' . $s['foto']);
                }
                if ($s['user_id']) {
                    $db->prepare("DELETE FROM users WHERE id = ?")->execute([$s['user_id']]);
                }
            }
            $db->exec("DELETE FROM siswa WHERE id IN ($idList)");
        }
        Helper::redirect('/siakad/siswa');
    }

    public function siswaResetPasswordBulk() {
        if (!empty($_POST['ids']) && is_array($_POST['ids'])) {
            $db = Database::connect();
            $ids = array_map('intval', $_POST['ids']);
            $idList = implode(',', $ids);
            
            $siswas = $db->query("SELECT user_id, nik, nama FROM siswa WHERE id IN ($idList)")->fetchAll();
            $successCount = 0;
            foreach ($siswas as $s) {
                if ($s['user_id'] && !empty($s['nik'])) {
                    $nik = $s['nik'];
                    $passwordHash = password_hash($nik, PASSWORD_BCRYPT);
                    $db->prepare("UPDATE users SET username = ?, password_hash = ? WHERE id = ?")->execute([$nik, $passwordHash, $s['user_id']]);
                    $successCount++;
                }
            }
            $_SESSION['flash_success'] = "Berhasil mereset username dan password $successCount siswa menjadi NIK.";
        }
        Helper::redirect('/siakad/siswa');
    }


    public function kelasAssignWali() {
        $db = Database::connect();
        $nama_kelas = trim($_POST['nama_kelas'] ?? '');
        $guru_id = trim($_POST['guru_id'] ?? '');
        
        if (!empty($nama_kelas)) {
            $active_year = AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            
            $stmt = $db->prepare("DELETE FROM guru_tugas WHERE tugas = 'Wali Kelas' AND keterangan = ? AND tahun_ajaran = ?");
            $stmt->execute([$nama_kelas, $active_year_name]);
            
            if (!empty($guru_id)) {
                $stmt = $db->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, 'Wali Kelas', ?, ?)");
                $stmt->execute([$guru_id, $nama_kelas, $active_year_name]);
            }
        }
        
        Helper::redirect('/siakad/kelas');
    }

    // --- 5. Data Ekstrakurikuler ---
    public function ekstra() {
        $db = Database::connect();
        $ekstraList = $db->query("SELECT e.*, g.nama as nama_pembina FROM ekstrakurikuler e LEFT JOIN guru g ON e.pembina_id = g.id ORDER BY e.nama_ekstra ASC")->fetchAll();
        $guruList = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll();

        $title = "Data Ekstrakurikuler - SIAKAD MTs RS";
        $activeMenu = 'siakad_ekstra';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/ekstra.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function ekstraSave() {
        $db = Database::connect();
        $nama_ekstra = trim($_POST['nama_ekstra'] ?? '');
        $pembina_id = intval($_POST['pembina_id'] ?? 0);
        $hari = $_POST['hari'] ?? 'Sabtu';
        $jam_kegiatan = trim($_POST['jam_kegiatan'] ?? '14:00 - 16:00');

        if (!empty($nama_ekstra)) {
            $stmt = $db->prepare("INSERT INTO ekstrakurikuler (nama_ekstra, pembina_id, hari, jam_kegiatan) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama_ekstra, $pembina_id, $hari, $jam_kegiatan]);
        }

        Helper::redirect('/siakad/ekstra');
    }

    public function kopSurat() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        $db = Database::connect();
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        $activeMenu = 'siakad_kop_surat';
        $title = 'Pengaturan Kop Surat - SIAKAD';
        
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/kop_surat.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../resources/views/layout.php';
    }

    public function kopSuratSave() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login'); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::connect();
            
            $yayasan = $_POST['yayasan'] ?? '';
            $nama = $_POST['nama'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $desa = $_POST['desa'] ?? '';
            $kecamatan = $_POST['kecamatan'] ?? '';
            $kota = $_POST['kota'] ?? '';
            $provinsi = $_POST['provinsi'] ?? '';
            $kodepos = $_POST['kodepos'] ?? '';
            $website = $_POST['website'] ?? '';
            $email = $_POST['email'] ?? '';
            $kop_font_yayasan = isset($_POST['kop_font_yayasan']) ? intval($_POST['kop_font_yayasan']) : 14;
            $kop_font_nama = isset($_POST['kop_font_nama']) ? intval($_POST['kop_font_nama']) : 18;
            
            $sql = "UPDATE institusi SET 
                    yayasan = ?, nama = ?, alamat = ?, desa = ?, kecamatan = ?, kota = ?, provinsi = ?, kodepos = ?, website = ?, email = ?,
                    kop_font_yayasan = ?, kop_font_nama = ?
                    WHERE id = 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$yayasan, $nama, $alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos, $website, $email, $kop_font_yayasan, $kop_font_nama]);
            
            $_SESSION['flash_message'] = "Pengaturan Kop Surat berhasil disimpan!";
            header('Location: /siakad/kop-surat');
            exit;
        }
    }

    // --- 6. Data Guru & Manajemen Guru ---
    public function guru() {
        $db = Database::connect();
        $guruList = $db->query("SELECT g.*, u.username, u.qr_token FROM guru g LEFT JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC")->fetchAll();

        $title = "Data Guru - SIAKAD MTs RS";
        $activeMenu = 'siakad_guru';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/guru.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function guruSave() {
        $db = Database::connect();
        $id = $_POST['id'] ?? null;
        $nip = trim($_POST['nip'] ?? '');
        $niy = trim($_POST['niy'] ?? '');
        $nik = trim($_POST['nik'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $jk = $_POST['jenis_kelamin'] ?? 'L';
        $jabatan = trim($_POST['jabatan'] ?? 'Guru Pengajar');
        $email = trim($_POST['email'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        
        $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
        $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null;
        $alamat = trim($_POST['alamat'] ?? '');
        $provinsi = trim($_POST['provinsi'] ?? '');
        $kota = trim($_POST['kota'] ?? '');
        $kecamatan = trim($_POST['kecamatan'] ?? '');
        $desa = trim($_POST['desa'] ?? '');
        $rt = trim($_POST['rt'] ?? '');
        $rw = trim($_POST['rw'] ?? '');
        
        $status_kepegawaian = trim($_POST['status_kepegawaian'] ?? 'Honorer');
        $npk = trim($_POST['npk'] ?? '');
        $npwp = trim($_POST['npwp'] ?? '');
        $no_rekening = trim($_POST['no_rekening'] ?? '');
        $kode_pos = trim($_POST['kode_pos'] ?? '');
        
        $nrg = trim($_POST['nrg'] ?? '');
        $no_peserta_sertifikasi = trim($_POST['no_peserta_sertifikasi'] ?? '');
        $no_sertifikat = trim($_POST['no_sertifikat'] ?? '');
        $tgl_sertifikat = !empty($_POST['tgl_sertifikat']) ? $_POST['tgl_sertifikat'] : null;
        $jenjang_sertifikat = trim($_POST['jenjang_sertifikat'] ?? '');
        $mapel_sertifikat = trim($_POST['mapel_sertifikat'] ?? '');
        
        $is_kamad = isset($_POST['is_kamad']) ? 1 : 0;

        if (empty($nama)) {
            Helper::redirect('/siakad/guru');
        }
        
        if (empty($id)) {
            $username = !empty($nik) ? $nik : 'guru_' . time();
            $passwordHash = password_hash(!empty($nik) ? $nik : 'guru123', PASSWORD_BCRYPT);
            $qrToken = uniqid('MTSRS-G-', true) . bin2hex(random_bytes(4));

            $stmtUser = $db->prepare("INSERT INTO users (username, password_hash, role_id, qr_token, status) VALUES (?, ?, 2, ?, 'active')");
            $stmtUser->execute([$username, $passwordHash, $qrToken]);
            $userId = $db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO guru (user_id, nip, niy, nik, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, telepon, email, jabatan, alamat, provinsi, kota, kecamatan, desa, rt, rw, status_kepegawaian, npk, npwp, no_rekening, kode_pos, nrg, no_peserta_sertifikasi, no_sertifikat, tgl_sertifikat, jenjang_sertifikat, mapel_sertifikat, is_kamad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $nip, $niy, $nik, $nama, $jk, $tempat_lahir, $tanggal_lahir, $telepon, $email, $jabatan, $alamat, $provinsi, $kota, $kecamatan, $desa, $rt, $rw, $status_kepegawaian, $npk, $npwp, $no_rekening, $kode_pos, $nrg, $no_peserta_sertifikasi, $no_sertifikat, $tgl_sertifikat, $jenjang_sertifikat, $mapel_sertifikat, $is_kamad]);
            $id = $db->lastInsertId();
            Helper::logActivity('SIAKAD', 'INSERT', "Menambahkan Guru baru: $nama");
        } else {
            $stmt = $db->prepare("UPDATE guru SET nip = ?, niy = ?, nik = ?, nama = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, telepon = ?, email = ?, jabatan = ?, alamat = ?, provinsi = ?, kota = ?, kecamatan = ?, desa = ?, rt = ?, rw = ?, status_kepegawaian = ?, npk = ?, npwp = ?, no_rekening = ?, kode_pos = ?, nrg = ?, no_peserta_sertifikasi = ?, no_sertifikat = ?, tgl_sertifikat = ?, jenjang_sertifikat = ?, mapel_sertifikat = ?, is_kamad = ? WHERE id = ?");
            $stmt->execute([$nip, $niy, $nik, $nama, $jk, $tempat_lahir, $tanggal_lahir, $telepon, $email, $jabatan, $alamat, $provinsi, $kota, $kecamatan, $desa, $rt, $rw, $status_kepegawaian, $npk, $npwp, $no_rekening, $kode_pos, $nrg, $no_peserta_sertifikasi, $no_sertifikat, $tgl_sertifikat, $jenjang_sertifikat, $mapel_sertifikat, $is_kamad, $id]);
            
            // Update username jika NIK berubah (opsional, karena NIK adalah kredensial)
            $guru = $db->query("SELECT user_id FROM guru WHERE id = " . intval($id))->fetch();
            if ($guru && $guru['user_id'] && !empty($nik)) {
                $db->prepare("UPDATE users SET username = ? WHERE id = ?")->execute([$nik, $guru['user_id']]);
            }
            Helper::logActivity('SIAKAD', 'UPDATE', "Update data profil Guru: $nama");
        }

        // Handle foto upload
        if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] == 0) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $fotoName = 'guru_' . time() . '_' . uniqid() . '.' . $ext;
            $thumbName = 'guru_' . time() . '_' . uniqid() . '_thumb.' . $ext;
            $uploadPath = __DIR__ . '/../../public/uploads/guru/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
            if(move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath . $fotoName)) {
                $this->createThumbnail($uploadPath . $fotoName, $uploadPath . $thumbName, 150, 150);
                $stmt = $db->prepare("UPDATE guru SET foto = ? WHERE id = ?");
                $stmt->execute([$fotoName, $id]);
            }
        }

        Helper::redirect('/siakad/guru');
    }

    public function guruUpdateFoto() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = intval($_POST['id'] ?? 0);
                $db = Database::connect();
                if ($id > 0 && isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $old = $db->query("SELECT foto FROM guru WHERE id = $id")->fetch();
                    if ($old && $old['foto'] && file_exists(__DIR__ . "/../../public/uploads/guru/" . $old['foto'])) {
                        @unlink(__DIR__ . "/../../public/uploads/guru/" . $old['foto']);
                        $oldThumb = pathinfo($old['foto'], PATHINFO_FILENAME) . '_thumb.' . pathinfo($old['foto'], PATHINFO_EXTENSION);
                        if (file_exists(__DIR__ . "/../../public/uploads/guru/" . $oldThumb)) {
                            @unlink(__DIR__ . "/../../public/uploads/guru/" . $oldThumb);
                        }
                    }
                    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $foto_name = "guru_" . time() . "_" . rand(100, 999) . "." . $ext;
                    $thumb_name = pathinfo($foto_name, PATHINFO_FILENAME) . '_thumb.' . $ext;
                    $upload_dir = __DIR__ . "/../../public/uploads/guru/";
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name)) {
                        $this->createThumbnail($upload_dir . $foto_name, $upload_dir . $thumb_name, 150, 150);
                        
                        $stmt = $db->prepare("UPDATE guru SET foto = ? WHERE id = ?");
                        $stmt->execute([$foto_name, $id]);
                        echo json_encode(['success' => true, 'foto' => $foto_name, 'thumb' => $thumb_name]);
                        exit;
                    }
                }
                echo json_encode(['success' => false, 'message' => 'Upload gagal']);
            } catch (\Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
            exit;
        }
    }

    public function guruDeleteBulk() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $db = Database::connect();
            $ids_raw = $_POST['ids'];
            $ids = is_array($ids_raw) ? array_map('intval', $ids_raw) : array_map('intval', explode(',', $ids_raw));
            $ids_str = implode(',', $ids);
            
            $gurus = $db->query("SELECT user_id, foto FROM guru WHERE id IN ($ids_str)")->fetchAll();
            $userIds = [];
            foreach ($gurus as $g) {
                if ($g['user_id']) $userIds[] = $g['user_id'];
                if ($g['foto'] && file_exists(__DIR__ . '/../../public/uploads/guru/' . $g['foto'])) {
                    @unlink(__DIR__ . '/../../public/uploads/guru/' . $g['foto']);
                }
            }
            
            $db->exec("DELETE FROM penugasan_mengajar WHERE guru_id IN ($ids_str)");
            $db->exec("DELETE FROM guru_tugas WHERE guru_id IN ($ids_str)");
            $db->exec("DELETE FROM guru_tugas_tambahan WHERE guru_id IN ($ids_str)");
            $db->exec("DELETE FROM jadwal_pelajaran WHERE guru_id IN ($ids_str)");
            $db->exec("DELETE FROM absensi_guru WHERE guru_id IN ($ids_str)");
            $db->exec("DELETE FROM jurnal_guru WHERE guru_id IN ($ids_str)");
            $db->exec("DELETE FROM rapat_peserta WHERE guru_id IN ($ids_str)");

            try {
                $db->exec("DELETE FROM guru WHERE id IN ($ids_str)");
                if (!empty($userIds)) {
                    $uIdsStr = implode(',', $userIds);
                    $db->exec("DELETE FROM users WHERE id IN ($uIdsStr)");
                }
                Helper::logActivity('SIAKAD', 'DELETE', "Menghapus " . count($ids) . " data Guru (Bulk Delete).");
            } catch (\PDOException $e) {
                die("Gagal menghapus guru terpilih. Pesan error: " . $e->getMessage());
            }
        }
        Helper::redirect('/siakad/guru');
    }

    public function guruCetakKartuMassal() {
        $db = Database::connect();
        $gurus = $db->query("SELECT g.*, u.username, u.qr_token FROM guru g JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC")->fetchAll();
        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'horizontal';
        
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/guru_cetak_kartu_view.php';
        echo ob_get_clean();
        exit;
    }

    public function guruCetakKartuBulk() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $db = Database::connect();
            $ids_raw = $_POST['ids'];
            $ids = is_array($ids_raw) ? array_map('intval', $ids_raw) : array_map('intval', explode(',', $ids_raw));
            $ids_str = implode(',', $ids);
            
            $gurus = $db->query("SELECT g.*, u.username, u.qr_token FROM guru g JOIN users u ON g.user_id = u.id WHERE g.id IN ($ids_str) ORDER BY g.nama ASC")->fetchAll();
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
            $mode = isset($_GET['mode']) ? $_GET['mode'] : 'horizontal';
            
            ob_start();
            include __DIR__ . '/../../resources/views/siakad/guru_cetak_kartu_view.php';
            echo ob_get_clean();
            exit;
        }
        Helper::redirect('/siakad/guru');
    }

    public function guruImport() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paste_data'])) {
            $data_raw = trim($_POST['paste_data']);
            $lines = explode("\n", $data_raw);
            $db = Database::connect();
            
            $successCount = 0;
            $errors = [];
            
            foreach ($lines as $index => $line) {
                if (stripos($line, '1. NIK') !== false || stripos($line, 'Nama Lengkap') !== false) continue;
                
                $line = trim($line, "\r\n");
                if (empty(trim($line))) continue;

                $cols = explode("\t", $line);
                if (count($cols) < 3) {
                    $errors[] = "Baris " . ($index + 1) . ": Tidak lengkap";
                    continue;
                }
                
                $nik = trim($cols[0] ?? '');
                $nip = trim($cols[1] ?? '');
                $nama = trim($cols[2] ?? '');
                $jk = trim($cols[3] ?? 'L');
                $tempat_lahir = trim($cols[4] ?? '');
                $tanggal_lahir = trim($cols[5] ?? null);
                $telepon = trim($cols[6] ?? '');
                $email = trim($cols[7] ?? '');
                $jabatan = trim($cols[8] ?? 'Guru Pengajar');
                $status_kepegawaian = trim($cols[9] ?? 'Honorer');
                $npk = trim($cols[10] ?? '');
                $npwp = trim($cols[11] ?? '');
                $no_rekening = trim($cols[12] ?? '');
                $nrg = trim($cols[13] ?? '');
                $alamat = trim($cols[14] ?? '');
                $rt = trim($cols[15] ?? '');
                $rw = trim($cols[16] ?? '');
                $provinsi = trim($cols[17] ?? '');
                $kota = trim($cols[18] ?? '');
                $kecamatan = trim($cols[19] ?? '');
                $desa = trim($cols[20] ?? '');
                $kode_pos = trim($cols[21] ?? '');
                $no_peserta_sertifikasi = trim($cols[22] ?? '');
                $no_sertifikat = trim($cols[23] ?? '');
                $tgl_sertifikat = trim($cols[24] ?? null);
                $jenjang_sertifikat = trim($cols[25] ?? '');
                $mapel_sertifikat = trim($cols[26] ?? '');
                
                if (empty($nik) || empty($nama)) {
                    $errors[] = "Baris " . ($index + 1) . ": NIK/Nama kosong";
                    continue;
                }

                $check = $db->prepare("SELECT id FROM users WHERE username = ?");
                $check->execute([$nik]);
                if ($check->fetch()) {
                    $errors[] = "Baris " . ($index + 1) . ": NIK ($nik) sudah ada";
                    continue;
                }

                try {
                    $db->beginTransaction();
                    $username = $nik;
                    $passwordHash = password_hash($nik, PASSWORD_BCRYPT);
                    $qrToken = uniqid('MTSRS-G-', true) . bin2hex(random_bytes(4));

                    $stmt = $db->prepare("INSERT INTO users (username, password_hash, role_id, qr_token, status) VALUES (?, ?, 2, ?, 'active')");
                    $stmt->execute([$username, $passwordHash, $qrToken]);
                    $userId = $db->lastInsertId();
                    
                    $sqlInsert = "INSERT INTO guru (
                        user_id, nik, nip, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, telepon, email, 
                        jabatan, status_kepegawaian, npk, npwp, no_rekening, nrg, alamat, rt, rw, 
                        provinsi, kota, kecamatan, desa, kode_pos, no_peserta_sertifikasi, no_sertifikat, 
                        tgl_sertifikat, jenjang_sertifikat, mapel_sertifikat
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?
                    )";
                    $stmt = $db->prepare($sqlInsert);
                    $tanggal_lahir = !empty($tanggal_lahir) ? $tanggal_lahir : null;
                    $tgl_sertifikat = !empty($tgl_sertifikat) ? $tgl_sertifikat : null;
                    
                    $stmt->execute([
                        $userId, $nik, $nip, $nama, $jk, $tempat_lahir, $tanggal_lahir, $telepon, $email,
                        $jabatan, $status_kepegawaian, $npk, $npwp, $no_rekening, $nrg, $alamat, $rt, $rw,
                        $provinsi, $kota, $kecamatan, $desa, $kode_pos, $no_peserta_sertifikasi, $no_sertifikat,
                        $tgl_sertifikat, $jenjang_sertifikat, $mapel_sertifikat
                    ]);
                    $db->commit();
                    $successCount++;
                } catch (\Exception $e) {
                    $db->rollBack();
                    $errors[] = "Baris " . ($index + 1) . ": Error DB (" . $e->getMessage() . ")";
                }
            }

            if ($successCount > 0) {
                $_SESSION['flash_success'] = "Berhasil mengimpor $successCount data guru.";
            }
            if (!empty($errors)) {
                $_SESSION['flash_error'] = "Gagal mengimpor beberapa data:<br>" . implode("<br>", array_slice($errors, 0, 5)) . (count($errors) > 5 ? "<br>...dan " . (count($errors) - 5) . " error lainnya." : "");
            }
        }
        Helper::redirect('/siakad/guru');
    }

    public function guruTemplateImport() {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Template_Import_Guru_27Kolom.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta charset="UTF-8"></head><body>';
        echo '<table border="1">';
        echo '<tr style="background:#f1f5f9;font-weight:bold;">';
        $headers = [
            'NIK', 'NIP', 'Nama Lengkap', 'L/P', 'Tempat Lahir', 'Tgl Lahir (YYYY-MM-DD)', 
            'No HP', 'Email', 'Jabatan', 'Status Pegawai', 'NPK', 'NPWP', 'No Rekening', 'NRG',
            'Alamat Jalan', 'RT', 'RW', 'Provinsi', 'Kab/Kota', 'Kecamatan', 'Desa', 'Kode Pos',
            'No Peserta Sertifikasi', 'No Sertifikat', 'Tgl Sertifikat (YYYY-MM-DD)', 'Jenjang', 'Mapel Sertifikasi'
        ];
        foreach($headers as $h) echo "<td>{$h}</td>";
        echo '</tr>';
        echo '<tr>';
        $dummy = [
            '3201234567890001', '198001012010011001', 'Budi Santoso', 'L', 'Jakarta', '1980-05-20',
            '081234567890', 'budi@sekolah.id', 'Guru Matematika', 'PNS', '', '', '', '',
            'Jl. Merdeka No 1', '01', '02', 'Jawa Barat', 'Bogor', 'Cibinong', 'Cibinong', '16911',
            '', '', '', '', ''
        ];
        foreach($dummy as $d) echo "<td>{$d}</td>";
        echo '</tr>';
        echo '</table></body></html>';
        exit;
    }

    public function guruExport() {
        $db = Database::connect();
        $gurus = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll();
        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        echo "<!DOCTYPE html><html><head><title>Data Guru</title></head><body onload='window.print()'>
        <center><h2>DAFTAR GURU " . strtoupper($inst['nama']) . "</h2></center>
        <table border='1' cellpadding='5' cellspacing='0' width='100%'>
        <tr><th>No</th><th>NIP/NIK</th><th>Nama</th><th>Jabatan</th><th>No HP</th><th>Alamat</th></tr>";
        $no = 1;
        foreach($gurus as $g) {
            echo "<tr><td align='center'>".$no++."</td><td>{$g['nip']}<br><small>{$g['nik']}</small></td><td>{$g['nama']}</td><td>{$g['jabatan']}</td><td>{$g['telepon']}</td><td>{$g['alamat']}</td></tr>";
        }
        echo "</table></body></html>";
        exit;
    }

    public function guruDelete($id) {
        $db = Database::connect();
        $id = intval($id);

        if ($id > 0) {
            $guru = $db->query("SELECT user_id, foto FROM guru WHERE id = $id")->fetch();
            
            if ($guru) {
                if (!empty($guru['foto']) && file_exists(__DIR__ . '/../../public/uploads/guru/' . $guru['foto'])) {
                    @unlink(__DIR__ . '/../../public/uploads/guru/' . $guru['foto']);
                }
                
                try {
                    $db->prepare("DELETE FROM penugasan_mengajar WHERE guru_id = ?")->execute([$id]);
                    $db->prepare("DELETE FROM guru_tugas WHERE guru_id = ?")->execute([$id]);
                    $db->prepare("DELETE FROM guru_tugas_tambahan WHERE guru_id = ?")->execute([$id]);
                    $db->prepare("DELETE FROM jadwal_pelajaran WHERE guru_id = ?")->execute([$id]);
                    $db->prepare("DELETE FROM absensi_guru WHERE guru_id = ?")->execute([$id]);
                    $db->prepare("DELETE FROM jurnal_guru WHERE guru_id = ?")->execute([$id]);
                    $db->prepare("DELETE FROM rapat_peserta WHERE guru_id = ?")->execute([$id]);
                    
                    $db->prepare("DELETE FROM guru WHERE id = ?")->execute([$id]);
                    if (!empty($guru['user_id'])) {
                        $db->prepare("DELETE FROM users WHERE id = ?")->execute([$guru['user_id']]);
                    }
                } catch (\PDOException $e) {
                    die("Gagal menghapus data guru. Pesan error: " . $e->getMessage());
                }
            }
        }

        Helper::redirect('/siakad/guru');
    }

    public function guruEdit($id) {
        $db = Database::connect();
        
        $guru = $db->query("SELECT g.*, u.username FROM guru g LEFT JOIN users u ON g.user_id = u.id WHERE g.id = " . intval($id))->fetch();
        if (!$guru) {
            Helper::redirect('/siakad/guru');
        }

        $activeYear = AcademicYear::current();
        $active_year_name = $activeYear ? $activeYear['name'] : '2025/2026';

        $title = "Edit Guru - SIAKAD MTs RS";
        $activeMenu = 'siakad_guru';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/guru_edit.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function masterTugas() {
        $db = Database::connect();
        $masterList = $db->query("SELECT * FROM master_tugas_tambahan ORDER BY nama_tugas ASC")->fetchAll();
        $title = "Master Tugas Tambahan - SIAKAD MTs RS";
        $activeMenu = 'siakad_master_tugas';
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/master_tugas.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function masterTugasSave() {
        $db = Database::connect();
        $id = intval($_POST['id'] ?? 0);
        $nama_tugas = trim($_POST['nama_tugas'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');

        if (!empty($nama_tugas)) {
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE master_tugas_tambahan SET nama_tugas = ?, keterangan = ? WHERE id = ?");
                $stmt->execute([$nama_tugas, $keterangan, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO master_tugas_tambahan (nama_tugas, keterangan) VALUES (?, ?)");
                $stmt->execute([$nama_tugas, $keterangan]);
            }
        }
        Helper::redirect('/siakad/master-tugas');
    }

    public function masterTugasDelete() {
        $db = Database::connect();
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM master_tugas_tambahan WHERE id = ?");
            $stmt->execute([$id]);
        }
        Helper::redirect('/siakad/master-tugas');
    }

    public function guruTugas() {
        $db = Database::connect();
        $tugasList = $db->query("SELECT t.*, g.nama as nama_guru, g.nip FROM guru_tugas_tambahan t JOIN guru g ON t.guru_id = g.id ORDER BY g.nama ASC")->fetchAll();
        $guruList = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll();
        $masterTugas = $db->query("SELECT * FROM master_tugas_tambahan ORDER BY nama_tugas ASC")->fetchAll();

        $title = "Tugas Tambahan Guru - SIAKAD MTs RS";
        $activeMenu = 'siakad_guru_tugas';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/guru_tugas.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function guruTugasSave() {
        $db = Database::connect();
        $id = intval($_POST['id'] ?? 0);
        $guru_id = intval($_POST['guru_id'] ?? 0);
        $jabatan_tugas = trim($_POST['jabatan_tugas'] ?? '');
        $sk_nomor = trim($_POST['sk_nomor'] ?? '');

        if ($guru_id > 0 && !empty($jabatan_tugas)) {
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE guru_tugas_tambahan SET guru_id = ?, jabatan_tugas = ?, sk_nomor = ? WHERE id = ?");
                $stmt->execute([$guru_id, $jabatan_tugas, $sk_nomor, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO guru_tugas_tambahan (guru_id, jabatan_tugas, sk_nomor) VALUES (?, ?, ?)");
                $stmt->execute([$guru_id, $jabatan_tugas, $sk_nomor]);
            }
        }

        Helper::redirect('/siakad/guru/tugas');
    }

    public function guruMengajar() {
        $db = Database::connect();
        $selected_kelas = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : null;

        $kelasList = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        $guruList = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll();
        $mapelList = $db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        $assignments = [];
        if ($selected_kelas) {
            $existing = $db->prepare("SELECT mapel_id, guru_id FROM penugasan_mengajar WHERE kelas_id = ?");
            $existing->execute([$selected_kelas]);
            foreach ($existing->fetchAll() as $row) {
                $assignments[$row['mapel_id']] = $row['guru_id'];
            }
        }

        $title = "Penugasan Mengajar - SIAKAD MTs RS";
        $activeMenu = 'siakad_guru_mengajar';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/guru_mengajar.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function guruMengajarSave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guru']) && isset($_POST['kelas_id'])) {
            $db = Database::connect();
            $kelas_id = $_POST['kelas_id'];
            
            foreach ($_POST['guru'] as $mapel_id => $guru_id) {
                $guru_id = !empty($guru_id) ? $guru_id : null;
                
                if ($guru_id) {
                    $cek = $db->prepare("SELECT id FROM penugasan_mengajar WHERE kelas_id = ? AND mapel_id = ?");
                    $cek->execute([$kelas_id, $mapel_id]);
                    
                    if ($cek->fetch()) {
                        $stmt = $db->prepare("UPDATE penugasan_mengajar SET guru_id = ?, jumlah_jam = 2 WHERE kelas_id = ? AND mapel_id = ?");
                        $stmt->execute([$guru_id, $kelas_id, $mapel_id]);
                        
                        // Sync otomatis ke jadwal pelajaran jika sudah ada jadwal
                        $sync = $db->prepare("UPDATE jadwal_pelajaran SET guru_id = ? WHERE kelas_id = ? AND mapel_id = ?");
                        $sync->execute([$guru_id, $kelas_id, $mapel_id]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO penugasan_mengajar (kelas_id, mapel_id, guru_id, jumlah_jam) VALUES (?, ?, ?, 2)");
                        $stmt->execute([$kelas_id, $mapel_id, $guru_id]);
                        
                        // Sync otomatis ke jadwal pelajaran jika sudah ada jadwal (untuk berjaga-jaga)
                        $sync = $db->prepare("UPDATE jadwal_pelajaran SET guru_id = ? WHERE kelas_id = ? AND mapel_id = ?");
                        $sync->execute([$guru_id, $kelas_id, $mapel_id]);
                    }
                } else {
                    $stmt = $db->prepare("DELETE FROM penugasan_mengajar WHERE kelas_id = ? AND mapel_id = ?");
                    $stmt->execute([$kelas_id, $mapel_id]);
                }
            }
        }
        $redirect = isset($_POST['kelas_id']) ? '?kelas_id=' . $_POST['kelas_id'] : '';
        Helper::redirect('/siakad/guru/mengajar' . $redirect);
    }

    public function updateJurnalSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::connect();
            $mode = isset($_POST['strict_jurnal_mode']) ? 1 : 0;
            $grace = isset($_POST['jurnal_grace_period']) ? intval($_POST['jurnal_grace_period']) : 30;
            
            $db->prepare("UPDATE institusi SET strict_jurnal_mode = ?, jurnal_grace_period = ?")->execute([$mode, $grace]);
            
            $_SESSION['flash_message'] = "Pengaturan Kunci Jurnal Otomatis berhasil diperbarui!";
            $_SESSION['flash_type'] = "success";
            Helper::redirect('/siakad/jurnal-global');
        }
    }

    public function jurnalGlobal() {
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

        $institusi = $db->query("SELECT strict_jurnal_mode, jurnal_grace_period FROM institusi LIMIT 1")->fetch();
        $strict_mode = $institusi['strict_jurnal_mode'] ?? 0;
        $grace_period = $institusi['jurnal_grace_period'] ?? 30;

        $title = "Jurnal Global Guru - SIAKAD MTs RS";
        $activeMenu = 'siakad_jurnal_global';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/jurnal_global.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function jurnalGlobalCetak() {
        $db = Database::connect();
        
        $filter_tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
        $filter_tanggal_sampai = $_GET['tanggal_akhir'] ?? date('Y-m-t'); // note: the frontend uses tanggal_akhir, so keep using it but assign to $filter_tanggal_sampai
        $filter_kelas = $_GET['kelas_id'] ?? ''; // frontend uses kelas_id
        $filter_guru = $_GET['guru_id'] ?? ''; // frontend uses guru_id
        $filter_mapel = $_GET['mapel_id'] ?? ''; // frontend uses mapel_id
        $filter_jenis = $_GET['jenis_jurnal'] ?? ''; // frontend uses jenis_jurnal

        $kelasList = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        $guruList = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll();
        $mapelList = $db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        $kelasMap = [];
        foreach($kelasList as $k) {
            $kelasMap[$k['id']] = $k['nama_kelas'];
        }
        $mapelMap = [];
        $mapelKategoriMap = [];
        foreach($mapelList as $m) {
            $mapelMap[$m['id']] = $m['nama_mapel'];
            $mapelKategoriMap[$m['id']] = $m['kategori_id'] ?? 'Umum';
        }

        $sql_where = " WHERE j.tanggal >= :tgl_mulai AND j.tanggal <= :tgl_sampai ";
        $params = [
            'tgl_mulai' => $filter_tanggal_mulai,
            'tgl_sampai' => $filter_tanggal_sampai
        ];
        
        if ($filter_kelas > 0) {
            $sql_where .= " AND j.kelas_id = :kid";
            $params['kid'] = $filter_kelas;
        }
        if ($filter_mapel > 0) {
            $sql_where .= " AND j.mapel_id = :mpid";
            $params['mpid'] = $filter_mapel;
        }
        if ($filter_guru > 0) {
            $sql_where .= " AND j.guru_id = :gid";
            $params['gid'] = $filter_guru;
        }
        
        $jurnal_global = [];
        if (!empty($filter_jenis)) {
            $query = "SELECT j.* FROM jurnal_guru j $sql_where ORDER BY j.tanggal ASC, j.id ASC";
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $jurnal_global = $stmt->fetchAll();
        }

        $guruMap = [];
        foreach($guruList as $g) {
            $guruMap[$g['id']] = $g['nama'];
        }

        $ta_row = $db->query("SELECT name, semester FROM tahun_ajaran WHERE is_active = 1")->fetch();
        $active_ta = $ta_row['name'] ?? '2025/2026';
        $active_semester = ($ta_row['semester'] ?? 1) == 1 ? 'Ganjil' : 'Genap';
        
        $nama_wali_kelas = "...................................................";
        if ($filter_kelas > 0 && isset($kelasMap[$filter_kelas])) {
            $nama_k = $kelasMap[$filter_kelas];
            $nama_k_prefix = "Kelas " . $nama_k;
            $stmt_wk = $db->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND (gt.keterangan = ? OR gt.keterangan = ?) AND gt.tahun_ajaran = ? LIMIT 1");
            $stmt_wk->execute([$nama_k, $nama_k_prefix, $active_ta]);
            $wk = $stmt_wk->fetchColumn();
            if ($wk) {
                $nama_wali_kelas = $wk;
            }
        }
        $stmt_kc = $db->prepare("SELECT kelas_id, COUNT(*) as jml FROM siswa WHERE status = 'Aktif' GROUP BY kelas_id");
        $stmt_kc->execute([]);
        $kelasCountList = $stmt_kc->fetchAll();
        $kelasCountMap = [];
        foreach($kelasCountList as $kc) $kelasCountMap[$kc['kelas_id']] = $kc['jml'];

        foreach($jurnal_global as &$j) {
            $j['nama_guru'] = isset($guruMap[$j['guru_id']]) ? $guruMap[$j['guru_id']] : 'Guru ID '.$j['guru_id'];
            $j['nama_kelas'] = isset($kelasMap[$j['kelas_id']]) ? $kelasMap[$j['kelas_id']] : 'Kelas ID '.$j['kelas_id'];
            $j['nama_mapel'] = isset($mapelMap[$j['mapel_id']]) ? $mapelMap[$j['mapel_id']] : 'Mapel ID '.$j['mapel_id'];
            $j['total_siswa'] = isset($kelasCountMap[$j['kelas_id']]) ? $kelasCountMap[$j['kelas_id']] : 0;
            
            // Ambil breakdown absensi
            $stmt_absen = $db->prepare("SELECT status, COUNT(*) as jml FROM absensi_permapel WHERE jurnal_id = ? GROUP BY status");
            $stmt_absen->execute([$j['id']]);
            $absenList = $stmt_absen->fetchAll();
            $j['absen'] = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
            foreach($absenList as $ab) {
                $status = $ab['status'];
                if ($status == 'Alpha' || $status == 'A' || $status == 'Bolos') $status = 'Alpa';
                if (isset($j['absen'][$status])) {
                    $j['absen'][$status] += $ab['jml'];
                }
            }
        }
        unset($j);

        if (($filter_jenis == 'kelas' && $filter_kelas > 0) || ($filter_jenis == 'guru' && $filter_guru > 0)) {
            if ($filter_jenis == 'kelas') {
                $q = "SELECT * FROM jadwal_pelajaran WHERE kelas_id = " . intval($filter_kelas);
                if ($filter_mapel > 0) $q .= " AND mapel_id = " . intval($filter_mapel);
                $jadwalList = $db->query($q . " ORDER BY jam_mulai ASC")->fetchAll();
            } else {
                $q = "SELECT * FROM jadwal_pelajaran WHERE guru_id = " . intval($filter_guru);
                if ($filter_kelas > 0) $q .= " AND kelas_id = " . intval($filter_kelas);
                if ($filter_mapel > 0) $q .= " AND mapel_id = " . intval($filter_mapel);
                $jadwalList = $db->query($q . " ORDER BY jam_mulai ASC")->fetchAll();
            }
            $jadwal_by_hari = [];
            foreach($jadwalList as $jdwl) {
                $kat = isset($mapelKategoriMap[$jdwl['mapel_id']]) ? $mapelKategoriMap[$jdwl['mapel_id']] : 'Umum';
                if (strtolower($kat) === 'umum') {
                    // Deduplicate: hanya 1 jadwal per mapel per kelas per hari
                    $found = false;
                    $hari_jadwal = ucfirst(strtolower(trim($jdwl['hari'])));
                    if ($hari_jadwal === 'Ahad') {
                        $hari_jadwal = 'Minggu';
                    }
                    if (isset($jadwal_by_hari[$hari_jadwal])) {
                        foreach($jadwal_by_hari[$hari_jadwal] as $existing) {
                            if ($existing['mapel_id'] == $jdwl['mapel_id'] && $existing['kelas_id'] == $jdwl['kelas_id']) {
                                $found = true; break;
                            }
                        }
                    }
                    if (!$found) {
                        $jadwal_by_hari[$hari_jadwal][] = $jdwl;
                    }
                }
            }
            
            $liburList = [];
            // Mengambil daftar libur dari kalender pendidikan
            try {
                $kalenderList = $db->query("SELECT * FROM kalender_pendidikan WHERE kategori IN ('Libur', 'Kegiatan', 'Pulang Dipercepat')")->fetchAll();
            } catch (\Exception $e) {
                $kalenderList = [];
            }
            
            $new_jurnal_global = [];
            
            $startDate = new \DateTime($filter_tanggal_mulai);
            $endDate = new \DateTime($filter_tanggal_sampai);
            $endDate->modify('+1 day'); // include the end date in DatePeriod
            
            $interval = new \DateInterval('P1D');
            $dateRange = new \DatePeriod($startDate, $interval, $endDate);
            
            $nama_hari_map = ['Sunday'=>'Ahad','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
            
            foreach ($dateRange as $dt) {
                $dateStr = $dt->format('Y-m-d');
                $dayEng = $dt->format('l');
                
                $dayInd = $nama_hari_map[$dayEng];
                
                $isLibur = false;
                $keteranganLibur = "";
                $isKegiatan = false;
                $keteranganKegiatan = "";
                $jamPulang = null;
                $keteranganPulang = "";
                
                foreach($kalenderList as $kal) {
                    if ($dateStr >= $kal['tanggal_mulai'] && (empty($kal['tanggal_selesai']) || $dateStr <= $kal['tanggal_selesai'])) {
                        if ($kal['kategori'] === 'Libur') {
                            $isLibur = true;
                            $keteranganLibur = isset($kal['kegiatan']) ? $kal['kegiatan'] : (isset($kal['keterangan']) ? $kal['keterangan'] : 'Libur');
                        } elseif ($kal['kategori'] === 'Kegiatan') {
                            $isKegiatan = true;
                            $keteranganKegiatan = $kal['kegiatan'] ?? 'Kegiatan';
                        } elseif ($kal['kategori'] === 'Pulang Dipercepat') {
                            $jamPulang = $kal['jam_pulang'];
                            $keteranganPulang = $kal['kegiatan'] ?? 'Pulang Dipercepat';
                        }
                    }
                }
                
                if ($isLibur) {
                    $new_jurnal_global[] = [
                        'tanggal' => $dateStr,
                        'is_libur' => true,
                        'keterangan_libur' => "LIBUR NASIONAL: " . $keteranganLibur
                    ];
                    continue;
                }
                
                if ($isKegiatan) {
                    $new_jurnal_global[] = [
                        'tanggal' => $dateStr,
                        'is_libur' => true, // use same styling as libur
                        'keterangan_libur' => "KEGIATAN: " . $keteranganKegiatan
                    ];
                    continue;
                }
                
                $jurnal_hari_ini = array_filter($jurnal_global, function($jg) use ($dateStr) {
                    return $jg['tanggal'] == $dateStr;
                });
                
                $jadwals = isset($jadwal_by_hari[$dayInd]) ? $jadwal_by_hari[$dayInd] : [];
                $used_jurnal_ids = [];
                
                if (empty($jadwals) && empty($jurnal_hari_ini)) {
                    continue; // Jika tidak ada jadwal dan tidak ada jurnal, skip
                }

                foreach($jadwals as $jdwl) {
                    $matched_journals = array_filter($jurnal_hari_ini, function($jh) use ($jdwl) {
                        return $jh['mapel_id'] == $jdwl['mapel_id'] && $jh['kelas_id'] == $jdwl['kelas_id'];
                    });
                    
                    if (!empty($matched_journals)) {
                        foreach($matched_journals as $jh) {
                            $new_jurnal_global[] = $jh;
                            $used_jurnal_ids[] = $jh['id'];
                        }
                    } else {
                        // CEK JAM PULANG DIPERCEPAT
                        if ($jamPulang !== null && !empty($jdwl['jam_mulai'])) {
                            if ($jdwl['jam_mulai'] >= $jamPulang) {
                                $new_jurnal_global[] = [
                                    'tanggal' => $dateStr,
                                    'is_kosong' => true,
                                    'is_kegiatan_mapel' => true,
                                    'keterangan_kegiatan' => $keteranganPulang,
                                    'nama_mapel' => isset($mapelMap[$jdwl['mapel_id']]) ? $mapelMap[$jdwl['mapel_id']] : 'Mapel ID '.$jdwl['mapel_id'],
                                    'nama_guru' => isset($guruMap[$jdwl['guru_id']]) ? $guruMap[$jdwl['guru_id']] : 'Guru ID '.$jdwl['guru_id'],
                                    'nama_kelas' => isset($kelasMap[$jdwl['kelas_id']]) ? $kelasMap[$jdwl['kelas_id']] : 'Kelas ID '.$jdwl['kelas_id']
                                ];
                                continue;
                            }
                        }

                        $new_jurnal_global[] = [
                            'tanggal' => $dateStr,
                            'is_kosong' => true,
                            'nama_mapel' => isset($mapelMap[$jdwl['mapel_id']]) ? $mapelMap[$jdwl['mapel_id']] : 'Mapel ID '.$jdwl['mapel_id'],
                            'nama_guru' => isset($guruMap[$jdwl['guru_id']]) ? $guruMap[$jdwl['guru_id']] : 'Guru ID '.$jdwl['guru_id'],
                            'nama_kelas' => isset($kelasMap[$jdwl['kelas_id']]) ? $kelasMap[$jdwl['kelas_id']] : 'Kelas ID '.$jdwl['kelas_id']
                        ];
                    }
                }
                
                foreach($jurnal_hari_ini as $jh) {
                    if (!in_array($jh['id'], $used_jurnal_ids)) {
                        $kat = isset($mapelKategoriMap[$jh['mapel_id']]) ? $mapelKategoriMap[$jh['mapel_id']] : 'Umum';
                        if (strtolower($kat) === 'umum') {
                            $new_jurnal_global[] = $jh;
                        }
                    }
                }
            }
            $jurnal_global = $new_jurnal_global;
        }

        $inst = \App\Core\Branding::getInstitusi();
        $kepsek = [
            'nama' => $inst['nama_kepala'] ?? '_______________________',
            'nip' => $inst['nip_kepala'] ?? ''
        ];

        include __DIR__ . '/../../resources/views/siakad/jurnal_global_cetak.php';
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
        $activeMenu = "siakad_monitoring_nilai";

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/monitoring_nilai.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
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
        // Minimal 1 kolom PH kalau kosong
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
        $activeMenu = "siakad_monitoring_nilai";

        ob_start();
        require __DIR__ . '/../../resources/views/siakad/monitoring_nilai_detail.php';
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
        // Minimal 1 kolom PH kalau kosong
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

        require __DIR__ . '/../../resources/views/siakad/monitoring_nilai_cetak.php';
    }

    public function guruCetakKartu() {
        $db = Database::connect();
        $guruList = $db->query("SELECT g.*, u.qr_token FROM guru g LEFT JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC")->fetchAll();
        $siswaList = $db->query("SELECT s.*, k.nama_kelas, u.qr_token FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id LEFT JOIN users u ON s.user_id = u.id WHERE s.status = 'Aktif' ORDER BY k.nama_kelas ASC, s.nama ASC")->fetchAll();
        $institusi = Branding::getInstitusi();

        include __DIR__ . '/../../resources/views/siakad/guru_cetak_kartu_view.php';
    }

    // --- 7. Data Siswa & Manajemen Siswa ---
    public function siswa() {
        $db = Database::connect();
        $selected_kelas = $_GET['kelas_id'] ?? '';

        $kelas = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();

        $sql = "SELECT s.*, k.nama_kelas, u.qr_token FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id LEFT JOIN users u ON s.user_id = u.id WHERE s.status = 'Aktif'";
        if (!empty($selected_kelas) && $selected_kelas !== 'all') {
            $sql .= " AND s.kelas_id = " . intval($selected_kelas);
        }
        $sql .= " ORDER BY k.nama_kelas ASC, s.nama ASC";

        $siswas = $db->query($sql)->fetchAll();
        
        $activeYear = AcademicYear::current();
        $active_year_name = $activeYear ? $activeYear['name'] : '2025/2026';

        $title = "Data Siswa - SIAKAD MTs RS";
        $activeMenu = 'siakad_siswa';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/siswa.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function siswaUpdateFoto() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = intval($_POST['id'] ?? 0);
                $db = Database::connect();
                if ($id > 0 && isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $old = $db->query("SELECT foto FROM siswa WHERE id = $id")->fetch();
                    if ($old && $old['foto'] && file_exists(__DIR__ . "/../../public/uploads/siswa/" . $old['foto'])) {
                        @unlink(__DIR__ . "/../../public/uploads/siswa/" . $old['foto']);
                        $oldThumb = pathinfo($old['foto'], PATHINFO_FILENAME) . '_thumb.' . pathinfo($old['foto'], PATHINFO_EXTENSION);
                        if (file_exists(__DIR__ . "/../../public/uploads/siswa/" . $oldThumb)) {
                            @unlink(__DIR__ . "/../../public/uploads/siswa/" . $oldThumb);
                        }
                    }
                    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $foto_name = "siswa_" . time() . "_" . rand(100, 999) . "." . $ext;
                    $thumb_name = pathinfo($foto_name, PATHINFO_FILENAME) . '_thumb.' . $ext;
                    $upload_dir = __DIR__ . "/../../public/uploads/siswa/";
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name)) {
                        $this->createThumbnail($upload_dir . $foto_name, $upload_dir . $thumb_name, 150, 150);
                        
                        $stmt = $db->prepare("UPDATE siswa SET foto = ? WHERE id = ?");
                        $stmt->execute([$foto_name, $id]);
                        echo json_encode(['success' => true, 'foto' => $foto_name, 'thumb' => $thumb_name]);
                        exit;
                    }
                }
                echo json_encode(['success' => false, 'message' => 'Upload gagal']);
            } catch (\Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
            exit;
        }
    }

    public function siswaSave() {
        $db = Database::connect();

        $id = $_POST['id'] ?? null;
        $nis = trim($_POST['nis'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        
        if (empty($nis) || empty($nama)) {
            Helper::redirect('/siakad/siswa');
        }

        // Get all other fields
        $fields = [
            'nik', 'no_kk', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 
            'gol_darah', 'kewarganegaraan', 'anak_ke', 'pendidikan_terakhir', 'sekolah_asal', 
            'no_ijazah', 'no_paspor', 'no_kitas', 'nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 
            'pekerjaan_ibu', 'penghasilan_ortu', 'no_hp_ortu', 'nama_wali', 'pekerjaan_wali', 
            'no_hp_wali', 'alamat', 'rt', 'rw', 'provinsi', 'kota', 'kecamatan', 'desa', 'kode_pos'
        ];
        
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = trim($_POST[$field] ?? '');
        }

        // Handle foto upload
        $fotoName = null;
        if (!empty($_FILES['foto']['name'])) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $fotoName = 'siswa_' . time() . '_' . uniqid() . '.' . $ext;
            $thumbName = 'siswa_' . time() . '_' . uniqid() . '_thumb.' . $ext;
            $uploadPath = __DIR__ . '/../../public/uploads/siswa/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            if(move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath . $fotoName)) {
                $this->createThumbnail($uploadPath . $fotoName, $uploadPath . $thumbName, 150, 150);
            }
        }

        if (empty($id)) {
            // New Siswa
            try {
                $db->beginTransaction();
                $username = !empty($data['nik']) ? $data['nik'] : (!empty($data['nisn']) ? $data['nisn'] : $nis);
                $passwordHash = password_hash($username, PASSWORD_BCRYPT);
                $qrToken = uniqid('MTSRS-S-', true) . bin2hex(random_bytes(4));

            $stmtUser = $db->prepare("INSERT INTO users (username, password_hash, role_id, qr_token, status) VALUES (?, ?, 3, ?, 'active')");
            $stmtUser->execute([$username, $passwordHash, $qrToken]);
            $userId = $db->lastInsertId();

            $sqlInsert = "INSERT INTO siswa (user_id, nis, nama, kelas_id, status, foto, " . implode(", ", $fields) . ") VALUES (?, ?, ?, ?, 'Aktif', ?, " . str_repeat('?, ', count($fields) - 1) . "?)";
            $params = array_merge([$userId, $nis, $nama, $kelas_id, $fotoName], array_values($data));
            $stmt = $db->prepare($sqlInsert);
            $stmt->execute($params);
            $siswaId = $db->lastInsertId();

            $activeYear = AcademicYear::current();
            $db->prepare("INSERT INTO riwayat_kelas_siswa (siswa_id, kelas_id, tahun_ajaran_id) VALUES (?, ?, ?)")->execute([$siswaId, $kelas_id, $activeYear['id']]);
            $db->prepare("INSERT INTO keuangan_tabungan (siswa_id, saldo) VALUES (?, 0.00)")->execute([$siswaId]);
            $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                $_SESSION['flash_error'] = "Gagal menyimpan data: NIK/NIS sudah terdaftar atau error database (" . $e->getMessage() . ")";
                Helper::redirect('/siakad/siswa');
                exit;
            }
        } else {
            // Update Siswa
            $setParams = [];
            foreach ($fields as $field) {
                $setParams[] = "$field = ?";
            }
            $sqlSet = implode(", ", $setParams);
            
            $params = array_values($data);
            $params[] = $nis;
            $params[] = $nama;
            $params[] = $kelas_id;
            
            $updateFoto = "";
            if ($fotoName) {
                $updateFoto = ", foto = ?";
                $params[] = $fotoName;
            }
            
            $params[] = $id;

            $stmt = $db->prepare("UPDATE siswa SET $sqlSet, nis = ?, nama = ?, kelas_id = ? $updateFoto WHERE id = ?");
            $stmt->execute($params);
            
            // Optionally update users table if we need to sync NIK to username
            if (!empty($data['nik'])) {
                $siswa = $db->query("SELECT user_id FROM siswa WHERE id = " . intval($id))->fetch();
                if ($siswa && $siswa['user_id']) {
                    $db->prepare("UPDATE users SET username = ? WHERE id = ?")->execute([$data['nik'], $siswa['user_id']]);
                }
            }
        }

        Helper::redirect('/siakad/siswa');

    }
    
    public function siswaUploadFotoMasal() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_masal'])) {
            $db = \App\Core\Database::connect();
            $successCount = 0;
            $failedCount = 0;
            
            $files = $_FILES['foto_masal'];
            $totalFiles = count($files['name']);
            
            for ($i = 0; $i < $totalFiles; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $originalName = $files['name'][$i];
                    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    $nisn = trim(pathinfo($originalName, PATHINFO_FILENAME));
                    
                    // Allow only image extensions
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $failedCount++;
                        continue;
                    }
                    
                    $stmt = $db->prepare("SELECT id, foto, nama FROM siswa WHERE nisn = ? OR nis = ?");
                    $stmt->execute([$nisn, $nisn]);
                    $siswa = $stmt->fetch();
                    
                    if ($siswa) {
                        $id = $siswa['id'];
                        
                        $fotoName = 'siswa_' . time() . '_' . uniqid() . '.' . $ext;
                        $thumbName = 'siswa_' . time() . '_' . uniqid() . '_thumb.' . $ext;
                        $uploadPath = __DIR__ . '/../../public/uploads/siswa/';
                        
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }
                        
                        if (move_uploaded_file($files['tmp_name'][$i], $uploadPath . $fotoName)) {
                            $this->createThumbnail($uploadPath . $fotoName, $uploadPath . $thumbName, 150, 150);
                            
                            // Delete old photo if exists
                            if (!empty($siswa['foto'])) {
                                $oldPhotoPath = $uploadPath . $siswa['foto'];
                                $oldThumbPath = $uploadPath . str_replace('.', '_thumb.', $siswa['foto']);
                                if (file_exists($oldPhotoPath)) unlink($oldPhotoPath);
                                if (file_exists($oldThumbPath)) unlink($oldThumbPath);
                            }
                            
                            $db->prepare("UPDATE siswa SET foto = ? WHERE id = ?")->execute([$fotoName, $id]);
                            $successCount++;
                        } else {
                            $failedCount++;
                        }
                    } else {
                        $failedCount++;
                    }
                }
            }
            
            \App\Core\Helper::logActivity('SIAKAD', 'UPLOAD_MASAL', "Upload foto masal siswa: Sukses $successCount, Gagal $failedCount");
            \App\Core\Helper::redirect('/siakad/siswa?upload_sukses=' . $successCount . '&upload_gagal=' . $failedCount);
        } else {
            \App\Core\Helper::redirect('/siakad/siswa');
        }
    }
    
    public function siswaUploadFotoMasalAjax() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
            $db = \App\Core\Database::connect();
            $file = $_FILES['foto'];
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $originalName = $file['name'];
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $nisn = trim(pathinfo($originalName, PATHINFO_FILENAME));
                
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Format tidak didukung', 'file' => $originalName]);
                    exit;
                }
                
                $stmt = $db->prepare("SELECT id, foto, nama FROM siswa WHERE nisn = ? OR nis = ?");
                $stmt->execute([$nisn, $nisn]);
                $siswa = $stmt->fetch();
                
                if ($siswa) {
                    $id = $siswa['id'];
                    $fotoName = 'siswa_' . time() . '_' . uniqid() . '.' . $ext;
                    $thumbName = 'siswa_' . time() . '_' . uniqid() . '_thumb.' . $ext;
                    $uploadPath = __DIR__ . '/../../public/uploads/siswa/';
                    
                    if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
                    
                    if (move_uploaded_file($file['tmp_name'], $uploadPath . $fotoName)) {
                        $this->createThumbnail($uploadPath . $fotoName, $uploadPath . $thumbName, 150, 150);
                        
                        if (!empty($siswa['foto'])) {
                            $oldPhotoPath = $uploadPath . $siswa['foto'];
                            $oldThumbPath = $uploadPath . str_replace('.', '_thumb.', $siswa['foto']);
                            if (file_exists($oldPhotoPath)) @unlink($oldPhotoPath);
                            if (file_exists($oldThumbPath)) @unlink($oldThumbPath);
                        }
                        
                        $db->prepare("UPDATE siswa SET foto = ? WHERE id = ?")->execute([$fotoName, $id]);
                        
                        echo json_encode(['status' => 'success', 'message' => 'Upload berhasil', 'file' => $originalName]);
                        exit;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file', 'file' => $originalName]);
                        exit;
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'NIS/NISN tidak ditemukan', 'file' => $originalName]);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error upload', 'file' => $file['name']]);
                exit;
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        exit;
    }
    
    public function siswaImport() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paste_data'])) {
            $data_raw = trim($_POST['paste_data']);
            $lines = explode("\n", $data_raw);
            $db = Database::connect();
            
            $default_kelas_id = intval($_POST['kelas_id'] ?? 0);
            $activeYear = AcademicYear::current();
            
            $successCount = 0;
            $errors = [];
            
            foreach ($lines as $index => $line) {
                if (stripos($line, '1. NIK') !== false || stripos($line, 'Nama Lengkap') !== false) continue;
                
                $line = trim($line, "\r\n");
                if (empty(trim($line))) continue;

                $cols = explode("\t", $line);
                if (count($cols) < 4) {
                    $errors[] = "Baris " . ($index + 1) . ": Tidak lengkap";
                    continue;
                }
                
                $nik = trim($cols[0] ?? '');
                $nis = trim($cols[1] ?? '');
                $nisn = trim($cols[2] ?? '');
                $nama = trim($cols[3] ?? '');
                $jk = trim($cols[4] ?? 'L');
                $tempat_lahir = trim($cols[5] ?? '');
                $tanggal_lahir = trim($cols[6] ?? null);
                $agama = trim($cols[7] ?? 'Islam');
                $gol_darah = trim($cols[8] ?? '-');
                $kewarganegaraan = trim($cols[9] ?? 'WNI');
                $anak_ke = trim($cols[10] ?? '');
                
                $parsed_kelas_id = trim($cols[11] ?? '');
                $kelas_id = (!empty($parsed_kelas_id) && is_numeric($parsed_kelas_id)) ? intval($parsed_kelas_id) : $default_kelas_id;
                
                $pendidikan_terakhir = trim($cols[12] ?? '');
                $sekolah_asal = trim($cols[13] ?? '');
                $no_ijazah = trim($cols[14] ?? '');
                $no_paspor = trim($cols[15] ?? '');
                $no_kitas = trim($cols[16] ?? '');
                
                $nama_ayah = trim($cols[17] ?? '');
                $pekerjaan_ayah = trim($cols[18] ?? '');
                $nama_ibu = trim($cols[19] ?? '');
                $pekerjaan_ibu = trim($cols[20] ?? '');
                $penghasilan_ortu = trim($cols[21] ?? '');
                $no_hp_ortu = trim($cols[22] ?? '');
                $nama_wali = trim($cols[23] ?? '');
                $pekerjaan_wali = trim($cols[24] ?? '');
                $no_hp_wali = trim($cols[25] ?? '');
                
                $alamat = trim($cols[26] ?? '');
                $rt = trim($cols[27] ?? '');
                $rw = trim($cols[28] ?? '');
                $provinsi = trim($cols[29] ?? '');
                $kota = trim($cols[30] ?? '');
                $kecamatan = trim($cols[31] ?? '');
                $desa = trim($cols[32] ?? '');
                $kode_pos = trim($cols[33] ?? '');
                
                if (empty($nik) || empty($nama)) {
                    $errors[] = "Baris " . ($index + 1) . ": NIK/Nama kosong";
                    continue;
                }

                $check = $db->prepare("SELECT id FROM users WHERE username = ?");
                $check->execute([$nik]);
                if ($check->fetch()) {
                    $errors[] = "Baris " . ($index + 1) . ": NIK ($nik) sudah ada";
                    continue; 
                }

                try {
                    $db->beginTransaction();
                    $username = $nik;
                    $passwordHash = password_hash($nik, PASSWORD_BCRYPT);
                    $qrToken = uniqid('MTSRS-S-', true) . bin2hex(random_bytes(4));

                    $stmt = $db->prepare("INSERT INTO users (username, password_hash, role_id, qr_token, status) VALUES (?, ?, 3, ?, 'active')");
                    $stmt->execute([$username, $passwordHash, $qrToken]);
                    $userId = $db->lastInsertId();
                    
                    $sqlInsert = "INSERT INTO siswa (
                        user_id, nik, nis, nisn, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, 
                        agama, gol_darah, kewarganegaraan, anak_ke, kelas_id, pendidikan_terakhir, 
                        sekolah_asal, no_ijazah, no_paspor, no_kitas, nama_ayah, pekerjaan_ayah, 
                        nama_ibu, pekerjaan_ibu, penghasilan_ortu, no_hp_ortu, nama_wali, 
                        pekerjaan_wali, no_hp_wali, alamat, rt, rw, provinsi, kota, kecamatan, 
                        desa, kode_pos, status
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, 'Aktif'
                    )";
                    
                    $tanggal_lahir = !empty($tanggal_lahir) ? $tanggal_lahir : null;
                    $stmt = $db->prepare($sqlInsert);
                    $stmt->execute([
                        $userId, $nik, $nis, $nisn, $nama, $jk, $tempat_lahir, $tanggal_lahir,
                        $agama, $gol_darah, $kewarganegaraan, $anak_ke, $kelas_id, $pendidikan_terakhir,
                        $sekolah_asal, $no_ijazah, $no_paspor, $no_kitas, $nama_ayah, $pekerjaan_ayah,
                        $nama_ibu, $pekerjaan_ibu, $penghasilan_ortu, $no_hp_ortu, $nama_wali,
                        $pekerjaan_wali, $no_hp_wali, $alamat, $rt, $rw, $provinsi, $kota, $kecamatan,
                        $desa, $kode_pos
                    ]);
                    
                    $siswaId = $db->lastInsertId();
                    
                    if ($activeYear && $kelas_id > 0) {
                        $db->prepare("INSERT INTO riwayat_kelas_siswa (siswa_id, kelas_id, tahun_ajaran_id) VALUES (?, ?, ?)")->execute([$siswaId, $kelas_id, $activeYear['id']]);
                    }
                    $db->prepare("INSERT INTO keuangan_tabungan (siswa_id, saldo) VALUES (?, 0.00)")->execute([$siswaId]);
                    $db->commit();
                    $successCount++;
                } catch (\Exception $e) {
                    $db->rollBack();
                    $errors[] = "Baris " . ($index + 1) . ": Error DB (" . $e->getMessage() . ")";
                }
            }

            if ($successCount > 0) {
                $_SESSION['flash_success'] = "Berhasil mengimpor $successCount data siswa.";
            }
            if (!empty($errors)) {
                $_SESSION['flash_error'] = "Gagal mengimpor beberapa data:<br>" . implode("<br>", array_slice($errors, 0, 5)) . (count($errors) > 5 ? "<br>...dan " . (count($errors) - 5) . " error lainnya." : "");
            }
        }
        Helper::redirect('/siakad/siswa');
    }

    public function siswaTemplateImport() {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Template_Import_Siswa_34Kolom.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta charset="UTF-8"></head><body>';
        echo '<table border="1">';
        echo '<tr style="background:#f1f5f9;font-weight:bold;">';
        $headers = [
            'NIK', 'NIS', 'NISN', 'Nama Lengkap', 'L/P', 'Tempat Lahir', 'Tgl Lahir (YYYY-MM-DD)', 
            'Agama', 'Gol Darah', 'Warga Negara', 'Anak Ke-', 'ID Kelas', 'Pend. Terakhir', 'Sekolah Asal', 
            'No Ijazah', 'No Paspor', 'No KITAS', 'Nama Ayah', 'Pekerjaan Ayah', 'Nama Ibu', 
            'Pekerjaan Ibu', 'Gaji Ortu', 'No HP Ortu', 'Nama Wali', 'Pekerjaan Wali', 'No HP Wali', 
            'Alamat Jalan', 'RT', 'RW', 'Provinsi', 'Kota', 'Kecamatan', 'Desa', 'Kode Pos'
        ];
        foreach($headers as $h) echo "<td>{$h}</td>";
        echo '</tr>';
        echo '<tr>';
        $dummy = [
            '3201234567890002', '23001', '0051234567', 'Ahmad Subagyo', 'L', 'Jakarta', '2010-05-15',
            'Islam', 'O', 'WNI', '1', '1', 'SD', 'SDN 1 Jakarta',
            '', '', '', 'Bapak', 'Wiraswasta', 'Ibu',
            'Ibu Rumah Tangga', '2000000', '081234567891', '', '', '',
            'Jl. Mawar No 2', '03', '04', 'DKI Jakarta', 'Jakarta Selatan', 'Tebet', 'Tebet Timur', '12820'
        ];
        foreach($dummy as $d) echo "<td>{$d}</td>";
        echo '</tr>';
        echo '</table></body></html>';
        exit;
    }

    public function siswaNaikKelas() {
        $db = Database::connect();
        $kelasList = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();

        $kelasAsalId = $_GET['kelas_asal'] ?? ($kelasList[0]['id'] ?? 0);

        $stmt = $db->prepare("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.kelas_id = ? AND s.status = 'Aktif' ORDER BY s.nama ASC");
        $stmt->execute([$kelasAsalId]);
        $siswaList = $stmt->fetchAll();

        $title = "Proses Naik Kelas - SIAKAD MTs RS";
        $activeMenu = 'siakad_siswa_naik';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/siswa_naik_kelas.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function siswaNaikKelasProcess() {
        $db = Database::connect();
        $kelasTujuanId = intval($_POST['kelas_tujuan'] ?? 0);
        $siswaSelected = $_POST['siswa_ids'] ?? [];

        if ($kelasTujuanId > 0 && !empty($siswaSelected)) {
            $stmt = $db->prepare("UPDATE siswa SET kelas_id = ? WHERE id = ?");
            foreach ($siswaSelected as $sid) {
                $stmt->execute([$kelasTujuanId, $sid]);
            }
        }

        Helper::redirect('/siakad/siswa');
    }

    public function siswaAlumni() {
        $db = Database::connect();
        $alumniList = $db->query("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.status IN ('Alumni', 'Lulus') ORDER BY s.nama ASC")->fetchAll();

        $title = "Data Alumni - SIAKAD MTs RS";
        $activeMenu = 'siakad_alumni';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/alumni.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function mutasi() {
        $db = Database::connect();
        $mutasiList = $db->query("SELECT m.*, s.nama as nama_siswa, s.nis FROM mutasi_siswa m JOIN siswa s ON m.siswa_id = s.id ORDER BY m.tanggal_mutasi DESC")->fetchAll();
        $siswaList = $db->query("SELECT id, nis, nama FROM siswa WHERE status = 'Aktif' OR id IN (SELECT siswa_id FROM mutasi_siswa) ORDER BY nama ASC")->fetchAll();

        $title = "Mutasi Siswa - SIAKAD MTs RS";
        $activeMenu = 'siakad_mutasi';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/mutasi.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function mutasiSave() {
        $db = Database::connect();
        $siswa_id = intval($_POST['siswa_id'] ?? 0);
        $jenis_mutasi = $_POST['jenis_mutasi'] ?? 'Keluar';
        $tanggal_mutasi = $_POST['tanggal_mutasi'] ?? date('Y-m-d');
        $sekolah_asal_tujuan = trim($_POST['sekolah_asal_tujuan'] ?? '');
        $alasan = trim($_POST['alasan'] ?? '');

        $id = intval($_POST['id'] ?? 0);
        if ($siswa_id > 0) {
            if ($id > 0) {
                // Edit
                $stmt = $db->prepare("UPDATE mutasi_siswa SET siswa_id = ?, jenis_mutasi = ?, tanggal_mutasi = ?, sekolah_asal_tujuan = ?, alasan = ? WHERE id = ?");
                $stmt->execute([$siswa_id, $jenis_mutasi, $tanggal_mutasi, $sekolah_asal_tujuan, $alasan, $id]);
            } else {
                // Add
                $stmt = $db->prepare("INSERT INTO mutasi_siswa (siswa_id, jenis_mutasi, tanggal_mutasi, sekolah_asal_tujuan, alasan) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$siswa_id, $jenis_mutasi, $tanggal_mutasi, $sekolah_asal_tujuan, $alasan]);
            }

            if ($jenis_mutasi === 'Keluar' || $jenis_mutasi === 'DO' || $jenis_mutasi === 'Lulus') {
                $db->prepare("UPDATE siswa SET status = 'Non-Aktif' WHERE id = ?")->execute([$siswa_id]);
            } else if ($jenis_mutasi === 'Masuk') {
                $db->prepare("UPDATE siswa SET status = 'Aktif' WHERE id = ?")->execute([$siswa_id]);
            }
        }

        Helper::redirect('/siakad/mutasi');
    }

    public function mutasiDelete($id = null) {
        if ($id) {
            $db = Database::connect();
            $db->prepare("DELETE FROM mutasi_siswa WHERE id = ?")->execute([$id]);
        }
        Helper::redirect('/siakad/mutasi');
    }

    // --- 8. Kalender Pendidikan ---
    public function kalender() {
        $db = Database::connect();
        $kalenderList = $db->query("SELECT * FROM kalender_pendidikan ORDER BY tanggal_mulai ASC")->fetchAll();

        $title = "Kalender Pendidikan - SIAKAD MTs RS";
        $activeMenu = 'siakad_kalender';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/kalender.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function kalenderSave() {
        $db = Database::connect();
        $tanggal_mulai = $_POST['tanggal_mulai'] ?? date('Y-m-d');
        $tanggal_selesai = $_POST['tanggal_selesai'] ?? date('Y-m-d');
        $kegiatan = trim($_POST['kegiatan'] ?? '');
        $kategori = $_POST['kategori'] ?? 'Kegiatan';
        $jam_pulang = null;
        
        if ($kategori === 'Pulang Dipercepat' && !empty($_POST['jam_pulang'])) {
            $jam_pulang = $_POST['jam_pulang'];
            if (strlen($jam_pulang) === 5) $jam_pulang .= ':00';
        }

        if (!empty($kegiatan)) {
            $stmt = $db->prepare("INSERT INTO kalender_pendidikan (tanggal_mulai, tanggal_selesai, kegiatan, kategori, jam_pulang) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal_mulai, $tanggal_selesai, $kegiatan, $kategori, $jam_pulang]);
        }

        Helper::redirect('/siakad/kalender');
    }

    public function kalenderUpdate() {
        $db = Database::connect();
        $id = intval($_POST['id'] ?? 0);
        $tanggal_mulai = $_POST['tanggal_mulai'] ?? date('Y-m-d');
        $tanggal_selesai = $_POST['tanggal_selesai'] ?? date('Y-m-d');
        $kegiatan = trim($_POST['kegiatan'] ?? '');
        $kategori = $_POST['kategori'] ?? 'Kegiatan';
        $jam_pulang = null;
        
        if ($kategori === 'Pulang Dipercepat' && !empty($_POST['jam_pulang'])) {
            $jam_pulang = $_POST['jam_pulang'];
            if (strlen($jam_pulang) === 5) $jam_pulang .= ':00';
        }

        if ($id > 0 && !empty($kegiatan)) {
            $stmt = $db->prepare("UPDATE kalender_pendidikan SET tanggal_mulai = ?, tanggal_selesai = ?, kegiatan = ?, kategori = ?, jam_pulang = ? WHERE id = ?");
            $stmt->execute([$tanggal_mulai, $tanggal_selesai, $kegiatan, $kategori, $jam_pulang, $id]);
        }

        Helper::redirect('/siakad/kalender');
    }

    public function kalenderDelete() {
        $db = Database::connect();
        $id = intval($_GET['id'] ?? 0);
        
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM kalender_pendidikan WHERE id = ?");
            $stmt->execute([$id]);
        }
        
        Helper::redirect('/siakad/kalender');
    }

    // --- 9. Penilaian, Input Guru, & Template ---
    public function nilai() {
        $db = Database::connect();
        $activeYear = AcademicYear::current();

        $kelasList = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $mapelList = $db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        $kelasId = $_GET['kelas_id'] ?? ($kelasList[0]['id'] ?? 0);
        $mapelId = $_GET['mapel_id'] ?? ($mapelList[0]['id'] ?? 0);

        $sql = "SELECT s.id as siswa_id, s.nama, s.nis, n.nilai, n.jenis_evaluasi, n.keterangan 
            FROM siswa s 
            LEFT JOIN nilai_harian n ON s.id = n.siswa_id AND n.mapel_id = ? AND n.kelas_id = ? AND n.tahun_ajaran_id = ?
            WHERE s.kelas_id = ? AND s.status = 'Aktif' 
            ORDER BY s.nama ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$mapelId, $kelasId, $activeYear['id'], $kelasId]);
        $nilaiList = $stmt->fetchAll();

        $title = "Penilaian Akademik - SIAKAD MTs RS";
        $activeMenu = 'siakad_nilai';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/nilai.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function nilaiSave() {
        $db = Database::connect();
        $activeYear = AcademicYear::current();

        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        $mapel_id = intval($_POST['mapel_id'] ?? 0);
        $nilaiData = $_POST['nilai'] ?? [];

        foreach ($nilaiData as $siswaId => $nilaiVal) {
            $val = floatval($nilaiVal);
            $stmtCek = $db->prepare("SELECT id FROM nilai_harian WHERE siswa_id = ? AND mapel_id = ? AND kelas_id = ? AND tahun_ajaran_id = ?");
            $stmtCek->execute([$siswaId, $mapel_id, $kelas_id, $activeYear['id']]);
            $existingId = $stmtCek->fetchColumn();

            if ($existingId) {
                $db->prepare("UPDATE nilai_harian SET nilai = ? WHERE id = ?")->execute([$val, $existingId]);
            } else {
                $db->prepare("INSERT INTO nilai_harian (siswa_id, mapel_id, kelas_id, tahun_ajaran_id, semester, nilai) VALUES (?, ?, ?, ?, ?, ?)")
                   ->execute([$siswaId, $mapel_id, $kelas_id, $activeYear['id'], $activeYear['semester'], $val]);
            }
        }

        Helper::redirect("/siakad/nilai?kelas_id=$kelas_id&mapel_id=$mapel_id");
    }

    public function nilaiInput() {
        $db = Database::connect();
        $kelasList = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $mapelList = $db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        $kelasId = $_GET['kelas_id'] ?? ($kelasList[0]['id'] ?? 0);
        $mapelId = $_GET['mapel_id'] ?? ($mapelList[0]['id'] ?? 0);

        $activeYear = AcademicYear::current();
        $stmt = $db->prepare("SELECT s.id as siswa_id, s.nama, s.nis, n.nilai FROM siswa s LEFT JOIN nilai_harian n ON s.id = n.siswa_id AND n.mapel_id = ? AND n.kelas_id = ? AND n.tahun_ajaran_id = ? WHERE s.kelas_id = ? AND s.status = 'Aktif' ORDER BY s.nama ASC");
        $stmt->execute([$mapelId, $kelasId, $activeYear['id'], $kelasId]);
        $nilaiList = $stmt->fetchAll();

        $title = "Form Input Nilai Guru - SIAKAD MTs RS";
        $activeMenu = 'siakad_nilai_input';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/nilai_input.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function nilaiTemplate() {
        $db = Database::connect();
        $kelasList = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $mapelList = $db->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        $title = "Template Nilai Format Excel - SIAKAD MTs RS";
        $activeMenu = 'siakad_nilai_template';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/nilai_template.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    // --- 10. Jadwal Pelajaran ---
    public function jadwal() {
        $db = Database::connect();
        $activeYear = AcademicYear::current();

        $kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
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

        $title = "Jadwal Pelajaran - SIAKAD MTs RS";
        $activeMenu = 'siakad_jadwal';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/jadwal.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function jadwalSave() {
        $db = Database::connect();
        $activeYear = AcademicYear::current();

        $hari = $_POST['hari'] ?? 'Sabtu';
        $jam_ke = intval($_POST['jam_ke'] ?? 1);
        $jam_mulai = $_POST['jam_mulai'] ?? '07:30';
        $jam_selesai = $_POST['jam_selesai'] ?? '08:15';
        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        $mapel_id = intval($_POST['mapel_id'] ?? 0);
        $guru_id = intval($_POST['guru_id'] ?? 0);

        if ($kelas_id > 0 && $mapel_id > 0 && $guru_id > 0) {
            $stmt = $db->prepare("INSERT INTO jadwal_pelajaran (hari, jam_ke, jam_mulai, jam_selesai, kelas_id, mapel_id, guru_id, tahun_ajaran_id, semester) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$hari, $jam_ke, $jam_mulai, $jam_selesai, $kelas_id, $mapel_id, $guru_id, $activeYear['id'], $activeYear['semester']]);
        }

        Helper::redirect('/siakad/jadwal?kelas_id=' . $kelas_id);
    }

    // --- 10. Jadwal Edit Inline (Like Masmifhda) ---
    public function jadwalEdit($id) {
        $db = Database::connect();
        $activeYear = AcademicYear::current();

        $stmt = $db->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->execute([$id]);
        $kelas = $stmt->fetch();

        if (!$kelas) {
            Helper::redirect('/siakad/jadwal');
        }

        $stmt_jadwal = $db->prepare("
            SELECT jp.*, m.nama_mapel, g.nama as nama_guru 
            FROM jadwal_pelajaran jp
            JOIN mapel m ON jp.mapel_id = m.id
            JOIN guru g ON jp.guru_id = g.id
            WHERE jp.kelas_id = ? AND jp.tahun_ajaran_id = ? 
            ORDER BY jp.jam_mulai ASC
        ");
        $stmt_jadwal->execute([$id, $activeYear['id']]);
        $jadwal_raw = $stmt_jadwal->fetchAll();

        $waktuList = $db->query("SELECT * FROM jadwal_waktu ORDER BY jam_ke ASC")->fetchAll();
        if (empty($waktuList)) {
            Helper::redirect('/siakad/jadwal?err=no_waktu');
        }
        $jam_waktu = [];
        foreach($waktuList as $w) {
            $jam_waktu[$w['jam_ke']] = [substr($w['jam_mulai'], 0, 5), substr($w['jam_selesai'], 0, 5), $w['is_istirahat']];
        }
        $jadwal = [];
        foreach ($jadwal_raw as $j) {
            $jam_mulai = substr($j['jam_mulai'], 0, 5);
            $jadwal[$j['hari']][$jam_mulai] = $j;
        }

        $mapel = $db->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll();
        $guru = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();
        $stmt_mengajar = $db->prepare("SELECT mapel_id, guru_id FROM penugasan_mengajar WHERE kelas_id = ?");
        $stmt_mengajar->execute([$id]);
        $mengajar_list = $stmt_mengajar->fetchAll();

        $title = "Atur Jadwal - " . htmlspecialchars($kelas['nama_kelas']);
        $activeMenu = 'siakad_jadwal';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/jadwal_edit.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function jadwalWaktu() {
        $db = Database::connect();
        $waktuList = $db->query("SELECT * FROM jadwal_waktu ORDER BY jam_ke ASC")->fetchAll();
        
        $title = "Pengaturan Jam Pelajaran";
        $activeMenu = 'siakad_jadwal';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/jadwal_waktu.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function jadwalWaktuSave() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = Database::connect();
            $mulais = $_POST['jam_mulai'] ?? [];
            $selesais = $_POST['jam_selesai'] ?? [];
            $istirahat_flags = $_POST['is_istirahat'] ?? [];
            
            $db->exec("TRUNCATE TABLE jadwal_waktu");
            
            $stmt = $db->prepare("INSERT INTO jadwal_waktu (jam_ke, jam_mulai, jam_selesai, is_istirahat) VALUES (?, ?, ?, ?)");
            
            $jam_ke = 1;
            foreach ($mulais as $index => $m_val) {
                if (trim($m_val) === '' || trim($selesais[$index]) === '') continue; // skip empty
                
                // Ensure format HH:MM:SS
                $m = (strlen(trim($m_val)) == 5) ? trim($m_val) . ':00' : trim($m_val);
                $s = (strlen(trim($selesais[$index])) == 5) ? trim($selesais[$index]) . ':00' : trim($selesais[$index]);
                
                $is_ist = isset($istirahat_flags[$index]) ? intval($istirahat_flags[$index]) : 0;
                $stmt->execute([$jam_ke, $m, $s, $is_ist]);
                $jam_ke++;
            }
            Helper::redirect('/siakad/jadwal/waktu?msg=saved');
        }
    }

    public function jadwalSaveInline($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            $hari = $_POST['hari'] ?? '';
            $jam_mulai = $_POST['jam_mulai'] ?? '';
            $jam_selesai = $_POST['jam_selesai'] ?? '';
            $mapel_id = $_POST['mapel_id'] ?? '';
            $guru_id = $_POST['guru_id'] ?? '';
            $jadwal_id = $_POST['jadwal_id'] ?? '';

            if (!$hari || !$jam_mulai || !$jam_selesai) {
                echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
                exit;
            }

            $db = Database::connect();
            
            if (empty($mapel_id) || empty($guru_id)) {
                if ($jadwal_id) {
                    $stmt = $db->prepare("DELETE FROM jadwal_pelajaran WHERE id = ?");
                    $stmt->execute([$jadwal_id]);
                    echo json_encode(['status' => 'success', 'message' => 'Jadwal dikosongkan', 'jadwal_id' => null]);
                } else {
                    echo json_encode(['status' => 'success', 'message' => 'Tidak ada perubahan', 'jadwal_id' => null]);
                }
                exit;
            }

            $activeYear = AcademicYear::current();

            if ($jadwal_id) {
                if (empty($mapel_id)) {
                    $stmt = $db->prepare("DELETE FROM jadwal_pelajaran WHERE id = ?");
                    $stmt->execute([$jadwal_id]);
                    echo json_encode(['status' => 'success', 'message' => 'Jadwal dikosongkan', 'jadwal_id' => null]);
                    exit;
                }
                $stmt = $db->prepare("UPDATE jadwal_pelajaran SET mapel_id = ?, guru_id = ? WHERE id = ?");
                $stmt->execute([$mapel_id, $guru_id, $jadwal_id]);
                echo json_encode(['status' => 'success', 'message' => 'Jadwal diperbarui', 'jadwal_id' => $jadwal_id]);
            } else {
                if (empty($mapel_id)) {
                     echo json_encode(['status' => 'success', 'message' => 'Tidak ada perubahan', 'jadwal_id' => null]);
                     exit;
                }
                $stmt = $db->prepare("INSERT INTO jadwal_pelajaran (tahun_ajaran_id, semester, kelas_id, hari, jam_mulai, jam_selesai, mapel_id, guru_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                // Assuming semester logic
                $semester = 'Ganjil';
                $stmt->execute([$activeYear['id'], $semester, $id, $hari, $jam_mulai, $jam_selesai, $mapel_id, $guru_id]);
                echo json_encode(['status' => 'success', 'message' => 'Jadwal disimpan', 'jadwal_id' => $db->lastInsertId()]);
            }
            exit;
        }
    }

    public function jadwalEditJam() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = Database::connect();
            $old_jam = $_POST['old_jam'] ?? ''; // e.g. "07:30 - 08:15"
            $new_mulai = $_POST['new_mulai'] ?? '';
            $new_selesai = $_POST['new_selesai'] ?? '';
            
            if ($old_jam && $new_mulai && $new_selesai) {
                $parts = explode(' - ', $old_jam);
                if (count($parts) == 2) {
                    $old_mulai = trim($parts[0]) . ':00';
                    $old_selesai = trim($parts[1]) . ':00';
                    $new_mulai = $new_mulai . ':00';
                    $new_selesai = $new_selesai . ':00';
                    
                    $activeYear = AcademicYear::current();
                    $stmt = $db->prepare("UPDATE jadwal_pelajaran SET jam_mulai = ?, jam_selesai = ? WHERE jam_mulai = ? AND jam_selesai = ? AND tahun_ajaran_id = ?");
                    $stmt->execute([$new_mulai, $new_selesai, $old_mulai, $old_selesai, $activeYear['id']]);
                }
            }
            Helper::redirect('/siakad/jadwal');
        }
    }

    // --- 11. Raport & Leger ---
    public function raport() {
        $db = Database::connect();
        $kelasList = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $kelasId = $_GET['kelas_id'] ?? ($kelasList[0]['id'] ?? 0);

        $stmt = $db->prepare("SELECT s.*, k.nama_kelas, (SELECT AVG(nilai) FROM nilai_harian n WHERE n.siswa_id = s.id) as rata_nilai FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.kelas_id = ? AND s.status = 'Aktif' ORDER BY s.nama ASC");
        $stmt->execute([$kelasId]);
        $siswaList = $stmt->fetchAll();

        $title = "Cetak Rapor & Leger - SIAKAD MTs RS";
        $activeMenu = 'siakad_raport';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/raport_list.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function raportCetak($siswaId) {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();

        $stmtS = $db->prepare("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.id = ?");
        $stmtS->execute([$siswaId]);
        $siswa = $stmtS->fetch();

        $stmtN = $db->prepare("SELECT m.nama_mapel, m.kelompok, n.nilai FROM nilai_harian n JOIN mapel m ON n.mapel_id = m.id WHERE n.siswa_id = ?");
        $stmtN->execute([$siswaId]);
        $nilaiList = $stmtN->fetchAll();

        include __DIR__ . '/../../resources/views/siakad/raport_print.php';
    }

    // --- 12. Lain-lain (Rapat & Broadcast) ---
    public function rapat() {
        $db = Database::connect();
        $rapatList = $db->query("SELECT * FROM rapat ORDER BY tanggal DESC")->fetchAll();

        $title = "Daftar Hadir Rapat - SIAKAD MTs RS";
        $activeMenu = 'siakad_rapat';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/rapat.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function rapatSave() {
        $db = Database::connect();
        $nama_rapat = trim($_POST['nama_rapat'] ?? '');
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $waktu = trim($_POST['waktu'] ?? '09:00 WIB');
        $tempat = trim($_POST['tempat'] ?? 'Ruang Guru');

        if (!empty($nama_rapat)) {
            $stmt = $db->prepare("INSERT INTO rapat (nama_rapat, tanggal, waktu, tempat) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama_rapat, $tanggal, $waktu, $tempat]);
        }

        Helper::redirect('/siakad/lain-lain/rapat');
    }

    public function broadcast() {
        $db = Database::connect();
        $broadcastList = $db->query("SELECT b.*, COALESCE(u.username, 'Admin/Dev') as username FROM broadcast_notifikasi b LEFT JOIN users u ON b.pengirim_id = u.id ORDER BY b.created_at DESC")->fetchAll();

        $title = "Broadcast Notifikasi - SIAKAD MTs RS";
        $activeMenu = 'siakad_broadcast';

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/broadcast.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function broadcastSave() {
        $db = Database::connect();
        $judul = trim($_POST['judul'] ?? '');
        $pesan = trim($_POST['pesan'] ?? '');
        $target_role = $_POST['target_role'] ?? 'Semua';

        if (!empty($judul) && !empty($pesan)) {
            $stmt = $db->prepare("INSERT INTO broadcast_notifikasi (judul, pesan, target_role, pengirim_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$judul, $pesan, $target_role, $_SESSION['user_id'] ?? 1]);
            
            // Send Push Notification via OneSignal
            Helper::sendOneSignalPush($judul, $pesan, $target_role);
        }

        Helper::redirect('/siakad/lain-lain/broadcast');
    }

    public function broadcastDelete() {
        $db = Database::connect();
        $id = $_GET['id'] ?? 0;
        
        if ($id) {
            $stmt = $db->prepare("DELETE FROM broadcast_notifikasi WHERE id = ?");
            $stmt->execute([$id]);
        }
        
        Helper::redirect('/siakad/lain-lain/broadcast');
    }
}
