<?php
require 'app/Core/Database.php';
$db = \App\Core\Database::connect('siakad');
$stmt = $db->query('DESCRIBE jadwal_pelajaran');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo $c['Field'] . "\n";
}
