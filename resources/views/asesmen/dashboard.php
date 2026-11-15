<?php
$title = $title ?? 'Dashboard Asesmen & Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_dashboard';

ob_start();
?>

<!-- Header Asesmen -->
<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="file-check-2" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Asesmen & Ujian Madrasah
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Pusat manajemen Bank Soal, Distribusi Ruangan & Nomor Meja, Berita Acara, serta Administrasi STS/PTS, SAS/PAS, dan Asesmen Madrasah.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?php echo \App\Core\Helper::url('/asesmen/event'); ?>" class="btn" style="background: white; color: #4338ca; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Event Ujian Baru
        </a>
    </div>
</div>

<!-- Stat Grid Boxes -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <!-- Stat 1: Event Ujian -->
    <div class="z-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 18px; border-left: 5px solid #4f46e5; border-radius: 16px;">
        <div style="width: 54px; height: 54px; border-radius: 14px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="calendar-check" style="width: 28px; height: 28px;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--z-text-muted); font-weight: 600;">Event Ujian Aktif</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: var(--z-text);"><?php echo $totalEvent; ?></div>
        </div>
    </div>

    <!-- Stat 2: Bank Soal -->
    <div class="z-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 18px; border-left: 5px solid #10b981; border-radius: 16px;">
        <div style="width: 54px; height: 54px; border-radius: 14px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="file-question" style="width: 28px; height: 28px;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--z-text-muted); font-weight: 600;">Bank Soal Tersimpan</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: var(--z-text);"><?php echo $totalBankSoal; ?></div>
        </div>
    </div>

    <!-- Stat 3: Ruangan -->
    <div class="z-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 18px; border-left: 5px solid #f59e0b; border-radius: 16px;">
        <div style="width: 54px; height: 54px; border-radius: 14px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="building" style="width: 28px; height: 28px;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--z-text-muted); font-weight: 600;">Ruang Ujian Terdata</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: var(--z-text);"><?php echo $totalRuang; ?></div>
        </div>
    </div>

    <!-- Stat 4: Berita Acara -->
    <div class="z-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 18px; border-left: 5px solid #0ea5e9; border-radius: 16px;">
        <div style="width: 54px; height: 54px; border-radius: 14px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="file-text" style="width: 28px; height: 28px;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--z-text-muted); font-weight: 600;">Berita Acara Selesai</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: var(--z-text);"><?php echo $totalBeritaAcara; ?></div>
        </div>
    </div>
</div>

<!-- Quick Module Shortcut Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <!-- Shortcut 1: Event & Kategori -->
    <a href="<?php echo \App\Core\Helper::url('/asesmen/event'); ?>" style="text-decoration: none; color: inherit;">
        <div class="z-card" style="padding: 1.5rem; border-radius: 16px; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; border: 1px solid var(--z-border);" onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='#4f46e5';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--z-border)';">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="calendar-range" style="width: 22px; height: 22px;"></i>
                </div>
                <span class="pill pill-purple" style="font-size: 0.7rem;">Kategori</span>
            </div>
            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 6px; color: var(--z-text);">Event & Kategori Ujian</h3>
            <p style="font-size: 0.82rem; color: var(--z-text-muted); line-height: 1.5; margin: 0;">
                Kelola jadwal STS/PTS, SAS/PAS, SAT, dan Ujian Madrasah untuk siswa kelas akhir.
            </p>
        </div>
    </a>

    <!-- Shortcut 2: Bank Soal & Kisi-kisi -->
    <a href="<?php echo \App\Core\Helper::url('/asesmen/bank-soal'); ?>" style="text-decoration: none; color: inherit;">
        <div class="z-card" style="padding: 1.5rem; border-radius: 16px; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; border: 1px solid var(--z-border);" onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='#10b981';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--z-border)';">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="file-question" style="width: 22px; height: 22px;"></i>
                </div>
                <span class="pill pill-green" style="font-size: 0.7rem;">Soal</span>
            </div>
            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 6px; color: var(--z-text);">Bank Soal & Kisi-Kisi</h3>
            <p style="font-size: 0.82rem; color: var(--z-text-muted); line-height: 1.5; margin: 0;">
                Kompilasi master naskah soal, kunci jawaban, dan kisi-kisi per mata pelajaran.
            </p>
        </div>
    </a>

    <!-- Shortcut 3: Ruang & Nomor Meja -->
    <a href="<?php echo \App\Core\Helper::url('/asesmen/ruang'); ?>" style="text-decoration: none; color: inherit;">
        <div class="z-card" style="padding: 1.5rem; border-radius: 16px; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; border: 1px solid var(--z-border);" onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='#f59e0b';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--z-border)';">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #fffbeb; color: #f59e0b; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="building" style="width: 22px; height: 22px;"></i>
                </div>
                <span class="pill pill-yellow" style="font-size: 0.7rem;">Ruangan</span>
            </div>
            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 6px; color: var(--z-text);">Ruangan & Nomor Meja</h3>
            <p style="font-size: 0.82rem; color: var(--z-text-muted); line-height: 1.5; margin: 0;">
                Penataan denah ruang ujian, pembagian kapasitas, dan cetak nomor meja peserta.
            </p>
        </div>
    </a>

    <!-- Shortcut 4: Berita Acara & Presensi -->
    <a href="<?php echo \App\Core\Helper::url('/asesmen/berita-acara'); ?>" style="text-decoration: none; color: inherit;">
        <div class="z-card" style="padding: 1.5rem; border-radius: 16px; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; border: 1px solid var(--z-border);" onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='#0ea5e9';" onmouseout="this.style.transform='none'; this.style.borderColor='var(--z-border)';">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #f0f9ff; color: #0ea5e9; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="file-text" style="width: 22px; height: 22px;"></i>
                </div>
                <span class="pill pill-blue" style="font-size: 0.7rem;">Cetak Resmi</span>
            </div>
            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 6px; color: var(--z-text);">Berita Acara & Presensi</h3>
            <p style="font-size: 0.82rem; color: var(--z-text-muted); line-height: 1.5; margin: 0;">
                Format resmi Berita Acara, daftar hadir siswa & pengawas ruang siap cetak kop madrasah.
            </p>
        </div>
    </a>
</div>

<!-- Section: Event Ujian Aktif -->
<div class="z-card" style="padding: 1.75rem; border-radius: 16px; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="layers" style="color: #4f46e5; width: 20px; height: 20px;"></i> Daftar Event Ujian Terdaftar
            </h2>
            <p style="font-size: 0.85rem; color: var(--z-text-muted); margin: 4px 0 0 0;">
                Event aktif yang digunakan dalam pembagian sesi, soal, dan berita acara.
            </p>
        </div>
        <a href="<?php echo \App\Core\Helper::url('/asesmen/event'); ?>" class="btn btn-outline btn-sm" style="font-weight: 600;">
            Kelola Semua Event <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Event / Ujian</th>
                    <th>Kode</th>
                    <th>Jenis</th>
                    <th>Semester</th>
                    <th>Tingkat</th>
                    <th>Status</th>
                    <th style="text-align: right; width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--z-text-muted);">
                        Belum ada event ujian yang terdaftar. Silakan buat event baru.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($events as $idx => $ev): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td>
                            <strong style="color: #1e293b;"><?php echo htmlspecialchars($ev['nama']); ?></strong>
                            <?php if (!empty($ev['keterangan'])): ?>
                                <div style="font-size: 0.75rem; color: var(--z-text-muted);"><?php echo htmlspecialchars($ev['keterangan']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><span style="font-family: monospace; font-weight: 700; color: #4338ca; background: #eef2ff; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem;"><?php echo htmlspecialchars($ev['kode']); ?></span></td>
                        <td>
                            <?php 
                                $badgeColor = '#4f46e5';
                                if ($ev['jenis'] === 'am') $badgeColor = '#e11d48';
                                elseif ($ev['jenis'] === 'sas') $badgeColor = '#059669';
                                elseif ($ev['jenis'] === 'sts') $badgeColor = '#0ea5e9';
                            ?>
                            <span class="pill" style="background: <?php echo $badgeColor; ?>15; color: <?php echo $badgeColor; ?>; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">
                                <?php echo strtoupper($ev['jenis']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($ev['semester'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($ev['tingkat_kelas'] ?? 'Semua'); ?></td>
                        <td>
                            <?php if ($ev['status'] === 'aktif'): ?>
                                <span class="pill pill-green" style="font-size: 0.75rem;"><i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Aktif</span>
                            <?php else: ?>
                                <span class="pill pill-yellow" style="font-size: 0.75rem;">Arsip</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                <a href="<?php echo \App\Core\Helper::url('/asesmen/ruang?event_id=' . $ev['id']); ?>" class="btn btn-outline btn-sm" title="Atur Ruangan">
                                    <i data-lucide="building" style="width: 14px; height: 14px;"></i>
                                </a>
                                <a href="<?php echo \App\Core\Helper::url('/asesmen/berita-acara?event_id=' . $ev['id']); ?>" class="btn btn-primary btn-sm" title="Berita Acara">
                                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
