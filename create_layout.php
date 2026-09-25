<?php
$layout = file_get_contents('C:/xampp/htdocs/mtsrs/resources/views/layout.php');
$head = substr($layout, 0, strpos($layout, '<body>') + 6);
$foot = '<script src="https://unpkg.com/lucide@latest"></script><script>lucide.createIcons();</script></body></html>';
$newLayout = $head . "\n" . '<?php echo $content ?? ""; ?>' . "\n" . $foot;
file_put_contents('C:/xampp/htdocs/mtsrs/resources/views/absen_v2_layout.php', $newLayout);
echo "absen_v2_layout.php created.\n";
?>
