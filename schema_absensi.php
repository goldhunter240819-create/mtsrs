<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');
print_r($db->query('DESCRIBE absensi_siswa')->fetchAll(PDO::FETCH_ASSOC));
