<?php
require 'app/Core/Database.php';

$db = Database::connect();
$niks = ['1804052806130001', '1806151203130005', '1804066308130001', '1804183105130001', '1807124802140004'];
foreach ($niks as $nik) {
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$nik]);
    $user = $stmt->fetch();
    if ($user) {
        echo "NIK $nik found in users table!\n";
    } else {
        echo "NIK $nik NOT found.\n";
    }
}
