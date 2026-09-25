<?php
require 'app/Core/Database.php'; 
require 'app/Core/Helper.php'; 

$db = App\Core\Database::connect('core');
$webSettings = $db->query("SELECT onesignal_app_id, onesignal_rest_api_key FROM website_settings LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
$app_id = $webSettings['onesignal_app_id'];
$api_key = $webSettings['onesignal_rest_api_key'];

$fields = [
    'app_id' => $app_id,
    'contents' => ["en" => "Test"],
    'headings' => ["en" => "Test"],
    'included_segments' => ['Total Subscriptions']
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json; charset=utf-8',
    'Authorization: Basic ' . $api_key
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
echo "Total Subscriptions Response: " . $response . "\n";

$fields['included_segments'] = ['Active Users'];
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
$response = curl_exec($ch);
echo "Active Users Response: " . $response . "\n";

$fields['included_segments'] = ['Subscribed Users'];
$fields['target_channel'] = 'push';
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
$response = curl_exec($ch);
echo "Subscribed Users + push channel Response: " . $response . "\n";
