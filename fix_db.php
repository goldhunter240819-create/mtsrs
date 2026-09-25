<?php
require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::connect();
    
    // Get active year
    $stmt = $db->query("SELECT name FROM tahun_ajaran WHERE status = 'active' LIMIT 1");
    if ($row = $stmt->fetch()) {
        $activeYearName = $row['name'];
        // Update all students to have the active year if they are active
        $db->prepare("UPDATE siswa SET tahun_ajaran = ? WHERE status = 'Aktif' AND (tahun_ajaran IS NULL OR tahun_ajaran = '')")->execute([$activeYearName]);
        echo "Fixed tahun_ajaran for active students.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
