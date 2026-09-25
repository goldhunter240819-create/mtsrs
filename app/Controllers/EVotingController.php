<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;

class EVotingController {

    public function loginBilik() {
        if (isset($_SESSION['evoting_token'])) {
            Helper::redirect('/evoting/bilik');
        }
        
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $db = Database::connect();
        $activeEvent = $db->query("SELECT * FROM evoting_events WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetch();

        ob_start();
        include __DIR__ . '/../../resources/views/evoting/login_bilik.php';
        $content = ob_get_clean();
        
        // We might not want to use the main layout for the login page, but we'll include it or make a standalone view
        echo $content;
    }

    public function verifyToken() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = strtoupper(trim($_POST['token'] ?? ''));
            
            $db = Database::connect();
            $activeEvent = $db->query("SELECT * FROM evoting_events WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetch();
            
            if (!$activeEvent) {
                $_SESSION['flash_error'] = "Tidak ada event pemilihan yang sedang aktif saat ini.";
                Helper::redirect('/evoting');
            }

            $stmt = $db->prepare("SELECT * FROM evoting_voters WHERE event_id = ? AND token = ?");
            $stmt->execute([$activeEvent['id'], $token]);
            $voter = $stmt->fetch();

            if (!$voter) {
                $_SESSION['flash_error'] = "Token (PIN) tidak valid. Silakan cek kembali kartu token Anda.";
                Helper::redirect('/evoting');
            }

            if ($voter['has_voted']) {
                $_SESSION['flash_error'] = "Token ini sudah digunakan untuk memilih sebelumnya.";
                Helper::redirect('/evoting');
            }

            // Valid, create evoting session
            $_SESSION['evoting_token'] = $voter['token'];
            $_SESSION['evoting_voter_id'] = $voter['id'];
            $_SESSION['evoting_event_id'] = $activeEvent['id'];
            
            Helper::redirect('/evoting/bilik');
        }
    }

    public function bilikSuara() {
        if (!isset($_SESSION['evoting_token'])) {
            Helper::redirect('/evoting');
        }

        $voterId = $_SESSION['evoting_voter_id'];
        $eventId = $_SESSION['evoting_event_id'];

        $db = Database::connect();
        
        // Cek lagi apakah masih aktif
        $event = $db->prepare("SELECT * FROM evoting_events WHERE id = ? AND status = 'Aktif'");
        $event->execute([$eventId]);
        $eventData = $event->fetch();

        if (!$eventData) {
            unset($_SESSION['evoting_token']);
            $_SESSION['flash_error'] = "Sesi pemilihan telah ditutup.";
            Helper::redirect('/evoting');
        }

        // Cek apakah sudah memilih (untuk jaga-jaga kalau buka 2 tab)
        $voter = $db->prepare("SELECT has_voted, user_type, user_id FROM evoting_voters WHERE id = ?");
        $voter->execute([$voterId]);
        $voterData = $voter->fetch();
        
        if ($voterData['has_voted']) {
            Helper::redirect('/evoting/success');
        }
        
        // Ambil nama pemilih untuk sapaan
        $voterName = "Pemilih";
        if ($voterData['user_type'] === 'Siswa') {
            $n = $db->prepare("SELECT nama FROM siswa WHERE id = ?");
            $n->execute([$voterData['user_id']]);
            $res = $n->fetchColumn();
            if($res) $voterName = $res;
        } else if ($voterData['user_type'] === 'Guru') {
            $n = $db->prepare("SELECT nama FROM guru WHERE id = ?");
            $n->execute([$voterData['user_id']]);
            $res = $n->fetchColumn();
            if($res) $voterName = $res;
        }

        $candidates = $db->prepare("SELECT * FROM evoting_candidates WHERE event_id = ? ORDER BY no_urut ASC");
        $candidates->execute([$eventId]);
        $candidatesData = $candidates->fetchAll();

        ob_start();
        include __DIR__ . '/../../resources/views/evoting/bilik_suara.php';
        $content = ob_get_clean();
        echo $content;
    }

    public function coblos() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['evoting_token'])) {
            $candidateId = $_POST['candidate_id'] ?? 0;
            $voterId = $_SESSION['evoting_voter_id'];
            $eventId = $_SESSION['evoting_event_id'];

            $db = Database::connect();
            
            // Double check has_voted
            $check = $db->prepare("SELECT has_voted FROM evoting_voters WHERE id = ?");
            $check->execute([$voterId]);
            if ($check->fetchColumn()) {
                Helper::redirect('/evoting/success');
            }

            $db->beginTransaction();
            try {
                // Insert vote (using ordinary save method with user id tracking)
                $stmt = $db->prepare("INSERT INTO evoting_votes (event_id, candidate_id, voter_id) VALUES (?, ?, ?)");
                $stmt->execute([$eventId, $candidateId, $voterId]);

                // Update voter status
                $stmt = $db->prepare("UPDATE evoting_voters SET has_voted = 1, voted_at = NOW() WHERE id = ?");
                $stmt->execute([$voterId]);

                $db->commit();
                
                // Jangan unset session dulu biar bisa lihat halaman success
                $_SESSION['evoting_success'] = true;
                Helper::redirect('/evoting/success');

            } catch (\Exception $e) {
                $db->rollBack();
                $_SESSION['flash_error'] = "Terjadi kesalahan sistem saat menyimpan suara Anda.";
                Helper::redirect('/evoting/bilik');
            }
        }
    }

    public function success() {
        if (!isset($_SESSION['evoting_success'])) {
            Helper::redirect('/evoting');
        }
        
        // Hapus session setelah masuk ke halaman success
        unset($_SESSION['evoting_token']);
        unset($_SESSION['evoting_voter_id']);
        unset($_SESSION['evoting_event_id']);
        unset($_SESSION['evoting_success']);

        ob_start();
        include __DIR__ . '/../../resources/views/evoting/success.php';
        $content = ob_get_clean();
        echo $content;
    }
}
