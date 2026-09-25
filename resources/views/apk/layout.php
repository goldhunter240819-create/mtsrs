<?php
// GHTech Analytics Tracking
$_ghtech_key = "96d8e171f6e319449cb43ea5d2d4825e";
$_ghtech_ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]);
if ($_ghtech_ip == "127.0.0.1" || $_ghtech_ip == "::1") {
    if (!isset($_COOKIE["ghtech_vid"])) { $vid = uniqid("loc_"); setcookie("ghtech_vid", $vid, time() + 86400, "/"); $_ghtech_ip = $vid; } else { $_ghtech_ip = $_COOKIE["ghtech_vid"]; }
}
// Track IP langsung
@file_get_contents("http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip));
// Update username setelah session aktif
register_shutdown_function(function() {
    global $_ghtech_key, $_ghtech_ip;
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_u = "";
        if (isset($_SESSION["guru_nama"])) { $_u = $_SESSION["guru_nama"]; }
        elseif (isset($_SESSION["siswa_nama"])) { $_u = $_SESSION["siswa_nama"]; }
        elseif (isset($_SESSION["nama"])) { $_u = $_SESSION["nama"]; }
        if (!empty($_u)) {
            @file_get_contents("http://localhost/ghtech/api/track.php?key=" . $_ghtech_key . "&ip=" . urlencode($_ghtech_ip) . "&user=" . urlencode($_u));
        }
    }
});
?>
<?php
use App\Core\Helper;
use App\Core\Branding;

$institusi = Branding::getInstitusi();
$logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
$faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
$primaryColor = !empty($institusi['warna_primer']) ? $institusi['warna_primer'] : '#10b981';

if (!isset($title)) $title = "APK Web View";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($title); ?></title>
    
    <link rel="icon" type="image/png" href="<?php echo $faviconSrc; ?>">
    <link rel="stylesheet" href="<?php echo Helper::url('/assets/css/z-style.css'); ?>?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php
    $db_settings = \App\Core\Database::connect('core');
    $webSettings = $db_settings->query("SELECT onesignal_app_id FROM website_settings LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
    $os_app_id = $webSettings['onesignal_app_id'] ?? '';
    
    $os_user_id = null;
    $os_role = null;
    if (isset($_SESSION['guru_id'])) {
        $os_user_id = 'guru_' . $_SESSION['guru_id'];
        $os_role = 'Guru';
    } elseif (isset($_SESSION['siswa_id'])) {
        $os_user_id = 'siswa_' . $_SESSION['siswa_id'];
        $os_role = 'Siswa';
    }
    ?>
    <?php if (!empty($os_app_id)): ?>
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
      window.OneSignalDeferred = window.OneSignalDeferred || [];
      OneSignalDeferred.push(async function(OneSignal) {
        await OneSignal.init({
          appId: "<?php echo htmlspecialchars($os_app_id); ?>",
        });
        <?php if ($os_user_id): ?>
        OneSignal.login("<?php echo $os_user_id; ?>");
        OneSignal.User.addTag("role", "<?php echo $os_role; ?>");
        <?php endif; ?>
      });
    </script>
    <?php endif; ?>
    
    <style>
        :root {
            --apk-primary: <?php echo $primaryColor; ?>;
            --apk-bg: #f8fafc;
        }
        body {
            background: var(--apk-bg);
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Mobile-First Constraints */
        .apk-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            position: relative;
            min-height: 100vh;
            background: #f1f5f9;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }

        /* Vector Header Pattern */
        .apk-vector-header {
            background-color: var(--apk-primary);
            background-image: 
                linear-gradient(30deg, rgba(255,255,255,0.1) 12%, transparent 12.5%, transparent 87%, rgba(255,255,255,0.1) 87.5%, rgba(255,255,255,0.1)),
                linear-gradient(150deg, rgba(255,255,255,0.1) 12%, transparent 12.5%, transparent 87%, rgba(255,255,255,0.1) 87.5%, rgba(255,255,255,0.1)),
                linear-gradient(30deg, rgba(255,255,255,0.1) 12%, transparent 12.5%, transparent 87%, rgba(255,255,255,0.1) 87.5%, rgba(255,255,255,0.1)),
                linear-gradient(150deg, rgba(255,255,255,0.1) 12%, transparent 12.5%, transparent 87%, rgba(255,255,255,0.1) 87.5%, rgba(255,255,255,0.1)),
                linear-gradient(60deg, rgba(255,255,255,0.1) 25%, transparent 25.5%, transparent 75%, rgba(255,255,255,0.1) 75%, rgba(255,255,255,0.1)), 
                linear-gradient(60deg, rgba(255,255,255,0.1) 25%, transparent 25.5%, transparent 75%, rgba(255,255,255,0.1) 75%, rgba(255,255,255,0.1));
            background-size: 80px 140px;
            background-position: 0 0, 0 0, 40px 70px, 40px 70px, 0 0, 40px 70px;
            padding: 30px 20px 80px 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
        }
        
        .apk-vector-header::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 40px;
            background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.2));
        }

        .apk-top-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            position: relative;
            z-index: 10;
        }
        
        .apk-top-logo img {
            width: 35px; height: 35px;
            background: #fff;
            padding: 4px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .apk-avatar-top {
            width: 45px; height: 45px;
            border-radius: 15px;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.5);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: relative;
            z-index: 10;
        }

        /* Overlapping Content Area */
        .apk-main-board {
            background: #fff;
            border-radius: 30px 30px 0 0;
            margin-top: -50px;
            padding: 30px 20px;
            padding-bottom: 135px !important; /* Space for bottom nav, ensure it overrides inline styles */
            position: relative;
            z-index: 20;
            flex: 1; /* Make it fill the remaining height */
            box-shadow: 0 -10px 25px rgba(0,0,0,0.1);
        }

        /* Glassmorphism Bottom Nav */
        .apk-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            height: 85px;
            padding-bottom: 15px;
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 9999;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.03);
            border-radius: 24px 24px 0 0;
        }

        /* Hide Bottom Nav when Keyboard is Open (small screen height) */
        @media (max-height: 550px) {
            .apk-bottom-nav {
                display: none !important;
            }
        }
        .apk-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            text-decoration: none;
            color: #94a3b8;
            flex: 1;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .apk-nav-item i {
            width: 22px; height: 22px;
            transition: all 0.3s;
        }
        .apk-nav-item span {
            font-size: 0.65rem;
            font-weight: 700;
        }
        .apk-nav-item.active {
            color: var(--apk-primary);
        }
        .apk-nav-item.active i {
            transform: scale(1.15) translateY(-2px);
            filter: drop-shadow(0 4px 6px rgba(16, 185, 129, 0.4));
        }
        .apk-nav-item:active {
            transform: scale(0.9);
        }

        /* FAB Style for Center Action (Aplikasi) */
        .apk-nav-fab-container {
            position: relative;
            flex: 1;
            display: flex;
            justify-content: center;
            height: 100%;
            align-items: flex-end;
        }
        .apk-nav-fab {
            position: relative;
            bottom: 22px; /* Jarak dari bawah navbar (70px). Jika tinggi FAB 58px + 22px = 80px, berarti cuma nonjol 10px (sekitar 17%) */
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, var(--apk-primary), #059669);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff !important;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            text-decoration: none;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 4px solid #fff; /* Create cutout effect */
            z-index: 10000;
        }
        .apk-nav-fab i {
            width: 24px; height: 24px;
        }
        .apk-nav-fab span {
            font-size: 0.55rem;
            font-weight: 800;
            margin-top: 2px;
            opacity: 0.9;
        }
        .apk-nav-fab:active {
            transform: scale(0.9);
        }

        /* Premium Header */
        .apk-header {
            padding: 20px 20px;
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .apk-header-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .apk-avatar {
            width: 40px; height: 40px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* Content Area */
        .apk-content {
            padding: 0 20px 20px 20px;
            flex: 1;
        }
    </style>
</head>
<body>

<div class="apk-container">
    <?php echo $content ?? ''; ?>
    
    <?php if(!isset($hideNav) || !$hideNav): ?>
    <div class="apk-bottom-nav">
        <a href="/apk/<?= isset($_SESSION['guru_id']) ? 'guru' : 'siswa' ?>/dashboard" class="apk-nav-item <?= ($activeNav ?? '') == 'home' ? 'active' : '' ?>">
            <i data-lucide="home"></i>
            <span>Beranda</span>
        </a>
        <?php if(isset($_SESSION['guru_id'])): ?>
        <a href="/apk/guru/jurnal" class="apk-nav-item <?= ($activeNav ?? '') == 'jurnal' ? 'active' : '' ?>">
            <i data-lucide="book-open"></i>
            <span>Jurnal</span>
        </a>
        <?php endif; ?>
        
        <div class="apk-nav-fab-container">
            <a href="/apk/aplikasi-saya" class="apk-nav-fab <?= ($activeNav ?? '') == 'aplikasi' ? 'active' : '' ?>">
                <i data-lucide="layout-grid"></i>
                <span>Aplikasi</span>
            </a>
        </div>

        <a href="/apk/berkas" class="apk-nav-item <?= ($activeNav ?? '') == 'berkas' ? 'active' : '' ?>">
            <i data-lucide="folder"></i>
            <span>Berkas</span>
        </a>
        <a href="/apk/profile" class="apk-nav-item <?= ($activeNav ?? '') == 'profile' ? 'active' : '' ?>">
            <i data-lucide="user"></i>
            <span>Profil</span>
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>
