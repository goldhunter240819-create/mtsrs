<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Branding;
use App\Core\AcademicYear;

class PortalController {

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $db = Database::connect();
        $institusi = Branding::getInstitusi();
        $academicYear = AcademicYear::current();

        // Stats
        $statSiswa = $db->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();
        $statGuru = $db->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        $statKelas = $db->query("SELECT COUNT(*) FROM kelas")->fetchColumn();
        
        $today = date('Y-m-d');
        $statAbsenToday = $db->query("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = '$today' AND status = 'Hadir'")->fetchColumn();

        $userName = $_SESSION['nama'] ?? 'User';
        $userRole = $_SESSION['role_name'] ?? 'User';

        $title = "Portal Dashboard - MTs RS System";
        $activeMenu = 'portal';

        ob_start();
        include __DIR__ . '/../../resources/views/portal.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../resources/views/layout.php';
    }
}
