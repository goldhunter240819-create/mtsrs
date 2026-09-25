<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');

try {
    $db->query('ALTER TABLE absensi_siswa CHANGE waktu_scan jam_masuk TIME NULL');
    echo 'Renamed to jam_masuk. ';
} catch(Exception $e) {
    echo 'Rename error: ' . $e->getMessage() . '. ';
}

try {
    $db->query('ALTER TABLE absensi_siswa ADD COLUMN jam_pulang TIME NULL AFTER jam_masuk');
    echo 'Added jam_pulang.';
} catch(Exception $e) {
    echo 'Add error: ' . $e->getMessage();
}
