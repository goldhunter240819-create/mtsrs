<?php

namespace App\Controllers;

use App\Core\Helper;
use App\Core\Database;
use PDO;

class BkController
{
    private function checkAccess()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    private function getActiveTahunAjaran()
    {
        return \App\Core\AcademicYear::current()['id'] ?? 1;
    }

    public function index()
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $ta_id = $this->getActiveTahunAjaran();

        // Get Top 5 Pelanggar (Siswa dengan poin tertinggi tahun ini)
        $stmt = $db->prepare("
            SELECT s.nama as nama_siswa, k.nama_kelas, SUM(kat.poin) as total_poin
            FROM bk_poin bp
            JOIN siswa s ON bp.siswa_id = s.id
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = ?
            JOIN kelas k ON rks.kelas_id = k.id
            JOIN bk_kategori kat ON bp.kategori_id = kat.id
            WHERE bp.tahun_ajaran_id = ? AND kat.tipe = 'pelanggaran'
            GROUP BY s.id
            ORDER BY total_poin DESC
            LIMIT 5
        ");
        $stmt->execute([$ta_id, $ta_id]);
        $topPelanggar = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get recent entries
        $stmt2 = $db->prepare("
            SELECT bp.*, s.nama as nama_siswa, k.nama_kelas, kat.nama_kategori, kat.poin, kat.tipe, g.nama as nama_guru
            FROM bk_poin bp
            JOIN siswa s ON bp.siswa_id = s.id
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = bp.tahun_ajaran_id
            JOIN kelas k ON rks.kelas_id = k.id
            JOIN bk_kategori kat ON bp.kategori_id = kat.id
            JOIN guru g ON bp.guru_id = g.id
            WHERE bp.tahun_ajaran_id = ?
            ORDER BY bp.created_at DESC
            LIMIT 10
        ");
        $stmt2->execute([$ta_id]);
        $recentLog = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'bk_dashboard';
        $title = 'Dashboard E-BK | MTs RS';
        require_once __DIR__ . '/../../resources/views/bk/dashboard.php';
    }

    public function kategori()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $kategoriList = $db->query("SELECT * FROM bk_kategori ORDER BY tipe, poin ASC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'bk_kategori';
        $title = 'Kategori Poin BK | MTs RS';
        require_once __DIR__ . '/../../resources/views/bk/kategori.php';
    }

    public function saveKategori()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $id = $_POST['id'] ?? '';
        $nama = $_POST['nama_kategori'] ?? '';
        $tipe = $_POST['tipe'] ?? 'pelanggaran';
        $tingkat = $_POST['tingkat'] ?? null;
        if ($tipe === 'prestasi') {
            $tingkat = null;
        }
        $poin = (int)($_POST['poin'] ?? 0);

        if ($id) {
            $stmt = $db->prepare("UPDATE bk_kategori SET nama_kategori = ?, tipe = ?, tingkat = ?, poin = ? WHERE id = ?");
            $stmt->execute([$nama, $tipe, $tingkat, $poin, $id]);
            $_SESSION['flash_success'] = 'Kategori berhasil diupdate.';
        } else {
            $stmt = $db->prepare("INSERT INTO bk_kategori (nama_kategori, tipe, tingkat, poin) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $tipe, $tingkat, $poin]);
            $_SESSION['flash_success'] = 'Kategori berhasil ditambahkan.';
        }

        Helper::redirect('/bk/kategori');
    }

    public function deleteKategori($id)
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $stmt = $db->prepare("DELETE FROM bk_kategori WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Kategori berhasil dihapus.';
        Helper::redirect('/bk/kategori');
    }

    public function pelanggaran()
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $ta_id = $this->getActiveTahunAjaran();

        // Ambil list siswa untuk dropdown
        $stmtSiswa = $db->prepare("
            SELECT s.id, s.nama as nama_siswa, k.nama_kelas 
            FROM siswa s 
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = ?
            JOIN kelas k ON rks.kelas_id = k.id
            ORDER BY k.nama_kelas ASC, s.nama ASC
        ");
        $stmtSiswa->execute([$ta_id]);
        $siswaList = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

        // Ambil list kategori
        $kategoriList = $db->query("SELECT * FROM bk_kategori ORDER BY tipe ASC, poin ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Ambil riwayat input poin
        $stmtLog = $db->prepare("
            SELECT bp.*, s.nama as nama_siswa, k.nama_kelas, kat.nama_kategori, kat.poin, kat.tipe, g.nama as nama_guru
            FROM bk_poin bp
            JOIN siswa s ON bp.siswa_id = s.id
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = bp.tahun_ajaran_id
            JOIN kelas k ON rks.kelas_id = k.id
            JOIN bk_kategori kat ON bp.kategori_id = kat.id
            JOIN guru g ON bp.guru_id = g.id
            WHERE bp.tahun_ajaran_id = ?
            ORDER BY bp.created_at DESC
        ");
        $stmtLog->execute([$ta_id]);
        $riwayat = $stmtLog->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'bk_pelanggaran';
        $title = 'Catat Poin Siswa | MTs RS';
        require_once __DIR__ . '/../../resources/views/bk/pelanggaran.php';
    }

    public function savePelanggaran()
    {
        $this->checkAccess();
        $db = Database::connect('core');
        
        $siswa_id = $_POST['siswa_id'] ?? '';
        $kategori_id = $_POST['kategori_id'] ?? '';
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $keterangan = $_POST['keterangan'] ?? '';
        
        $guru_id = $_SESSION['guru_id'] ?? 1; // Fallback to 1 if admin
        $ta_id = $this->getActiveTahunAjaran();

        if ($siswa_id && $kategori_id) {
            $stmt = $db->prepare("INSERT INTO bk_poin (siswa_id, kategori_id, tahun_ajaran_id, guru_id, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$siswa_id, $kategori_id, $ta_id, $guru_id, $tanggal, $keterangan]);
            $_SESSION['flash_success'] = 'Catatan poin berhasil disimpan!';
        }

        Helper::redirect('/bk/pelanggaran');
    }

    public function deletePelanggaran($id)
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $stmt = $db->prepare("DELETE FROM bk_poin WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Catatan poin berhasil dihapus.';
        Helper::redirect('/bk/pelanggaran');
    }
    public function laporan()
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $ta_id = $this->getActiveTahunAjaran();

        // Get class list
        $kelasList = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Get student list
        $stmtSiswa = $db->prepare("
            SELECT s.id, s.nama as nama_siswa, k.nama_kelas 
            FROM siswa s 
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = ?
            JOIN kelas k ON rks.kelas_id = k.id
            ORDER BY k.nama_kelas ASC, s.nama ASC
        ");
        $stmtSiswa->execute([$ta_id]);
        $siswaList = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'bk_laporan';
        $title = 'Cetak Laporan BK | MTs RS';
        require_once __DIR__ . '/../../resources/views/bk/laporan.php';
    }

    public function cetakKelas()
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $ta_id = $this->getActiveTahunAjaran();
        $kelas_id = $_GET['kelas_id'] ?? '';

        if (!$kelas_id) {
            Helper::redirect('/bk/laporan');
        }

        // Get info kelas
        $stmtK = $db->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmtK->execute([$kelas_id]);
        $kelas = $stmtK->fetch(PDO::FETCH_ASSOC);

        // Get Wali Kelas from Siakad DB
        $dbSiakad = Database::connect();
        $stmtWali = $dbSiakad->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.keterangan LIKE ? ORDER BY gt.id DESC LIMIT 1");
        $stmtWali->execute(['%' . $kelas['nama_kelas'] . '%']);
        $kelas['nama_wali'] = $stmtWali->fetchColumn();

        // Get class list for filter
        $kelasList = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Get list of all teachers for dropdown
        $listGuru = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Get selected BK teacher (from GET or logged in user)
        $guru_id_cetak = $_GET['guru_id'] ?? ($_SESSION['user_id'] ?? 0);
        $stmtBk = $db->prepare("SELECT nama FROM guru WHERE id = ?");
        $stmtBk->execute([$guru_id_cetak]);
        $guruBk = $stmtBk->fetchColumn() ?: '......................';

        $tempat_cetak = $_GET['tempat'] ?? 'Gunung Terang';
        $tgl_cetak = $_GET['tanggal'] ?? date('Y-m-d');

        // Get students in this class and their total points
        $stmt = $db->prepare("
            SELECT s.id, s.nama, s.nis, s.nisn, 
                   COALESCE(SUM(kat.poin), 0) as total_poin
            FROM siswa s
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = ?
            LEFT JOIN bk_poin bp ON s.id = bp.siswa_id AND bp.tahun_ajaran_id = ?
            LEFT JOIN bk_kategori kat ON bp.kategori_id = kat.id AND kat.tipe = 'pelanggaran'
            WHERE rks.kelas_id = ?
            GROUP BY s.id
            ORDER BY s.nama ASC
        ");
        $stmt->execute([$ta_id, $ta_id, $kelas_id]);
        $siswaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $title = 'Cetak Rekap BK Kelas ' . $kelas['nama_kelas'];
        
        // Setup institusi info if needed
        $dbSiakad = Database::connect();
        $stmtInst = $dbSiakad->query("SELECT * FROM institusi LIMIT 1");
        $institusi = $stmtInst->fetch(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/bk/cetak_kelas.php';
    }

    public function cetakSiswa()
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $ta_id = $this->getActiveTahunAjaran();
        $siswa_id = $_GET['siswa_id'] ?? '';

        if (!$siswa_id) {
            Helper::redirect('/bk/laporan');
        }

        // Get student info
        $stmtS = $db->prepare("
            SELECT s.*, k.nama_kelas 
            FROM siswa s
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = ?
            JOIN kelas k ON rks.kelas_id = k.id
            WHERE s.id = ?
        ");
        $stmtS->execute([$ta_id, $siswa_id]);
        $siswa = $stmtS->fetch(PDO::FETCH_ASSOC);

        // Get Wali Kelas from Siakad DB
        $dbSiakad = Database::connect();
        $stmtWali = $dbSiakad->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.keterangan LIKE ? ORDER BY gt.id DESC LIMIT 1");
        $stmtWali->execute(['%' . $siswa['nama_kelas'] . '%']);
        $siswa['nama_wali'] = $stmtWali->fetchColumn();

        // Get student list for filter
        $stmtSiswa = $db->prepare("
            SELECT s.id, s.nama as nama_siswa, k.nama_kelas 
            FROM siswa s 
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = ?
            JOIN kelas k ON rks.kelas_id = k.id
            ORDER BY k.nama_kelas ASC, s.nama ASC
        ");
        $stmtSiswa->execute([$ta_id]);
        $siswaList = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

        // Get list of all teachers for dropdown
        $listGuru = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Get selected BK teacher (from GET or logged in user)
        $guru_id_cetak = $_GET['guru_id'] ?? ($_SESSION['user_id'] ?? 0);
        $stmtBk = $db->prepare("SELECT nama FROM guru WHERE id = ?");
        $stmtBk->execute([$guru_id_cetak]);
        $guruBk = $stmtBk->fetchColumn() ?: '......................';

        $tempat_cetak = $_GET['tempat'] ?? 'Gunung Terang';
        $tgl_cetak = $_GET['tanggal'] ?? date('Y-m-d');
        $stmtS->execute([$ta_id, $siswa_id]);
        $siswa = $stmtS->fetch(PDO::FETCH_ASSOC);

        // Get student's infractions
        $stmtLog = $db->prepare("
            SELECT bp.*, kat.nama_kategori, kat.poin, kat.tipe, g.nama as nama_guru
            FROM bk_poin bp
            JOIN bk_kategori kat ON bp.kategori_id = kat.id
            JOIN guru g ON bp.guru_id = g.id
            WHERE bp.siswa_id = ? AND bp.tahun_ajaran_id = ?
            ORDER BY bp.tanggal ASC, bp.created_at ASC
        ");
        $stmtLog->execute([$siswa_id, $ta_id]);
        $riwayat = $stmtLog->fetchAll(PDO::FETCH_ASSOC);

        $totalPoin = 0;
        foreach ($riwayat as $r) {
            if ($r['tipe'] === 'pelanggaran') {
                $totalPoin += (int)$r['poin'];
            }
        }

        $title = 'Surat Panggilan / Detail Pelanggaran - ' . $siswa['nama'];
        
        // Setup institusi info if needed
        $dbSiakad = Database::connect();
        $stmtInst = $dbSiakad->query("SELECT * FROM institusi LIMIT 1");
        $institusi = $stmtInst->fetch(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/bk/cetak_siswa.php';
    }
}
