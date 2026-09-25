<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('core');

try {
    // Generate for users
    $users = $db->query("SELECT id FROM users WHERE qr_token IS NULL OR qr_token = ''")->fetchAll();
    foreach ($users as $u) {
        $token = bin2hex(random_bytes(16));
        $db->query("UPDATE users SET qr_token = '{$token}' WHERE id = {$u['id']}");
    }
    
    // Sync to guru
    $gurus = $db->query("SELECT g.id, u.qr_token FROM guru g JOIN users u ON g.user_id = u.id")->fetchAll();
    foreach ($gurus as $g) {
        $db->query("UPDATE guru SET qr_token = '{$g['qr_token']}' WHERE id = {$g['id']}");
    }
    
    // Sync to siswa
    $siswas = $db->query("SELECT s.id, u.qr_token FROM siswa s JOIN users u ON s.user_id = u.id")->fetchAll();
    foreach ($siswas as $s) {
        $db->query("UPDATE siswa SET qr_token = '{$s['qr_token']}' WHERE id = {$s['id']}");
    }
    
    echo "Tokens generated and synced!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
