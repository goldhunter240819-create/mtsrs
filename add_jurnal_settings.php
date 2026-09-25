<?php
$pdo = new PDO('mysql:host=localhost;dbname=db_mts_rs', 'root', '');
$stmt = $pdo->query("SHOW COLUMNS FROM institusi LIKE 'strict_jurnal_mode'");
if ($stmt->rowCount() == 0) {
    $pdo->exec("ALTER TABLE institusi ADD COLUMN strict_jurnal_mode TINYINT(1) DEFAULT 0");
    $pdo->exec("ALTER TABLE institusi ADD COLUMN jurnal_grace_period INT(11) DEFAULT 30");
    echo "Columns added.";
} else {
    echo "Columns already exist.";
}
