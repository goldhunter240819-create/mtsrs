<?php
// Script to patch render method in controllers
function patchController($file) {
    $content = file_get_contents($file);
    
    $oldRender = "extract(\$data);\n        include __DIR__ . '/../../resources/views/keuangan/' . \$view . '.php';";
    $newRender = "extract(\$data);
        ob_start();
        include __DIR__ . '/../../resources/views/keuangan/' . \$view . '.php';
        \$content = ob_get_clean();
        
        \$activeMenu = isset(\$active_page) ? \$active_page : \$view;
        // Fix active menu string to match layout.php sidebar matches
        if (strpos(\$activeMenu, 'keuangan_') !== 0 && strpos(\$activeMenu, 'tabungan_') !== 0 && strpos(\$activeMenu, 'absen_') !== 0 && strpos(\$activeMenu, 'siakad_') !== 0) {
            \$activeMenu = 'keuangan_' . \$activeMenu;
        }
        
        \$title = isset(\$page_title) ? \$page_title : 'Keuangan & SPP';
        require __DIR__ . '/../../resources/views/layout.php';";
        
    $content = str_replace($oldRender, $newRender, $content);
    file_put_contents($file, $content);
}

patchController('c:/xampp/htdocs/mtsrs/app/Controllers/AdminFinanceController.php');
patchController('c:/xampp/htdocs/mtsrs/app/Controllers/AdminTabunganController.php');
echo "Controllers patched.\n";
