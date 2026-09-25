<?php
$pdo = new PDO('mysql:host=localhost;dbname=db_mts_rs', 'root', '');
$stmt = $pdo->query('SHOW TABLES');
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo $row[0] . "\n";
}
