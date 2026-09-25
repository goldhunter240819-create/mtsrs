<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;

class AdminController
{
    private static function checkAdmin()
    {
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            header('Location: /login');
            exit;
        }
    }

    public static function portalUsers()
    {
        self::checkAdmin();
        $activeMenu = 'admin_portal_users';
        $title = 'Akses Portal Guru | Admin Panel';
        $db = Database::connect('core');

        // Fetch users who are guru (role_id = 2). 
        $users = $db->query("
            SELECT u.id, u.username, u.portal_access, u.portal_enabled, g.nama
            FROM users u
            LEFT JOIN guru g ON u.id = g.user_id
            WHERE u.role_id = 2
            ORDER BY g.nama ASC, u.username ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/admin/portal_users.php';
    }

    public static function portalUsersToggle()
    {
        self::checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $db = Database::connect('core');
            $userId = $_POST['user_id'] ?? 0;
            $moduleKey = $_POST['module_key'] ?? '';
            $isActive = $_POST['is_active'] ?? '0';

            try {
                $stmt = $db->prepare("SELECT portal_access FROM users WHERE id = ? AND role_id = 2");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$user) {
                    echo json_encode(['status' => 'error', 'message' => 'Guru tidak ditemukan']);
                    return;
                }

                $accessStr = $user['portal_access'];
                $allKeys = array_keys(\App\Core\Helper::getPortalModules());
                
                // If NULL, they had full access. We need to convert it to a full list before removing/adding.
                if (is_null($accessStr)) {
                    $accessList = $allKeys;
                } else if ($accessStr === '') {
                    $accessList = [];
                } else {
                    $accessList = array_filter(array_map('trim', explode(',', $accessStr)));
                }

                if ($isActive === '1') {
                    if (!in_array($moduleKey, $accessList)) {
                        $accessList[] = $moduleKey;
                    }
                } else {
                    $accessList = array_diff($accessList, [$moduleKey]);
                }

                // Convert back to string
                $newAccessStr = empty($accessList) ? '' : implode(',', $accessList);

                $updateStmt = $db->prepare("UPDATE users SET portal_access = ? WHERE id = ? AND role_id = 2");
                $updateStmt->execute([$newAccessStr, $userId]);

                echo json_encode(['status' => 'success']);
            } catch (\Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }

    public static function portalUsersToggleMaster()
    {
        self::checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $db = Database::connect('core');
            $userId = $_POST['user_id'] ?? 0;
            $isEnabled = $_POST['is_enabled'] ?? '0';

            try {
                $stmt = $db->prepare("UPDATE users SET portal_enabled = ? WHERE id = ? AND role_id = 2");
                $stmt->execute([$isEnabled, $userId]);

                if ($stmt->rowCount() > 0) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Guru tidak ditemukan']);
                }
            } catch (\Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }

    public static function profil()
    {
        self::checkAdmin();
        $activeMenu = 'admin_profil';
        $title = 'Profil Admin | Admin Panel';
        $db = Database::connect('core');

        $userId = $_SESSION['user_id'];
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/admin/profil.php';
    }

    public static function profilSave()
    {
        self::checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::connect('core');
            $userId = $_SESSION['user_id'];

            $nama = trim($_POST['nama'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($nama) || empty($username)) {
                echo "<script>alert('Nama dan Username tidak boleh kosong!'); history.back();</script>";
                return;
            }

            // Check if username already exists for another user
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $userId]);
            if ($stmt->fetch()) {
                echo "<script>alert('Username sudah digunakan!'); history.back();</script>";
                return;
            }

            $query = "UPDATE users SET nama = ?, username = ?";
            $params = [$nama, $username];

            if (!empty($password)) {
                $query .= ", password_hash = ?";
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }

            // Handle photo upload
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/profil/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExt = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png'];

                if (in_array($fileExt, $allowedExts)) {
                    $newFileName = 'admin_' . $userId . '_' . time() . '.' . $fileExt;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $newFileName)) {
                        $query .= ", foto = ?";
                        $params[] = $newFileName;
                        $_SESSION['foto'] = $newFileName;
                    }
                }
            }

            $query .= " WHERE id = ?";
            $params[] = $userId;

            $stmt = $db->prepare($query);
            if ($stmt->execute($params)) {
                if (!empty($password)) {
                    // Password changed, auto logout
                    session_unset();
                    session_destroy();
                    echo "<script>alert('Password berhasil diubah! Sesi diakhiri, silakan login kembali dengan password baru Anda.'); window.location.href = '" . Helper::url('/login') . "';</script>";
                    exit;
                } else {
                    // Only update session if password not changed (stay logged in)
                    $_SESSION['nama'] = $nama;
                    $_SESSION['username'] = $username;
                    echo "<script>alert('Profil berhasil diperbarui!'); window.location.href = '" . Helper::url('/admin/profil') . "';</script>";
                }
            } else {
                echo "<script>alert('Gagal memperbarui profil!'); history.back();</script>";
            }
        }
    }
}
