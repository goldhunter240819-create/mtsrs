<?php

namespace App\Middleware;

use App\Core\Helper;

class AuthMiddleware {
    public static function handle() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name("MTS_RS_SESSION");
            session_start();
        }

        $publicRoutes = ['/', '/profil', '/berita', '/ppdb', '/kontak', '/login', '/auth/login', '/auth/logout', '/apk/login', '/apk/logout', '/apk'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Normalize path & subfolder base path
        $basePath = Helper::getBasePath();
        
        $path = $uri;
        if (!empty($basePath) && stripos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        if (empty($path) || $path === '') {
            $path = '/';
        }

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        if (!in_array($path, $publicRoutes) && !isset($_SESSION['user_id'])) {
            Helper::redirect('/login');
        }

        // Role-Based Access Control
        if (isset($_SESSION['user_id'])) {
            $role_id = $_SESSION['role_id'] ?? 1;

            // Guru cannot access admin portal/modules
            if ($role_id == 2) {
                // Temporary debug log
                $debugLog = date('Y-m-d H:i:s') . " | path=$path | user_id=" . ($_SESSION['user_id'] ?? 'N/A') . "\n";
                @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', $debugLog, FILE_APPEND);

                // If guru tries to access /portal, check if portal is enabled for them
                if (strpos($path, '/portal') === 0) {
                    $db = \App\Core\Database::connect('core');
                    $stmt = $db->prepare("SELECT portal_enabled FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $portalEnabled = $stmt->fetchColumn();
                    if (!$portalEnabled) {
                        @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> BLOCKED: portal not enabled\n", FILE_APPEND);
                        Helper::redirect('/guru');
                    }
                    @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> ALLOWED: portal enabled\n", FILE_APPEND);
                } else {
                    // Modules that guru CAN access if portal_enabled AND in portal_access
                    $portalModuleMap = [
                        '/siakad' => 'siakad',
                        '/keuangan' => 'keuangan',
                        '/tabungan' => 'tabungan',
                        '/bos' => 'bos',
                        '/bk' => 'bk',
                        '/kurikulum' => 'kurikulum',
                        '/manajemen-berkas' => 'manajemen_berkas',
                        '/absen' => 'absen',
                        '/pengaturan-apk' => 'pengaturan_apk',
                        '/evoting' => 'evoting',
                    ];

                    // Always blocked for guru regardless
                    $alwaysBlocked = ['/dev', '/admin'];
                    foreach ($alwaysBlocked as $bp) {
                        if (strpos($path, $bp) === 0) {
                            @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> BLOCKED: always blocked path $bp\n", FILE_APPEND);
                            Helper::redirect('/guru');
                        }
                    }

                    // Check portal modules
                    $matchedModule = false;
                    foreach ($portalModuleMap as $routePrefix => $moduleKey) {
                        if (strpos($path, $routePrefix) === 0) {
                            $matchedModule = true;
                            $db = \App\Core\Database::connect('core');
                            $stmt = $db->prepare("SELECT portal_enabled, portal_access FROM users WHERE id = ?");
                            $stmt->execute([$_SESSION['user_id']]);
                            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
                            
                            @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> Matched module: $moduleKey | portal_enabled=" . ($userData['portal_enabled'] ?? 'N/A') . " | portal_access=" . ($userData['portal_access'] ?? 'NULL') . "\n", FILE_APPEND);
                            
                            if (!$userData || !$userData['portal_enabled']) {
                                @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> BLOCKED: portal not enabled for module\n", FILE_APPEND);
                                Helper::redirect('/guru');
                            }
                            
                            $accessStr = $userData['portal_access'];
                            if (is_null($accessStr)) {
                                @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> ALLOWED: NULL = full access\n", FILE_APPEND);
                                break;
                            }
                            if ($accessStr === '') {
                                @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> BLOCKED: empty = no access\n", FILE_APPEND);
                                Helper::redirect('/guru');
                            }
                            $allowedModules = array_map('trim', explode(',', $accessStr));
                            if (!in_array($moduleKey, $allowedModules)) {
                                @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> BLOCKED: module $moduleKey not in allowed list\n", FILE_APPEND);
                                Helper::redirect('/guru');
                            }
                            @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> ALLOWED: module $moduleKey in access list\n", FILE_APPEND);
                            break;
                        }
                    }

                    if (!$matchedModule) {
                        @file_put_contents(__DIR__ . '/../../scratch/auth_debug.log', "  -> NO MATCH: path not a portal module, allowing through\n", FILE_APPEND);
                    }

                    // Guru shouldn't access /siswa
                    if (strpos($path, '/siswa') === 0 && $path !== '/siakad/siswa') Helper::redirect('/guru');
                }
            }
            
            // Siswa cannot access admin portal/modules or guru portal
            if ($role_id == 3) {
                $adminPaths = ['/portal', '/siakad', '/keuangan', '/tabungan', '/bos', '/dev', '/bk', '/guru'];
                foreach ($adminPaths as $p) {
                    if (strpos($path, $p) === 0) Helper::redirect('/siswa');
                }
            }

            // Admin shouldn't ideally access /guru or /siswa dashboard directly, but we can allow it or block it
            if ($role_id == 1 || $role_id == 99) {
                if ($path === '/guru' || $path === '/siswa') {
                    Helper::redirect('/portal');
                }
            }
        }
    }
}
