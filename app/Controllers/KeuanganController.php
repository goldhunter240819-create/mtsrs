<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Branding;
use App\Core\AcademicYear;
use App\Core\Helper;

class KeuanganController {

    public function dashboard() {
        $db = Database::connect();

        $statPemasukan = $db->query("SELECT COALESCE(SUM(nominal), 0) FROM keuangan_transaksi")->fetchColumn();
        $statTagihanTotal = $db->query("SELECT COALESCE(SUM(nominal), 0) FROM keuangan_tagihan")->fetchColumn();
        $statTunggakan = $db->query("SELECT COALESCE(SUM(nominal - terbayar), 0) FROM keuangan_tagihan WHERE status != 'Lunas'")->fetchColumn();
        $statTabunganTotal = $db->query("SELECT COALESCE(SUM(saldo), 0) FROM keuangan_tabungan")->fetchColumn();

        $recentTrx = $db->query("SELECT t.*, s.nama as nama_siswa, k.nama_komponen 
            FROM keuangan_transaksi t 
            JOIN siswa s ON t.siswa_id = s.id 
            JOIN keuangan_tagihan tg ON t.tagihan_id = tg.id 
            JOIN keuangan_komponen k ON tg.komponen_id = k.id 
            ORDER BY t.tanggal_bayar DESC LIMIT 5")->fetchAll();

        $title = "Dashboard Keuangan - MTs RS System";
        $activeMenu = 'keuangan_dashboard';

        ob_start();
        include __DIR__ . '/../../resources/views/keuangan/dashboard.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function komponen() {
        $db = Database::connect();
        $komponenList = $db->query("SELECT * FROM keuangan_komponen ORDER BY id ASC")->fetchAll();

        $title = "Komponen Tagihan - Keuangan MTs RS";
        $activeMenu = 'keuangan_komponen';

        ob_start();
        include __DIR__ . '/../../resources/views/keuangan/komponen.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function komponenSave() {
        $db = Database::connect();

        $nama_komponen = trim($_POST['nama_komponen'] ?? '');
        $jenis = $_POST['jenis'] ?? 'SPP';
        $nominal = floatval($_POST['nominal_default'] ?? 0);

        if (!empty($nama_komponen)) {
            $stmt = $db->prepare("INSERT INTO keuangan_komponen (nama_komponen, jenis, nominal_default) VALUES (?, ?, ?)");
            $stmt->execute([$nama_komponen, $jenis, $nominal]);
        }

        Helper::redirect('/keuangan/komponen');
    }

    public function tagihan() {
        $db = Database::connect();

        $kelasList = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $komponenList = $db->query("SELECT * FROM keuangan_komponen WHERE is_active = 1")->fetchAll();

        $tagihanList = $db->query("SELECT t.*, s.nama as nama_siswa, s.nis, k.nama_komponen, kl.nama_kelas 
            FROM keuangan_tagihan t 
            JOIN siswa s ON t.siswa_id = s.id 
            JOIN keuangan_komponen k ON t.komponen_id = k.id 
            LEFT JOIN kelas kl ON s.kelas_id = kl.id 
            ORDER BY t.id DESC LIMIT 50")->fetchAll();

        $title = "Daftar Tagihan Siswa - Keuangan MTs RS";
        $activeMenu = 'keuangan_tagihan';

        ob_start();
        include __DIR__ . '/../../resources/views/keuangan/tagihan.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function tagihanGenerate() {
        $db = Database::connect();
        $activeYear = AcademicYear::current();

        $komponen_id = intval($_POST['komponen_id'] ?? 0);
        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        $bulan = trim($_POST['bulan'] ?? '');

        if ($komponen_id > 0) {
            $stmtK = $db->prepare("SELECT nominal_default FROM keuangan_komponen WHERE id = ?");
            $stmtK->execute([$komponen_id]);
            $nominal = $stmtK->fetchColumn();

            $sqlSiswa = "SELECT id FROM siswa WHERE status = 'Aktif'";
            if ($kelas_id > 0) {
                $sqlSiswa .= " AND kelas_id = " . $kelas_id;
            }
            $siswaIds = $db->query($sqlSiswa)->fetchAll(\PDO::FETCH_COLUMN);

            $stmtIns = $db->prepare("INSERT INTO keuangan_tagihan (siswa_id, komponen_id, tahun_ajaran_id, bulan, nominal, terbayar, status) VALUES (?, ?, ?, ?, ?, 0.00, 'Belum')");

            foreach ($siswaIds as $sid) {
                $stmtIns->execute([$sid, $komponen_id, $activeYear['id'], $bulan, $nominal]);
            }
        }

        Helper::redirect('/keuangan/tagihan');
    }

    public function pembayaran() {
        $db = Database::connect();

        $tagihanId = $_GET['tagihan_id'] ?? null;
        $tagihanDetail = null;

        if ($tagihanId) {
            $stmtT = $db->prepare("SELECT t.*, s.nama as nama_siswa, s.nis, k.nama_komponen 
                FROM keuangan_tagihan t 
                JOIN siswa s ON t.siswa_id = s.id 
                JOIN keuangan_komponen k ON t.komponen_id = k.id 
                WHERE t.id = ?");
            $stmtT->execute([$tagihanId]);
            $tagihanDetail = $stmtT->fetch();
        }

        $pendingTagihan = $db->query("SELECT t.*, s.nama as nama_siswa, s.nis, k.nama_komponen, kl.nama_kelas 
            FROM keuangan_tagihan t 
            JOIN siswa s ON t.siswa_id = s.id 
            JOIN keuangan_komponen k ON t.komponen_id = k.id 
            LEFT JOIN kelas kl ON s.kelas_id = kl.id 
            WHERE t.status != 'Lunas' 
            ORDER BY s.nama ASC")->fetchAll();

        $title = "Form Pembayaran SPP & Tagihan - Keuangan MTs RS";
        $activeMenu = 'keuangan_pembayaran';

        ob_start();
        include __DIR__ . '/../../resources/views/keuangan/pembayaran.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function pembayaranProcess() {
        $db = Database::connect();

        $tagihan_id = intval($_POST['tagihan_id'] ?? 0);
        $nominal_bayar = floatval($_POST['nominal'] ?? 0);
        $metode = $_POST['metode_pembayaran'] ?? 'Tunai';

        if ($tagihan_id > 0 && $nominal_bayar > 0) {
            $stmtT = $db->prepare("SELECT * FROM keuangan_tagihan WHERE id = ?");
            $stmtT->execute([$tagihan_id]);
            $tagihan = $stmtT->fetch();

            if ($tagihan) {
                $terbayarBaru = floatval($tagihan['terbayar']) + $nominal_bayar;
                $statusBaru = ($terbayarBaru >= floatval($tagihan['nominal'])) ? 'Lunas' : 'Sebagian';

                // Update tagihan
                $db->prepare("UPDATE keuangan_tagihan SET terbayar = ?, status = ? WHERE id = ?")->execute([$terbayarBaru, $statusBaru, $tagihan_id]);

                // Insert transaksi
                $kodeTrx = 'TRX-' . date('Ymd') . '-' . rand(1000, 9999);
                $noKwitansi = 'KW-' . date('Ym') . '-' . sprintf('%04d', rand(1, 9999));

                $stmtTrx = $db->prepare("INSERT INTO keuangan_transaksi (kode_trx, tagihan_id, siswa_id, nominal, metode_pembayaran, nomor_kwitansi, petugas_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmtTrx->execute([$kodeTrx, $tagihan_id, $tagihan['siswa_id'], $nominal_bayar, $metode, $noKwitansi, $_SESSION['user_id']]);
                $trxId = $db->lastInsertId();

                Helper::redirect("/keuangan/kwitansi/$trxId");
            }
        }

        Helper::redirect('/keuangan/pembayaran');
    }

    public function kwitansi($id) {
        $db = Database::connect();
        $institusi = Branding::getInstitusi();

        $stmt = $db->prepare("SELECT t.*, s.nama as nama_siswa, s.nis, kl.nama_kelas, k.nama_komponen, tg.bulan 
            FROM keuangan_transaksi t 
            JOIN siswa s ON t.siswa_id = s.id 
            LEFT JOIN kelas kl ON s.kelas_id = kl.id 
            JOIN keuangan_tagihan tg ON t.tagihan_id = tg.id 
            JOIN keuangan_komponen k ON tg.komponen_id = k.id 
            WHERE t.id = ?");
        $stmt->execute([$id]);
        $trx = $stmt->fetch();

        if (!$trx) {
            echo "Kwitansi tidak ditemukan.";
            exit;
        }

        include __DIR__ . '/../../resources/views/keuangan/kwitansi.php';
    }

    public function tabungan() {
        $db = Database::connect();

        $tabunganList = $db->query("SELECT t.*, s.nama as nama_siswa, s.nis, k.nama_kelas 
            FROM keuangan_tabungan t 
            JOIN siswa s ON t.siswa_id = s.id 
            LEFT JOIN kelas k ON s.kelas_id = k.id 
            ORDER BY s.nama ASC")->fetchAll();

        $recentMutasi = $db->query("SELECT m.*, s.nama as nama_siswa 
            FROM keuangan_tabungan_mutasi m 
            JOIN siswa s ON m.siswa_id = s.id 
            ORDER BY m.created_at DESC LIMIT 20")->fetchAll();

        $title = "Tabungan Siswa - Keuangan MTs RS";
        $activeMenu = 'keuangan_tabungan';

        ob_start();
        include __DIR__ . '/../../resources/views/keuangan/tabungan.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function tabunganMutasi() {
        $db = Database::connect();

        $siswa_id = intval($_POST['siswa_id'] ?? 0);
        $jenis = $_POST['jenis'] ?? 'Setor';
        $nominal = floatval($_POST['nominal'] ?? 0);
        $keterangan = trim($_POST['keterangan'] ?? '');

        if ($siswa_id > 0 && $nominal > 0) {
            $stmtT = $db->prepare("SELECT * FROM keuangan_tabungan WHERE siswa_id = ?");
            $stmtT->execute([$siswa_id]);
            $tab = $stmtT->fetch();

            if (!$tab) {
                $db->prepare("INSERT INTO keuangan_tabungan (siswa_id, saldo) VALUES (?, 0.00)")->execute([$siswa_id]);
                $saldoAwal = 0.00;
                $tabId = $db->lastInsertId();
            } else {
                $saldoAwal = floatval($tab['saldo']);
                $tabId = $tab['id'];
            }

            if ($jenis === 'Setor') {
                $saldoAkhir = $saldoAwal + $nominal;
            } else {
                if ($nominal > $saldoAwal) {
                    Helper::redirect('/keuangan/tabungan?error=saldo_insufficient');
                }
                $saldoAkhir = $saldoAwal - $nominal;
            }

            $db->prepare("UPDATE keuangan_tabungan SET saldo = ? WHERE id = ?")->execute([$saldoAkhir, $tabId]);
            $db->prepare("INSERT INTO keuangan_tabungan_mutasi (tabungan_id, siswa_id, jenis, nominal, saldo_akhir, keterangan, petugas_id) VALUES (?, ?, ?, ?, ?, ?, ?)")
               ->execute([$tabId, $siswa_id, $jenis, $nominal, $saldoAkhir, $keterangan, $_SESSION['user_id']]);
        }

        Helper::redirect('/keuangan/tabungan');
    }
}
