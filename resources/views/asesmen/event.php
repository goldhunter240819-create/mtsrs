<?php
$title = $title ?? 'Event & Kategori Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_event';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="calendar-check" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Event & Kategori Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Pengaturan sesi STS/PTS, SAS/PAS, SAT, Tryout, dan Asesmen Madrasah Kelas Akhir.
        </p>
    </div>
    <div>
        <button type="button" onclick="document.getElementById('modalAddEvent').style.display='flex'" class="btn" style="background: white; color: #4338ca; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Tambah Event Ujian
        </button>
    </div>
</div>

<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Event / Ujian</th>
                    <th>Kode</th>
                    <th>Jenis</th>
                    <th>Semester</th>
                    <th>Sasaran Kelas</th>
                    <th>Periode Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--z-text-muted);">
                        Belum ada event ujian terdaftar. Klik tombol <strong>Tambah Event Ujian</strong> di atas.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($events as $idx => $ev): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td>
                            <strong style="color: #1e293b; font-size: 0.95rem;"><?php echo htmlspecialchars($ev['nama']); ?></strong>
                            <?php if (!empty($ev['keterangan'])): ?>
                                <div style="font-size: 0.8rem; color: var(--z-text-muted); margin-top: 3px;"><?php echo htmlspecialchars($ev['keterangan']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><span style="font-family: monospace; font-weight: 700; color: #4338ca; background: #eef2ff; padding: 3px 8px; border-radius: 6px; font-size: 0.85rem;"><?php echo htmlspecialchars($ev['kode']); ?></span></td>
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
                        <td><span class="pill pill-blue" style="font-size: 0.75rem;"><?php echo htmlspecialchars($ev['tingkat_kelas'] ?? 'Semua'); ?></span></td>
                        <td>
                            <?php if (!empty($ev['tanggal_mulai'])): ?>
                                <div style="font-size: 0.85rem; font-weight: 600; color: #334155;">
                                    <?php echo date('d/m/Y', strtotime($ev['tanggal_mulai'])); ?>
                                    <?php if (!empty($ev['tanggal_selesai'])): ?>
                                        s/d <?php echo date('d/m/Y', strtotime($ev['tanggal_selesai'])); ?>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span style="color: var(--z-text-muted); font-size: 0.8rem;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($ev['status'] === 'aktif'): ?>
                                <span class="pill pill-green" style="font-size: 0.75rem;"><i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Aktif</span>
                            <?php else: ?>
                                <span class="pill pill-yellow" style="font-size: 0.75rem;">Arsip</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Event -->
<div id="modalAddEvent" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="z-card" style="width: 100%; max-width: 550px; padding: 2rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="plus-circle" style="color: #4f46e5;"></i> Tambah Event Ujian Baru
            </h3>
            <button type="button" onclick="document.getElementById('modalAddEvent').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--z-text-muted);">&times;</button>
        </div>

        <form action="<?php echo \App\Core\Helper::url('/asesmen/event/save'); ?>" method="POST">
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Nama Event Ujian <span style="color: red;">*</span></label>
                <input type="text" name="nama" required placeholder="Contoh: Asesmen Sumatif Tengah Semester (STS) Genap" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Kode Event <span style="color: red;">*</span></label>
                    <input type="text" name="kode" required placeholder="Contoh: STS-GENAP-26" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Jenis Ujian</label>
                    <select name="jenis" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="sts">STS / PTS (Sumatif Tengah Semester)</option>
                        <option value="sas">SAS / PAS (Sumatif Akhir Semester)</option>
                        <option value="sat">SAT / PAT (Sumatif Akhir Tahun)</option>
                        <option value="am">Asesmen Madrasah (AM / Kelulusan)</option>
                        <option value="tryout">Tryout / Uji Coba</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Semester</label>
                    <select name="semester" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Sasaran Tingkat Kelas</label>
                    <select name="tingkat_kelas" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="Semua">Semua Tingkat (7, 8, 9)</option>
                        <option value="Kelas 7">Kelas 7 Saja</option>
                        <option value="Kelas 8">Kelas 8 Saja</option>
                        <option value="Kelas 9">Kelas 9 Saja (AM)</option>
                        <option value="Kelas 7 & 8">Kelas 7 & 8 Saja</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan teknis ujian..." style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('modalAddEvent').style.display='none'" class="btn btn-outline" style="padding: 10px 18px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px; background: #4f46e5; border-color: #4f46e5; font-weight: 700;">Simpan Event</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
