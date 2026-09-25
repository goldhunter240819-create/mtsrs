<div class="z-card" style="padding: 2rem; border-radius: 16px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; margin-bottom: 2rem;">
    <h2 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: -0.5px;">Selamat Datang, Bapak/Ibu Guru!</h2>
    <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 1.5rem; max-width: 600px;">
        Ini adalah portal khusus untuk Guru MTs Roudlotus Sholihin. Dari sini Anda bisa mengelola jadwal, presensi, dan penilaian siswa dengan mudah.
    </p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
    <!-- Card Jadwal -->
    <div class="z-card" style="padding: 1.5rem; text-align: center; cursor: pointer; transition: transform 0.2s;">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); color: var(--z-primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i data-lucide="calendar-days" style="width: 32px; height: 32px;"></i>
        </div>
        <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--z-text);">Jadwal Mengajar</h3>
        <p style="font-size: 0.85rem; color: var(--z-muted); margin-top: 0.5rem;">Lihat jadwal dan absensi harian.</p>
    </div>

    <!-- Card Nilai -->
    <div class="z-card" style="padding: 1.5rem; text-align: center; cursor: pointer; transition: transform 0.2s;" onclick="window.location.href='<?php echo \App\Core\Helper::url('/guru/nilai-harian'); ?>'">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i data-lucide="file-check-2" style="width: 32px; height: 32px;"></i>
        </div>
        <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--z-text);">Penilaian Akademik</h3>
        <p style="font-size: 0.85rem; color: var(--z-muted); margin-top: 0.5rem;">Input nilai rapor dan tugas siswa.</p>
    </div>
</div>
