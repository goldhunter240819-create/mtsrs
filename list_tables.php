<?php
require 'app/Core/Database.php';
$db = \App\Core\Database::connect('siakad');
$res = $db->query('SHOW CREATE TABLE absensi_permapel')->fetch(PDO::FETCH_ASSOC);
print_r($res['Create Table']);
