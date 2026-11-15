<?php

namespace App\Controllers;

use App\Core\Helper;
use App\Core\Database;
use App\Core\AcademicYear;
use PDO;

class AsesmenController
{
    private function checkAccess()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . Helper::url('/login'));
            exit;
        }

        $db = Database::connect('core');
        $this->ensureTablesExist($db);
    }

    private function ensureTablesExist($db)
    {
        static $checked = false;
        if ($checked) return;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS asesmen_event (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nama VARCHAR(150) NOT NULL,
                kode VARCHAR(50) NOT NULL,
                jenis ENUM('sts', 'sas', 'sat', 'am', 'tryout', 'lainnya') DEFAULT 'sts',
                tahun_ajaran_id INT NULL,
                semester ENUM('Ganjil', 'Genap') DEFAULT 'Ganjil',
                tingkat_kelas VARCHAR(50) DEFAULT 'Semua',
                tanggal_mulai DATE NULL,
                tanggal_selesai DATE NULL,
                keterangan TEXT NULL,
                status ENUM('aktif', 'arsip') DEFAULT 'aktif',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $db->exec("CREATE TABLE IF NOT EXISTS asesmen_ruang (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NULL,
                nama_ruang VARCHAR(50) NOT NULL,
                kapasitas INT DEFAULT 20,
                tipe_meja TINYINT DEFAULT 1,
                jumlah_kolom INT DEFAULT 5,
                jumlah_baris INT DEFAULT 4,
                pola_penomoran VARCHAR(20) DEFAULT 'kolom',
                lokasi VARCHAR(100) NULL,
                keterangan VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Alter if missing columns
            try {
                $colsR = $db->query("SHOW COLUMNS FROM asesmen_ruang")->fetchAll(PDO::FETCH_COLUMN);
                if (!in_array('tipe_meja', $colsR)) $db->exec("ALTER TABLE asesmen_ruang ADD COLUMN tipe_meja TINYINT DEFAULT 1 AFTER kapasitas");
                if (!in_array('jumlah_kolom', $colsR)) $db->exec("ALTER TABLE asesmen_ruang ADD COLUMN jumlah_kolom INT DEFAULT 5 AFTER tipe_meja");
                if (!in_array('jumlah_baris', $colsR)) $db->exec("ALTER TABLE asesmen_ruang ADD COLUMN jumlah_baris INT DEFAULT 4 AFTER jumlah_kolom");
                if (!in_array('pola_penomoran', $colsR)) $db->exec("ALTER TABLE asesmen_ruang ADD COLUMN pola_penomoran VARCHAR(20) DEFAULT 'kolom' AFTER jumlah_baris");
            } catch (\Exception $e) {}

            $db->exec("CREATE TABLE IF NOT EXISTS asesmen_peserta (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                siswa_id INT NOT NULL,
                ruang_id INT NULL,
                nomor_peserta VARCHAR(50) NULL,
                nomor_meja INT NULL,
                posisi_baris INT NULL,
                posisi_kolom INT NULL,
                posisi_bangku INT DEFAULT 1,
                sesi INT DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_event_siswa (event_id, siswa_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            try {
                $colsP = $db->query("SHOW COLUMNS FROM asesmen_peserta")->fetchAll(PDO::FETCH_COLUMN);
                if (!in_array('posisi_baris', $colsP)) $db->exec("ALTER TABLE asesmen_peserta ADD COLUMN posisi_baris INT NULL AFTER nomor_meja");
                if (!in_array('posisi_kolom', $colsP)) $db->exec("ALTER TABLE asesmen_peserta ADD COLUMN posisi_kolom INT NULL AFTER posisi_baris");
                if (!in_array('posisi_bangku', $colsP)) $db->exec("ALTER TABLE asesmen_peserta ADD COLUMN posisi_bangku INT DEFAULT 1 AFTER posisi_kolom");
            } catch (\Exception $e) {}

            $db->exec("CREATE TABLE IF NOT EXISTS asesmen_bank_soal (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NULL,
                mapel_id INT NOT NULL,
                guru_id INT NULL,
                tingkat VARCHAR(10) DEFAULT '7',
                judul VARCHAR(200) NOT NULL,
                kode_soal VARCHAR(50) NULL,
                kisi_kisi_file VARCHAR(255) NULL,
                naskah_soal_file VARCHAR(255) NULL,
                kunci_jawaban_file VARCHAR(255) NULL,
                jumlah_pg INT DEFAULT 40,
                jumlah_esai INT DEFAULT 5,
                status ENUM('draft', 'diajukan', 'disetujui') DEFAULT 'draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $db->exec("CREATE TABLE IF NOT EXISTS asesmen_jadwal (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                mapel_id INT NOT NULL,
                tingkat VARCHAR(10) DEFAULT 'Semua',
                tanggal DATE NOT NULL,
                jam_mulai TIME NOT NULL,
                jam_selesai TIME NOT NULL,
                durasi_menit INT DEFAULT 90,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $db->exec("CREATE TABLE IF NOT EXISTS asesmen_berita_acara (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                ruang_id INT NOT NULL,
                jadwal_id INT NULL,
                tanggal DATE NOT NULL,
                jam_mulai TIME NULL,
                jam_selesai TIME NULL,
                pengawas_1_id INT NULL,
                pengawas_2_id INT NULL,
                jumlah_seharusnya INT DEFAULT 0,
                jumlah_hadir INT DEFAULT 0,
                jumlah_tidak_hadir INT DEFAULT 0,
                peserta_tidak_hadir TEXT NULL,
                catatan_kejadian TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Seed default events if table is empty
            $check = $db->query("SELECT COUNT(*) FROM asesmen_event")->fetchColumn();
            if ($check == 0) {
                $db->exec("INSERT INTO asesmen_event (nama, kode, jenis, semester, tingkat_kelas, status, keterangan) 
                VALUES 
                ('Asesmen Sumatif Tengah Semester (STS) Ganjil', 'STS-GANJIL-26', 'sts', 'Ganjil', 'Semua (7, 8, 9)', 'aktif', 'Pelaksanaan STS Ganjil Tahun Ajaran 2026/2027'),
                ('Asesmen Sumatif Akhir Semester (SAS) Ganjil', 'SAS-GANJIL-26', 'sas', 'Ganjil', 'Semua (7, 8, 9)', 'aktif', 'Penilaian Sumatif Akhir Semester Ganjil'),
                ('Asesmen Madrasah (AM) Kelas IX', 'AM-2027', 'am', 'Genap', 'Kelas 9', 'aktif', 'Ujian Madrasah Kelulusan Santri & Siswa Kelas Akhir')");
            }

            // Seed default rooms if table is empty
            $checkRuang = $db->query("SELECT COUNT(*) FROM asesmen_ruang")->fetchColumn();
            if ($checkRuang == 0) {
                $db->exec("INSERT INTO asesmen_ruang (nama_ruang, kapasitas, tipe_meja, jumlah_kolom, jumlah_baris, pola_penomoran, lokasi, keterangan) 
                VALUES 
                ('Ruang 01', 30, 1, 5, 6, 'kolom', 'Gedung A - Lantai 1', 'Formasi 1 x 5 x 6 (1 Meja Mandiri, 5 Kolom, 6 Baris)'),
                ('Ruang 02', 30, 1, 5, 6, 'kolom', 'Gedung A - Lantai 1', 'Formasi 1 x 5 x 6 (1 Meja Mandiri, 5 Kolom, 6 Baris)'),
                ('Ruang 03', 36, 2, 3, 6, 'kolom', 'Gedung B - Lantai 2', 'Formasi 2 x 3 x 6 (2 Meja Gandeng, 3 Deret, 6 Baris)'),
                ('Ruang 04', 36, 2, 3, 6, 'kolom', 'Gedung B - Lantai 2', 'Formasi 2 x 3 x 6 (2 Meja Gandeng, 3 Deret, 6 Baris)')");
            }

            $checked = true;
        } catch (\Exception $e) {
            // Ignore error
        }
    }

    public function dashboard()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $totalEvent = $db->query("SELECT COUNT(*) FROM asesmen_event WHERE status = 'aktif'")->fetchColumn() ?: 0;
        $totalBankSoal = $db->query("SELECT COUNT(*) FROM asesmen_bank_soal")->fetchColumn() ?: 0;
        $totalRuang = $db->query("SELECT COUNT(*) FROM asesmen_ruang")->fetchColumn() ?: 0;
        $totalPeserta = $db->query("SELECT COUNT(DISTINCT siswa_id) FROM asesmen_peserta")->fetchColumn() ?: 0;
        $totalBeritaAcara = $db->query("SELECT COUNT(*) FROM asesmen_berita_acara")->fetchColumn() ?: 0;

        $events = $db->query("SELECT * FROM asesmen_event ORDER BY status ASC, id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_dashboard';
        $title = 'Dashboard Asesmen & Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/dashboard.php';
    }

    public function event()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $events = $db->query("SELECT * FROM asesmen_event ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_event';
        $title = 'Event & Kategori Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/event.php';
    }

    public function saveEvent()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $nama = trim($_POST['nama'] ?? '');
        $kode = trim($_POST['kode'] ?? '');
        $jenis = $_POST['jenis'] ?? 'sts';
        $semester = $_POST['semester'] ?? 'Ganjil';
        $tingkat = $_POST['tingkat_kelas'] ?? 'Semua';
        $tgl_mulai = !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : null;
        $tgl_selesai = !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : null;
        $keterangan = trim($_POST['keterangan'] ?? '');

        if ($nama !== '') {
            $stmt = $db->prepare("INSERT INTO asesmen_event (nama, kode, jenis, semester, tingkat_kelas, tanggal_mulai, tanggal_selesai, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'aktif')");
            $stmt->execute([$nama, $kode, $jenis, $semester, $tingkat, $tgl_mulai, $tgl_selesai, $keterangan]);
            $_SESSION['flash_success'] = 'Event ujian berhasil ditambahkan.';
        }

        header('Location: ' . Helper::url('/asesmen/event'));
        exit;
    }

    public function bankSoal()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $soalList = $db->query("
            SELECT bs.*, m.nama_mapel, g.nama as nama_guru, e.nama as nama_event
            FROM asesmen_bank_soal bs
            LEFT JOIN mapel m ON bs.mapel_id = m.id
            LEFT JOIN guru g ON bs.guru_id = g.id
            LEFT JOIN asesmen_event e ON bs.event_id = e.id
            ORDER BY bs.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $mapels = $db->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);
        $events = $db->query("SELECT id, nama FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_bank_soal';
        $title = 'Bank Soal & Kunci Jawaban | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/bank_soal.php';
    }

    public function kisiKisi()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $soalList = $db->query("
            SELECT bs.*, m.nama_mapel, g.nama as nama_guru, e.nama as nama_event
            FROM asesmen_bank_soal bs
            LEFT JOIN mapel m ON bs.mapel_id = m.id
            LEFT JOIN guru g ON bs.guru_id = g.id
            LEFT JOIN asesmen_event e ON bs.event_id = e.id
            ORDER BY bs.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $mapels = $db->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);
        $events = $db->query("SELECT id, nama FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_kisi_kisi';
        $title = 'Kisi-Kisi Soal Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/kisi_kisi.php';
    }

    public function ruang()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $ruangList = $db->query("
            SELECT r.*, e.nama as nama_event, COUNT(p.id) as terisi
            FROM asesmen_ruang r
            LEFT JOIN asesmen_event e ON r.event_id = e.id
            LEFT JOIN asesmen_peserta p ON r.id = p.ruang_id
            GROUP BY r.id
            ORDER BY r.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $events = $db->query("SELECT id, nama FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_ruang';
        $title = 'Pengaturan Ruang Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/ruang.php';
    }

    public function saveRuang()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $id = (int)($_POST['id'] ?? 0);
        $nama_ruang = trim($_POST['nama_ruang'] ?? '');
        $tipe_meja = (int)($_POST['tipe_meja'] ?? 1); // 1 = Tunggal (1 siswa/meja), 2 = Gandeng (2 siswa/meja)
        $jumlah_kolom = (int)($_POST['jumlah_kolom'] ?? 5);
        $jumlah_baris = (int)($_POST['jumlah_baris'] ?? 6);
        $pola_penomoran = trim($_POST['pola_penomoran'] ?? 'kolom');

        $kapasitas = (int)($_POST['kapasitas'] ?? 0);
        if ($kapasitas <= 0) {
            $kapasitas = $tipe_meja * $jumlah_kolom * $jumlah_baris;
        }

        $lokasi = trim($_POST['lokasi'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $event_id = !empty($_POST['event_id']) ? (int)$_POST['event_id'] : null;

        if ($nama_ruang !== '') {
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE asesmen_ruang SET event_id = ?, nama_ruang = ?, kapasitas = ?, tipe_meja = ?, jumlah_kolom = ?, jumlah_baris = ?, pola_penomoran = ?, lokasi = ?, keterangan = ? WHERE id = ?");
                $stmt->execute([$event_id, $nama_ruang, $kapasitas, $tipe_meja, $jumlah_kolom, $jumlah_baris, $pola_penomoran, $lokasi, $keterangan, $id]);
                $_SESSION['flash_success'] = "Ruangan '$nama_ruang' dan formasi meja ($tipe_meja x $jumlah_kolom x $jumlah_baris) berhasil diperbarui.";
            } else {
                $stmt = $db->prepare("INSERT INTO asesmen_ruang (event_id, nama_ruang, kapasitas, tipe_meja, jumlah_kolom, jumlah_baris, pola_penomoran, lokasi, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$event_id, $nama_ruang, $kapasitas, $tipe_meja, $jumlah_kolom, $jumlah_baris, $pola_penomoran, $lokasi, $keterangan]);
                $_SESSION['flash_success'] = "Ruangan '$nama_ruang' berhasil ditambahkan dengan formasi $tipe_meja x $jumlah_kolom x $jumlah_baris ($kapasitas peserta).";
            }
        }

        $returnUrl = $_POST['return_url'] ?? Helper::url('/asesmen/ruang');
        header('Location: ' . $returnUrl);
        exit;
    }

    public function deleteRuang()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $delP = $db->prepare("DELETE FROM asesmen_peserta WHERE ruang_id = ?");
            $delP->execute([$id]);
            $delR = $db->prepare("DELETE FROM asesmen_ruang WHERE id = ?");
            $delR->execute([$id]);
            $_SESSION['flash_success'] = 'Ruang ujian dan alokasi meja siswa terkait berhasil dihapus.';
        }

        header('Location: ' . Helper::url('/asesmen/ruang'));
        exit;
    }

    public function denah()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $ruangId = (int)($_GET['ruang_id'] ?? 0);

        $events = $db->query("SELECT id, nama, kode FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        if ($eventId === 0 && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $ruangList = $db->query("SELECT * FROM asesmen_ruang ORDER BY nama_ruang ASC")->fetchAll(PDO::FETCH_ASSOC);
        if ($ruangId === 0 && !empty($ruangList)) {
            $ruangId = $ruangList[0]['id'];
        }

        $selectedRuang = null;
        foreach ($ruangList as $r) {
            if ($r['id'] == $ruangId) {
                $selectedRuang = $r;
                break;
            }
        }
        if (!$selectedRuang && !empty($ruangList)) {
            $selectedRuang = $ruangList[0];
            $ruangId = $selectedRuang['id'];
        }

        // Fetch students occupying desks in this room for this event
        $pesertaMeja = [];
        if ($selectedRuang && $eventId > 0) {
            $stmt = $db->prepare("
                SELECT p.*, s.nama as nama_siswa, s.nis, s.nisn, s.foto, k.nama_kelas, k.tingkat
                FROM asesmen_peserta p
                JOIN siswa s ON p.siswa_id = s.id
                LEFT JOIN kelas k ON s.kelas_id = k.id
                WHERE p.event_id = ? AND p.ruang_id = ?
                ORDER BY p.nomor_meja ASC
            ");
            $stmt->execute([$eventId, $ruangId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $pesertaMeja[$row['nomor_meja']] = $row;
            }
        }

        $totalSiswaAktif = $db->query("SELECT COUNT(*) FROM siswa WHERE status = 'aktif'")->fetchColumn() ?: 0;

        $activeMenu = 'asesmen_denah';
        $title = 'Denah Ruangan & Atur Meja Siswa | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/denah.php';
    }

    public function cetakDenah()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $ruangId = (int)($_GET['ruang_id'] ?? 0);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        $stmtEv = $db->prepare("SELECT * FROM asesmen_event WHERE id = ?");
        $stmtEv->execute([$eventId]);
        $event = $stmtEv->fetch(PDO::FETCH_ASSOC);

        $stmtR = $db->prepare("SELECT * FROM asesmen_ruang WHERE id = ?");
        $stmtR->execute([$ruangId]);
        $ruang = $stmtR->fetch(PDO::FETCH_ASSOC);

        $pesertaMeja = [];
        if ($ruang && $eventId > 0) {
            $stmt = $db->prepare("
                SELECT p.*, s.nama as nama_siswa, s.nis, s.nisn, k.nama_kelas, k.tingkat
                FROM asesmen_peserta p
                JOIN siswa s ON p.siswa_id = s.id
                LEFT JOIN kelas k ON s.kelas_id = k.id
                WHERE p.event_id = ? AND p.ruang_id = ?
                ORDER BY p.nomor_meja ASC
            ");
            $stmt->execute([$eventId, $ruangId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $pesertaMeja[$row['nomor_meja']] = $row;
            }
        }

        require_once __DIR__ . '/../../resources/views/asesmen/cetak_denah.php';
    }

    public function peserta()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $ruangId = (int)($_GET['ruang_id'] ?? 0);

        $events = $db->query("SELECT id, nama, kode FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        if ($eventId === 0 && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $ruangs = $db->query("SELECT id, nama_ruang, kapasitas FROM asesmen_ruang ORDER BY nama_ruang ASC")->fetchAll(PDO::FETCH_ASSOC);

        $sql = "
            SELECT p.*, s.nama as nama_siswa, s.nis, s.nisn, s.jenis_kelamin, k.nama_kelas, r.nama_ruang, e.nama as nama_event
            FROM asesmen_peserta p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN kelas k ON s.kelas_id = k.id
            LEFT JOIN asesmen_ruang r ON p.ruang_id = r.id
            LEFT JOIN asesmen_event e ON p.event_id = e.id
            WHERE p.event_id = ?
        ";
        $params = [$eventId];

        if ($ruangId > 0) {
            $sql .= " AND p.ruang_id = ?";
            $params[] = $ruangId;
        }

        $sql .= " ORDER BY r.nama_ruang ASC, p.nomor_meja ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pesertaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalSiswaAktif = $db->query("SELECT COUNT(*) FROM siswa WHERE status = 'aktif'")->fetchColumn() ?: 0;

        $activeMenu = 'asesmen_peserta';
        $title = 'Distribusi Peserta Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/peserta.php';
    }

    public function acakSiswa()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_POST['event_id'] ?? 0);
        $ruangId = (int)($_POST['ruang_id'] ?? 0); // 0 = all rooms, >0 = specific room
        $tingkat = $_POST['tingkat'] ?? 'Semua';
        $metode = $_POST['metode'] ?? 'acak'; // 'acak' (random), 'silang' (cross-grade), 'urut' (ordered)

        if ($eventId <= 0) {
            if (Helper::isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Event ujian tidak valid.']);
                exit;
            }
            $_SESSION['flash_error'] = 'Event ujian tidak valid.';
            header('Location: ' . Helper::url('/asesmen/denah'));
            exit;
        }

        // Fetch event info
        $stmtEv = $db->prepare("SELECT * FROM asesmen_event WHERE id = ?");
        $stmtEv->execute([$eventId]);
        $event = $stmtEv->fetch(PDO::FETCH_ASSOC);
        $prefix = $event ? strtoupper($event['kode']) : 'UJIAN';

        // Fetch target rooms
        if ($ruangId > 0) {
            $stmtR = $db->prepare("SELECT * FROM asesmen_ruang WHERE id = ?");
            $stmtR->execute([$ruangId]);
            $targetRuangs = $stmtR->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $targetRuangs = $db->query("SELECT * FROM asesmen_ruang ORDER BY nama_ruang ASC")->fetchAll(PDO::FETCH_ASSOC);
        }

        if (empty($targetRuangs)) {
            $msg = 'Tidak ada ruangan ujian yang tersedia.';
            if (Helper::isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            header('Location: ' . Helper::url('/asesmen/denah'));
            exit;
        }

        // Fetch eligible active students
        $sqlSiswa = "
            SELECT s.id, s.nama, s.nis, s.nisn, s.kelas_id, k.nama_kelas, k.tingkat 
            FROM siswa s 
            LEFT JOIN kelas k ON s.kelas_id = k.id 
            WHERE s.status = 'aktif'
        ";
        if ($tingkat === '7' || $tingkat === '8' || $tingkat === '9') {
            $sqlSiswa .= " AND k.tingkat = '$tingkat'";
        } elseif ($tingkat === 'Kelas 9 Saja (AM)') {
            $sqlSiswa .= " AND k.tingkat = '9'";
        }
        $sqlSiswa .= " ORDER BY k.nama_kelas ASC, s.nama ASC";
        $students = $db->query($sqlSiswa)->fetchAll(PDO::FETCH_ASSOC);

        if (empty($students)) {
            $msg = 'Tidak ditemukan siswa aktif untuk tingkat kelas yang dipilih.';
            if (Helper::isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_error'] = $msg;
            header('Location: ' . Helper::url('/asesmen/denah?event_id=' . $eventId . ($ruangId ? '&ruang_id=' . $ruangId : '')));
            exit;
        }

        // Apply shuffling / sorting method
        if ($metode === 'acak') {
            // Full random shuffle
            shuffle($students);
        } elseif ($metode === 'silang') {
            // Cross-grade anti-cheating distribution
            $grouped = [];
            foreach ($students as $st) {
                $t = $st['tingkat'] ?: '7';
                $grouped[$t][] = $st;
            }
            foreach ($grouped as $k => $v) {
                shuffle($grouped[$k]);
            }
            $interleaved = [];
            $keys = array_keys($grouped);
            $maxCount = !empty($grouped) ? max(array_map('count', $grouped)) : 0;
            for ($i = 0; $i < $maxCount; $i++) {
                foreach ($keys as $k) {
                    if (isset($grouped[$k][$i])) {
                        $interleaved[] = $grouped[$k][$i];
                    }
                }
            }
            $students = $interleaved;
        } else {
            // Ordered by class & student name
        }

        // Clear existing participants for targeted scope
        if ($ruangId > 0) {
            $del = $db->prepare("DELETE FROM asesmen_peserta WHERE event_id = ? AND ruang_id = ?");
            $del->execute([$eventId, $ruangId]);
        } else {
            $del = $db->prepare("DELETE FROM asesmen_peserta WHERE event_id = ?");
            $del->execute([$eventId]);
        }

        // Prepare insert statement
        $insertStmt = $db->prepare("
            INSERT INTO asesmen_peserta 
            (event_id, siswa_id, ruang_id, nomor_peserta, nomor_meja, posisi_baris, posisi_kolom, posisi_bangku, sesi) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        $stdIdx = 0;
        $totalAssigned = 0;
        $globalCounter = 1;

        if ($ruangId > 0) {
            $countExist = $db->prepare("SELECT COUNT(*) FROM asesmen_peserta WHERE event_id = ? AND ruang_id != ?");
            $countExist->execute([$eventId, $ruangId]);
            $globalCounter = (int)$countExist->fetchColumn() + 1;
        }

        foreach ($targetRuangs as $r) {
            $rId = $r['id'];
            $tipeMeja = (int)($r['tipe_meja'] ?: 1); // 1 = Single, 2 = Double
            $kolom = (int)($r['jumlah_kolom'] ?: 5);
            $baris = (int)($r['jumlah_baris'] ?: 6);
            $pola = $r['pola_penomoran'] ?: 'kolom';
            $kapasitas = (int)$r['kapasitas'];
            if ($kapasitas <= 0) {
                $kapasitas = $tipeMeja * $kolom * $baris;
            }

            // Build desk grid
            $desks = [];
            if ($tipeMeja == 1) {
                // 1 student per desk
                if ($pola === 'kolom') {
                    $mejaNo = 1;
                    for ($k = 1; $k <= $kolom; $k++) {
                        for ($b = 1; $b <= $baris; $b++) {
                            if ($mejaNo <= $kapasitas) {
                                $desks[] = ['meja' => $mejaNo++, 'baris' => $b, 'kolom' => $k, 'bangku' => 1];
                            }
                        }
                    }
                } elseif ($pola === 'baris') {
                    $mejaNo = 1;
                    for ($b = 1; $b <= $baris; $b++) {
                        for ($k = 1; $k <= $kolom; $k++) {
                            if ($mejaNo <= $kapasitas) {
                                $desks[] = ['meja' => $mejaNo++, 'baris' => $b, 'kolom' => $k, 'bangku' => 1];
                            }
                        }
                    }
                } else { // zigzag
                    $mejaNo = 1;
                    for ($b = 1; $b <= $baris; $b++) {
                        $kRange = ($b % 2 == 1) ? range(1, $kolom) : range($kolom, 1);
                        foreach ($kRange as $k) {
                            if ($mejaNo <= $kapasitas) {
                                $desks[] = ['meja' => $mejaNo++, 'baris' => $b, 'kolom' => $k, 'bangku' => 1];
                            }
                        }
                    }
                }
            } else {
                // 2 students per desk (gandeng)
                $mejaNo = 1;
                for ($b = 1; $b <= $baris; $b++) {
                    for ($k = 1; $k <= $kolom; $k++) {
                        if ($mejaNo <= $kapasitas) {
                            $desks[] = ['meja' => $mejaNo++, 'baris' => $b, 'kolom' => $k, 'bangku' => 1];
                        }
                        if ($mejaNo <= $kapasitas) {
                            $desks[] = ['meja' => $mejaNo++, 'baris' => $b, 'kolom' => $k, 'bangku' => 2];
                        }
                    }
                }
            }

            foreach ($desks as $d) {
                if ($stdIdx >= count($students)) {
                    break;
                }
                $st = $students[$stdIdx++];
                $noPeserta = sprintf("%s-%03d", $prefix, $globalCounter++);
                $insertStmt->execute([
                    $eventId,
                    $st['id'],
                    $rId,
                    $noPeserta,
                    $d['meja'],
                    $d['baris'],
                    $d['kolom'],
                    $d['bangku']
                ]);
                $totalAssigned++;
            }

            if ($stdIdx >= count($students)) {
                break;
            }
        }

        $metodeName = ($metode === 'silang') ? 'Acak Silang Antar-Kelas' : (($metode === 'acak') ? 'Acak Murni (Shuffle)' : 'Urut Sesuai Kelas');
        $msg = "Berhasil mengacak dan mengisi $totalAssigned peserta ke meja ruangan ujian dengan metode $metodeName!";

        if (Helper::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $msg, 'total' => $totalAssigned]);
            exit;
        }

        $_SESSION['flash_success'] = $msg;
        $redirectUrl = $_POST['redirect_to'] ?? Helper::url('/asesmen/denah?event_id=' . $eventId . ($ruangId ? '&ruang_id=' . $ruangId : ''));
        header('Location: ' . $redirectUrl);
        exit;
    }

    public function generatePeserta()
    {
        // Re-use acakSiswa method for generate peserta
        $_POST['metode'] = $_POST['metode'] ?? 'acak';
        $_POST['ruang_id'] = (int)($_POST['ruang_id'] ?? 0);
        $_POST['redirect_to'] = Helper::url('/asesmen/peserta?event_id=' . ($_POST['event_id'] ?? 0));
        $this->acakSiswa();
    }

    public function nomorMeja()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $ruangId = (int)($_GET['ruang_id'] ?? 0);

        $events = $db->query("SELECT id, nama, kode FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        if ($eventId === 0 && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $ruangs = $db->query("SELECT id, nama_ruang, kapasitas FROM asesmen_ruang ORDER BY nama_ruang ASC")->fetchAll(PDO::FETCH_ASSOC);

        $sql = "
            SELECT p.*, s.nama as nama_siswa, s.nis, s.nisn, k.nama_kelas, r.nama_ruang, e.nama as nama_event
            FROM asesmen_peserta p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN kelas k ON s.kelas_id = k.id
            LEFT JOIN asesmen_ruang r ON p.ruang_id = r.id
            LEFT JOIN asesmen_event e ON p.event_id = e.id
            WHERE p.event_id = ?
        ";
        $params = [$eventId];

        if ($ruangId > 0) {
            $sql .= " AND p.ruang_id = ?";
            $params[] = $ruangId;
        }

        $sql .= " ORDER BY r.nama_ruang ASC, p.nomor_meja ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pesertaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_nomor_meja';
        $title = 'Cetak Nomor Meja Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/nomor_meja.php';
    }

    public function kartuPeserta()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $ruangId = (int)($_GET['ruang_id'] ?? 0);

        $events = $db->query("SELECT id, nama, kode FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        if ($eventId === 0 && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $ruangs = $db->query("SELECT id, nama_ruang, kapasitas FROM asesmen_ruang ORDER BY nama_ruang ASC")->fetchAll(PDO::FETCH_ASSOC);

        $sql = "
            SELECT p.*, s.nama as nama_siswa, s.nis, s.nisn, s.foto, k.nama_kelas, r.nama_ruang, e.nama as nama_event
            FROM asesmen_peserta p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN kelas k ON s.kelas_id = k.id
            LEFT JOIN asesmen_ruang r ON p.ruang_id = r.id
            LEFT JOIN asesmen_event e ON p.event_id = e.id
            WHERE p.event_id = ?
        ";
        $params = [$eventId];

        if ($ruangId > 0) {
            $sql .= " AND p.ruang_id = ?";
            $params[] = $ruangId;
        }

        $sql .= " ORDER BY r.nama_ruang ASC, p.nomor_meja ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pesertaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_kartu_peserta';
        $title = 'Cetak Kartu Peserta & Nomor Meja | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/kartu_peserta.php';
    }

    public function cetakKartuPeserta()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $ruangId = (int)($_GET['ruang_id'] ?? 0);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        $sql = "
            SELECT p.*, s.nama as nama_siswa, s.nis, s.nisn, s.foto, s.qr_token, k.nama_kelas, r.nama_ruang, e.nama as nama_event, e.kode, e.semester
            FROM asesmen_peserta p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN kelas k ON s.kelas_id = k.id
            LEFT JOIN asesmen_ruang r ON p.ruang_id = r.id
            LEFT JOIN asesmen_event e ON p.event_id = e.id
            WHERE p.event_id = ?
        ";
        $params = [$eventId];

        if ($ruangId > 0) {
            $sql .= " AND p.ruang_id = ?";
            $params[] = $ruangId;
        }

        $sql .= " ORDER BY r.nama_ruang ASC, p.nomor_meja ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pesertaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/asesmen/cetak_kartu_peserta.php';
    }

    public function cetakNomorMeja()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $ruangId = (int)($_GET['ruang_id'] ?? 0);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        $sql = "
            SELECT p.*, s.nama as nama_siswa, s.nis, s.nisn, k.nama_kelas, r.nama_ruang, e.nama as nama_event, e.kode
            FROM asesmen_peserta p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN kelas k ON s.kelas_id = k.id
            LEFT JOIN asesmen_ruang r ON p.ruang_id = r.id
            LEFT JOIN asesmen_event e ON p.event_id = e.id
            WHERE p.event_id = ?
        ";
        $params = [$eventId];

        if ($ruangId > 0) {
            $sql .= " AND p.ruang_id = ?";
            $params[] = $ruangId;
        }

        $sql .= " ORDER BY r.nama_ruang ASC, p.nomor_meja ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pesertaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/asesmen/cetak_nomor_meja.php';
    }

    public function daftarHadir()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $events = $db->query("SELECT id, nama FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $ruangs = $db->query("SELECT id, nama_ruang FROM asesmen_ruang ORDER BY nama_ruang ASC")->fetchAll(PDO::FETCH_ASSOC);
        $mapels = $db->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_daftar_hadir';
        $title = 'Daftar Hadir Peserta Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/daftar_hadir.php';
    }

    public function cetakDaftarHadir()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $ruangId = (int)($_GET['ruang_id'] ?? 0);
        $mapelId = (int)($_GET['mapel_id'] ?? 0);
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        $stmtEv = $db->prepare("SELECT * FROM asesmen_event WHERE id = ?");
        $stmtEv->execute([$eventId]);
        $event = $stmtEv->fetch(PDO::FETCH_ASSOC);

        $stmtR = $db->prepare("SELECT * FROM asesmen_ruang WHERE id = ?");
        $stmtR->execute([$ruangId]);
        $ruang = $stmtR->fetch(PDO::FETCH_ASSOC);

        $stmtM = $db->prepare("SELECT * FROM mapel WHERE id = ?");
        $stmtM->execute([$mapelId]);
        $mapel = $stmtM->fetch(PDO::FETCH_ASSOC);

        $stmtP = $db->prepare("
            SELECT p.*, s.nama as nama_siswa, s.nis, s.nisn, k.nama_kelas
            FROM asesmen_peserta p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN kelas k ON s.kelas_id = k.id
            WHERE p.event_id = ? AND p.ruang_id = ?
            ORDER BY p.nomor_meja ASC
        ");
        $stmtP->execute([$eventId, $ruangId]);
        $pesertaList = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/asesmen/cetak_daftar_hadir.php';
    }

    public function daftarHadirPengawas()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $events = $db->query("SELECT id, nama FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_pengawas';
        $title = 'Daftar Hadir Pengawas Ruang | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/daftar_hadir_pengawas.php';
    }

    public function cetakDaftarHadirPengawas()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        $stmtEv = $db->prepare("SELECT * FROM asesmen_event WHERE id = ?");
        $stmtEv->execute([$eventId]);
        $event = $stmtEv->fetch(PDO::FETCH_ASSOC);

        $ruangList = $db->query("SELECT * FROM asesmen_ruang ORDER BY nama_ruang ASC")->fetchAll(PDO::FETCH_ASSOC);
        $gurus = $db->query("SELECT id, nama, nip, niy FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/asesmen/cetak_daftar_hadir_pengawas.php';
    }

    public function tataTertib()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $events = $db->query("SELECT id, nama FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_tata_tertib';
        $title = 'Tata Tertib Ujian Madrasah | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/tata_tertib.php';
    }

    public function cetakTataTertib()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $tipe = $_GET['tipe'] ?? 'peserta'; // peserta / pengawas
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/asesmen/cetak_tata_tertib.php';
    }

    public function beritaAcara()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $baList = $db->query("
            SELECT ba.*, r.nama_ruang, e.nama as nama_event, m.nama_mapel,
                   g1.nama as nama_pengawas1, g2.nama as nama_pengawas2
            FROM asesmen_berita_acara ba
            LEFT JOIN asesmen_ruang r ON ba.ruang_id = r.id
            LEFT JOIN asesmen_event e ON ba.event_id = e.id
            LEFT JOIN asesmen_jadwal j ON ba.jadwal_id = j.id
            LEFT JOIN mapel m ON j.mapel_id = m.id
            LEFT JOIN guru g1 ON ba.pengawas_1_id = g1.id
            LEFT JOIN guru g2 ON ba.pengawas_2_id = g2.id
            ORDER BY ba.tanggal DESC, ba.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $events = $db->query("SELECT id, nama FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $ruangs = $db->query("SELECT id, nama_ruang FROM asesmen_ruang ORDER BY nama_ruang ASC")->fetchAll(PDO::FETCH_ASSOC);
        $gurus = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        $mapels = $db->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_berita_acara';
        $title = 'Berita Acara Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/berita_acara.php';
    }

    public function saveBeritaAcara()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $event_id = (int)($_POST['event_id'] ?? 0);
        $ruang_id = (int)($_POST['ruang_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $jam_mulai = $_POST['jam_mulai'] ?? '07:30';
        $jam_selesai = $_POST['jam_selesai'] ?? '09:00';
        $pengawas1 = !empty($_POST['pengawas_1_id']) ? (int)$_POST['pengawas_1_id'] : null;
        $pengawas2 = !empty($_POST['pengawas_2_id']) ? (int)$_POST['pengawas_2_id'] : null;
        $seharusnya = (int)($_POST['jumlah_seharusnya'] ?? 20);
        $hadir = (int)($_POST['jumlah_hadir'] ?? 20);
        $tidak_hadir = (int)($_POST['jumlah_tidak_hadir'] ?? 0);
        $peserta_tidak_hadir = trim($_POST['peserta_tidak_hadir'] ?? '');
        $catatan = trim($_POST['catatan_kejadian'] ?? '');

        if ($event_id > 0 && $ruang_id > 0) {
            $stmt = $db->prepare("
                INSERT INTO asesmen_berita_acara 
                (event_id, ruang_id, tanggal, jam_mulai, jam_selesai, pengawas_1_id, pengawas_2_id, jumlah_seharusnya, jumlah_hadir, jumlah_tidak_hadir, peserta_tidak_hadir, catatan_kejadian)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$event_id, $ruang_id, $tanggal, $jam_mulai, $jam_selesai, $pengawas1, $pengawas2, $seharusnya, $hadir, $tidak_hadir, $peserta_tidak_hadir, $catatan]);
            $_SESSION['flash_success'] = 'Berita Acara ujian berhasil disimpan.';
        }

        header('Location: ' . Helper::url('/asesmen/berita-acara'));
        exit;
    }

    public function cetakBeritaAcara()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $id = (int)($_GET['id'] ?? 0);
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("
            SELECT ba.*, r.nama_ruang, r.lokasi, e.nama as nama_event, e.kode, e.semester,
                   m.nama_mapel, g1.nama as nama_pengawas1, g1.niy as niy_pengawas1,
                   g2.nama as nama_pengawas2, g2.niy as niy_pengawas2
            FROM asesmen_berita_acara ba
            LEFT JOIN asesmen_ruang r ON ba.ruang_id = r.id
            LEFT JOIN asesmen_event e ON ba.event_id = e.id
            LEFT JOIN asesmen_jadwal j ON ba.jadwal_id = j.id
            LEFT JOIN mapel m ON j.mapel_id = m.id
            LEFT JOIN guru g1 ON ba.pengawas_1_id = g1.id
            LEFT JOIN guru g2 ON ba.pengawas_2_id = g2.id
            WHERE ba.id = ?
        ");
        $stmt->execute([$id]);
        $ba = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ba) {
            // Sample fallback if ID not found
            $ba = [
                'nama_event' => 'Asesmen Sumatif Tengah Semester (STS) Ganjil',
                'semester' => 'Ganjil',
                'tanggal' => date('Y-m-d'),
                'jam_mulai' => '07:30',
                'jam_selesai' => '09:00',
                'nama_ruang' => 'Ruang 01',
                'nama_mapel' => 'Bahasa Indonesia',
                'jumlah_seharusnya' => 20,
                'jumlah_hadir' => 20,
                'jumlah_tidak_hadir' => 0,
                'peserta_tidak_hadir' => '-',
                'catatan_kejadian' => 'Pelaksanaan ujian berjalan tertib, aman, dan lancar tanpa kendala.',
                'nama_pengawas1' => $institusi['nama_kepala'] ?? 'Pengawas 1',
                'niy_pengawas1' => '-',
                'nama_pengawas2' => '-',
                'niy_pengawas2' => '-'
            ];
        }

        require_once __DIR__ . '/../../resources/views/asesmen/cetak_berita_acara.php';
    }

    public function jadwal()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $jadwalList = $db->query("
            SELECT j.*, m.nama_mapel, e.nama as nama_event
            FROM asesmen_jadwal j
            LEFT JOIN mapel m ON j.mapel_id = m.id
            LEFT JOIN asesmen_event e ON j.event_id = e.id
            ORDER BY j.tanggal ASC, j.jam_mulai ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $events = $db->query("SELECT id, nama FROM asesmen_event WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $mapels = $db->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'asesmen_jadwal';
        $title = 'Jadwal Pelaksanaan Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/jadwal.php';
    }

    public function saveJadwal()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $event_id = (int)($_POST['event_id'] ?? 0);
        $mapel_id = (int)($_POST['mapel_id'] ?? 0);
        $tingkat = $_POST['tingkat'] ?? 'Semua';
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $jam_mulai = $_POST['jam_mulai'] ?? '07:30';
        $jam_selesai = $_POST['jam_selesai'] ?? '09:00';
        $durasi = (int)($_POST['durasi_menit'] ?? 90);

        if ($event_id > 0 && $mapel_id > 0) {
            $stmt = $db->prepare("INSERT INTO asesmen_jadwal (event_id, mapel_id, tingkat, tanggal, jam_mulai, jam_selesai, durasi_menit) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$event_id, $mapel_id, $tingkat, $tanggal, $jam_mulai, $jam_selesai, $durasi]);
            $_SESSION['flash_success'] = 'Jadwal ujian berhasil ditambahkan.';
        }

        header('Location: ' . Helper::url('/asesmen/jadwal'));
        exit;
    }

    public function cetakJadwal()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $eventId = (int)($_GET['event_id'] ?? 0);
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        $sql = "
            SELECT j.*, m.nama_mapel, e.nama as nama_event, e.kode, e.semester
            FROM asesmen_jadwal j
            LEFT JOIN mapel m ON j.mapel_id = m.id
            LEFT JOIN asesmen_event e ON j.event_id = e.id
        ";
        if ($eventId > 0) {
            $sql .= " WHERE j.event_id = $eventId";
        }
        $sql .= " ORDER BY j.tanggal ASC, j.jam_mulai ASC";

        $jadwalList = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/asesmen/cetak_jadwal.php';
    }
}
