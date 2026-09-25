<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');
$today = date('Y-m-d');

try {
    $stmt1 = $db->prepare("DELETE FROM absensi_siswa WHERE tanggal = ?");
    $stmt1->execute([$today]);
    echo "Deleted " . $stmt1->rowCount() . " rows from absensi_siswa.\n";

    $stmt2 = $db->prepare("DELETE FROM absensi_guru WHERE tanggal = ?");
    $stmt2->execute([$today]);
    echo "Deleted " . $stmt2->rowCount() . " rows from absensi_guru.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
