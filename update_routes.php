<?php
$file = 'c:/xampp/htdocs/mtsrs/index.php';
$c = file_get_contents($file);

// 1. Replace BOS routes
$c = str_replace(
    "'/keuangan/bos",
    "'/bos",
    $c
);
$c = preg_replace(
    "/\[\\\App\\\Controllers\\\AdminFinanceController::class, 'bos(.*?)\'\]/",
    "[\\App\\Controllers\\AdminBosController::class, 'bos$1']",
    $c
);
$c = preg_replace(
    "/\[\\\App\\\Controllers\\\AdminFinanceController::class, 'saveBos(.*?)\'\]/",
    "[\\App\\Controllers\\AdminBosController::class, 'saveBos$1']",
    $c
);
$c = preg_replace(
    "/\[\\\App\\\Controllers\\\AdminFinanceController::class, 'deleteBos(.*?)\'\]/",
    "[\\App\\Controllers\\AdminBosController::class, 'deleteBos$1']",
    $c
);

// 2. Replace Tabungan routes
$c = str_replace(
    "'/keuangan/tabungan",
    "'/tabungan",
    $c
);

file_put_contents($file, $c);
echo "Updated routes in index.php\n";
