<?php
$dbCore = new PDO('mysql:host=127.0.0.1;dbname=core_db', 'root', '');
$dbMtsrs = new PDO('mysql:host=127.0.0.1;dbname=db_mts_rs', 'root', '');

$stmtCore = $dbCore->query("SHOW TABLES LIKE '%keuangan%'");
$tablesCore = $stmtCore->fetchAll(PDO::FETCH_COLUMN);

echo "Tables in core_db (mismifhda) with 'keuangan':\n";
print_r($tablesCore);

$stmtMts = $dbMtsrs->query("SHOW TABLES LIKE '%keuangan%'");
$tablesMts = $stmtMts->fetchAll(PDO::FETCH_COLUMN);

echo "\nTables in db_mts_rs (mtsrs) with 'keuangan':\n";
print_r($tablesMts);
