<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Helper;

class OneSignalService {
    public static function sendNotification($title, $message, $url = null, $external_user_id = null) {
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
                'url' => $url ?: Helper::url('/apk')
            ];
            
            if ($external_user_id !== null) {
                // Gunakan include_external_user_ids (v9 API and backward compatibility)
                $fields['include_external_user_ids'] = [$external_user_id];
                
                // Juga sertakan include_aliases untuk versi API terbaru / Create Notification v16
                $fields['target_channel'] = "push";
                $fields['include_aliases'] = [
                    "external_id" => [$external_user_id]
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
