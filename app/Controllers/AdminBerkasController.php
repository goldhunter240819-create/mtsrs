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

    private static function findBerkasFile($filename) {
        $filename = basename(rawurldecode($filename));
        if (empty($filename)) return null;

        $candidates = [
            __DIR__ . '/../../public/uploads/berkas/' . $filename,
            __DIR__ . '/../../uploads/berkas/' . $filename,
            __DIR__ . '/../../public/uploads/perangkat/' . $filename,
            __DIR__ . '/../../uploads/perangkat/' . $filename,
            __DIR__ . '/../../public/uploads/' . $filename,
            __DIR__ . '/../../uploads/' . $filename,
            __DIR__ . '/../../storage/berkas/' . $filename,
            __DIR__ . '/../../storage/' . $filename,
            ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/public/uploads/berkas/' . $filename,
            ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/uploads/berkas/' . $filename,
        ];

        foreach ($candidates as $path) {
            if (!empty($path) && file_exists($path) && !is_dir($path)) {
                return $path;
            }
        }
        return null;
    }

    public function download($type, $filename) {
        $this->checkAccess();
        $filepath = self::findBerkasFile($filename);
        
        if ($filepath) {
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
            $_SESSION['flash_error'] = 'File fisik "' . htmlspecialchars(basename($filename)) . '" tidak ditemukan di disk server.';
            $redirectTarget = ($type === 'perangkat') ? '/manajemen-berkas/perangkat' : '/manajemen-berkas/pribadi';
            Helper::redirect($redirectTarget);
            exit;
        }
    }

    public function viewPdf($filename) {
        $this->checkAccess();
        $filepath = self::findBerkasFile($filename);
        
        if (!$filepath) {
            http_response_code(404);
            echo "<!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>Berkas Tidak Ditemukan</title>
                <meta name='viewport' content='width=device-width, initial-scale=1'>
                <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap' rel='stylesheet'>
                <style>
                    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
                    .card { background: white; padding: 32px; border-radius: 18px; max-width: 480px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
                    .icon { width: 56px; height: 56px; border-radius: 14px; background: #fef2f2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 16px; }
                    h2 { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 8px 0; }
                    p { font-size: 0.88rem; color: #64748b; line-height: 1.5; margin: 0 0 20px 0; }
                    .file-tag { background: #f1f5f9; padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-family: monospace; color: #0f172a; display: inline-block; margin-bottom: 16px; border: 1px solid #e2e8f0; }
                    .btn { display: inline-block; padding: 10px 22px; background: #2563eb; color: white; border-radius: 10px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: 0.2s; cursor: pointer; border: none; }
                    .btn:hover { background: #1d4ed8; }
                </style>
            </head>
            <body>
                <div class='card'>
                    <div class='icon'>📁</div>
                    <h2>File Berkas Tidak Ditemukan di Server</h2>
                    <div class='file-tag'>" . htmlspecialchars(basename($filename)) . "</div>
                    <p>File fisik arsip perangkat ini tidak ditemukan di direktori penyimpanan disk server. Kemungkinan berkas belum diunggah secara lengkap oleh guru yang bersangkutan atau terhapus saat sinkronisasi sebelumnya.</p>
                    <button onclick='window.close();' class='btn'>Tutup Tab</button>
                </div>
            </body>
            </html>";
            exit;
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
            $mime = function_exists('mime_content_type') ? @mime_content_type($filepath) : 'application/octet-stream';
            header('Content-Type: ' . $mime);
            header('Content-Disposition: inline; filename="' . basename($filepath) . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        }
    }
}
