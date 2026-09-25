<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;
use PDO;

class AdminBerkasController {

    private function checkAccess() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }

    public function dashboard() {
        $this->checkAccess();
        $activeMenu = 'manajemen_berkas_dashboard';
        $title = 'Dashboard Manajemen Berkas | MTs RS';
        $db = Database::connect('core');
        
        $countPribadi = $db->query("SELECT COUNT(*) FROM berkas_pribadi")->fetchColumn();
        $countPerangkat = $db->query("SELECT COUNT(*) FROM perangkat_pembelajaran")->fetchColumn();

        ob_start();
        include __DIR__ . '/../../resources/views/admin_berkas/dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function pribadi() {
        $this->checkAccess();
        $activeMenu = 'manajemen_berkas_pribadi';
        $title = 'Berkas Pribadi Guru | MTs RS';
        $db = Database::connect('core');
        
        $search = $_GET['q'] ?? '';
        
        $sql = "SELECT b.*, g.nama as nama_guru, g.nip 
                FROM berkas_pribadi b 
                LEFT JOIN guru g ON b.guru_id = g.id";
        if ($search) {
            $sql .= " WHERE g.nama LIKE :search OR b.judul_berkas LIKE :search";
        }
        $sql .= " ORDER BY g.nama ASC, b.tanggal_upload DESC";
        $stmt = $db->prepare($sql);
        if ($search) {
            $stmt->execute(['search' => "%$search%"]);
        } else {
            $stmt->execute();
        }
        $pribadi = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../resources/views/admin_berkas/pribadi.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function perangkat() {
        $this->checkAccess();
        $activeMenu = 'manajemen_berkas_perangkat';
        $title = 'Perangkat Pembelajaran Guru | MTs RS';
        $db = Database::connect('core');
        
        $search = $_GET['q'] ?? '';
        
        $sql = "SELECT p.*, g.nama as nama_guru, g.nip, 
                       (SELECT nama_kelas FROM db_mts_rs.kelas k WHERE k.id = p.kelas_id) as nama_kelas,
                       (SELECT nama_mapel FROM db_mts_rs.mapel m WHERE m.id = p.mapel_id) as nama_mapel
                FROM perangkat_pembelajaran p 
                LEFT JOIN guru g ON p.guru_id = g.id";
        if ($search) {
            $sql .= " WHERE g.nama LIKE :search OR p.judul_berkas LIKE :search";
        }
        $sql .= " ORDER BY g.nama ASC, p.tanggal_upload DESC";
        $stmt = $db->prepare($sql);
        if ($search) {
            $stmt->execute(['search' => "%$search%"]);
        } else {
            $stmt->execute();
        }
        $perangkat = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../resources/views/admin_berkas/perangkat.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function download($type, $filename) {
        $this->checkAccess();
        // Prevent path traversal
        $filename = basename($filename);
        $filepath = __DIR__ . '/../../public/uploads/berkas/' . $filename;
        
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            $_SESSION['flash_error'] = 'File tidak ditemukan.';
            Helper::redirect('/manajemen-berkas?tab=' . $type);
        }
    }

    public function viewPdf($filename) {
        $this->checkAccess();
        $filename = basename($filename);
        $filepath = __DIR__ . '/../../public/uploads/berkas/' . $filename;
        
        if (!file_exists($filepath)) {
            $_SESSION['flash_error'] = 'File tidak ditemukan.';
            Helper::redirect('/manajemen-berkas');
            return;
        }

        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            require_once __DIR__ . '/../Core/Fpdf/fpdf.php';
            
            $pdf = new \FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            
            $size = getimagesize($filepath);
            if ($size) {
                $w = $size[0] * 25.4 / 96;
                $h = $size[1] * 25.4 / 96;
                
                if ($w > 190 || $h > 277) {
                    $ratio = min(190 / $w, 277 / $h);
                    $w = $w * $ratio;
                    $h = $h * $ratio;
                }
                
                $x = (210 - $w) / 2;
                $y = (297 - $h) / 2;
                
                $pdf->Image($filepath, $x, $y, $w, $h);
            }
            $pdf->Output('I', pathinfo($filename, PATHINFO_FILENAME) . '.pdf');
            exit;
        } elseif ($ext === 'pdf') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($filepath) . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            $mime = mime_content_type($filepath);
            header('Content-Type: ' . $mime);
            header('Content-Disposition: inline; filename="' . basename($filepath) . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        }
    }
}
