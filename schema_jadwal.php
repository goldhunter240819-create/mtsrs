<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');
print_r($db->query('DESCRIBE jadwal_pelajaran')->fetchAll(PDO::FETCH_ASSOC));
