<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');

try {
    $db->exec("CREATE TABLE IF NOT EXISTS jam_absen_guru (
        id INT AUTO_INCREMENT PRIMARY KEY,
        jam_masuk_mulai TIME NOT NULL DEFAULT '06:00:00',
        jam_masuk_akhir TIME NOT NULL DEFAULT '07:30:00',
        jam_masuk_batas TIME NOT NULL DEFAULT '07:00:00',
        jam_pulang_mulai TIME NOT NULL DEFAULT '13:00:00',
        jam_pulang_akhir TIME NOT NULL DEFAULT '15:00:00',
        jam_pulang_kamis TIME NOT NULL DEFAULT '12:00:00',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Check if empty
    $count = $db->query("SELECT COUNT(*) FROM jam_absen_guru")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO jam_absen_guru (jam_masuk_mulai, jam_masuk_akhir, jam_masuk_batas, jam_pulang_mulai, jam_pulang_akhir, jam_pulang_kamis) 
            VALUES ('06:00:00', '07:30:00', '07:00:00', '13:00:00', '15:00:00', '12:00:00')");
    }
    
    echo "Table jam_absen_guru created and populated!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
