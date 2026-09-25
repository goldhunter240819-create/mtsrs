<?php
$pdo = new PDO('mysql:host=localhost;dbname=db_mts_rs', 'root', '');
$stmt = $pdo->query('DESCRIBE jadwal_pelajaran');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
$stmt = $pdo->query('DESCRIBE jadwal_waktu');
if ($stmt) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
}
