<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect();
$db->exec('ALTER TABLE absensi_pengaturan MODIFY nilai TEXT');
echo 'OK';
