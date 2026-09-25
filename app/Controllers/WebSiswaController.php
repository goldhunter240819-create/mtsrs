<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;

class WebSiswaController {
    public function dashboard() {
        $activeMenu = 'siswa_dashboard';
        $title = 'Dashboard Siswa | MTs RS';
        $db = Database::connect('core');

        $siswa_id = $_SESSION['siswa_id'] ?? null;
        
        // Fetch specific data for this siswa
        $siswa_info = null;
        if ($siswa_id) {
            $stmt = $db->prepare("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.id = ?");
            $stmt->execute([$siswa_id]);
            $siswa_info = $stmt->fetch();
        }

        ob_start();
        include __DIR__ . '/../../resources/views/siswa/dashboard.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }
}
