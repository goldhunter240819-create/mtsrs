<?php
use App\Core\Helper;
use App\Core\Branding;
use App\Core\AcademicYear;

if (!isset($title)) $title = "MTs Roudlotus Sholihin System";
if (!isset($activeMenu)) $activeMenu = 'portal';

$institusi = Branding::getInstitusi();
$currYear = AcademicYear::current();
$allYears = AcademicYear::getAll();

$userNama = $_SESSION['nama'] ?? 'Pengguna';
$userRole = $_SESSION['role_name'] ?? 'User';
$userInitial = strtoupper(substr($userNama, 0, 2));

$isPortalHub = ($activeMenu === 'portal');
$isSiakad = (strpos($activeMenu, 'siakad') === 0);
$isKeuangan = (strpos($activeMenu, 'keuangan') === 0);
$isAbsen = (strpos($activeMenu, 'absen') === 0);

$logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Tab Favicon -->
    <link rel="shortcut icon" href="<?php echo $logoSrc; ?>?v=<?php echo time(); ?>" type="image/png">
    <link rel="icon" href="<?php echo $logoSrc; ?>?v=<?php echo time(); ?>" type="image/png">
    
    <!-- Zamrud Style Base (MTs RS Royal Sapphire Theme) -->
    <link rel="stylesheet" href="<?php echo Helper::url('/assets/css/z-style.css'); ?>?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Widget Tahun Ajaran ala Zamrud */
        .academic-year-widget {
            margin: 0.5rem 1.25rem 1rem;
            padding: 10px 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
        }
        .academic-year-widget label {
            display: flex; align-items: center; gap: 6px;
            font-size: 0.65rem; font-weight: 800; color: rgba(255,255,255,0.6);
            text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .academic-year-select {
            width: 100%; padding: 6px 10px; border-radius: 8px;
            background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15);
            color: #fff; font-size: 0.82rem; font-weight: 700; outline: none;
            cursor: pointer; appearance: none;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .academic-year-select option { background: #0f172a; color: #fff; }
        .academic-year-select:focus { border-color: var(--z-primary); }
    </style>
</head>
<body>
<?php echo $content ?? ""; ?>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
    function toggleDropdown(el) {
        let parent = el.parentElement;
        let wasOpen = parent.classList.contains('open');
        document.querySelectorAll('.z-nav-dropdown').forEach(d => {
            d.classList.remove('open');
            d.querySelector('.z-nav-dropdown-toggle').classList.remove('active');
        });
        if(!wasOpen) {
            parent.classList.add('open');
            el.classList.add('active');
        }
    }
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('open');
    }
</script>
</body></html>