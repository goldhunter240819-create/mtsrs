<?php
use App\Core\Helper;
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="apk-vector-header" style="padding-bottom: 90px; justify-content: center; text-align: center; position: relative;">
    <div style="margin-bottom: 10px;">
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Profil Pengguna</div>
    </div>
    
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; display: flex; flex-direction: column; align-items: center; padding-top: 70px;">
    
    <div style="position: absolute; top: -55px; z-index: 30;">
        <div style="position: relative;">
            <?php if(!empty($userProfile['foto'])): ?>
                <img src="<?= Helper::url('/public/uploads/' . ($isGuru ? 'guru' : 'siswa') . '/' . rawurlencode($userProfile['foto'])) ?>" alt="Foto Profil" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 5px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: #f1f5f9;">
            <?php else: ?>
                <div style="width: 110px; height: 110px; border-radius: 50%; border: 5px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: linear-gradient(135deg, #e2e8f0, #cbd5e1); display: flex; align-items: center; justify-content: center; color: #64748b;">
                    <i data-lucide="user" style="width: 50px; height: 50px;"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div style="text-align: center; margin-bottom: 15px; width: 100%; padding-top: 20px;">
        <h2 style="margin: 0 0 5px 0; font-size: 1.2rem; font-weight: 800; color: #0f172a;"><?= htmlspecialchars($userProfile['nama'] ?? 'Pengguna') ?></h2>
        <div style="color: #64748b; font-size: 0.8rem; font-weight: 600; background: #f1f5f9; padding: 4px 12px; border-radius: 20px; display: inline-block;">
            <?= $isGuru ? 'Guru / Tenaga Pendidik' : 'Siswa Kelas ' . htmlspecialchars($userProfile['nama_kelas'] ?? '-') ?>
        </div>
    </div>
    
    <!-- Tombol Lihat Profil & Edit -->
    <a href="<?= Helper::url('/apk/edit-profile') ?>" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 20px; border-radius: 30px; font-weight: 700; font-size: 0.85rem; text-decoration: none; margin-bottom: 25px; color: #10b981; border: 1px solid #10b981; background: #ecfdf5; transition: all 0.2s;">
        <i data-lucide="edit-3" style="width: 14px;"></i>
        Lihat Detail & Edit
    </a>

    <!-- Menu Lainnya -->
    <div style="width: 100%; margin-bottom: 25px;">
        <h3 style="font-size: 0.85rem; font-weight: 800; color: #475569; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding-left: 10px;">Lainnya</h3>
        <div style="background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden;">
            
            <?php if($isGuru && $is_wali_kelas): ?>
            <a href="#" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit; transition: background 0.2s;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #ecfdf5; color: #10b981; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="message-square" style="width: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Pesan Masuk</div>
                        <div style="font-size: 0.7rem; color: #64748b;">Chat dari Wali Murid</div>
                    </div>
                </div>
                <i data-lucide="chevron-right" style="color: #cbd5e1; width: 16px;"></i>
            </a>
            <?php endif; ?>

            <?php if($isGuru): ?>

            <?php else: ?>
            <a href="#" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #ecfdf5; color: #10b981; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="message-circle" style="width: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Hubungi Wali Kelas</div>
                        <div style="font-size: 0.7rem; color: #64748b;">Chat langsung via aplikasi</div>
                    </div>
                </div>
                <i data-lucide="chevron-right" style="color: #cbd5e1; width: 16px;"></i>
            </a>
            <?php endif; ?>

            <a href="#" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #f8fafc; color: #64748b; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="calendar" style="width: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Kalender Akademik</div>
                        <div style="font-size: 0.7rem; color: #64748b;">Jadwal libur & agenda kegiatan</div>
                    </div>
                </div>
                <i data-lucide="chevron-right" style="color: #cbd5e1; width: 16px;"></i>
            </a>

            <a href="#" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #fef3c7; color: #d97706; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="book" style="width: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Buku Saku <?= $isGuru ? 'Guru' : 'Siswa' ?></div>
                        <div style="font-size: 0.7rem; color: #64748b;">Tata tertib & panduan</div>
                    </div>
                </div>
                <i data-lucide="chevron-right" style="color: #cbd5e1; width: 16px;"></i>
            </a>

            <a href="#" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #e0e7ff; color: #4f46e5; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="lock" style="width: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Ubah Password</div>
                        <div style="font-size: 0.7rem; color: #64748b;">Ganti kata sandi keamanan</div>
                    </div>
                </div>
                <i data-lucide="chevron-right" style="color: #cbd5e1; width: 16px;"></i>
            </a>

            <a href="#" onclick="openBantuan(); return false;" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #ecfdf5; color: #10b981; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="headphones" style="width: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Bantuan & IT Support</div>
                        <div style="font-size: 0.7rem; color: #64748b;">Hubungi admin sekolah</div>
                    </div>
                </div>
                <i data-lucide="chevron-right" style="color: #cbd5e1; width: 16px;"></i>
            </a>

            <a href="#" onclick="openTentang(); return false;" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; text-decoration: none; color: inherit;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #f8fafc; color: #94a3b8; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="info" style="width: 18px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">Tentang Aplikasi</div>
                        <div style="font-size: 0.7rem; color: #64748b;">Versi 26.2.2</div>
                    </div>
                </div>
                <i data-lucide="chevron-right" style="color: #cbd5e1; width: 16px;"></i>
            </a>
        </div>
    </div>

    <!-- Tombol Logout -->
    <a href="<?= Helper::url('/apk/logout') ?>" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 16px; background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; text-decoration: none; border-radius: 20px; font-weight: 800; font-size: 0.95rem; box-shadow: 0 4px 15px rgba(239,68,68,0.3); transition: transform 0.2s;">
        <i data-lucide="log-out" style="width: 20px;"></i> Keluar Aplikasi
    </a>

    <div style="margin-top: 40px; margin-bottom: 30px; color: #000; font-size: 0.75rem; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center;">
        <div style="font-size: 0.65rem; letter-spacing: 2px; color: #475569; font-weight: 800;">POWERED BY</div>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 5px;">
            <img src="<?= htmlspecialchars($faviconSrc ?? \App\Core\Helper::url('/public/assets/images/logo.png')) ?>" alt="Logo Sekolah" style="height: 32px; width: auto;" loading="lazy" decoding="async">
            <a href="https://wa.me/6281234567890" target="_blank" title="GHtech Studio">
                <img src="<?= \App\Core\Helper::url('/public/assets/images/ghtech_logo.png') ?>" alt="Logo GHtech" style="height: 32px; width: auto;" loading="lazy" decoding="async">
            </a>
        </div>
        <div>
            Sistem Informasi Akademik<br>MTs RS &copy; 2026<br>
            <span style="font-size: 0.7rem; color: #475569; font-weight: 600;">V.26.2.2 - GH Tech</span>
        </div>
    </div>

</div>

<script>
function openBantuan() {
    Swal.fire({
        title: 'Pusat Bantuan',
        html: `
            <div style="font-size: 0.9rem; color: #475569; margin-bottom: 20px; line-height: 1.5;">
                Jika Anda mengalami kendala aplikasi, lupa password, atau menemukan bug sistem, silakan hubungi tim IT Support / Admin Sekolah.
            </div>
            <a href="https://wa.me/6281234567890" target="_blank"
               style="display: flex; align-items: center; justify-content: center; gap: 10px; background: #10b981; color: white; padding: 12px 20px; border-radius: 12px; font-weight: bold; text-decoration: none; width: 100%; box-shadow: 0 4px 15px rgba(16,185,129,0.3);">
               Hubungi Admin via WhatsApp
            </a>
        `,
        showConfirmButton: true,
        confirmButtonColor: '#94a3b8',
        confirmButtonText: 'Tutup',
        backdrop: `rgba(15, 23, 42, 0.7)`
    });
}

function openTentang() {
    Swal.fire({
        title: 'Aplikasi MTs RS',
        html: `
            <div style="text-align:left; padding-top:10px;">
                <div style="text-align:center;">
                    <img src="<?= htmlspecialchars($faviconSrc) ?>" style="width:70px; height:70px; margin-bottom:15px; border-radius:18px; box-shadow:0 4px 15px rgba(0,0,0,0.1); padding: 5px; background: #fff;">
                    <p style="font-size:1.1rem; font-weight:800; margin:0; color:#1e293b;">Sistem Akademik MTs RS</p>
                    <p style="font-size:0.8rem; color:#64748b; margin-top:5px; font-weight: 600;">Versi 26.2.2 (Vector UI Edition)</p>
                </div>
                <hr style="border:0; border-top:1px dashed #cbd5e1; margin:20px 0;">
                <h4 style="font-size:0.9rem; color:#3b82f6; margin-bottom:10px; font-weight: 800;">Apa yang baru?</h4>
                <ul style="font-size:0.8rem; color:#475569; padding-left:20px; margin-bottom:15px; line-height:1.6; font-weight: 600;">
                    <li>Desain antarmuka <b>Glassmorphism Premium</b> yang sangat halus dan kekinian.</li>
                    <li>Sistem Scanner QR V2 yang lebih akurat dengan Voice Feedback AI.</li>
                    <li>Integrasi Aplikasi Saya untuk layanan Akademik terpadu satu pintu.</li>
                </ul>
                <div style="text-align:center; margin-top:20px; font-size:0.75rem; color:#94a3b8; font-weight:700;">
                    &copy; 2026 MTs RS Developer Team
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonColor: '#0ea5e9',
        confirmButtonText: 'Tutup',
        backdrop: `rgba(15, 23, 42, 0.7)`
    });
}
</script>
