<?php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/layout.php';
$c = file_get_contents($file);

$start = '<?php if ($isKeuangan): ?>';
$end = '<?php endif; ?>';
$start_pos = strpos($c, $start);
$end_pos = strpos($c, $end, $start_pos);

if ($start_pos !== false && $end_pos !== false) {
    $end_pos += strlen($end);
    
    $new_block = <<<PHP
                <?php if (\$isKeuangan): ?>
                    <div class="z-nav-cat">KEUANGAN</div>
                    <a href="<?php echo Helper::url('/keuangan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'keuangan_finance_dashboard') !== false || strpos(\$activeMenu, 'keuangan_dashboard') !== false ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Overview
                    </a>
                    
                    <!-- Transaksi -->
                    <?php \$isOpenTransaksi = strpos(\$activeMenu, 'komite_pemasukan') !== false || strpos(\$activeMenu, 'komite_pengeluaran') !== false; ?>
                    <div class="z-nav-dropdown <?php echo \$isOpenTransaksi ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo \$isOpenTransaksi ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="repeat"></i> Transaksi</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/keuangan/komite/pemasukan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_pemasukan') !== false ? 'active' : ''; ?>"><i data-lucide="arrow-down-left"></i> Pemasukan</a>
                            <a href="<?php echo Helper::url('/keuangan/komite/pengeluaran'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_pengeluaran') !== false ? 'active' : ''; ?>"><i data-lucide="arrow-up-right"></i> Pengeluaran</a>
                        </div>
                    </div>

                    <!-- Master Data -->
                    <?php \$isOpenMaster = strpos(\$activeMenu, 'komite_kategori') !== false || strpos(\$activeMenu, 'komite_jenis') !== false; ?>
                    <div class="z-nav-dropdown <?php echo \$isOpenMaster ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo \$isOpenMaster ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="database"></i> Master Data</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/keuangan/komite/kategori'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_kategori') !== false ? 'active' : ''; ?>"><i data-lucide="list"></i> Master Kategori</a>
                            <a href="<?php echo Helper::url('/keuangan/komite/jenis'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_jenis') !== false ? 'active' : ''; ?>"><i data-lucide="tag"></i> Jenis Tagihan</a>
                        </div>
                    </div>

                    <!-- Keuangan Siswa -->
                    <?php \$isOpenSiswa = strpos(\$activeMenu, 'keuangan_tagihan') !== false || strpos(\$activeMenu, 'keuangan_finance_tagihan') !== false || strpos(\$activeMenu, 'komite_rekap') !== false || strpos(\$activeMenu, 'komite_pembayaran_siswa') !== false; ?>
                    <div class="z-nav-dropdown <?php echo \$isOpenSiswa ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo \$isOpenSiswa ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="users"></i> Keuangan Siswa</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/siakad/siswa'); ?>" class="z-nav-item"><i data-lucide="user-check"></i> Pemb. Siswa</a>
                            <a href="<?php echo Helper::url('/keuangan/tagihan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'keuangan_tagihan') !== false || strpos(\$activeMenu, 'keuangan_finance_tagihan') !== false ? 'active' : ''; ?>"><i data-lucide="receipt"></i> Tagihan</a>
                            <a href="<?php echo Helper::url('/keuangan/komite/rekap'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_rekap') !== false ? 'active' : ''; ?>"><i data-lucide="pie-chart"></i> Rekap Tagihan</a>
                        </div>
                    </div>

                    <a href="<?php echo Helper::url('/keuangan/komite/laporan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_laporan') !== false ? 'active' : ''; ?>">
                        <i data-lucide="file-text"></i> Laporan
                    </a>

                    <a href="<?php echo Helper::url('/keuangan/akses'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'keuangan_akses') !== false || strpos(\$activeMenu, 'keuangan_finance_akses') !== false ? 'active' : ''; ?>">
                        <i data-lucide="shield-check"></i> Akses Bendahara
                    </a>
                <?php endif; ?>
PHP;

    $c = substr_replace($c, $new_block, $start_pos, $end_pos - $start_pos);
    file_put_contents($file, $c);
    echo "Updated layout.php with exact mismifhda Keuangan sidebar match.\n";
} else {
    echo "Could not find \$isKeuangan block.\n";
}
