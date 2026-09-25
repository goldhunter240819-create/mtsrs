<?php
$file = 'c:/xampp/htdocs/mtsrs/resources/views/layout.php';
$c = file_get_contents($file);

// 1. Remove all old dropdowns from layout.php
$c = preg_replace('/<\?php if \(\$isBos\): \?>.*?<\?php endif; \?>/s', '', $c);
$c = preg_replace('/<\?php if \(\$isTabungan\): \?>.*?<\?php endif; \?>/s', '', $c);
$c = preg_replace('/<\?php if \(\$isKeuangan\): \?>.*?<\?php endif; \?>/s', '', $c);

// 2. Insert new blocks
$new_blocks = <<<PHP
                <?php if (\$isBos): ?>
                    <div class="z-nav-cat">MANAJEMEN DANA BOS</div>
                    <a href="<?php echo Helper::url('/bos'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'bos_dashboard') !== false ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard BOS
                    </a>
                    <a href="<?php echo Helper::url('/bos/pemasukan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'bos_pemasukan') !== false ? 'active' : ''; ?>">
                        <i data-lucide="arrow-down-circle"></i> Pemasukan BOS
                    </a>
                    <a href="<?php echo Helper::url('/bos/pengeluaran'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'bos_pengeluaran') !== false ? 'active' : ''; ?>">
                        <i data-lucide="arrow-up-circle"></i> Pengeluaran BOS
                    </a>
                    <a href="<?php echo Helper::url('/bos/laporan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'bos_laporan') !== false ? 'active' : ''; ?>">
                        <i data-lucide="file-text"></i> Laporan BOS
                    </a>
                <?php endif; ?>

                <?php if (\$isTabungan): ?>
                    <div class="z-nav-cat">TABUNGAN SISWA</div>
                    <a href="<?php echo Helper::url('/tabungan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'tabungan_dashboard') !== false ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard Tabungan
                    </a>
                    <a href="<?php echo Helper::url('/tabungan/siswa'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'tabungan_siswa') !== false ? 'active' : ''; ?>">
                        <i data-lucide="users"></i> Data Tabungan
                    </a>
                    <a href="<?php echo Helper::url('/tabungan/kasir'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'tabungan_kasir') !== false ? 'active' : ''; ?>">
                        <i data-lucide="monitor"></i> Kasir Tabungan
                    </a>
                    <a href="<?php echo Helper::url('/tabungan/rekap'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'tabungan_rekap') !== false ? 'active' : ''; ?>">
                        <i data-lucide="clipboard-list"></i> Rekapitulasi
                    </a>
                <?php endif; ?>

                <?php if (\$isKeuangan): ?>
                    <div class="z-nav-cat">MODUL KEUANGAN</div>
                    <a href="<?php echo Helper::url('/keuangan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'keuangan_finance_dashboard') !== false || strpos(\$activeMenu, 'keuangan_dashboard') !== false ? 'active' : ''; ?>">
                        <i data-lucide="bar-chart-2"></i> Overview
                    </a>
                    
                    <a href="<?php echo Helper::url('/keuangan/tagihan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'keuangan_finance_tagihan') !== false || strpos(\$activeMenu, 'keuangan_tagihan') !== false ? 'active' : ''; ?>">
                        <i data-lucide="receipt"></i> Tagihan Siswa
                    </a>
                    
                    <a href="<?php echo Helper::url('/keuangan/gaji'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'keuangan_finance_gaji') !== false || strpos(\$activeMenu, 'keuangan_gaji') !== false ? 'active' : ''; ?>">
                        <i data-lucide="banknote"></i> Gaji & Insentif
                    </a>
                    
                    <a href="<?php echo Helper::url('/keuangan/laporan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'keuangan_finance_laporan') !== false || strpos(\$activeMenu, 'keuangan_laporan') !== false ? 'active' : ''; ?>">
                        <i data-lucide="book-open"></i> Laporan Kas
                    </a>

                    <!-- Dana Komite is inside Keuangan -->
                    <?php \$isOpenKomite = strpos(\$activeMenu, 'komite') !== false; ?>
                    <div class="z-nav-dropdown <?php echo \$isOpenKomite ? 'open' : ''; ?>">
                        <div class="z-nav-dropdown-toggle <?php echo \$isOpenKomite ? 'active' : ''; ?>" onclick="toggleDropdown(this)">
                            <div class="dt-left"><i data-lucide="users"></i> Dana Komite</div>
                            <i data-lucide="chevron-down" class="dt-icon"></i>
                        </div>
                        <div class="z-nav-dropdown-menu">
                            <a href="<?php echo Helper::url('/keuangan/komite/kategori'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_kategori') !== false ? 'active' : ''; ?>">Kategori Tagihan</a>
                            <a href="<?php echo Helper::url('/keuangan/komite/jenis'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_jenis') !== false ? 'active' : ''; ?>">Jenis Pembayaran</a>
                            <a href="<?php echo Helper::url('/keuangan/komite/rekap'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_rekap') !== false ? 'active' : ''; ?>">Rekap Tagihan</a>
                            <a href="<?php echo Helper::url('/keuangan/komite/laporan'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'komite_laporan') !== false ? 'active' : ''; ?>">Laporan Komite</a>
                        </div>
                    </div>

                    <div class="z-nav-cat">PENGATURAN</div>
                    
                    <a href="<?php echo Helper::url('/keuangan/biaya'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'keuangan_finance_biaya') !== false || strpos(\$activeMenu, 'keuangan_biaya') !== false ? 'active' : ''; ?>">
                        <i data-lucide="sliders"></i> Komponen Biaya
                    </a>
                    
                    <a href="<?php echo Helper::url('/keuangan/akses'); ?>" class="z-nav-item <?php echo strpos(\$activeMenu, 'keuangan_finance_akses') !== false || strpos(\$activeMenu, 'keuangan_akses') !== false ? 'active' : ''; ?>">
                        <i data-lucide="shield-check"></i> Akses Bendahara
                    </a>
                <?php endif; ?>
PHP;

$c = str_replace('<?php if ($isAbsen): ?>', $new_blocks . "\n\n                <?php if (\$isAbsen): ?>", $c);
file_put_contents($file, $c);
echo "done";
