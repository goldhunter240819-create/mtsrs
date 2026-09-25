<!-- Hero Section -->
<section id="beranda" class="hero-section">
    <div class="hero-background"></div>
    <div class="web-container hero-content">
        <span class="hero-badge">Selamat Datang di</span>
        <h1 class="hero-title"><?= htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin') ?></h1>
        <p class="hero-desc">
            Membangun generasi cerdas, berintegritas, dan berwawasan global dengan pondasi iman dan takwa yang kuat. 
            Bergabunglah bersama kami untuk masa depan yang gemilang.
        </p>
        <div class="hero-actions">
            <a href="#profil" class="btn-primary-web">Kenali Kami Lebih Dekat</a>
            <a href="#program" class="btn-secondary-web">Program Unggulan</a>
        </div>
    </div>
    <div class="hero-overlay"></div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="web-container stats-grid">
        <div class="stat-item">
            <h3 class="stat-num">A</h3>
            <p class="stat-label">Akreditasi</p>
        </div>
        <div class="stat-item">
            <h3 class="stat-num">30+</h3>
            <p class="stat-label">Tenaga Pendidik</p>
        </div>
        <div class="stat-item">
            <h3 class="stat-num">500+</h3>
            <p class="stat-label">Siswa Aktif</p>
        </div>
        <div class="stat-item">
            <h3 class="stat-num">20+</h3>
            <p class="stat-label">Ekstrakurikuler</p>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="profil" class="about-section">
    <div class="web-container about-grid">
        <div class="about-image">
            <div class="image-placeholder">
                <i data-lucide="image" style="width:64px; height:64px; color:#94a3b8; opacity:0.5;"></i>
                <p>Foto Gedung Sekolah</p>
            </div>
            <div class="about-experience">
                <span>Sekolah Ramah Anak</span>
            </div>
        </div>
        <div class="about-text">
            <h4 class="section-subtitle">Tentang Kami</h4>
            <h2 class="section-title">Membentuk Karakter & Prestasi Sejak Dini</h2>
            <p>
                <?= htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin') ?> berkomitmen untuk menyediakan lingkungan belajar yang kondusif, inovatif, dan berpusat pada perkembangan peserta didik. Kami menggabungkan kurikulum nasional dengan nilai-nilai religius.
            </p>
            <ul class="about-features">
                <li>
                    <div class="feature-icon"><i data-lucide="check-circle-2"></i></div>
                    <div class="feature-text">Fasilitas Pembelajaran Modern</div>
                </li>
                <li>
                    <div class="feature-icon"><i data-lucide="check-circle-2"></i></div>
                    <div class="feature-text">Tenaga Pendidik Profesional & Bersertifikasi</div>
                </li>
                <li>
                    <div class="feature-icon"><i data-lucide="check-circle-2"></i></div>
                    <div class="feature-text">Lingkungan Islami & Nyaman</div>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- Programs Section -->
<section id="program" class="programs-section">
    <div class="web-container">
        <div class="section-header center">
            <h4 class="section-subtitle">Keunggulan Kami</h4>
            <h2 class="section-title">Program Pendidikan Unggulan</h2>
            <p class="section-desc">Beragam program yang dirancang khusus untuk mengembangkan potensi akademik dan non-akademik siswa secara maksimal.</p>
        </div>
        
        <div class="programs-grid">
            <!-- Program 1 -->
            <div class="program-card">
                <div class="program-icon"><i data-lucide="book-open"></i></div>
                <h3 class="program-title">Tahfidz Qur'an</h3>
                <p class="program-desc">Program bimbingan hafalan Al-Qur'an terstruktur dengan metode yang mudah dan menyenangkan.</p>
            </div>
            <!-- Program 2 -->
            <div class="program-card">
                <div class="program-icon"><i data-lucide="microscope"></i></div>
                <h3 class="program-title">Sains & Teknologi</h3>
                <p class="program-desc">Pengenalan dan pendalaman ilmu pengetahuan dan teknologi (IT) sejak dini.</p>
            </div>
            <!-- Program 3 -->
            <div class="program-card">
                <div class="program-icon"><i data-lucide="languages"></i></div>
                <h3 class="program-title">Bilingual Class</h3>
                <p class="program-desc">Pembiasaan bahasa Arab dan Inggris dalam komunikasi sehari-hari di lingkungan sekolah.</p>
            </div>
            <!-- Program 4 -->
            <div class="program-card">
                <div class="program-icon"><i data-lucide="medal"></i></div>
                <h3 class="program-title">Pembinaan Prestasi</h3>
                <p class="program-desc">Bimbingan intensif untuk siswa yang dipersiapkan mengikuti olimpiade dan perlombaan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="web-container cta-inner">
        <h2>Siap Menjadi Bagian dari Kami?</h2>
        <p>Mari ukir masa depan yang gemilang bersama <?= htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin') ?>.</p>
        <div class="cta-buttons">
            <a href="#" class="btn-primary-web">Informasi Pendaftaran</a>
            <a href="#" class="btn-secondary-web outline">Hubungi Kami</a>
        </div>
    </div>
</section>
