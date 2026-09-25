<?php
$file = 'c:/xampp/htdocs/mtsrs/app/Controllers/AdminFinanceController.php';
$content = file_get_contents($file);

// Find the mangled function
$start = strpos($content, '        $allowed_menus = [\'all\']; // Default for superadmin');
if ($start !== false) {
    // We need to inject the method signature and top lines back in.
    $replacement = "    // Shared render helper\n    private static function render(\$view, \$data = array(), \$required_menu = '') {\n        // self::guard(\$required_menu); // Temporarily disabled for testing\n        \$data['adminNama']    = isset(\$_SESSION['nama']) ? \$_SESSION['nama'] : 'Administrator';\n        \$data['adminInitial'] = strtoupper(substr(\$data['adminNama'], 0, 2));\n        \$data['active_module'] = 'finance';\n        \n        // Pass allowed menus to the view so sidebar can hide menus\n        \$allowed_menus = ['all']; // Default for superadmin";
        
    $content = substr_replace($content, $replacement, $start, strlen("        \$allowed_menus = ['all']; // Default for superadmin"));
    file_put_contents($file, $content);
    echo "Fixed controller.\n";
} else {
    echo "Could not find start point.\n";
}
