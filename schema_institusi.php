<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('siakad');
print_r($db->query('DESCRIBE institusi')->fetchAll(PDO::FETCH_ASSOC));
print_r($db->query('SELECT * FROM institusi')->fetchAll(PDO::FETCH_ASSOC));
