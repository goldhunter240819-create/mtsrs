<?php
$file = 'c:\xampp\htdocs\mtsrs\resources\views\siakad\guru.php';
$content = file_get_contents($file);

// Replace href="/siakad/guru/...
$content = preg_replace('/href="\/siakad\/guru\/([^"]*)"/', 'href="<?php echo \App\Core\Helper::url(\'/siakad/guru/$1\'); ?>"', $content);

// Replace action="/siakad/guru/...
$content = preg_replace('/action="\/siakad\/guru\/([^"]*)"/', 'action="<?php echo \App\Core\Helper::url(\'/siakad/guru/$1\'); ?>"', $content);

// Replace fetch('/siakad/guru/...
$content = preg_replace('/fetch\(\'\/siakad\/guru\/([^\']*)\'/', 'fetch(\'<?php echo \App\Core\Helper::url(\'/siakad/guru/$1\'); ?>\'', $content);

// Replace form.action = '/siakad/guru/...
$content = preg_replace('/form\.action = \'\/siakad\/guru\/([^\']*)\'/', 'form.action = \'<?php echo \App\Core\Helper::url(\'/siakad/guru/$1\'); ?>\'', $content);

file_put_contents($file, $content);
echo "URLs fixed.";
