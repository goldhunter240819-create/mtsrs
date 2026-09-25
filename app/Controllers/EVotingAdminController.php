<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;

class EVotingAdminController {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            Helper::redirect('/login');
        }
        
        // Cek akses portal
        $db = Database::connect();
        $stmt = $db->prepare("SELECT portal_access FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($_SESSION['role_id'] != 99 && $_SESSION['role_id'] != 1) {
             Helper::redirect('/portal');
        }
        
        if ($_SESSION['role_id'] == 1 && $user && !is_null($user['portal_access']) && $user['portal_access'] !== '') {
            $allowedModules = array_map('trim', explode(',', strtolower($user['portal_access'])));
            if (!in_array('evoting', $allowedModules)) {
                Helper::redirect('/portal');
            }
        }
    }

    public function index() {
        $db = Database::connect();
        $events = $db->query("SELECT * FROM evoting_events ORDER BY created_at DESC")->fetchAll();
        
        $activeMenu = 'evoting_admin_dashboard';
        ob_start();
        include __DIR__ . '/../../resources/views/evoting/admin/dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function storeEvent() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::connect();
            $nama = $_POST['nama_event'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $mulai = $_POST['tgl_mulai'] ?? '';
            $selesai = $_POST['tgl_selesai'] ?? '';
            $status = $_POST['status'] ?? 'Draft';

            $stmt = $db->prepare("INSERT INTO evoting_events (nama_event, deskripsi, tgl_mulai, tgl_selesai, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $deskripsi, $mulai, $selesai, $status]);
            
            $_SESSION['flash_success'] = "Event E-Voting berhasil dibuat!";
        }
        Helper::redirect('/evoting/admin');
    }

    public function manageCandidates() {
        $eventId = $_GET['event_id'] ?? 0;
        $db = Database::connect();
        
        if (!$eventId) {
            $active = $db->query("SELECT id FROM evoting_events WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetch();
            if ($active) {
                $eventId = $active['id'];
            } else {
                $last = $db->query("SELECT id FROM evoting_events ORDER BY id DESC LIMIT 1")->fetch();
                if ($last) $eventId = $last['id'];
            }
        }

        $event = $db->prepare("SELECT * FROM evoting_events WHERE id = ?");
        $event->execute([$eventId]);
        $eventData = $event->fetch();

        if (!$eventData) {
            $_SESSION['flash_error'] = "Silakan buat Event Pemilihan terlebih dahulu sebelum mengelola kandidat.";
            Helper::redirect('/evoting/admin');
        }

        $candidates = $db->prepare("SELECT * FROM evoting_candidates WHERE event_id = ? ORDER BY no_urut ASC");
        $candidates->execute([$eventId]);
        $candidatesData = $candidates->fetchAll();

        $activeMenu = 'evoting_admin_candidates';
        ob_start();
        include __DIR__ . '/../../resources/views/evoting/admin/candidate_manage.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function storeCandidate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::connect();
            $eventId = $_POST['event_id'] ?? 0;
            $noUrut = $_POST['no_urut'] ?? 1;
            $nama = $_POST['nama_kandidat'] ?? '';
            $visi = $_POST['visi'] ?? '';
            $misi = $_POST['misi'] ?? '';
            
            $foto = '';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/evoting/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto = 'cand_' . time() . '_' . rand(100, 999) . '.' . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $foto);
            }

            $stmt = $db->prepare("INSERT INTO evoting_candidates (event_id, no_urut, nama_kandidat, foto, visi, misi) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$eventId, $noUrut, $nama, $foto, $visi, $misi]);
            
            $_SESSION['flash_success'] = "Kandidat berhasil ditambahkan!";
            Helper::redirect('/evoting/admin/candidates?event_id=' . $eventId);
        }
    }

    public function deleteCandidate() {
        $id = $_GET['id'] ?? 0;
        $eventId = $_GET['event_id'] ?? 0;
        
        $db = Database::connect();
        
        // Hapus foto jika ada
        $stmt = $db->prepare("SELECT foto FROM evoting_candidates WHERE id = ?");
        $stmt->execute([$id]);
        $cand = $stmt->fetch();
        if ($cand && !empty($cand['foto'])) {
            $path = __DIR__ . '/../../public/uploads/evoting/' . $cand['foto'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $stmt = $db->prepare("DELETE FROM evoting_candidates WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['flash_success'] = "Kandidat berhasil dihapus!";
        Helper::redirect('/evoting/admin/candidates?event_id=' . $eventId);
    }
    
    public function manageVoters() {
        $eventId = $_GET['event_id'] ?? 0;
        $db = Database::connect();
        
        if (!$eventId) {
            $active = $db->query("SELECT id FROM evoting_events WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetch();
            if ($active) {
                $eventId = $active['id'];
            } else {
                $last = $db->query("SELECT id FROM evoting_events ORDER BY id DESC LIMIT 1")->fetch();
                if ($last) $eventId = $last['id'];
            }
        }

        $event = $db->prepare("SELECT * FROM evoting_events WHERE id = ?");
        $event->execute([$eventId]);
        $eventData = $event->fetch();

        if (!$eventData) {
            $_SESSION['flash_error'] = "Silakan buat Event Pemilihan terlebih dahulu sebelum mengelola pemilih.";
            Helper::redirect('/evoting/admin');
        }

        $voters = $db->prepare("
            SELECT v.*, 
            CASE 
                WHEN v.user_type = 'Siswa' THEN (SELECT nama FROM siswa WHERE id = v.user_id)
                WHEN v.user_type = 'Guru' THEN (SELECT nama FROM guru WHERE id = v.user_id)
                ELSE 'Umum'
            END as nama,
            CASE 
                WHEN v.user_type = 'Siswa' THEN (SELECT k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.id = v.user_id)
                ELSE ''
            END as keterangan
            FROM evoting_voters v 
            WHERE v.event_id = ? 
            ORDER BY v.user_type ASC, nama ASC
        ");
        $voters->execute([$eventId]);
        $votersData = $voters->fetchAll();

        $activeMenu = 'evoting_admin_voters';
        ob_start();
        include __DIR__ . '/../../resources/views/evoting/admin/voter_manage.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }

    public function generateTokens() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventId = $_POST['event_id'] ?? 0;
            $targets = $_POST['target'] ?? [];
            
            $db = Database::connect();
            $count = 0;

            if (in_array('siswa', $targets)) {
                $siswas = $db->query("SELECT id FROM siswa WHERE status = 'Aktif'")->fetchAll();
                foreach($siswas as $s) {
                    $userId = $s['id'];
                    $check = $db->prepare("SELECT id FROM evoting_voters WHERE event_id = ? AND user_type = 'Siswa' AND user_id = ?");
                    $check->execute([$eventId, $userId]);
                    if (!$check->fetch()) {
                        $token = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
                        $stmt = $db->prepare("INSERT INTO evoting_voters (event_id, user_type, user_id, token) VALUES (?, 'Siswa', ?, ?)");
                        $stmt->execute([$eventId, $userId, $token]);
                        $count++;
                    }
                }
            }

            if (in_array('guru', $targets)) {
                $gurus = $db->query("SELECT id FROM guru")->fetchAll();
                foreach($gurus as $g) {
                    $userId = $g['id'];
                    $check = $db->prepare("SELECT id FROM evoting_voters WHERE event_id = ? AND user_type = 'Guru' AND user_id = ?");
                    $check->execute([$eventId, $userId]);
                    if (!$check->fetch()) {
                        $token = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
                        $stmt = $db->prepare("INSERT INTO evoting_voters (event_id, user_type, user_id, token) VALUES (?, 'Guru', ?, ?)");
                        $stmt->execute([$eventId, $userId, $token]);
                        $count++;
                    }
                }
            }

            $_SESSION['flash_success'] = "Berhasil membuat $count token pemilih baru!";
            Helper::redirect('/evoting/admin/voters?event_id=' . $eventId);
        }
    }

    public function printTokens() {
        $eventId = $_GET['event_id'] ?? 0;
        $db = Database::connect();
        $event = $db->prepare("SELECT * FROM evoting_events WHERE id = ?");
        $event->execute([$eventId]);
        $eventData = $event->fetch();

        if (!$eventData) {
            die("Event not found");
        }

        $voters = $db->prepare("
            SELECT v.*, 
            CASE 
                WHEN v.user_type = 'Siswa' THEN (SELECT nama FROM siswa WHERE id = v.user_id)
                WHEN v.user_type = 'Guru' THEN (SELECT nama FROM guru WHERE id = v.user_id)
                ELSE 'Umum'
            END as nama,
            CASE 
                WHEN v.user_type = 'Siswa' THEN (SELECT k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.id = v.user_id)
                ELSE ''
            END as keterangan
            FROM evoting_voters v 
            WHERE v.event_id = ? 
            ORDER BY v.user_type ASC, nama ASC
        ");
        $voters->execute([$eventId]);
        $votersData = $voters->fetchAll();

        include __DIR__ . '/../../resources/views/evoting/admin/print_tokens.php';
    }

    public function liveCount() {
        $eventId = $_GET['event_id'] ?? 0;
        $db = Database::connect();
        
        if (!$eventId) {
            // Find active event
            $active = $db->query("SELECT id FROM evoting_events WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetch();
            if ($active) $eventId = $active['id'];
        }

        $event = $db->prepare("SELECT * FROM evoting_events WHERE id = ?");
        $event->execute([$eventId]);
        $eventData = $event->fetch();

        if (!$eventData) {
            Helper::redirect('/evoting/admin');
        }

        $totalVoters = $db->prepare("SELECT COUNT(id) FROM evoting_voters WHERE event_id = ?");
        $totalVoters->execute([$eventId]);
        $totalVoters = $totalVoters->fetchColumn();

        $totalVotes = $db->prepare("SELECT COUNT(id) FROM evoting_votes WHERE event_id = ?");
        $totalVotes->execute([$eventId]);
        $totalVotes = $totalVotes->fetchColumn();

        $candidates = $db->prepare("
            SELECT c.*, (SELECT COUNT(id) FROM evoting_votes WHERE candidate_id = c.id) as vote_count 
            FROM evoting_candidates c 
            WHERE c.event_id = ? 
            ORDER BY vote_count DESC, c.no_urut ASC
        ");
        $candidates->execute([$eventId]);
        $candidatesData = $candidates->fetchAll();

        $activeMenu = 'evoting_admin_live';
        ob_start();
        include __DIR__ . '/../../resources/views/evoting/admin/live_count.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/layout.php';
    }
}
