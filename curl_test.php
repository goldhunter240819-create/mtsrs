<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://mtsrs.ghsystem.my.id/keuangan");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$output = curl_exec($ch);
curl_close($ch);
file_put_contents('curl_output.txt', $output);
echo "Done";
