<?php
require 'app/Core/Database.php';
$db = \App\Core\Database::connect();
try {
    $db->exec("CREATE TABLE IF NOT EXISTS jadwal_waktu (
        id INT AUTO_INCREMENT PRIMARY KEY,
        jam_ke INT NOT NULL,
        jam_mulai TIME NOT NULL,
        jam_selesai TIME NOT NULL,
        is_istirahat BOOLEAN DEFAULT FALSE
    )");
    
    // Insert default data if empty
    $count = $db->query("SELECT COUNT(*) FROM jadwal_waktu")->fetchColumn();
    if ($count == 0) {
        $defaults = [
            [1, '07:30:00', '08:15:00', 0],
            [2, '08:15:00', '09:00:00', 0],
            [3, '09:00:00', '09:45:00', 0],
            [4, '09:45:00', '10:00:00', 1],
            [5, '10:00:00', '10:45:00', 0],
            [6, '10:45:00', '11:30:00', 0],
            [7, '11:30:00', '12:15:00', 0],
            [8, '12:15:00', '12:45:00', 1],
            [9, '12:45:00', '13:30:00', 0],
            [10, '13:30:00', '14:15:00', 0]
        ];
        $stmt = $db->prepare("INSERT INTO jadwal_waktu (jam_ke, jam_mulai, jam_selesai, is_istirahat) VALUES (?, ?, ?, ?)");
        foreach ($defaults as $d) {
            $stmt->execute($d);
        }
        echo "Table created and seeded.";
    } else {
        echo "Table already exists and has data.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
