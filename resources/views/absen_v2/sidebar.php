<?php
$_s_uri = $_SERVER['REQUEST_URI'];
?>
<aside class="z-sidebar" id="zSidebar">
    <!-- Brand -->
    <div class="z-sidebar-logo">
        <div style="width:32px; height:32px; border-radius:8px; background:linear-gradient(135deg, #10b981 0%, #059669 100%); display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(16,185,129,0.3); flex-shrink: 0;">
            <i data-lucide="qr-code" style="color: white; width: 20px; height: 20px;"></i>
        </div>
        <div style="overflow: hidden; white-space: nowrap;">
            <div class="logo-text">Absen V2</div>
            <div class="logo-sub" style="color:#10b981;">Portal Admin</div>
        </div>
    </div>

    <!-- Nav -->
    <div class="z-sidebar-scroll">
        <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>
        
        <div class="z-nav-cat">MENU UTAMA</div>
        
        <a href="/absen" class="z-nav-item <?php echo ($_s_uri == '/absen' || $_s_uri == '/absen/') ? 'active' : ''; ?>">
            <i data-lucide="layout-dashboard"></i> Dashboard Absensi
        </a>
        


        <!-- ── SISWA ── -->
        <div class="z-nav-cat">ABSENSI SISWA</div>
        <?php $isOpenSiswa = in_array($_s_uri, ['/absen/rekap-harian', '/absen/rekap-detail', '/absen/rekap-bulanan', '/absen/ketidakhadiran', '/absen/statistik-siswa']); ?>
        <div class="z-nav-dropdown <?php echo $isOpenSiswa ? 'open' : ''; ?>">
            <div class="z-nav-dropdown-toggle <?php echo $isOpenSiswa ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                <div class="dt-left"><i data-lucide="users"></i> Rekap Absensi</div>
                <i data-lucide="chevron-down" class="dt-icon"></i>
            </div>
            <div class="z-nav-dropdown-menu">
                <a href="/absen/rekap-harian" class="z-nav-item <?php echo strpos($_s_uri, 'rekap-harian') !== false && strpos($_s_uri, 'guru') === false ? 'active' : ''; ?>">Rekap Harian Siswa</a>
                <a href="/absen/rekap-detail" class="z-nav-item <?php echo strpos($_s_uri, 'rekap-detail') !== false ? 'active' : ''; ?>">Rekap Detail (Timeline)</a>
                <a href="/absen/rekap-bulanan" class="z-nav-item <?php echo strpos($_s_uri, 'rekap-bulanan') !== false && strpos($_s_uri, 'guru') === false ? 'active' : ''; ?>">Rekap Bulanan Siswa</a>
                <a href="/absen/ketidakhadiran" class="z-nav-item <?php echo strpos($_s_uri, 'ketidakhadiran') !== false ? 'active' : ''; ?>">Data Ketidakhadiran</a>
                <a href="/absen/statistik-siswa" class="z-nav-item <?php echo strpos($_s_uri, 'statistik-siswa') !== false ? 'active' : ''; ?>">Statistik Kehadiran</a>
            </div>
        </div>

        <!-- ── GURU ── -->
        <div class="z-nav-cat" style="color:#3b82f6;">ABSENSI GURU</div>
        <?php
            $isOpenGuru = in_array($_s_uri, ['/absen/rekap-harian-guru', '/absen/rekap-bulanan-guru', '/absen/input-absen-guru', '/absen/scanner-guru']);
        ?>
        <div class="z-nav-dropdown <?php echo $isOpenGuru ? 'open' : ''; ?>">
            <div class="z-nav-dropdown-toggle <?php echo $isOpenGuru ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                <div class="dt-left"><i data-lucide="briefcase"></i> Rekap Absensi Guru</div>
                <i data-lucide="chevron-down" class="dt-icon"></i>
            </div>
            <div class="z-nav-dropdown-menu">

                <a href="/absen/rekap-harian-guru" class="z-nav-item <?php echo strpos($_s_uri, 'rekap-harian-guru') !== false ? 'active' : ''; ?>">Rekap Harian Guru</a>
                <a href="/absen/rekap-bulanan-guru" class="z-nav-item <?php echo strpos($_s_uri, 'rekap-bulanan-guru') !== false ? 'active' : ''; ?>">Rekap Bulanan Guru</a>
                <a href="/absen/input-absen-guru" class="z-nav-item <?php echo strpos($_s_uri, 'input-absen-guru') !== false ? 'active' : ''; ?>">Input Manual Guru</a>
            </div>
        </div>

        <!-- ── PENGATURAN ── -->
        <div class="z-nav-cat">PENGATURAN MESIN</div>
        <?php $isOpenSetting = in_array($_s_uri, ['/absen/pengaturan', '/absen/pengaturan-guru']); ?>
        <div class="z-nav-dropdown <?php echo $isOpenSetting ? 'open' : ''; ?>">
            <div class="z-nav-dropdown-toggle <?php echo $isOpenSetting ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                <div class="dt-left"><i data-lucide="settings"></i> Konfigurasi Sistem</div>
                <i data-lucide="chevron-down" class="dt-icon"></i>
            </div>
            <div class="z-nav-dropdown-menu">
                <a href="/absen/pengaturan" class="z-nav-item <?php echo strpos($_s_uri, '/absen/pengaturan') !== false && strpos($_s_uri, 'pengaturan-guru') === false ? 'active' : ''; ?>">Konfigurasi Jam</a>
                <a href="/absen/pengaturan-guru" class="z-nav-item <?php echo strpos($_s_uri, 'pengaturan-guru') !== false ? 'active' : ''; ?>">Custom Jam Guru</a>
                <a href="/siakad/siswa/cetak-kartu" class="z-nav-item" style="display:none;">Cetak Kartu QR</a>
            </div>
        </div>
    </div>
    
    <div class="z-sidebar-foot">
        <a href="/portal" class="btn btn-outline" style="width:100%; justify-content:center; border:1px solid rgba(255,255,255,0.1); color:#ef4444; background:rgba(239,68,68,0.05);">
            <i data-lucide="log-out"></i> Ke Portal Admin
        </a>
    </div>
</aside>
