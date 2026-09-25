<?php

namespace App\Controllers;

use App\Core\Helper;
use App\Core\Database;

class DevController
{
    public function __construct()
    {
        // Only allow Role ID 99 (Developer/Super Admin)
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 99) {
            $_SESSION['flash_error'] = 'Akses ditolak! Halaman khusus Developer.';
            Helper::redirect('/portal');
        }
    }

    public function index()
    {
        $activeMenu = 'dev';
        $title = 'Developer Panel | MTs RS';
        $db = Database::connect('core');

        // System Info
        $sysInfo = [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
        ];

        // Database Info
        $dbVersion = $db->query("SELECT VERSION() as v")->fetchColumn();
        $dbSizeQuery = $db->query("
            SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_mb 
            FROM information_schema.TABLES 
            WHERE table_schema = 'db_mts_rs'
        ")->fetchColumn();
        
        $dbInfo = [
            'version' => $dbVersion,
            'size' => round((float)$dbSizeQuery, 2) . ' MB',
        ];

        // Get recent errors from error log if exists
        $errorLogPath = ini_get('error_log');
        $recentErrors = [];
        if ($errorLogPath && file_exists($errorLogPath)) {
            // Get last 50 lines (basic implementation)
            $lines = file($errorLogPath);
            if ($lines) {
                $recentErrors = array_slice($lines, -50);
            }
        }

        require_once __DIR__ . '/../../resources/views/dev/index.php';
    }

    public function clearCache()
    {
        // Simple mock for cache clearing
        // E.g. delete files in a cache directory if exists
        $cacheDir = __DIR__ . '/../../storage/cache';
        $cleared = 0;
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*');
            foreach($files as $file) {
                if(is_file($file)) {
                    unlink($file);
                    $cleared++;
                }
            }
        }
        
        // Let's also pretend to clear opcache if enabled
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        $_SESSION['flash_success'] = "Cache berhasil dibersihkan! ($cleared file dihapus)";
        Helper::redirect('/dev');
    }

    public function backupDb()
    {
        $backupScript = realpath(__DIR__ . '/../../backup_server.ps1');
        
        if ($backupScript && file_exists($backupScript)) {
            // Jalankan PowerShell script di background Windows
            $cmd = "start /B powershell.exe -ExecutionPolicy Bypass -WindowStyle Hidden -File \"$backupScript\"";
            pclose(popen($cmd, "r"));
            $_SESSION['flash_success'] = 'Proses backup (DB & Source Code) sedang berjalan di background! File akan masuk ke I:\Drive Saya\backup_MTsRS';
        } else {
            $_SESSION['flash_error'] = 'Script backup tidak ditemukan!';
        }
        
        Helper::redirect('/dev');
    }

    public function runQuery()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $query = trim($_POST['query'] ?? '');
            if (empty($query)) {
                $_SESSION['flash_error'] = 'Query tidak boleh kosong.';
                Helper::redirect('/dev');
            }

            try {
                $db = Database::connect('core');
                
                // Very basic security: don't allow drop database
                if (stripos($query, 'DROP DATABASE') !== false) {
                    throw new \Exception("Aksi berbahaya dicegah oleh sistem!");
                }
                
                $stmt = $db->query($query);
                
                // If it's a SELECT query, try to fetch results
                if (stripos($query, 'SELECT') === 0) {
                    $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    $_SESSION['dev_query_results'] = $results;
                    $_SESSION['dev_query_count'] = count($results);
                } else {
                    $rowCount = $stmt->rowCount();
                    $_SESSION['flash_success'] = "Query berhasil dieksekusi. ($rowCount baris terpengaruh)";
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = 'Error mengeksekusi query: ' . $e->getMessage();
            }
            
            Helper::redirect('/dev');
        }
    }

    public function admins()
    {
        $activeMenu = 'dev_admins';
        $title = 'Manajemen Admin | Dev Panel';
        $db = Database::connect('core');

        $admins = $db->query("SELECT * FROM users WHERE role_id = 1 ORDER BY created_at DESC")->fetchAll(\PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/dev/admins.php';
    }

    public function adminSave()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::connect('core');
            $id = $_POST['id'] ?? '';
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username)) {
                $_SESSION['flash_error'] = 'Username tidak boleh kosong.';
                Helper::redirect('/dev/admins');
            }

            try {
                if (empty($id)) {
                    // Cek username kembar
                    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    if ($stmt->fetch()) {
                        throw new \Exception("Username sudah digunakan.");
                    }

                    if (empty($password)) {
                        throw new \Exception("Password tidak boleh kosong untuk admin baru.");
                    }

                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("INSERT INTO users (username, password_hash, role_id, status, portal_access) VALUES (?, ?, 1, 'active', '')");
                    $stmt->execute([$username, $hash]);
                    $_SESSION['flash_success'] = 'Admin berhasil ditambahkan.';
                } else {
                    if (!empty($password)) {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("UPDATE users SET username = ?, password_hash = ? WHERE id = ? AND role_id = 1");
                        $stmt->execute([$username, $hash, $id]);
                    } else {
                        $stmt = $db->prepare("UPDATE users SET username = ? WHERE id = ? AND role_id = 1");
                        $stmt->execute([$username, $id]);
                    }
                    $_SESSION['flash_success'] = 'Data admin berhasil diperbarui.';
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = 'Gagal menyimpan admin: ' . $e->getMessage();
            }
            
            Helper::redirect('/dev/admins');
        }
    }

    public function adminDelete($id)
    {
        try {
            $db = Database::connect('core');
            $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role_id = 1");
            $stmt->execute([$id]);
            $_SESSION['flash_success'] = 'Admin berhasil dihapus.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Gagal menghapus admin: ' . $e->getMessage();
        }
        Helper::redirect('/dev/admins');
    }

    public function portalAccess()
    {
        $activeMenu = 'dev_portal_access';
        $title = 'Akses Portal | Dev Panel';
        $db = Database::connect('core');

        $admins = $db->query("SELECT id, username, portal_access FROM users WHERE role_id = 1 ORDER BY created_at DESC")->fetchAll(\PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/dev/portal_access.php';
    }

    public function portalAccessToggle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $db = Database::connect('core');
            $adminId = $_POST['admin_id'] ?? 0;
            $moduleKey = $_POST['module_key'] ?? '';
            $isActive = $_POST['is_active'] ?? '0';

            try {
                $stmt = $db->prepare("SELECT portal_access FROM users WHERE id = ? AND role_id = 1");
                $stmt->execute([$adminId]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$user) {
                    echo json_encode(['status' => 'error', 'message' => 'Admin tidak ditemukan']);
                    return;
                }

                $accessStr = $user['portal_access'];
                $allKeys = array_keys(\App\Core\Helper::getPortalModules());
                $accessList = is_null($accessStr) ? $allKeys : array_filter(array_map('trim', explode(',', $accessStr)));

                if ($isActive === '1') {
                    if (!in_array($moduleKey, $accessList)) {
                        $accessList[] = $moduleKey;
                    }
                } else {
                    $accessList = array_diff($accessList, [$moduleKey]);
                }

                $newAccess = implode(',', $accessList);
                $update = $db->prepare("UPDATE users SET portal_access = ? WHERE id = ?");
                $update->execute([$newAccess, $adminId]);

                echo json_encode(['status' => 'success', 'message' => 'Akses berhasil diubah']);
            } catch (\Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    public function settings()
    {
        $activeMenu = 'dev_settings';
        $title = 'OneSignal & API Settings | Dev Panel';
        $db = Database::connect('core');

        $settings = $db->query("SELECT onesignal_app_id, onesignal_rest_api_key FROM website_settings LIMIT 1")->fetch(\PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../resources/views/dev/settings.php';
    }

    public function settingsSave()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::connect('core');
            $appId = trim($_POST['onesignal_app_id'] ?? '');
            $apiKey = trim($_POST['onesignal_rest_api_key'] ?? '');

            try {
                // Check if row exists
                $count = $db->query("SELECT COUNT(*) FROM website_settings")->fetchColumn();
                if ($count > 0) {
                    $stmt = $db->prepare("UPDATE website_settings SET onesignal_app_id = ?, onesignal_rest_api_key = ?");
                    $stmt->execute([$appId, $apiKey]);
                } else {
                    $stmt = $db->prepare("INSERT INTO website_settings (onesignal_app_id, onesignal_rest_api_key) VALUES (?, ?)");
                    $stmt->execute([$appId, $apiKey]);
                }
                $_SESSION['flash_success'] = 'Pengaturan OneSignal berhasil disimpan.';
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
            }
            Helper::redirect('/dev/settings');
        }
    }

    public function cctv()
    {
        $activeMenu = 'dev_cctv';
        $title = 'CCTV Monitoring | Dev Panel';
        $db = Database::connect('core');

        // Fetch logs with limit
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        
        // Build query
        $query = "SELECT * FROM system_logs ORDER BY created_at DESC LIMIT $limit";
        $logs = $db->query($query)->fetchAll();

        require_once __DIR__ . '/../../resources/views/dev/cctv.php';
    }
}
