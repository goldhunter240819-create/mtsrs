<?php
$src = file_get_contents('C:\xampp\htdocs\mismifhda\resources\views\siakad\guru.php');

// Replace variables
$src = str_replace('$gurus', '$guruList', $src);

// Replace routes
$src = str_replace('/siakad/guru/add', '/siakad/guru/save', $src);

// Remove container and includes
$src = preg_replace('/<div class="siakad-container">\s*<\?php include __DIR__ . \'\/sidebar\.php\'; \?>\s*<div class="z-main">\s*<\?php include __DIR__ . \'\/_siakad_header\.php\'; \?>/', '', $src);

// The div tags at the end of the container need to be removed.
// We have two closing divs for z-main and siakad-container right before <!-- Modal Add/Edit Guru -->
$src = str_replace("    </div>\n</div>\n\n<!-- Modal Add/Edit Guru -->", "<!-- Modal Add/Edit Guru -->", $src);
$src = str_replace("    </div>\r\n</div>\r\n\r\n<!-- Modal Add/Edit Guru -->", "<!-- Modal Add/Edit Guru -->", $src);

file_put_contents('C:\xampp\htdocs\mtsrs\resources\views\siakad\guru.php', $src);
echo "View migrated successfully.";
