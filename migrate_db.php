<?php
$dbCore = new PDO('mysql:host=127.0.0.1;dbname=core_db', 'root', '');
$dbMtsrs = new PDO('mysql:host=127.0.0.1;dbname=db_mts_rs', 'root', '');

// Drop all keuangan_ tables in db_mts_rs
$stmtMts = $dbMtsrs->query("SHOW TABLES LIKE 'keuangan_%'");
$tablesMts = $stmtMts->fetchAll(PDO::FETCH_COLUMN);
$dbMtsrs->query('SET FOREIGN_KEY_CHECKS = 0');
foreach($tablesMts as $t) {
    $dbMtsrs->exec("DROP TABLE IF EXISTS `$t`");
    echo "Dropped $t from db_mts_rs\n";
}
$dbMtsrs->query('SET FOREIGN_KEY_CHECKS = 1');

// Fetch structure from core_db and create in db_mts_rs
$stmtCore = $dbCore->query("SHOW TABLES LIKE 'keuangan_%'");
$tablesCore = $stmtCore->fetchAll(PDO::FETCH_COLUMN);

foreach($tablesCore as $t) {
    $createStmt = $dbCore->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_ASSOC);
    if(isset($createStmt['Create Table'])) {
        $sql = $createStmt['Create Table'];
        $dbMtsrs->exec($sql);
        echo "Created $t in db_mts_rs\n";
        
        $rows = $dbCore->query("SELECT * FROM `$t`")->fetchAll(PDO::FETCH_ASSOC);
        if(count($rows) > 0) {
            $cols = array_keys($rows[0]);
            $colList = implode(',', $cols);
            $placeholders = implode(',', array_fill(0, count($cols), '?'));
            
            $insertStmt = $dbMtsrs->prepare("INSERT IGNORE INTO `$t` ($colList) VALUES ($placeholders)");
            foreach($rows as $r) {
                $insertStmt->execute(array_values($r));
            }
            echo "Migrated " . count($rows) . " rows for $t\n";
        }
    }
}
echo "Database migration completed.\n";
