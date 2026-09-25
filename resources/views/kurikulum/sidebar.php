<?php
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function isKurikulumActive($path) {
    global $current_path;
    return ($current_path === $path) ? 'active' : '';
}

// Ensure institusi data is available for sidebar header
if (!isset($institusi)) {
    $db_core_sb = \App\Core\Database::connect('core');
    $institusi = $db_core_sb->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
}
?>

<div class="z-sidebar" id="zSidebar">
    <!-- Header -->
    <div class="z-sidebar-header">
        <div class="logo-box">
            <?php 
                $logoUrl = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png');
            ?>
            <img src="<?php echo $logoUrl; ?>" alt="Logo">
        </div>
        <div class="header-text">
            <h2>Waka Kurikulum</h2>
            <p><?php echo htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin'); ?></p>
        </div>
        <button class="z-sidebar-close" onclick="zToggleSidebar()">
            <i data-lucide="x"></i>
        </button>
    </div>

    <!-- User Section -->
    <div class="z-sidebar-user">
        <div class="avatar">
            <?php echo isset($initial) ? $initial : substr($_SESSION['nama'] ?? 'W', 0, 1); ?>
        </div>
        <div class="info">
            <div class="name" title="<?php echo htmlspecialchars($_SESSION['nama'] ?? 'Waka Kurikulum'); ?>">
                <?php echo htmlspecialchars($_SESSION['nama'] ?? 'Waka Kurikulum'); ?>
            </div>
            <div class="role">
                <?php 
                if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                    echo "Administrator";
                } elseif (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 99) {
                    echo "Developer";
                } else {
                    echo "Waka Kurikulum";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Menus -->
    <div class="z-sidebar-menu">
        <div class="menu-label">MENU UTAMA</div>
        
        <a href="/kurikulum" class="menu-item <?php echo isKurikulumActive('/kurikulum'); ?>">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>

        <div class="menu-label">SUPERVISI & EVALUASI</div>
        
        <a href="/kurikulum/supervisi-administrasi" class="menu-item <?php echo isKurikulumActive('/kurikulum/supervisi-administrasi'); ?>">
            <i data-lucide="folder-check"></i>
            <span>Supervisi Administrasi</span>
        </a>
        
        <a href="/kurikulum/supervisi-kelas" class="menu-item <?php echo isKurikulumActive('/kurikulum/supervisi-kelas'); ?>">
            <i data-lucide="monitor-play"></i>
            <span>Supervisi Kelas</span>
        </a>
        
        <a href="/kurikulum/monitoring-jurnal" class="menu-item <?php echo isKurikulumActive('/kurikulum/monitoring-jurnal'); ?>">
            <i data-lucide="book-check"></i>
            <span>Monitoring Jurnal</span>
        </a>

        <a href="/kurikulum/supervisi-penilaian" class="menu-item <?php echo isKurikulumActive('/kurikulum/supervisi-penilaian'); ?>">
            <i data-lucide="clipboard-list"></i>
            <span>Supervisi Penilaian</span>
        </a>

        <div class="menu-label">NAVIGASI SISTEM</div>
        <a href="/portal" class="menu-item" style="color: #64748b;">
            <i data-lucide="arrow-left-circle"></i>
            <span>Ke Portal Utama</span>
        </a>
    </div>
</div>
