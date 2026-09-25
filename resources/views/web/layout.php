<?php
use App\Core\Helper;
use App\Core\Branding;

if (!isset($title)) $title = "Sekolah Menengah Pertama";
$institusi = Branding::getInstitusi();
$logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
$faviconSrc = !empty($institusi['favicon']) ? Helper::url('/uploads/logo/' . $institusi['favicon']) : $logoSrc;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($faviconSrc) ?>">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Custom Web CSS -->
    <link rel="stylesheet" href="<?= Helper::url('/assets/css/web.css?v=' . time()) ?>">
</head>
<body>

    <!-- Navbar -->
    <nav class="web-navbar">
        <div class="web-container nav-inner">
            <a href="<?= Helper::url('/') ?>" class="nav-brand">
                <img src="<?= htmlspecialchars($logoSrc) ?>" alt="Logo">
                <div class="brand-text">
                    <span class="brand-title"><?= htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin') ?></span>
                    <span class="brand-subtitle"><?= htmlspecialchars($institusi['singkatan'] ?? 'Unggul & Berakhlak') ?></span>
                </div>
            </a>
            
            <div class="nav-menu">
                <a href="<?= Helper::url('/') ?>" class="nav-link <?= ($current_page ?? '') == 'home' ? 'active' : '' ?>">Beranda</a>
                <a href="<?= Helper::url('/profil') ?>" class="nav-link <?= ($current_page ?? '') == 'profil' ? 'active' : '' ?>">Profil</a>
                <a href="<?= Helper::url('/berita') ?>" class="nav-link <?= ($current_page ?? '') == 'berita' ? 'active' : '' ?>">Berita</a>
                <a href="<?= Helper::url('/ppdb') ?>" class="nav-link <?= ($current_page ?? '') == 'ppdb' ? 'active' : '' ?>">PPDB</a>
                <a href="<?= Helper::url('/kontak') ?>" class="nav-link <?= ($current_page ?? '') == 'kontak' ? 'active' : '' ?>">Kontak</a>
            </div>
            
            <div class="nav-action">
                <a href="<?= Helper::url('/login') ?>" class="btn-login-portal">
                    <i data-lucide="log-in"></i> Portal Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="web-footer">
        <div class="web-container footer-grid">
            <div class="footer-col">
                <div class="footer-brand">
                    <img src="<?= htmlspecialchars($logoSrc) ?>" alt="Logo">
                    <h3><?= htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin') ?></h3>
                </div>
                <p class="footer-desc">
                    Sekolah unggulan yang mencetak generasi berprestasi, berakhlak mulia, dan siap menghadapi tantangan global.
                </p>
            </div>
            <div class="footer-col">
                <h4>Tautan Cepat</h4>
                <ul class="footer-links">
                    <li><a href="<?= Helper::url('/') ?>">Beranda</a></li>
                    <li><a href="<?= Helper::url('/profil') ?>">Profil Sekolah</a></li>
                    <li><a href="<?= Helper::url('/berita') ?>">Berita Terbaru</a></li>
                    <li><a href="<?= Helper::url('/ppdb') ?>">Info PPDB</a></li>
                    <li><a href="<?= Helper::url('/login') ?>">SIAKAD Login</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Kontak Kami</h4>
                <ul class="footer-contact">
                    <li><i data-lucide="map-pin"></i> <?= htmlspecialchars($institusi['alamat'] ?? 'Jalan Sekolah No. 1, Kota') ?></li>
                    <li><i data-lucide="phone"></i> <?= htmlspecialchars($institusi['telepon'] ?? '(021) 1234567') ?></li>
                    <li><i data-lucide="mail"></i> <?= htmlspecialchars($institusi['email'] ?? 'info@sekolah.sch.id') ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="web-container">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin') ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.web-navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
