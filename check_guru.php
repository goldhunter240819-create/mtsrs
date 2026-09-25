<?php
require 'app/Core/Database.php';
$db = App\Core\Database::connect('core');
$res = $db->query('SHOW CREATE TABLE guru')->fetch();
file_put_contents('guru_schema.txt', print_r($res['Create Table'], true));
