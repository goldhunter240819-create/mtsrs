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
                lokasi VARCHAR(100) NULL,
                keterangan VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $db->exec("CREATE TABLE IF NOT EXISTS asesmen_peserta (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                siswa_id INT NOT NULL,
                ruang_id INT NULL,
                nomor_peserta VARCHAR(50) NULL,
                nomor_meja INT NULL,
                sesi INT DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_event_siswa (event_id, siswa_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

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

            $checked = true;
        } catch (\Exception $e) {
            // Ignore error
        }
    }

    public function dashboard()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $activeTA = AcademicYear::current();
        $ta_id = $activeTA['id'] ?? 1;

        // Fetch counts for dashboard stats
        $totalEvent = $db->query("SELECT COUNT(*) FROM asesmen_event WHERE status = 'aktif'")->fetchColumn() ?: 0;
        $totalBankSoal = $db->query("SELECT COUNT(*) FROM asesmen_bank_soal")->fetchColumn() ?: 0;
        $totalRuang = $db->query("SELECT COUNT(*) FROM asesmen_ruang")->fetchColumn() ?: 0;
        $totalPeserta = $db->query("SELECT COUNT(DISTINCT siswa_id) FROM asesmen_peserta")->fetchColumn() ?: 0;
        $totalBeritaAcara = $db->query("SELECT COUNT(*) FROM asesmen_berita_acara")->fetchColumn() ?: 0;

        // Fetch active events
        $stmtEvents = $db->query("SELECT * FROM asesmen_event ORDER BY status ASC, id DESC LIMIT 10");
        $events = $stmtEvents->fetchAll(PDO::FETCH_ASSOC);

        // Fetch recent bank soal
        $stmtSoal = $db->query("
            SELECT bs.*, m.nama_mapel, g.nama as nama_guru 
            FROM asesmen_bank_soal bs
            LEFT JOIN mapel m ON bs.mapel_id = m.id
            LEFT JOIN guru g ON bs.guru_id = g.id
            ORDER BY bs.id DESC LIMIT 5
        ");
        $recentSoal = $stmtSoal->fetchAll(PDO::FETCH_ASSOC);

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
        $title = 'Bank Soal & Kisi-Kisi | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/bank_soal.php';
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

        $nama_ruang = trim($_POST['nama_ruang'] ?? '');
        $kapasitas = (int)($_POST['kapasitas'] ?? 20);
        $lokasi = trim($_POST['lokasi'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $event_id = !empty($_POST['event_id']) ? (int)$_POST['event_id'] : null;

        if ($nama_ruang !== '') {
            $stmt = $db->prepare("INSERT INTO asesmen_ruang (event_id, nama_ruang, kapasitas, lokasi, keterangan) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$event_id, $nama_ruang, $kapasitas, $lokasi, $keterangan]);
            $_SESSION['flash_success'] = 'Ruang ujian berhasil ditambahkan.';
        }

        header('Location: ' . Helper::url('/asesmen/ruang'));
        exit;
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

        $activeMenu = 'asesmen_berita_acara';
        $title = 'Berita Acara Ujian | MTs RS';

        require_once __DIR__ . '/../../resources/views/asesmen/berita_acara.php';
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
}
