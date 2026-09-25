<?php
require 'app/Core/Database.php';
$db = \App\Core\Database::connect();
print_r($db->query("SELECT id, nik, nama FROM siswa WHERE nama='test'")->fetchAll(PDO::FETCH_ASSOC));
