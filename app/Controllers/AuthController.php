<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Branding;
use App\Core\Helper;

class AuthController {

    public function loginForm() {
        if (isset($_SESSION['user_id'])) {
            $role_id = $_SESSION['role_id'] ?? 1;
            if ($role_id == 2) {
                Helper::redirect('/guru');
            } elseif ($role_id == 3) {
                Helper::redirect('/siswa');
            } else {
                Helper::redirect('/portal');
            }
        }

        $branding = Branding::getInstitusi();
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        include __DIR__ . '/../../resources/views/login.php';
    }

    public function loginProcess() {
        $qr_data = trim($_POST['qr_data'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($qr_data) && (empty($username) || empty($password))) {
            $_SESSION['login_error'] = 'Username dan password wajib diisi.';
            Helper::redirect('/login');
        }

        $db = Database::connect();
        $user = null;
        $pass_ok = false;
        
        if (!empty($qr_data)) {
            $identifier = $qr_data;
            $qr_password = null;
            $is_uuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $qr_data);
            
            if (!$is_uuid && preg_match('/USERNAME:\s*([^\r\n]+?)\s*PASSWORD:\s*([A-Z0-9a-z]+)/i', $qr_data, $matchUP)) {
                $identifier = trim($matchUP[1]);
                $qr_password = trim($matchUP[2]);
            } elseif (preg_match('/NIS\/NISN:\s*([A-Z0-9]+(?:\/[A-Z0-9]+)?)/i', $qr_data, $m)) {
                $parts = array_filter(explode('/', $m[1]));
                $identifier = trim(end($parts));
            }
            
            if ($qr_password !== null) {
                $stmt = $db->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = :uname AND u.status = 'active' LIMIT 1");
                $stmt->execute(['uname' => $identifier]);
                $u = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($u) {
                    if (password_verify($qr_password, $u['password_hash']) || password_verify(strtoupper($qr_password), $u['password_hash'])) {
                        $user = $u;
                        $pass_ok = true;
                    }
                }
            } else {
                $stmt = $db->prepare("SELECT u.*, r.name as role_name FROM users u LEFT JOIN siswa s ON u.id = s.user_id LEFT JOIN guru g ON u.id = g.user_id JOIN roles r ON u.role_id = r.id WHERE (u.username = ? OR u.qr_token = ? OR s.nis = ? OR s.nisn = ? OR g.nip = ?) AND u.status = 'active' LIMIT 1");
                $stmt->execute([$identifier, $identifier, $identifier, $identifier, $identifier]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($user) $pass_ok = true;
            }
        } else {
            // 1. Check in `users` table first
            $stmt = $db->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ? AND u.status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $pass_ok = true;
            }

            // 2. Check in `developers` table if not found in users
            if (!$user) {
                $stmtDev = $db->prepare("SELECT * FROM developers WHERE username = ? AND is_active = 1");
                $stmtDev->execute([$username]);
                $dev = $stmtDev->fetch();

                if ($dev && password_verify($password, $dev['password_hash'])) {
                    $_SESSION['user_id'] = $dev['id'];
                    $_SESSION['username'] = $dev['username'];
                    $_SESSION['nama'] = $dev['username'] . ' (Developer)';
                    $_SESSION['role_id'] = 99;
                    $_SESSION['role_name'] = 'developer';

                    Helper::logActivity('AUTH', 'LOGIN', 'Developer ' . $dev['username'] . ' berhasil login.');

                    $db->query("UPDATE users SET last_activity = NOW() WHERE username = '$username'");
                    Helper::redirect('/portal');
                }
            }
        }

        if ($user && $pass_ok) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            $_SESSION['portal_access'] = $user['portal_access'];
            $_SESSION['portal_enabled'] = $user['portal_enabled'] ?? 0;
            $via = !empty($qr_data) ? 'via QR Code' : 'via web portal';
            Helper::logActivity('AUTH', 'LOGIN', 'User ' . $user['username'] . ' (' . $user['role_name'] . ') berhasil login ' . $via . '.');

            // Fetch display name based on role
            if ($user['role_id'] == 1 || $user['role_id'] == 99) {
                $_SESSION['nama'] = !empty($user['nama']) ? $user['nama'] : ucfirst($user['username']);
                $_SESSION['foto'] = $user['foto'] ?? '';
            } elseif ($user['role_id'] == 2) {
                $stmtG = $db->prepare("SELECT id, nama, is_kamad FROM guru WHERE user_id = ?");
                $stmtG->execute([$user['id']]);
                $guru = $stmtG->fetch();
                $_SESSION['nama'] = $guru ? $guru['nama'] : $user['username'];
                $_SESSION['guru_id'] = $guru ? $guru['id'] : null;
                $_SESSION['is_kamad'] = $guru ? $guru['is_kamad'] : 0;
            } elseif ($user['role_id'] == 3) {
                $stmtS = $db->prepare("SELECT id, nama, kelas_id FROM siswa WHERE user_id = ?");
                $stmtS->execute([$user['id']]);
                $siswa = $stmtS->fetch();
                $_SESSION['nama'] = $siswa ? $siswa['nama'] : $user['username'];
                $_SESSION['siswa_id'] = $siswa ? $siswa['id'] : null;
                $_SESSION['kelas_id'] = $siswa ? $siswa['kelas_id'] : null;
            } else {
                $_SESSION['nama'] = ucfirst($user['username']);
            }

            $db->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?")->execute([$user['id']]);

            if ($user['role_id'] == 2) {
                Helper::redirect('/guru');
            } elseif ($user['role_id'] == 3) {
                Helper::redirect('/siswa');
            } else {
                Helper::redirect('/portal');
            }
        }

        $_SESSION['login_error'] = !empty($qr_data) ? 'QR Code tidak valid atau akun tidak aktif.' : 'Username atau password tidak valid.';
        Helper::redirect('/login');
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $username = $_SESSION['username'] ?? 'Unknown';
        Helper::logActivity('AUTH', 'LOGOUT', "User $username berhasil logout.");
        
        session_unset();
        session_destroy();

        Helper::redirect('/login');
    }
}
