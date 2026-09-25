<!-- Kontak Section -->
<section class="page-header" style="background: var(--web-primary); padding: 120px 0 60px; color: white; text-align: center;">
    <div class="web-container">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 10px;">Hubungi Kami</h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">Kami siap membantu dan menjawab pertanyaan Anda</p>
    </div>
</section>

<section style="padding: 80px 0;">
    <div class="web-container">
        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 60px;">
            <!-- Info Kontak -->
            <div>
                <h2 style="font-size: 2rem; color: var(--web-primary); margin-bottom: 25px;">Informasi Kontak</h2>
                
                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <div style="width: 50px; height: 50px; background: rgba(59, 130, 246, 0.1); color: var(--web-primary-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="map-pin" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.1rem; color: var(--web-text); margin-bottom: 5px;">Alamat Sekolah</h4>
                        <p style="color: var(--web-text-light); line-height: 1.6;"><?= htmlspecialchars($institusi['alamat'] ?? 'Jalan Raya Pendidikan No. 123, Kecamatan, Kota, Provinsi') ?></p>
                    </div>
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <div style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="phone" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.1rem; color: var(--web-text); margin-bottom: 5px;">Telepon / WhatsApp</h4>
                        <p style="color: var(--web-text-light); line-height: 1.6;"><?= htmlspecialchars($institusi['telepon'] ?? '(021) 1234 5678') ?></p>
                    </div>
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <div style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.1); color: var(--web-secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="mail" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.1rem; color: var(--web-text); margin-bottom: 5px;">Email Resmi</h4>
                        <p style="color: var(--web-text-light); line-height: 1.6;"><?= htmlspecialchars($institusi['email'] ?? 'info@sekolah.sch.id') ?></p>
                    </div>
                </div>
                
                <h2 style="font-size: 2rem; color: var(--web-primary); margin: 50px 0 25px;">Jam Operasional</h2>
                <ul style="list-style: none; color: var(--web-text-light); padding: 0;">
                    <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--web-border); padding: 10px 0;"><span>Sabtu - Rabu</span> <b>07.00 - 15.00 WIB</b></li>
                    <li style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--web-border); padding: 10px 0;"><span>Kamis</span> <b>07.00 - 11.30 WIB</b></li>
                    <li style="display: flex; justify-content: space-between; padding: 10px 0; color: #ef4444;"><span>Jumat / Tanggal Merah</span> <b>Tutup</b></li>
                </ul>
            </div>
            
            <!-- Form & Map -->
            <div>
                <!-- Peta Mockup -->
                <div style="width: 100%; height: 300px; background: #e2e8f0; border-radius: 20px; margin-bottom: 40px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-weight: 600;">
                    <i data-lucide="map" style="width: 48px; height: 48px; margin-right: 10px; opacity: 0.5;"></i> Google Maps Tersemat Di Sini
                </div>

                <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid var(--web-border);">
                    <h3 style="font-size: 1.5rem; color: var(--web-primary); margin-bottom: 25px;">Kirim Pesan / Pertanyaan</h3>
                    <form onsubmit="event.preventDefault(); alert('Pesan berhasil terkirim!');">
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; color: var(--web-text); margin-bottom: 8px;">Nama Lengkap</label>
                            <input type="text" placeholder="Masukkan nama Anda" style="width: 100%; height: 45px; border-radius: 10px; border: 1px solid var(--web-border); padding: 0 15px; font-family: inherit; font-size: 1rem; outline: none;">
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; color: var(--web-text); margin-bottom: 8px;">Email atau No HP</label>
                            <input type="text" placeholder="Untuk balasan kami" style="width: 100%; height: 45px; border-radius: 10px; border: 1px solid var(--web-border); padding: 0 15px; font-family: inherit; font-size: 1rem; outline: none;">
                        </div>
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 600; color: var(--web-text); margin-bottom: 8px;">Pesan Anda</label>
                            <textarea placeholder="Tulis pesan atau pertanyaan Anda di sini..." style="width: 100%; height: 120px; border-radius: 10px; border: 1px solid var(--web-border); padding: 15px; font-family: inherit; font-size: 1rem; outline: none; resize: vertical;"></textarea>
                        </div>
                        <button type="submit" class="btn-primary-web" style="width: 100%; border: none; cursor: pointer; padding: 15px; border-radius: 10px; display: flex; justify-content: center;">
                            Kirim Pesan <i data-lucide="send" style="margin-left: 10px; width: 18px; height: 18px;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
