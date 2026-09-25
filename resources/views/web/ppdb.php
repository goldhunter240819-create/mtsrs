<!-- PPDB Section -->
<section class="page-header" style="background: var(--web-primary); padding: 120px 0 60px; color: white; text-align: center;">
    <div class="web-container">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">Penerimaan Peserta Didik Baru (PPDB)</h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">Tahun Ajaran Mendatang</p>
    </div>
</section>

<section style="padding: 80px 0;">
    <div class="web-container">
        <div style="background: white; padding: 50px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); text-align: center; max-width: 800px; margin: 0 auto;">
            <div style="width: 80px; height: 80px; background: rgba(245, 158, 11, 0.1); color: var(--web-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i data-lucide="graduation-cap" style="width: 40px; height: 40px;"></i>
            </div>
            <h2 style="font-size: 2.2rem; color: var(--web-primary); margin-bottom: 20px;">Pendaftaran Sedang Dibuka!</h2>
            <p style="font-size: 1.1rem; color: var(--web-text-light); margin-bottom: 40px; line-height: 1.8;">
                Jangan lewatkan kesempatan untuk bergabung menjadi bagian dari <?= htmlspecialchars($institusi['nama'] ?? 'Madrasah Kami') ?>. Kuota terbatas, segera daftarkan putra/putri Anda dan jadikan mereka generasi penerus yang cerdas dan berakhlak mulia.
            </p>
            
            <div style="display: flex; gap: 20px; justify-content: center;">
                <a href="#" class="btn-primary-web" style="font-size: 1.1rem; padding: 15px 40px;"><i data-lucide="edit-3" style="margin-right: 10px;"></i> Daftar Sekarang</a>
                <a href="#" class="btn-secondary-web outline" style="font-size: 1.1rem; padding: 15px 40px;"><i data-lucide="download" style="margin-right: 10px;"></i> Unduh Brosur</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 80px;">
            <div>
                <h3 style="font-size: 1.8rem; color: var(--web-primary); margin-bottom: 20px;">Syarat Pendaftaran</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--web-border);">
                        <i data-lucide="check-circle" style="color: var(--web-secondary);"></i> Fotokopi Akte Kelahiran (2 Lembar)
                    </li>
                    <li style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--web-border);">
                        <i data-lucide="check-circle" style="color: var(--web-secondary);"></i> Fotokopi Kartu Keluarga (2 Lembar)
                    </li>
                    <li style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--web-border);">
                        <i data-lucide="check-circle" style="color: var(--web-secondary);"></i> Pas Foto 3x4 dan 4x6 (Masing-masing 4 Lembar)
                    </li>
                    <li style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid var(--web-border);">
                        <i data-lucide="check-circle" style="color: var(--web-secondary);"></i> Surat Keterangan Lulus / Ijazah (Jika sudah ada)
                    </li>
                </ul>
            </div>
            <div>
                <h3 style="font-size: 1.8rem; color: var(--web-primary); margin-bottom: 20px;">Alur Pendaftaran</h3>
                <div style="position: relative; padding-left: 30px; border-left: 2px dashed #cbd5e1;">
                    <div style="position: relative; margin-bottom: 30px;">
                        <div style="position: absolute; left: -40px; top: 0; width: 20px; height: 20px; background: var(--web-primary-light); border-radius: 50%;"></div>
                        <h4 style="font-size: 1.2rem; color: var(--web-primary); margin-bottom: 5px;">1. Mengisi Formulir</h4>
                        <p style="color: var(--web-text-light);">Mengisi formulir secara online melalui website atau datang langsung ke sekretariat.</p>
                    </div>
                    <div style="position: relative; margin-bottom: 30px;">
                        <div style="position: absolute; left: -40px; top: 0; width: 20px; height: 20px; background: var(--web-primary-light); border-radius: 50%;"></div>
                        <h4 style="font-size: 1.2rem; color: var(--web-primary); margin-bottom: 5px;">2. Menyerahkan Berkas</h4>
                        <p style="color: var(--web-text-light);">Menyerahkan seluruh persyaratan administrasi yang telah ditentukan.</p>
                    </div>
                    <div style="position: relative; margin-bottom: 30px;">
                        <div style="position: absolute; left: -40px; top: 0; width: 20px; height: 20px; background: var(--web-primary-light); border-radius: 50%;"></div>
                        <h4 style="font-size: 1.2rem; color: var(--web-primary); margin-bottom: 5px;">3. Tes Seleksi / Wawancara</h4>
                        <p style="color: var(--web-text-light);">Calon siswa dan orang tua mengikuti tes potensi akademik dan wawancara.</p>
                    </div>
                    <div style="position: relative;">
                        <div style="position: absolute; left: -40px; top: 0; width: 20px; height: 20px; background: var(--web-secondary); border-radius: 50%; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);"></div>
                        <h4 style="font-size: 1.2rem; color: var(--web-primary); margin-bottom: 5px;">4. Pengumuman Kelulusan</h4>
                        <p style="color: var(--web-text-light);">Hasil seleksi akan diumumkan melalui website dan surat resmi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
