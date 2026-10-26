<?php

// Webhook untuk otomatis pull dari GitHub
// 1. Bersihkan perubahan lokal tak sengaja di server agar pull tidak abort
shell_exec("git -c safe.directory=* checkout -- . 2>&1");
shell_exec("git -c safe.directory=* clean -fd 2>&1");

// 2. Tarik update terbaru dari origin/main
$output = shell_exec("git -c safe.directory=* pull origin main 2>&1");

// 3. Reset OPcache agar PHP langsung menyajikan kode terbaru
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

// Log output
$logContent = date('Y-m-d H:i:s') . "\n" . $output . "\n\n";
file_put_contents(__DIR__ . '/webhook_log.txt', $logContent, FILE_APPEND);

echo "OK\n" . $output;

