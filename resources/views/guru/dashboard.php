<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="layout-dashboard" style="color: #bfdbfe;"></i> Selamat Datang, Bapak/Ibu Guru!
        </h1>
        <p class="mph-subtitle">Ini adalah portal khusus untuk Guru MTs Roudlotus Sholihin. Dari sini Anda bisa mengelola jadwal, presensi, penilaian siswa, serta pengumpulan bukti fisik PKKM.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
    <!-- Card Jadwal -->
    <div class="z-card" style="padding: 1.5rem; text-align: center; cursor: pointer; transition: transform 0.2s;" onclick="window.location.href='<?php echo \App\Core\Helper::url('/guru/jadwal'); ?>'">
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

    <!-- Card PKKM Digital -->
    <div class="z-card" style="padding: 1.5rem; text-align: center; cursor: pointer; transition: transform 0.2s; background: linear-gradient(135deg, #f0fdf4, #ffffff); border-color: #bbf7d0;" onclick="window.location.href='<?php echo \App\Core\Helper::url('/guru/pkkm/instrumen'); ?>'">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); color: #047857; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i data-lucide="award" style="width: 32px; height: 32px;"></i>
        </div>
        <h3 style="font-size: 1.2rem; font-weight: 700; color: #065f46;">Penilaian PKKM</h3>
        <p style="font-size: 0.85rem; color: #047857; margin-top: 0.5rem;">Instrumen 42 indikator & berkas bukti fisik tim Anda.</p>
    </div>
</div>
