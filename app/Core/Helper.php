<?php

namespace App\Core;

class Helper {
    public static function getBasePath() {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php'; 
        $basePath = str_replace('\\', '/', dirname($scriptName)); 
        if ($basePath === '/' || $basePath === '\\') {
            return '';
        }
        return '/' . trim($basePath, '/');
    }

    public static function redirect($path) {
        $basePath = self::getBasePath();
        $target = '/' . ltrim($path, '/');
        
        if (!empty($basePath)) {
            // Prevent double prepending if target already starts with basePath
            if (stripos($target, $basePath) !== 0) {
                $target = $basePath . $target;
            }
        }

        header('Location: ' . $target);
        exit;
    }

    public static function url($path) {
        $basePath = self::getBasePath();
        $target = '/' . ltrim($path, '/');
        
        if (!empty($basePath)) {
            if (stripos($target, $basePath) !== 0) {
                return $basePath . $target;
            }
        }
        return $target;
    }

    public static function getPortalModules() {
        return [
            'siakad' => [
                'url' => '/siakad',
                'title' => 'SIAKAD',
                'subtitle' => 'Sistem Informasi Akademik',
                'icon' => 'graduation-cap',
                'color' => '#2563eb',
                'card_class' => 'card-siakad',
                'badge_text' => 'Live',
                'badge_class' => 'pill-blue',
                'badge_style' => 'background: rgba(37,99,235,0.15);',
            ],
            'keuangan' => [
                'url' => '/keuangan',
                'title' => 'Keuangan & SPP',
                'subtitle' => 'Pengelolaan SPP & Tagihan',
                'icon' => 'wallet',
                'color' => '#7c3aed',
                'card_class' => 'card-keuangan',
                'badge_text' => 'Live',
                'badge_class' => 'pill-purple',
                'badge_style' => 'background: rgba(124,58,237,0.15);',
            ],
            'tabungan' => [
                'url' => '/tabungan',
                'title' => 'Tabungan Siswa',
                'subtitle' => 'Manajemen Tabungan Harian',
                'icon' => 'piggy-bank',
                'color' => '#0d9488',
                'card_class' => 'card-tabungan',
                'badge_text' => 'Live',
                'badge_class' => 'pill-green',
                'badge_style' => 'background: rgba(13,148,136,0.15); color: #0d9488;',
            ],
            'bos' => [
                'url' => '/bos/pemasukan',
                'title' => 'Dana BOS',
                'subtitle' => 'Manajemen Transaksi BOS',
                'icon' => 'landmark',
                'color' => '#d97706',
                'card_class' => 'card-bos',
                'badge_text' => 'Live',
                'badge_class' => 'pill-yellow',
                'badge_style' => 'background: rgba(217,119,6,0.15); color: #d97706;',
            ],
            'absen' => [
                'url' => '/absen',
                'title' => 'Absensi QR',
                'subtitle' => 'Presensi Siswa & Guru',
                'icon' => 'qr-code',
                'color' => '#16a34a',
                'card_class' => 'card-absen',
                'badge_text' => 'V2',
                'badge_class' => 'pill-green',
                'badge_style' => 'background: rgba(22,163,74,0.15);',
            ],
            'bk' => [
                'url' => '/bk',
                'title' => 'Bimbingan Konseling',
                'subtitle' => 'E-BK & Kedisiplinan Siswa',
                'icon' => 'shield',
                'color' => '#0f766e',
                'card_class' => 'card-bk',
                'badge_text' => 'New',
                'badge_class' => 'pill-teal',
                'badge_style' => 'background: rgba(15,118,110,0.15); color: #0f766e;',
            ],
            'manajemen_berkas' => [
                'url' => '/manajemen-berkas',
                'title' => 'Manajemen Berkas',
                'subtitle' => 'Arsip & Dokumen Guru',
                'icon' => 'folder-open',
                'color' => '#be123c',
                'card_class' => 'card-berkas',
                'badge_text' => 'Baru',
                'badge_class' => 'pill-red',
                'badge_style' => 'background: rgba(190,18,60,0.15); color: #be123c;',
            ],
            'kurikulum' => [
                'url' => '/kurikulum',
                'title' => 'Waka Kurikulum',
                'subtitle' => 'Supervisi, Jurnal, & Penilaian',
                'icon' => 'book-open',
                'color' => '#0ea5e9',
                'card_class' => 'card-kurikulum',
                'badge_text' => 'Baru',
                'badge_class' => 'pill-blue',
                'badge_style' => 'background: rgba(14,165,233,0.15); color: #0ea5e9;',
            ],
            'pengaturan_apk' => [
                'url' => '/pengaturan-apk',
                'title' => 'Pengaturan APK',
                'subtitle' => 'Layout & Tema Aplikasi Mobile',
                'icon' => 'smartphone',
                'color' => '#c026d3',
                'card_class' => 'card-apk',
                'badge_text' => 'App',
                'badge_class' => 'pill-purple',
                'badge_style' => 'background: rgba(192,38,211,0.15); color: #c026d3;',
            ],
            'evoting' => [
                'url' => '/evoting/admin',
                'title' => 'E-Voting',
                'subtitle' => 'Pemilihan Online & Bilik Suara',
                'icon' => 'box-select',
                'color' => '#e11d48',
                'card_class' => 'card-evoting',
                'badge_text' => 'New',
                'badge_class' => 'pill-red',
                'badge_style' => 'background: rgba(225,29,72,0.15); color: #e11d48;',
            ],
        ];
    }

    public static function showFlash() {
        if (isset($_SESSION['flash_success'])) {
            echo '<div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700;"><i data-lucide="check-circle" style="width:18px; vertical-align:middle; margin-right:8px; color:#10b981;"></i> ' . htmlspecialchars($_SESSION['flash_success']) . '</div>
                <button onclick="this.parentElement.style.display=\'none\'" style="background:none; border:none; color:#065f46; cursor:pointer;"><i data-lucide="x"></i></button>
            </div>';
            unset($_SESSION['flash_success']);
        }
        if (isset($_SESSION['flash_error'])) {
            echo '<div style="background: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="font-weight: 700;"><i data-lucide="alert-circle" style="width:18px; vertical-align:middle; margin-right:8px; color:#ef4444;"></i> ' . htmlspecialchars($_SESSION['flash_error']) . '</div>
                <button onclick="this.parentElement.style.display=\'none\'" style="background:none; border:none; color:#991b1b; cursor:pointer;"><i data-lucide="x"></i></button>
            </div>';
            unset($_SESSION['flash_error']);
        }
    }

    public static function logActivity($module, $action, $description) {
        try {
            $db = Database::connect('core');
            $user_id = $_SESSION['user_id'] ?? null;
            $username = $_SESSION['username'] ?? 'Guest';
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            
            $stmt = $db->prepare("INSERT INTO system_logs (user_id, username, module, action, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $username, $module, $action, $description, $ip_address]);
        } catch (\Exception $e) {
            // Silently fail logging so it doesn't break main app flow
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }

    public static function sendOneSignalPush($title, $message, $target_role = 'Semua') {
        try {
            $db = Database::connect('core');
            $webSettings = $db->query("SELECT onesignal_app_id, onesignal_rest_api_key FROM website_settings LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            
            $app_id = $webSettings['onesignal_app_id'] ?? '';
            $api_key = $webSettings['onesignal_rest_api_key'] ?? '';
            
            if (empty($app_id) || empty($api_key)) {
                return false;
            }

            $content = ["en" => $message];
            $headings = ["en" => $title];
            
            $fields = [
                'app_id' => $app_id,
                'contents' => $content,
                'headings' => $headings,
                'url' => self::url('/apk')
            ];
            
            if ($target_role !== 'Semua') {
                $fields['filters'] = [
                    ["field" => "tag", "key" => "role", "relation" => "=", "value" => $target_role]
                ];
            } else {
                $fields['included_segments'] = ['Total Subscriptions'];
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $api_key
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            return $response;
        } catch (\Exception $e) {
            error_log("Failed to send OneSignal: " . $e->getMessage());
            return false;
        }
    }
}
