<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;

class PengaturanApkController {

    private function _hasAccess() {
        if (!isset($_SESSION['role_id'])) return false;
        if ($_SESSION['role_id'] == 99) return true;
        if ($_SESSION['role_id'] != 1) return false;
        
        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT portal_access FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        $portalAccess = $user ? $user['portal_access'] : null;
        
        if (is_null($portalAccess)) return true; // Default allow if null
        if ($portalAccess === '') return false;
        
        $allowedModules = array_map('trim', explode(',', strtolower($portalAccess)));
        return in_array('pengaturan_apk', $allowedModules);
    }

    public function dashboard() {
        if (!$this->_hasAccess()) {
            Helper::redirect('/portal');
        }

        $title = "Dashboard Pengaturan APK - MTs RS";
        $activeMenu = 'pengaturan_apk_dashboard';

        ob_start();
        include __DIR__ . '/../../resources/views/pengaturan_apk/dashboard.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function navbar() {
        if (!$this->_hasAccess()) {
            Helper::redirect('/portal');
        }

        $title = "Pengaturan Nav Bar APK - MTs RS";
        $activeMenu = 'pengaturan_apk_navbar';

        ob_start();
        include __DIR__ . '/../../resources/views/pengaturan_apk/navbar.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function aplikasi() {
        if (!$this->_hasAccess()) {
            Helper::redirect('/portal');
        }

        $db = Database::connect();
        
        // Ambil daftar guru
        $gurus = $db->query("SELECT id, nama, nip, is_kamad, jabatan FROM guru ORDER BY nama ASC")->fetchAll();
        
        // Ambil hak akses custom
        $akses_raw = $db->query("SELECT guru_id, app_code FROM apk_akses_guru")->fetchAll();
        $akses_map = [];
        foreach ($akses_raw as $row) {
            $akses_map[$row['guru_id']][] = $row['app_code'];
        }

        // Definisi aplikasi yang bisa didelegasikan
        $apk_apps = [
            'Keuangan & Tabungan' => [
                'kasir_tabungan' => ['icon' => 'wallet', 'name' => 'Kasir Tab.', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
                'kasir_bendahara' => ['icon' => 'banknote', 'name' => 'Kasir Bend.', 'color' => '#10b981', 'bg' => '#ecfdf5'],
                'bku_bendahara' => ['icon' => 'book-text', 'name' => 'BKU Bend.', 'color' => '#3b82f6', 'bg' => '#eff6ff']
            ],
            'Absensi & Perizinan' => [
                'scan_siswa' => ['icon' => 'scan', 'name' => 'Scan Siswa', 'color' => '#0ea5e9', 'bg' => '#e0f2fe'],
                'scan_guru' => ['icon' => 'scan-face', 'name' => 'Scan Guru', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
                'izin_siswa' => ['icon' => 'user-check', 'name' => 'Izin Siswa', 'color' => '#10b981', 'bg' => '#ecfdf5'],
                'persetujuan_izin' => ['icon' => 'clipboard-check', 'name' => 'Izin Guru', 'color' => '#f59e0b', 'bg' => '#fffbeb']
            ],
            'Kepala Madrasah' => [
                'mon_keuangan' => ['icon' => 'line-chart', 'name' => 'Mon. Keuangan', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
                'qr_absen_guru' => ['icon' => 'scan-line', 'name' => 'QR Absen Guru', 'color' => '#4f46e5', 'bg' => '#e0e7ff']
            ],
            'Waka Kurikulum' => [
                'wakakur_jadwal' => ['icon' => 'calendar-check', 'name' => 'Jadwal KBM', 'color' => '#d946ef', 'bg' => '#fdf4ff'],
                'mon_jurnal' => ['icon' => 'book-open', 'name' => 'Monitor Jurnal', 'color' => '#0ea5e9', 'bg' => '#e0f2fe'],
                'val_ekinerja' => ['icon' => 'clipboard-check', 'name' => 'Supervisi Adm.', 'color' => '#d946ef', 'bg' => '#fdf4ff'],
                'supervisi_kelas' => ['icon' => 'presentation', 'name' => 'Supervisi Kelas', 'color' => '#d97706', 'bg' => '#fffbeb'],
                'mon_penilaian' => ['icon' => 'clipboard-list', 'name' => 'Supervisi Penilaian', 'color' => '#c026d3', 'bg' => '#fdf4ff']
            ],
            'Waka Kesiswaan' => [
                'wakasis_poin' => ['icon' => 'clipboard-list', 'name' => 'Poin Log', 'color' => '#ef4444', 'bg' => '#fef2f2'],
                'wakasis_kehadiran' => ['icon' => 'pie-chart', 'name' => 'Kehadiran', 'color' => '#4338ca', 'bg' => '#e0e7ff'],
                'absen_qr' => ['icon' => 'scan-line', 'name' => 'QR Absen Siswa', 'color' => '#9333ea', 'bg' => '#f3e8ff'],
                'absen_mapel' => ['icon' => 'book-check', 'name' => 'Absen Mapel', 'color' => '#10b981', 'bg' => '#ecfdf5']
            ],
            'Bimbingan Konseling' => [
                'bk_jurnal' => ['icon' => 'book-user', 'name' => 'Jurnal Konseling', 'color' => '#6366f1', 'bg' => '#eef2ff'],
                'bk_poin' => ['icon' => 'alert-triangle', 'name' => 'Poin Kedisiplinan', 'color' => '#ef4444', 'bg' => '#fef2f2']
            ],
            'Wali Kelas' => [
                'wali_siswa' => ['icon' => 'users', 'name' => 'Data Siswa', 'color' => '#6366f1', 'bg' => '#eef2ff'],
                'wali_jurnal' => ['icon' => 'clipboard-list', 'name' => 'Jurnal Kelas', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
                'wali_absen' => ['icon' => 'user-check', 'name' => 'Absensi Kelas', 'color' => '#10b981', 'bg' => '#ecfdf5'],
                'wali_catatan' => ['icon' => 'notebook-pen', 'name' => 'Catatan Wali Kelas', 'color' => '#8b5cf6', 'bg' => '#f3e8ff'],
                'wali_buku' => ['icon' => 'list-todo', 'name' => 'Buku Kerja', 'color' => '#0ea5e9', 'bg' => '#e0f2fe'],
                'wali_poin' => ['icon' => 'alert-circle', 'name' => 'Log Kedisiplinan', 'color' => '#ef4444', 'bg' => '#fef2f2']
            ]
        ];

        $title = "Pengaturan Aplikasi APK - MTs RS";
        $activeMenu = 'pengaturan_apk_aplikasi';

        ob_start();
        include __DIR__ . '/../../resources/views/pengaturan_apk/aplikasi.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function saveAkses() {
        if (!$this->_hasAccess()) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $guru_id = $_POST['guru_id'] ?? 0;
        $app_code = $_POST['app_code'] ?? '';
        $is_active = $_POST['is_active'] ?? 0;

        if (!$guru_id || !$app_code) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            return;
        }

        $db = Database::connect();

        if ($is_active) {
            $stmt = $db->prepare("INSERT IGNORE INTO apk_akses_guru (guru_id, app_code) VALUES (?, ?)");
            $stmt->execute([$guru_id, $app_code]);
        } else {
            $stmt = $db->prepare("DELETE FROM apk_akses_guru WHERE guru_id = ? AND app_code = ?");
            $stmt->execute([$guru_id, $app_code]);
        }

        echo json_encode(['status' => 'success']);
    }
}
