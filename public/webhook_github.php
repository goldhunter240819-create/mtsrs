<?php

// Webhook untuk otomatis pull dari GitHub
// Menggunakan bypass safe.directory '*' sesuai instruksi
$output = shell_exec("git -c safe.directory='*' pull origin main 2>&1");

// Log output
$logContent = date('Y-m-d H:i:s') . "\n" . $output . "\n\n";
file_put_contents(__DIR__ . '/webhook_log.txt', $logContent, FILE_APPEND);

echo "OK";
