<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');
$res = $db->query('SHOW CREATE TABLE absensi_siswa')->fetch();
file_put_contents('absensi_siswa_schema.txt', print_r($res, true));
