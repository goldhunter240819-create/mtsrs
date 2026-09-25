<?php
$file = 'c:\xampp\htdocs\mtsrs\resources\views\siakad\guru.php';
$content = file_get_contents($file);

$content = str_replace('class="z-modal"', 'class="mm-modal"', $content);
$content = str_replace('class="z-modal-content', 'class="mm-modal-content', $content);
$content = str_replace('class="z-modal-header', 'class="mm-modal-header', $content);
$content = str_replace('class="z-modal-body', 'class="mm-modal-body', $content);
$content = str_replace('class="z-modal-footer', 'class="mm-modal-footer', $content);

$content = str_replace('.z-modal ', '.mm-modal ', $content);
$content = str_replace('.z-modal{', '.mm-modal{', $content);
$content = str_replace('.z-modal-content', '.mm-modal-content', $content);
$content = str_replace('.z-modal-header', '.mm-modal-header', $content);
$content = str_replace('.z-modal-body', '.mm-modal-body', $content);
$content = str_replace('.z-modal-footer', '.mm-modal-footer', $content);

file_put_contents($file, $content);
echo "Modal classes renamed.";
