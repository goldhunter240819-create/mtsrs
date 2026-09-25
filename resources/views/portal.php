<?php
use App\Core\Helper;
use App\Core\Branding;

$institusi = Branding::getInstitusi();
$logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
?>
<!-- Elegant Welcome Text -->
<div style="text-align: center; margin: 3.5rem 0 4rem;">
    <div style="margin-bottom: 1.5rem;">
        <img src="<?php echo $logoSrc; ?>" alt="Logo" style="width: 100px; height: 100px; object-fit: contain; filter: drop-shadow(0 8px 16px rgba(0,0,0,0.08));">
    </div>
    <h1 style="font-size: 2.25rem; font-weight: 800; color: #1e293b; margin-bottom: 0.75rem; letter-spacing: -0.5px;">
        Selamat Datang di <span style="background: linear-gradient(135deg, #2563eb, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">MTs Roudlotus Sholihin</span>
    </h1>
    <p style="color: #64748b; font-size: 1.05rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
        Platform portal digital terpadu untuk Sistem Informasi Akademik (SIAKAD), Pengelolaan Keuangan & SPP, serta Absensi Presensi QR.
    </p>
</div>

<!-- Main Module Hub Grid -->
<div class="z-page-head-row" style="margin-top: 1rem;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
            <i data-lucide="grid-2x2" style="color: var(--z-primary); width:20px; height:20px;"></i> Modul Aplikasi Utama
        </h2>
    </div>
    <span class="pill pill-blue">Database: db_mts_rs</span>
</div>

<style>
    .portal-module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .portal-module-card {
        border-radius: 20px;
        padding: 1.75rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: 1px solid rgba(255,255,255,0.6);
        position: relative;
        overflow: hidden;
    }
    .portal-module-card::before {
        content: '';
        position: absolute; inset: 0;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        opacity: 0; transition: opacity 0.3s; z-index: 0;
    }
    .portal-module-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px -5px rgba(0,0,0,0.1);
    }
    .portal-module-card:hover::before { opacity: 1; }

    .card-siakad {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        box-shadow: 0 8px 25px -5px rgba(37,99,235,0.15);
        border-color: rgba(37,99,235,0.2);
    }
    .card-siakad:hover { border-color: rgba(37,99,235,0.4); }

    .card-keuangan {
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        box-shadow: 0 8px 25px -5px rgba(124,58,237,0.15);
        border-color: rgba(124,58,237,0.2);
    }
    .card-keuangan:hover { border-color: rgba(124,58,237,0.4); }

    .card-absen {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        box-shadow: 0 8px 25px -5px rgba(22,163,74,0.15);
        border-color: rgba(22,163,74,0.2);
    }
    .card-absen:hover {
        border-color: rgba(22,163,74,0.3);
        box-shadow: 0 10px 20px -5px rgba(22,163,74,0.1);
        transform: translateY(-4px);
    }
    .card-absen .pm-icon { background: rgba(22,163,74,0.1); color: #16a34a; }
    .card-absen:hover .pm-icon { background: #16a34a; color: white; }

    .card-bk {
        background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
        box-shadow: 0 8px 25px -5px rgba(15,118,110,0.15);
        border-color: rgba(15,118,110,0.2);
    }
    .card-bk:hover {
        border-color: rgba(15,118,110,0.3);
        box-shadow: 0 10px 20px -5px rgba(15,118,110,0.1);
        transform: translateY(-4px);
    }
    .card-bk .pm-icon { background: rgba(15,118,110,0.1); color: #0f766e; }
    .card-bk:hover .pm-icon { background: #0f766e; color: white; }

    .card-apk {
        background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%);
        box-shadow: 0 8px 25px -5px rgba(192,38,211,0.15);
        border-color: rgba(192,38,211,0.2);
    }
    .card-apk:hover {
        border-color: rgba(192,38,211,0.3);
        box-shadow: 0 10px 20px -5px rgba(192,38,211,0.1);
        transform: translateY(-4px);
    }
    .card-apk .pm-icon { background: rgba(192,38,211,0.1); color: #c026d3; }
    .card-apk:hover .pm-icon { background: #c026d3; color: white; }

    .card-tabungan {
        background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
        box-shadow: 0 8px 25px -5px rgba(13,148,136,0.15);
        border-color: rgba(13,148,136,0.2);
    }
    .card-tabungan:hover { border-color: rgba(13,148,136,0.4); }

    .card-bos {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        box-shadow: 0 8px 25px -5px rgba(217,119,6,0.15);
        border-color: rgba(217,119,6,0.2);
    }
    .card-bos:hover { border-color: rgba(217,119,6,0.4); }

    .card-bk {
        background: linear-gradient(135deg, #f0fdf4 0%, #ccfbf1 100%);
        box-shadow: 0 8px 25px -5px rgba(15,118,110,0.15);
        border-color: rgba(15,118,110,0.2);
    }
    .card-bk:hover { border-color: rgba(15,118,110,0.4); }

    .card-berkas {
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        box-shadow: 0 8px 25px -5px rgba(190,18,60,0.15);
        border-color: rgba(190,18,60,0.2);
    }
    .card-berkas:hover { border-color: rgba(190,18,60,0.4); }

    .card-kurikulum {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        box-shadow: 0 8px 25px -5px rgba(14,165,233,0.15);
        border-color: rgba(14,165,233,0.2);
    }
    .card-kurikulum:hover { border-color: rgba(14,165,233,0.4); }

    .card-apk {
        background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%);
        box-shadow: 0 8px 25px -5px rgba(192,38,211,0.15);
        border-color: rgba(192,38,211,0.2);
    }
    .card-apk:hover { border-color: rgba(192,38,211,0.4); }

    .card-evoting {
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        box-shadow: 0 8px 25px -5px rgba(225,29,72,0.15);
        border-color: rgba(225,29,72,0.2);
    }
    .card-evoting:hover { border-color: rgba(225,29,72,0.4); }

    .card-dev {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        box-shadow: 0 8px 25px -5px rgba(15,23,42,0.3);
        border-color: rgba(255,255,255,0.1);
    }
    .card-dev:hover { border-color: rgba(255,255,255,0.3); }

    .pm-icon {
        width: 64px; height: 64px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; flex-shrink: 0;
        position: relative; z-index: 1;
        background: rgba(255,255,255,0.8);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .card-siakad .pm-icon { color: #2563eb; }
    .card-keuangan .pm-icon { color: #7c3aed; }
    .card-absen .pm-icon { color: #16a34a; }
    .card-tabungan .pm-icon { color: #0d9488; }
    .card-bos .pm-icon { color: #d97706; }
    .card-bk .pm-icon { color: #0f766e; }
    .card-berkas .pm-icon { color: #be123c; }
    .card-kurikulum .pm-icon { color: #0ea5e9; }
    .card-apk .pm-icon { color: #c026d3; }
    .card-evoting .pm-icon { color: #e11d48; }
    .card-dev .pm-icon { color: #0f172a; }

    .pm-info {
        flex: 1; display: flex; flex-direction: column; justify-content: center;
        position: relative; z-index: 1;
    }
    .pm-title { 
        font-size: 1.25rem; font-weight: 800; color: #1e293b; 
        letter-spacing: -0.3px; margin-bottom: 6px; 
        display: flex; align-items: center; gap: 8px; 
    }
    .card-dev .pm-title { color: white; }
    .pm-subtitle { font-size: 0.85rem; color: #475569; line-height: 1.4; font-weight: 500; }
    .card-dev .pm-subtitle { color: #94a3b8; }
    
    .card-arrow {
        position: relative; z-index: 1;
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.6);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: 0.3s; opacity: 0.7;
    }
    .card-siakad .card-arrow { color: #2563eb; }
    .card-keuangan .card-arrow { color: #7c3aed; }
    .card-absen .card-arrow { color: #16a34a; }
    .card-tabungan .card-arrow { color: #0d9488; }
    .card-bos .card-arrow { color: #d97706; }
    .card-bk .card-arrow { color: #0f766e; }
    .card-berkas .card-arrow { color: #be123c; }
    .card-kurikulum .card-arrow { color: #0ea5e9; }
    .card-apk .card-arrow { color: #c026d3; }
    .card-evoting .card-arrow { color: #e11d48; }
    .card-dev .card-arrow { color: #0f172a; }
    
    .portal-module-card:hover .card-arrow {
        transform: translateX(5px); opacity: 1; background: #fff;
    }
</style>

<?php
// Function to check if user has access to a specific portal module
function hasPortalAccess($moduleKey) {
    if (!isset($_SESSION['role_id'])) return false;
    // Developer (99) and roles other than Admin (1) typically have their own access logic, 
    // but for portal cards, we might just allow 99, or if role != 1 we allow them to click and let the controller handle auth.
    // For Admin (1), we strictly check the portal_access column.
    if ($_SESSION['role_id'] == 99) return true;
    
    // If it's a student (role 3), they only see what they should based on logic, but for now we let the portal show it
    if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) return true;

    // For Admin (1) and Guru (2), fetch directly from database for real-time updates
    static $userAccessStr = [];
    $uid = $_SESSION['user_id'];
    
    if (!isset($userAccessStr[$uid])) {
        $db = \App\Core\Database::connect('core');
        $stmt = $db->prepare("SELECT portal_access FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        $userAccessStr[$uid] = $user ? $user['portal_access'] : null;
    }
    
    $accessStr = $userAccessStr[$uid];
    
    // If NULL, we assume they have full access to prevent breaking existing setups
    if (is_null($accessStr)) return true;
    
    // If empty string, it means explicitly NO access
    if ($accessStr === '') return false;
    
    $allowedModules = array_map('trim', explode(',', strtolower($accessStr)));
    return in_array($moduleKey, $allowedModules);
}
?>
<div class="portal-module-grid">
    <?php 
    $portalModules = \App\Core\Helper::getPortalModules();
    foreach($portalModules as $key => $mod): 
        if (hasPortalAccess($key)): 
    ?>
    <div class="portal-module-card <?php echo $mod['card_class']; ?>" onclick="location.href='<?php echo Helper::url($mod['url']); ?>'">
        <div class="pm-icon"><i data-lucide="<?php echo $mod['icon']; ?>"></i></div>
        <div class="pm-info">
            <div class="pm-title"><?php echo $mod['title']; ?> <span class="pill <?php echo $mod['badge_class']; ?>" style="font-size: 0.65rem; padding: 2px 8px; <?php echo $mod['badge_style']; ?>"><?php echo $mod['badge_text']; ?></span></div>
            <div class="pm-subtitle"><?php echo $mod['subtitle']; ?></div>
        </div>
        <div class="card-arrow"><i data-lucide="chevron-right" style="width: 20px; height: 20px;"></i></div>
    </div>
    <?php 
        endif; 
    endforeach; 
    ?>

    <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
    <div class="portal-module-card card-dev" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%);" onclick="location.href='<?php echo Helper::url('/admin/portal-users'); ?>'">
        <div class="pm-icon"><i data-lucide="shield"></i></div>
        <div class="pm-info">
            <div class="pm-title">Admin Panel <span class="pill pill-purple" style="font-size: 0.65rem; padding: 2px 8px; background: rgba(255,255,255,0.15); color: #fff;">App</span></div>
            <div class="pm-subtitle">Pengaturan Akses Portal Guru</div>
        </div>
        <div class="card-arrow"><i data-lucide="chevron-right" style="width: 20px; height: 20px;"></i></div>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 99): ?>
    <div class="portal-module-card card-dev" onclick="location.href='<?php echo Helper::url('/dev'); ?>'">
        <div class="pm-icon"><i data-lucide="terminal"></i></div>
        <div class="pm-info">
            <div class="pm-title">Dev Panel <span class="pill pill-purple" style="font-size: 0.65rem; padding: 2px 8px; background: rgba(255,255,255,0.15); color: #fff;">Beta</span></div>
            <div class="pm-subtitle">Khusus Developer / Super Admin</div>
        </div>
        <div class="card-arrow"><i data-lucide="chevron-right" style="width: 20px; height: 20px;"></i></div>
    </div>
    <?php endif; ?>
</div>
