<?php
$pdo = new PDO('mysql:host=localhost;dbname=db_mts_rs', 'root', '');
$stmt = $pdo->query('DESCRIBE institusi');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
