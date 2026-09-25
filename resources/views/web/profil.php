<!-- Profil Section -->
<section class="page-header" style="background: var(--web-primary); padding: 120px 0 60px; color: white; text-align: center;">
    <div class="web-container">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">Profil Madrasah</h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">Mengenal lebih dekat <?= htmlspecialchars($institusi['nama'] ?? 'MTs Roudlotus Sholihin') ?></p>
    </div>
</section>

<section style="padding: 80px 0;">
    <div class="web-container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; margin-bottom: 80px;">
            <div>
                <h3 style="color: var(--web-primary-light); margin-bottom: 15px; text-transform: uppercase; font-size: 1rem; letter-spacing: 1px;">Sambutan Kepala Madrasah</h3>
                <h2 style="font-size: 2.5rem; color: var(--web-primary); margin-bottom: 20px; line-height: 1.2;">Mewujudkan Generasi Cerdas & Berakhlak Mulia</h2>
                <p style="color: var(--web-text-light); line-height: 1.8; margin-bottom: 20px;">
                    Assalamu'alaikum Warahmatullahi Wabarakatuh. Puji syukur ke hadirat Allah SWT, kami menyambut Anda di portal resmi <?= htmlspecialchars($institusi['nama'] ?? 'MTs RS') ?>. Di era digital ini, kami berkomitmen untuk tidak hanya mendidik secara akademis, namun juga menanamkan nilai-nilai islami yang kuat agar lulusan kami siap menghadapi tantangan zaman.
                </p>
                <p style="font-weight: 700; color: var(--web-text);">H. Fulan, S.Pd., M.Pd.</p>
                <p style="font-size: 0.9rem; color: var(--web-text-light);">Kepala Madrasah</p>
            </div>
            <div style="background: #e2e8f0; height: 400px; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                <i data-lucide="user" style="width: 64px; height: 64px; opacity: 0.5;"></i>
                <span style="margin-left: 10px; font-weight: 600;">Foto Kepala Madrasah</span>
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 2.5rem; color: var(--web-primary); margin-bottom: 15px;">Visi & Misi</h2>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
            <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid var(--web-border);">
                <div style="width: 60px; height: 60px; background: rgba(59, 130, 246, 0.1); color: var(--web-primary-light); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <i data-lucide="eye" style="width: 30px; height: 30px;"></i>
                </div>
                <h3 style="font-size: 1.8rem; color: var(--web-primary); margin-bottom: 15px;">Visi</h3>
                <p style="font-size: 1.1rem; color: var(--web-text-light); line-height: 1.6;">
                    "Terwujudnya Peserta Didik yang Beriman, Bertaqwa, Berprestasi, Terampil, dan Berbudaya Lingkungan."
                </p>
            </div>
            <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid var(--web-border);">
                <div style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.1); color: var(--web-secondary); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <i data-lucide="target" style="width: 30px; height: 30px;"></i>
                </div>
                <h3 style="font-size: 1.8rem; color: var(--web-primary); margin-bottom: 15px;">Misi</h3>
                <ul style="color: var(--web-text-light); line-height: 1.8; margin-left: 20px;">
                    <li>Menumbuhkan penghayatan terhadap ajaran agama Islam.</li>
                    <li>Melaksanakan pembelajaran dan bimbingan secara efektif.</li>
                    <li>Mendorong dan membantu setiap siswa untuk mengenali potensi dirinya.</li>
                    <li>Menumbuhkan semangat keunggulan secara intensif kepada seluruh warga madrasah.</li>
                </ul>
            </div>
        </div>
    </div>
</section>
