<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect();
print_r($db->query('DESCRIBE kalender_pendidikan')->fetchAll(PDO::FETCH_ASSOC));
