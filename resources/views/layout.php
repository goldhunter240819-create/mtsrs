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
$userFoto = $_SESSION['foto'] ?? '';

$isPortalHub = ($activeMenu === 'portal');
$isSiakad = (strpos($activeMenu, 'siakad') === 0);
$isKeuangan = (strpos($activeMenu, 'keuangan') === 0 && strpos($activeMenu, 'keuangan_tabungan') === false && strpos($activeMenu, 'keuangan_bos') === false);
$isTabungan = (strpos($activeMenu, 'tabungan') === 0 || strpos($activeMenu, 'keuangan_tabungan') === 0);
$isBos = (strpos($activeMenu, 'bos') === 0 || strpos($activeMenu, 'keuangan_bos') === 0);
$isAbsen = (strpos($activeMenu, 'absen') === 0);
$isBk = (strpos($activeMenu, 'bk') === 0);
$isPengaturanApk = (strpos($activeMenu, 'pengaturan_apk') === 0);
$isManajemenBerkas = (strpos($activeMenu, 'manajemen_berkas') === 0);
$isKurikulum = (strpos($activeMenu, 'kurikulum') === 0);
$isAdminPanel = (strpos($activeMenu, 'admin_') === 0);
$isDev = (strpos($activeMenu, 'dev') === 0);
$isGuruWeb = (strpos($activeMenu, 'guru_') === 0);
$isSiswaWeb = (strpos($activeMenu, 'siswa_') === 0);
$isEvoting = (strpos($activeMenu, 'evoting_') === 0);

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<?php if ($isPortalHub): ?>
    <!-- PORTAL HUB LAYOUT (NO SIDEBAR) -->
    <div style="max-width: 1300px; margin: 0 auto; width: 100%; padding: 1.5rem;">
        <header class="z-header" style="position: static; margin-bottom: 2rem;">
            <div class="z-header-left">
                <img src="<?php echo $logoSrc; ?>" alt="Logo" style="width:36px; height:36px; object-fit:contain; border-radius:10px; background:#fff; padding:3px; box-shadow:0 4px 12px rgba(37,99,235,0.3);">
                <div>
                    <div class="z-header-title">MTs Roudlotus Sholihin</div>
                    <div style="font-size:0.65rem; color:var(--z-muted); font-weight:700;">Sistem Platform Digital</div>
                </div>
            </div>
            <div class="z-header-user">
                <div style="text-align: right; margin-right: 12px; display: none;">
                    <div class="name"><?php echo htmlspecialchars($userNama); ?></div>
                    <div class="role"><?php echo htmlspecialchars($userRole); ?></div>
                </div>
                <?php if (!empty($userFoto)): ?>
                    <img src="<?php echo Helper::url('/public/uploads/profil/' . htmlspecialchars($userFoto)); ?>" class="z-avatar" style="object-fit: cover;" alt="Profil">
                <?php else: ?>
                    <div class="z-avatar"><?php echo htmlspecialchars($userInitial); ?></div>
                <?php endif; ?>
                <a href="<?php echo Helper::url('/logout'); ?>" class="btn btn-outline btn-sm" style="margin-left:8px;" title="Keluar">
                    <i data-lucide="log-out"></i>
                </a>
            </div>
        </header>
        <?php echo $content ?? ''; ?>
    </div>

<?php else: ?>
    <!-- DASHBOARD LAYOUT (WITH SIDEBAR) -->
    <div class="z-wrap">
        
        <div class="z-overlay" id="mobileOverlay" onclick="toggleMobileMenu()"></div>

        <!-- SIDEBAR -->
        <aside class="z-sidebar" id="zSidebar">
            <div class="z-sidebar-logo">
                <img src="<?php echo $logoSrc; ?>" alt="Logo" style="width:32px; height:32px; object-fit:contain; border-radius:8px; background:#fff; padding:2px; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
                <div>
                    <div class="logo-text">MTs Roudlotus Sholihin</div>
                    <div class="logo-sub">Platform Digital</div>
                </div>
            </div>

            <div class="z-sidebar-scroll">
                
                <!-- Widget Tahun Ajaran -->
                <div class="academic-year-widget">
                    <label><i data-lucide="calendar" style="width:12px;height:12px;"></i> TAHUN AJARAN AKTIF</label>
                    <select class="academic-year-select" onchange="if(this.value) window.location.href='<?php echo Helper::url('/siakad/tahun-ajaran/activate/'); ?>' + this.value">
                        <?php foreach($allYears as $y): ?>
                            <option value="<?php echo $y['id']; ?>" <?php echo ($currYear['id'] ?? 1) == $y['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($y['name']); ?> <?php echo $y['is_active'] ? ' (AKTIF)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($isGuruWeb): ?>
                    <div class="z-nav-cat">PORTAL GURU</div>
                    <a href="<?php echo Helper::url('/guru'); ?>" class="z-nav-item <?php echo $activeMenu === 'guru_dashboard' ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard Guru
                    </a>
                    <a href="<?php echo Helper::url('/guru/jadwal'); ?>" class="z-nav-item <?php echo $activeMenu === 'guru_jadwal' ? 'active' : ''; ?>">
                        <i data-lucide="calendar-days"></i> Jadwal Mengajar
                    </a>
                    <a href="<?php echo Helper::url('/guru/jurnal'); ?>" class="z-nav-item <?php echo $activeMenu === 'guru_jurnal' ? 'active' : ''; ?>">
                        <i data-lucide="file-signature"></i> Jurnal & Absensi
                    </a>
                    <a href="<?php echo Helper::url('/guru/nilai-harian'); ?>" class="z-nav-item <?php echo $activeMenu === 'guru_nilai_harian' ? 'active' : ''; ?>">
                        <i data-lucide="file-check-2"></i> Nilai Harian
                    </a>
                    <a href="<?php echo Helper::url('/guru/berkas'); ?>" class="z-nav-item <?php echo $activeMenu === 'guru_berkas' ? 'active' : ''; ?>">
                        <i data-lucide="folder"></i> Arsip Berkas
                    </a>
                <?php elseif ($isSiswaWeb): ?>
                    <div class="z-nav-cat">PORTAL SISWA</div>
                    <a href="<?php echo Helper::url('/siswa'); ?>" class="z-nav-item <?php echo $activeMenu === 'siswa_dashboard' ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard Siswa
                    </a>
                <?php elseif ($isSiakad): ?>
                    <div class="z-nav-cat">SIAKAD AKADEMIK</div>
                    <a href="<?php echo Helper::url('/siakad'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_dashboard' ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard SIAKAD
                    </a>

                    <!-- Dropdown: Data Master -->
                    <?php $isOpenMaster = in_array($activeMenu, ['siakad_institusi', 'siakad_mapel', 'siakad_kelas', 'siakad_ekstra']); ?>
                    <div class="z-nav-dropdown <?php echo $isOpenMaster ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenMaster ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="database"></i> Data Master</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/siakad/institusi'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_institusi' ? 'active' : ''; ?>"><i data-lucide="building"></i> Profil Institusi</a>
                            <a href="<?php echo Helper::url('/siakad/mapel'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_mapel' ? 'active' : ''; ?>"><i data-lucide="book-open"></i> Mata Pelajaran</a>
                            <a href="<?php echo Helper::url('/siakad/kelas'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_kelas' ? 'active' : ''; ?>"><i data-lucide="door-open"></i> Data Rombel Kelas</a>
                            <a href="<?php echo Helper::url('/siakad/ekstra'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_ekstra' ? 'active' : ''; ?>"><i data-lucide="activity"></i> Ekstrakurikuler</a>
                            <a href="<?php echo Helper::url('/siakad/kop-surat'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_kop_surat' ? 'active' : ''; ?>"><i data-lucide="mail"></i> Kop Surat</a>
                        </div>
                    </div>

                    <!-- Dropdown: Manajemen Guru -->
                    <?php $isOpenGuru = in_array($activeMenu, ['siakad_guru', 'siakad_guru_tugas', 'siakad_guru_mengajar', 'siakad_jurnal_global']); ?>
                    <div class="z-nav-dropdown <?php echo $isOpenGuru ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenGuru ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="users"></i> Manajemen Guru</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/siakad/guru'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_guru' ? 'active' : ''; ?>"><i data-lucide="user-check"></i> Data Guru</a>
                            <a href="<?php echo Helper::url('/siakad/guru/tugas'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_guru_tugas' ? 'active' : ''; ?>"><i data-lucide="briefcase"></i> Penugasan Tambahan</a>
                            <a href="<?php echo Helper::url('/siakad/guru/mengajar'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_guru_mengajar' ? 'active' : ''; ?>"><i data-lucide="book"></i> Penugasan Mengajar</a>
                            <a href="<?php echo Helper::url('/siakad/jurnal-global'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_jurnal_global' ? 'active' : ''; ?>"><i data-lucide="book-marked"></i> Jurnal Global</a>
                        </div>
                    </div>

                    <!-- Dropdown: Manajemen Siswa -->
                    <?php $isOpenSiswa = in_array($activeMenu, ['siakad_siswa', 'siakad_siswa_naik', 'siakad_alumni', 'siakad_mutasi']); ?>
                    <div class="z-nav-dropdown <?php echo $isOpenSiswa ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenSiswa ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="user-square"></i> Manajemen Siswa</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/siakad/siswa'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_siswa' ? 'active' : ''; ?>"><i data-lucide="users"></i> Data Siswa</a>
                            <a href="<?php echo Helper::url('/siakad/siswa/naik-kelas'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_siswa_naik' ? 'active' : ''; ?>"><i data-lucide="trending-up"></i> Naik Kelas</a>
                            <a href="<?php echo Helper::url('/siakad/siswa/alumni'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_alumni' ? 'active' : ''; ?>"><i data-lucide="graduation-cap"></i> Data Alumni</a>
                            <a href="<?php echo Helper::url('/siakad/mutasi'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_mutasi' ? 'active' : ''; ?>"><i data-lucide="arrow-right-left"></i> Mutasi Siswa</a>
                        </div>
                    </div>

                    <!-- Dropdown: Akademik & Nilai -->
                    <?php $isOpenNilai = in_array($activeMenu, ['siakad_kalender', 'siakad_nilai', 'siakad_nilai_input', 'siakad_nilai_template', 'siakad_jadwal', 'siakad_raport', 'siakad_monitoring_nilai']); ?>
                    <div class="z-nav-dropdown <?php echo $isOpenNilai ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenNilai ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="book-open-check"></i> Akademik & Nilai</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/siakad/kalender'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_kalender' ? 'active' : ''; ?>"><i data-lucide="calendar"></i> Kalender Pendidikan</a>
                            <a href="<?php echo Helper::url('/siakad/jadwal'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_jadwal' ? 'active' : ''; ?>"><i data-lucide="calendar-clock"></i> Jadwal Pelajaran</a>
                            <a href="<?php echo Helper::url('/siakad/nilai/template'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_nilai_template' ? 'active' : ''; ?>"><i data-lucide="file-spreadsheet"></i> Template Nilai</a>
                            <a href="<?php echo Helper::url('/siakad/monitoring-nilai'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_monitoring_nilai' ? 'active' : ''; ?>"><i data-lucide="line-chart"></i> Monitoring Nilai</a>
                            
                            <?php if(false): // Disembunyikan sementara ?>
                            <a href="<?php echo Helper::url('/siakad/nilai'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_nilai' ? 'active' : ''; ?>"><i data-lucide="award"></i> Nilai Admin</a>
                            <a href="<?php echo Helper::url('/siakad/nilai/input'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_nilai_input' ? 'active' : ''; ?>"><i data-lucide="edit-3"></i> Input Nilai Guru</a>
                            <a href="<?php echo Helper::url('/siakad/raport'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_raport' ? 'active' : ''; ?>"><i data-lucide="file-text"></i> Leger & Rapor</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Dropdown: Lain-lain -->
                    <?php $isOpenLain = in_array($activeMenu, ['siakad_rapat', 'siakad_broadcast']); ?>
                    <div class="z-nav-dropdown <?php echo $isOpenLain ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenLain ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="folder-open"></i> Lain-lain</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/siakad/lain-lain/rapat'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_rapat' ? 'active' : ''; ?>"><i data-lucide="users"></i> Presensi Rapat</a>
                            <a href="<?php echo Helper::url('/siakad/lain-lain/broadcast'); ?>" class="z-nav-item <?php echo $activeMenu === 'siakad_broadcast' ? 'active' : ''; ?>"><i data-lucide="megaphone"></i> Broadcast Pesan</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($isPengaturanApk): ?>
                    <div class="z-nav-cat">PENGATURAN APK</div>
                    <a href="<?php echo Helper::url('/pengaturan-apk'); ?>" class="z-nav-item <?php echo $activeMenu === 'pengaturan_apk_dashboard' ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                    <a href="<?php echo Helper::url('/pengaturan-apk/navbar'); ?>" class="z-nav-item <?php echo $activeMenu === 'pengaturan_apk_navbar' ? 'active' : ''; ?>">
                        <i data-lucide="navigation"></i> Pengaturan Nav Bar
                    </a>
                    <a href="<?php echo Helper::url('/pengaturan-apk/aplikasi'); ?>" class="z-nav-item <?php echo $activeMenu === 'pengaturan_apk_aplikasi' ? 'active' : ''; ?>">
                        <i data-lucide="settings"></i> Pengaturan Aplikasi
                    </a>
                <?php endif; ?>

                <?php if ($isEvoting): ?>
                    <div class="z-nav-cat">E-VOTING ADMIN</div>
                    <a href="<?php echo Helper::url('/evoting/admin'); ?>" class="z-nav-item <?php echo $activeMenu === 'evoting_admin_dashboard' ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard & Event
                    </a>
                    <a href="<?php echo Helper::url('/evoting/admin/candidates'); ?>" class="z-nav-item <?php echo $activeMenu === 'evoting_admin_candidates' ? 'active' : ''; ?>">
                        <i data-lucide="users"></i> Kelola Kandidat
                    </a>
                    <a href="<?php echo Helper::url('/evoting/admin/voters'); ?>" class="z-nav-item <?php echo $activeMenu === 'evoting_admin_voters' ? 'active' : ''; ?>">
                        <i data-lucide="key"></i> Token Pemilih
                    </a>
                    <a href="<?php echo Helper::url('/evoting/admin/live'); ?>" class="z-nav-item <?php echo $activeMenu === 'evoting_admin_live' ? 'active' : ''; ?>">
                        <i data-lucide="bar-chart-2"></i> Live Count
                    </a>
                <?php endif; ?>


                <?php if ($isManajemenBerkas): ?>
                    <div class="z-nav-cat">MANAJEMEN BERKAS</div>
                    <a href="<?php echo Helper::url('/manajemen-berkas'); ?>" class="z-nav-item <?php echo ($activeMenu === 'manajemen_berkas_dashboard') ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                    <a href="<?php echo Helper::url('/manajemen-berkas/pribadi'); ?>" class="z-nav-item <?php echo ($activeMenu === 'manajemen_berkas_pribadi') ? 'active' : ''; ?>">
                        <i data-lucide="user"></i> Berkas Pribadi
                    </a>
                    <a href="<?php echo Helper::url('/manajemen-berkas/perangkat'); ?>" class="z-nav-item <?php echo ($activeMenu === 'manajemen_berkas_perangkat') ? 'active' : ''; ?>">
                        <i data-lucide="book-open"></i> Perangkat Pembelajaran
                    </a>
                <?php endif; ?>

                <?php if ($isKurikulum): ?>
                    <div class="z-nav-cat">WAKA KURIKULUM</div>
                    <a href="<?php echo Helper::url('/kurikulum'); ?>" class="z-nav-item <?php echo ($activeMenu === 'kurikulum_dashboard' || $activeMenu === 'kurikulum') ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                    
                    <!-- Dropdown: Akademik & Monitoring -->
                    <?php $isOpenMonitoring = in_array($activeMenu, ['kurikulum_jadwal', 'kurikulum_kkm', 'kurikulum_rekap_ketuntasan', 'kurikulum_monitoring_jurnal', 'kurikulum_monitoring_nilai']); ?>
                    <div class="z-nav-dropdown <?php echo $isOpenMonitoring ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenMonitoring ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="activity"></i> Akademik & Monitoring</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/kurikulum/jadwal-pelajaran'); ?>" class="z-nav-item <?php echo ($activeMenu === 'kurikulum_jadwal') ? 'active' : ''; ?>">Monitoring Jadwal</a>
                            <a href="<?php echo Helper::url('/kurikulum/monitoring-jurnal'); ?>" class="z-nav-item <?php echo ($activeMenu === 'kurikulum_monitoring_jurnal') ? 'active' : ''; ?>">Monitoring Jurnal</a>
                            <a href="<?php echo Helper::url('/kurikulum/monitoring-nilai'); ?>" class="z-nav-item <?php echo ($activeMenu === 'kurikulum_monitoring_nilai') ? 'active' : ''; ?>">Monitoring Nilai</a>
                            <a href="<?php echo Helper::url('/kurikulum/monitoring-kkm'); ?>" class="z-nav-item <?php echo ($activeMenu === 'kurikulum_kkm') ? 'active' : ''; ?>">Pengaturan KKM</a>
                            <a href="<?php echo Helper::url('/kurikulum/rekap-ketuntasan'); ?>" class="z-nav-item <?php echo ($activeMenu === 'kurikulum_rekap_ketuntasan') ? 'active' : ''; ?>">Rekap Ketuntasan</a>
                        </div>
                    </div>

                    <!-- Dropdown: Supervisi & Evaluasi -->
                    <?php $isOpenSupervisi = in_array($activeMenu, ['kurikulum_supervisi_administrasi', 'kurikulum_supervisi_kelas', 'kurikulum_supervisi_penilaian']); ?>
                    <div class="z-nav-dropdown <?php echo $isOpenSupervisi ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenSupervisi ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="clipboard-list"></i> Supervisi & Evaluasi</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/kurikulum/supervisi-administrasi'); ?>" class="z-nav-item <?php echo ($activeMenu === 'kurikulum_supervisi_administrasi') ? 'active' : ''; ?>">Supervisi Administrasi</a>
                            <a href="<?php echo Helper::url('/kurikulum/supervisi-kelas'); ?>" class="z-nav-item <?php echo ($activeMenu === 'kurikulum_supervisi_kelas') ? 'active' : ''; ?>">Supervisi Kelas</a>
                            <a href="<?php echo Helper::url('/kurikulum/supervisi-penilaian'); ?>" class="z-nav-item <?php echo ($activeMenu === 'kurikulum_supervisi_penilaian') ? 'active' : ''; ?>">Supervisi Penilaian</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($isBos): ?>
                    <div class="z-nav-cat">MANAJEMEN DANA BOS</div>
                    <a href="<?php echo Helper::url('/bos'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'bos_dashboard') !== false ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard BOS
                    </a>
                    
                    <?php $isOpenBosTx = in_array($activeMenu, ['bos_pemasukan', 'bos_pengeluaran', 'bos_laporan']); ?>
                    <div class="z-nav-dropdown <?php echo $isOpenBosTx ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenBosTx ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="wallet"></i> Transaksi & Laporan</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/bos/pemasukan'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'bos_pemasukan') !== false ? 'active' : ''; ?>">Pemasukan BOS</a>
                            <a href="<?php echo Helper::url('/bos/pengeluaran'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'bos_pengeluaran') !== false ? 'active' : ''; ?>">Pengeluaran BOS</a>
                            <a href="<?php echo Helper::url('/bos/laporan'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'bos_laporan') !== false ? 'active' : ''; ?>">Laporan BOS</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($isTabungan): ?>
                    <div class="z-nav-cat">TABUNGAN SISWA</div>
                    <a href="<?php echo Helper::url('/tabungan'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'tabungan_dashboard') !== false ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard Tabungan
                    </a>
                    
                    <?php $isOpenTabunganTx = in_array($activeMenu, ['tabungan_siswa', 'tabungan_kasir', 'tabungan_rekap']); ?>
                    <div class="z-nav-dropdown <?php echo $isOpenTabunganTx ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenTabunganTx ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="coins"></i> Kelola Tabungan</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/tabungan/siswa'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'tabungan_siswa') !== false ? 'active' : ''; ?>">Data Tabungan</a>
                            <a href="<?php echo Helper::url('/tabungan/kasir'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'tabungan_kasir') !== false ? 'active' : ''; ?>">Kasir Tabungan</a>
                            <a href="<?php echo Helper::url('/tabungan/rekap'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'tabungan_rekap') !== false ? 'active' : ''; ?>">Rekapitulasi</a>
                        </div>
                    </div>
                <?php endif; ?>

                                <?php if ($isKeuangan): ?>
                    <div class="z-nav-cat">KEUANGAN</div>
                    <a href="<?php echo Helper::url('/keuangan'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'keuangan_finance_dashboard') !== false || strpos($activeMenu, 'keuangan_dashboard') !== false ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Overview
                    </a>
                    
                    <!-- Transaksi -->
                    <?php $isOpenTransaksi = strpos($activeMenu, 'komite_pemasukan') !== false || strpos($activeMenu, 'komite_pengeluaran') !== false; ?>
                    <div class="z-nav-dropdown <?php echo $isOpenTransaksi ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenTransaksi ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="repeat"></i> Transaksi</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/keuangan/komite/pemasukan'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'komite_pemasukan') !== false ? 'active' : ''; ?>"><i data-lucide="arrow-down-left"></i> Pemasukan</a>
                            <a href="<?php echo Helper::url('/keuangan/komite/pengeluaran'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'komite_pengeluaran') !== false ? 'active' : ''; ?>"><i data-lucide="arrow-up-right"></i> Pengeluaran</a>
                        </div>
                    </div>

                    <!-- Master Data -->
                    <?php $isOpenMaster = strpos($activeMenu, 'komite_kategori') !== false || strpos($activeMenu, 'komite_jenis') !== false; ?>
                    <div class="z-nav-dropdown <?php echo $isOpenMaster ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenMaster ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="database"></i> Master Data</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/keuangan/komite/kategori'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'komite_kategori') !== false ? 'active' : ''; ?>"><i data-lucide="list"></i> Master Kategori</a>
                            <a href="<?php echo Helper::url('/keuangan/komite/jenis'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'komite_jenis') !== false ? 'active' : ''; ?>"><i data-lucide="tag"></i> Jenis Tagihan</a>
                        </div>
                    </div>

                    <!-- Keuangan Siswa -->
                    <?php $isOpenSiswa = strpos($activeMenu, 'keuangan_tagihan') !== false || strpos($activeMenu, 'keuangan_finance_tagihan') !== false || strpos($activeMenu, 'komite_rekap') !== false || strpos($activeMenu, 'komite_pembayaran_siswa') !== false || strpos($activeMenu, 'keuangan_kolektif') !== false; ?>
                    <div class="z-nav-dropdown <?php echo $isOpenSiswa ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo $isOpenSiswa ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="users"></i> Keuangan Siswa</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/keuangan/komite/pembayaran/0'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'komite_pembayaran_siswa') !== false ? 'active' : ''; ?>"><i data-lucide="user-check"></i> Pemb. Manual</a>
                            <a href="<?php echo Helper::url('/keuangan/kolektif'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'keuangan_kolektif') !== false && strpos($activeMenu, 'keuangan_kolektif_setting') === false ? 'active' : ''; ?>"><i data-lucide="layers"></i> Pemb. Kolektif</a>
                            <a href="<?php echo Helper::url('/keuangan/tagihan'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'keuangan_tagihan') !== false || strpos($activeMenu, 'keuangan_finance_tagihan') !== false ? 'active' : ''; ?>"><i data-lucide="receipt"></i> Tagihan</a>
                            <a href="<?php echo Helper::url('/keuangan/komite/rekap'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'komite_rekap') !== false ? 'active' : ''; ?>"><i data-lucide="pie-chart"></i> Rekap Tagihan</a>
                            <a href="<?php echo Helper::url('/keuangan/kolektif/setting'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'keuangan_kolektif_setting') !== false ? 'active' : ''; ?>"><i data-lucide="settings"></i> Aturan Kolektif</a>
                        </div>
                    </div>

                    <a href="<?php echo Helper::url('/keuangan/komite/laporan'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'komite_laporan') !== false ? 'active' : ''; ?>">
                        <i data-lucide="file-text"></i> Laporan
                    </a>

                    <a href="<?php echo Helper::url('/keuangan/akses'); ?>" class="z-nav-item <?php echo strpos($activeMenu, 'keuangan_akses') !== false || strpos($activeMenu, 'keuangan_finance_akses') !== false ? 'active' : ''; ?>">
                        <i data-lucide="shield-check"></i> Pengaturan & Akses
                    </a>
                <?php endif; ?>

                <?php if ($isAbsen): ?>
                    <div class="z-nav-cat">ABSENSI QR V2</div>
                    <a href="<?php echo Helper::url('/absen'); ?>" class="z-nav-item <?php echo $activeMenu === 'absen_dashboard' ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard Absensi
                    </a>

                    <a href="<?php echo Helper::url('/absen/rekap-harian'); ?>" class="z-nav-item <?php echo $activeMenu === 'absen_rekap_harian' ? 'active' : ''; ?>">
                        <i data-lucide="list-checks"></i> Rekap Harian
                    </a>
                    <a href="<?php echo Helper::url('/absen/rekap-bulanan'); ?>" class="z-nav-item <?php echo $activeMenu === 'absen_rekap_bulanan' ? 'active' : ''; ?>">
                        <i data-lucide="calendar"></i> Rekap Bulanan
                    </a>
                    <a href="<?php echo Helper::url('/absen/admin-izin-piket'); ?>" class="z-nav-item <?php echo $activeMenu === 'absen_izin_piket' ? 'active' : ''; ?>">
                        <i data-lucide="clipboard-check"></i> Izin Guru
                    </a>
                <?php endif; ?>

                <?php if ($isBk): ?>
                    <div class="z-nav-cat">E-BK & DISIPLIN</div>
                    <a href="<?php echo Helper::url('/bk'); ?>" class="z-nav-item <?php echo $activeMenu === 'bk_dashboard' ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard BK
                    </a>
                    <a href="<?php echo Helper::url('/bk/pelanggaran'); ?>" class="z-nav-item <?php echo $activeMenu === 'bk_pelanggaran' ? 'active' : ''; ?>">
                        <i data-lucide="clipboard-list"></i> Catat Poin Siswa
                    </a>
                    <a href="<?php echo Helper::url('/bk/kategori'); ?>" class="z-nav-item <?php echo $activeMenu === 'bk_kategori' ? 'active' : ''; ?>">
                        <i data-lucide="tags"></i> Kategori Poin
                    </a>
                    <a href="<?php echo Helper::url('/bk/laporan'); ?>" class="z-nav-item <?php echo $activeMenu === 'bk_laporan' ? 'active' : ''; ?>">
                        <i data-lucide="printer"></i> Cetak Laporan
                    </a>
                <?php endif; ?>

                <?php if ($isAdminPanel): ?>
                    <div class="z-nav-cat">ADMIN PANEL</div>
                    <a href="<?php echo Helper::url('/admin/portal-users'); ?>" class="z-nav-item <?php echo $activeMenu === 'admin_portal_users' ? 'active' : ''; ?>">
                        <i data-lucide="shield"></i> Akses Portal Guru
                    </a>
                    <a href="<?php echo Helper::url('/admin/profil'); ?>" class="z-nav-item <?php echo $activeMenu === 'admin_profil' ? 'active' : ''; ?>">
                        <i data-lucide="user"></i> Profil Admin
                    </a>
                <?php endif; ?>

                <?php if ($isDev): ?>
                    <div class="z-nav-cat">DEV PANEL BETA</div>
                    <a href="<?php echo Helper::url('/dev'); ?>" class="z-nav-item <?php echo $activeMenu === 'dev' ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                    <a href="<?php echo Helper::url('/dev/admins'); ?>" class="z-nav-item <?php echo $activeMenu === 'dev_admins' ? 'active' : ''; ?>">
                        <i data-lucide="users"></i> Akun Admin
                    </a>
                    <a href="<?php echo Helper::url('/dev/portal-access'); ?>" class="z-nav-item <?php echo $activeMenu === 'dev_portal_access' ? 'active' : ''; ?>">
                        <i data-lucide="toggle-right"></i> Akses Portal
                    </a>
                    <a href="<?php echo Helper::url('/dev/settings'); ?>" class="z-nav-item <?php echo $activeMenu === 'dev_settings' ? 'active' : ''; ?>">
                        <i data-lucide="blocks"></i> API & Integrasi
                    </a>
                    <a href="<?php echo Helper::url('/dev/cctv'); ?>" class="z-nav-item <?php echo $activeMenu === 'dev_cctv' ? 'active' : ''; ?>">
                        <i data-lucide="cctv"></i> CCTV Monitoring
                    </a>
                <?php endif; ?>

            </div>
            
            <div class="z-sidebar-foot">
                <?php 
                $showPortalBtn = true;
                if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) {
                    $__db = \App\Core\Database::connect('core');
                    $__stmt = $__db->prepare("SELECT portal_enabled FROM users WHERE id = ?");
                    $__stmt->execute([$_SESSION['user_id']]);
                    $showPortalBtn = (bool)$__stmt->fetchColumn();
                }
                ?>
                <?php if ($showPortalBtn): ?>
                <a href="<?php echo Helper::url('/portal'); ?>" class="btn btn-outline" style="width:100%; justify-content:center; border:1px solid rgba(255,255,255,0.1); color:#fff; background:rgba(255,255,255,0.05);">
                    <i data-lucide="arrow-left"></i> Ke Portal Hub
                </a>
                <?php endif; ?>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="z-main">
            <!-- FLOATING HEADER -->
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" onclick="toggleMobileMenu()">
                        <i data-lucide="menu"></i>
                    </button>
                    <div class="z-header-title">MTs Roudlotus Sholihin</div>
                    <span class="z-badge" style="display:none;">LIVE</span> <!-- Optional live badge -->
                </div>
                
                <div class="z-header-user">
                    <?php $showHeaderPortalBtn = $showPortalBtn; ?>
                    <?php if ($showHeaderPortalBtn): ?>
                    <a href="<?php echo Helper::url('/portal'); ?>" class="btn btn-outline btn-sm" style="display:none;" id="portalBtnDesktop">
                        <i data-lucide="layout-grid"></i> Portal Hub
                    </a>
                    <?php endif; ?>
                    <div class="z-user-info">
                        <div class="name"><?php echo htmlspecialchars($userNama); ?></div>
                        <div class="role"><?php echo htmlspecialchars($userRole); ?></div>
                    </div>
                    <?php if (!empty($userFoto)): ?>
                        <img src="<?php echo Helper::url('/public/uploads/profil/' . htmlspecialchars($userFoto)); ?>" class="z-avatar" style="object-fit: cover; width: 36px; height: 36px; border-radius: 50%;" alt="Profil">
                    <?php else: ?>
                        <div class="z-avatar"><?php echo htmlspecialchars($userInitial); ?></div>
                    <?php endif; ?>
                    <a href="<?php echo Helper::url('/logout'); ?>" class="btn btn-danger btn-sm" title="Logout" style="padding: 6px; border-radius: 10px;">
                        <i data-lucide="log-out"></i>
                    </a>
                </div>
            </header>
            <style>
                @media(min-width: 769px) { #portalBtnDesktop { display: inline-flex !important; } }
            </style>

            <div class="z-scroll">
                <?php echo $content ?? ''; ?>
            </div>
        </main>
    </div>
<?php endif; ?>
<!-- Global App Modal -->
<div id="globalAppModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(10px); z-index:9999; align-items:center; justify-content:center; padding:1.5rem; opacity:0; transition: opacity 0.3s ease;">
    <div class="floating-card" style="width:100%; max-width:400px; background:#ffffff; text-align:center; padding: 2rem; transform: translateY(20px); transition: transform 0.3s ease;" id="globalAppModalContent">
        <div id="gamIconContainer" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
            <i id="gamIcon" data-lucide="info" style="width: 32px; height: 32px;"></i>
        </div>
        <h3 id="gamTitle" style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.75rem;">Title</h3>
        <p id="gamMessage" style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem; line-height:1.5;">Message</p>
        <div id="gamButtons" style="display:flex; justify-content:center; gap: 10px;">
            <!-- Buttons injected here -->
        </div>
    </div>
</div>

<script>
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

    function toggleMobileMenu() {
        document.getElementById('zSidebar').classList.toggle('open');
        document.getElementById('mobileOverlay').classList.toggle('open');
    }

    /**
     * options = {
     *   title: 'string',
     *   message: 'string',
     *   type: 'info|warning|error|success',
     *   buttons: [ { text: 'string', class: 'btn btn-primary', href: 'url', onClick: function } ]
     * }
     */
    function showAppModal(options) {
        const modal = document.getElementById('globalAppModal');
        const content = document.getElementById('globalAppModalContent');
        
        document.getElementById('gamTitle').innerHTML = options.title || 'Informasi';
        document.getElementById('gamMessage').innerHTML = options.message || '';
        
        const iconCont = document.getElementById('gamIconContainer');
        const icon = document.getElementById('gamIcon');
        
        let type = options.type || 'info';
        if (type === 'error') {
            iconCont.style.background = '#fee2e2'; iconCont.style.color = '#ef4444';
            icon.setAttribute('data-lucide', 'alert-circle');
        } else if (type === 'warning') {
            iconCont.style.background = '#fef3c7'; iconCont.style.color = '#f59e0b';
            icon.setAttribute('data-lucide', 'alert-triangle');
        } else if (type === 'success') {
            iconCont.style.background = '#dcfce3'; iconCont.style.color = '#10b981';
            icon.setAttribute('data-lucide', 'check-circle');
        } else {
            iconCont.style.background = '#e0e7ff'; iconCont.style.color = '#4f46e5';
            icon.setAttribute('data-lucide', 'info');
        }

        const btnContainer = document.getElementById('gamButtons');
        btnContainer.innerHTML = '';
        if (options.buttons && options.buttons.length > 0) {
            options.buttons.forEach(b => {
                if (b.href) {
                    const a = document.createElement('a');
                    a.href = b.href;
                    a.className = b.class || 'btn btn-primary';
                    a.innerHTML = b.text;
                    a.style.textDecoration = 'none';
                    btnContainer.appendChild(a);
                } else {
                    const btn = document.createElement('button');
                    btn.className = b.class || 'btn btn-primary';
                    btn.innerHTML = b.text;
                    btn.onclick = () => {
                        if(b.onClick) b.onClick();
                        else closeAppModal();
                    };
                    btnContainer.appendChild(btn);
                }
            });
        } else {
            const btn = document.createElement('button');
            btn.className = 'btn btn-secondary';
            btn.innerHTML = 'Tutup';
            btn.onclick = closeAppModal;
            btnContainer.appendChild(btn);
        }

        if(typeof lucide !== 'undefined') lucide.createIcons();

        modal.style.display = 'flex';
        void modal.offsetWidth;
        modal.style.opacity = '1';
        content.style.transform = 'translateY(0)';
    }

    function closeAppModal() {
        const modal = document.getElementById('globalAppModal');
        const content = document.getElementById('globalAppModalContent');
        modal.style.opacity = '0';
        content.style.transform = 'translateY(20px)';
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    lucide.createIcons();
</script>
</body>
</html>
