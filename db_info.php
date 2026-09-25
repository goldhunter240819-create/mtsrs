<?php
require 'C:/xampp/htdocs/mtsrs/config/database.php';
$c = require('C:/xampp/htdocs/mtsrs/config/database.php');
$db = new PDO('mysql:host='.$c['core']['host'].';dbname='.$c['core']['db_name'], $c['core']['username'], $c['core']['password']);
$stmt = $db->query('SHOW TABLES');
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo "TABLE: " . $row[0] . "\n";
    $cols = $db->query("DESCRIBE " . $row[0]);
    while($col = $cols->fetch(PDO::FETCH_ASSOC)) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
}
