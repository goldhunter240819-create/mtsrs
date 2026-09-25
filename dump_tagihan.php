<?php
require 'app/core/Database.php';
$db = \App\Core\Database::connect();
$row = $db->query("SELECT * FROM keuangan_tagihan WHERE siswa_id = 254 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
echo json_encode($row);
