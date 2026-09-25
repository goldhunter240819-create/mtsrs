<?php

namespace App\Controllers;

use App\Core\Database;

class AdminBosController {

    private static function guard($required_menu = '') {
        if (!isset($_SESSION['role_id'])) {
            header('Location: /');
            exit;
        }
        $role_id = $_SESSION['role_id'];
        if (in_array($role_id, array(1, 99))) return; // Superadmin bebas
        
        // TODO: Implement specific BOS access rights later
        // For now, only Superadmin can access
        header('Location: /admin/portal?msg=no_access');
        exit;
    }

    private static function render($view, $data = array(), $required_menu = '') {
        self::guard($required_menu);
        $data['adminNama']    = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Administrator';
        $data['adminInitial'] = strtoupper(substr($data['adminNama'], 0, 2));
        $data['active_module'] = 'bos';
        $data['allowed_menus'] = ['all']; 
        
        extract($data);
        ob_start();
        include __DIR__ . '/../../resources/views/keuangan/' . $view . '.php';
        $content = ob_get_clean();
        
        $activeMenu = isset($active_page) ? $active_page : $view;
        // Prefix with bos_ so sidebar matches
        $activeMenu = 'bos_' . $activeMenu;
        
        $title = isset($page_title) ? $page_title : 'Manajemen Dana BOS';
        require __DIR__ . '/../../resources/views/layout.php';
    }

    public static function bosPemasukan() {
        $db = Database::connect('core');
        $data = $db->query("SELECT id, tanggal_transaksi as tanggal, keterangan, kategori as sumber, jumlah FROM keuangan_bos_transaksi WHERE jenis='Pemasukan' ORDER BY tanggal_transaksi DESC")->fetchAll();
        self::render('finance_bos_pemasukan', ['pemasukan' => $data, 'active_page' => 'bos_pemasukan'], 'bos_pemasukan');
    }

    public static function bosPengeluaran() {
        $db = Database::connect('core');
        $data = $db->query("SELECT id, tanggal_transaksi as tanggal, keterangan, kategori, jumlah, '' as bukti FROM keuangan_bos_transaksi WHERE jenis='Pengeluaran' ORDER BY tanggal_transaksi DESC")->fetchAll();
        self::render('finance_bos_pengeluaran', ['pengeluaran' => $data, 'active_page' => 'bos_pengeluaran'], 'bos_pengeluaran');
    }

    public static function bosLaporan() {
        self::render('finance_bos_laporan', ['active_page' => 'bos_laporan'], 'bos_laporan');
    }

    public static function saveBosTransaksi() {
        self::guard();
        $db = Database::connect('core');
        $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : 'Pemasukan';
        $tanggal = isset($_POST['tanggal_transaksi']) ? $_POST['tanggal_transaksi'] : date('Y-m-d');
        $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';
        $kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
        $jumlah = isset($_POST['jumlah']) ? floatval($_POST['jumlah']) : 0;
        
        if ($jumlah > 0 && !empty($keterangan)) {
            $stmt = $db->prepare("INSERT INTO keuangan_bos_transaksi (tanggal_transaksi, keterangan, jenis, kategori, jumlah) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal, $keterangan, $jenis, $kategori, $jumlah]);
        }
        $r = $jenis == 'Pemasukan' ? 'pemasukan' : 'pengeluaran';
        header('Location: /bos/' . $r . '?msg=saved');
        exit;
    }

    public static function deleteBosTransaksi($id) {
        self::guard();
        $db = Database::connect('core');
        $t = $db->query("SELECT jenis FROM keuangan_bos_transaksi WHERE id = " . intval($id))->fetch();
        if ($t) {
            $db->prepare("DELETE FROM keuangan_bos_transaksi WHERE id = ?")->execute([$id]);
            $r = $t['jenis'] == 'Pemasukan' ? 'pemasukan' : 'pengeluaran';
            header('Location: /bos/' . $r . '?msg=deleted');
        } else {
            header('Location: /bos/pemasukan');
        }
        exit;
    }
}
