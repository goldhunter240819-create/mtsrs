<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');

try {
    $db->query("ALTER TABLE absensi_guru ADD COLUMN tahun_ajaran_id INT NULL");
    echo "Added tahun_ajaran_id";
} catch(Exception $e) {
    echo $e->getMessage();
}
