<?php
$db = new PDO('mysql:host=localhost;dbname=db_mts_rs', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
foreach([7,8,9] as $t) { 
    $db->query("INSERT IGNORE INTO kurikulum_kkm (tahun_ajaran_id, mapel_id, tingkat, nilai_kkm) SELECT 3, id, $t, 70 FROM mapel"); 
    $db->query("UPDATE kurikulum_kkm SET nilai_kkm = 70 WHERE tahun_ajaran_id = 3 AND tingkat = $t");
}
echo "KKM set to 70\n";
