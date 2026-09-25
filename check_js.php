<?php
$c = file_get_contents('c:/xampp/htdocs/mtsrs/resources/views/siakad/guru.php');
preg_match('/<script>(.*?)<\/script>/s', $c, $m);
file_put_contents('c:/xampp/htdocs/mtsrs/scratch.js', $m[1]);
system('node -c c:/xampp/htdocs/mtsrs/scratch.js', $ret);
exit($ret);
