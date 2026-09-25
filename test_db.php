<?php
$_GET['tanggal'] = '2026-09-02';
$_SESSION['guru_id'] = 1;
require_once 'C:\xampp\htdocs\mtsrs\app\Core\Database.php';
require_once 'C:\xampp\htdocs\mtsrs\app\Core\Helper.php';
require_once 'C:\xampp\htdocs\mtsrs\app\Controllers\ApkController.php';

$apk = new \App\Controllers\ApkController();
$apk->getJadwalHarian();

