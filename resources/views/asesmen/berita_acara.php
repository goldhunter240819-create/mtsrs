<?php
$title = $title ?? 'Berita Acara Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_berita_acara';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="file-text" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Berita Acara Pelaksanaan Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Dokumen resmi Berita Acara Pelaksanaan Ujian Madrasah, catatan insiden, dan presensi pengawas ruang.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" onclick="document.getElementById('modalAddBA').style.display='flex'" class="btn" style="background: white; color: #4338ca; font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Input Berita Acara
        </button>
        <a href="<?php echo \App\Core\Helper::url('/asesmen/berita-acara/cetak'); ?>" target="_blank" class="btn btn-outline" style="background: rgba(255,255,255,0.15); color: white; border-color: rgba(255,255,255,0.4); font-weight: 700; border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Cetak Blangko Kosong
        </a>
    </div>
</div>

<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="overflow-x: auto;">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Tanggal & Waktu</th>
                    <th>Ruang</th>
                    <th>Event Ujian</th>
                    <th>Pengawas Ruang</th>
                    <th>Kehadiran Peserta</th>
                    <th style="text-align: right; width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($baList)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: var(--z-text-muted);">
                        Belum ada Berita Acara ujian yang tersimpan. Klik <strong>Input Berita Acara</strong> di atas atau cetak blangko manual.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($baList as $idx => $ba): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td>
                            <strong><?php echo date('d/m/Y', strtotime($ba['tanggal'])); ?></strong>
                            <?php if(!empty($ba['jam_mulai'])): ?>
                                <div style="font-size: 0.78rem; color: var(--z-text-muted);"><?php echo substr($ba['jam_mulai'], 0, 5); ?> - <?php echo substr($ba['jam_selesai'], 0, 5); ?> WIB</div>
                            <?php endif; ?>
                        </td>
                        <td><span class="pill pill-purple" style="font-size: 0.78rem;"><?php echo htmlspecialchars($ba['nama_ruang']); ?></span></td>
                        <td>
                            <strong><?php echo htmlspecialchars($ba['nama_event'] ?: '-'); ?></strong>
                            <?php if(!empty($ba['nama_mapel'])): ?>
                                <div style="font-size: 0.8rem; color: #4338ca; font-weight: 600;"><?php echo htmlspecialchars($ba['nama_mapel']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div>1. <?php echo htmlspecialchars($ba['nama_pengawas1'] ?: '-'); ?></div>
                            <?php if(!empty($ba['nama_pengawas2'])): ?>
                                <div>2. <?php echo htmlspecialchars($ba['nama_pengawas2']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="pill pill-green" style="font-size: 0.75rem;">Hadir: <?php echo $ba['jumlah_hadir']; ?></span>
                            <?php if($ba['jumlah_tidak_hadir'] > 0): ?>
                                <span class="pill pill-red" style="font-size: 0.75rem;">Absen: <?php echo $ba['jumlah_tidak_hadir']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <a href="<?php echo \App\Core\Helper::url('/asesmen/berita-acara/cetak?id=' . $ba['id']); ?>" target="_blank" class="btn btn-outline btn-sm" title="Cetak Berita Acara Resmi">
                                <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Input Berita Acara -->
<div id="modalAddBA" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="z-card" style="width: 100%; max-width: 600px; padding: 2rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="plus-circle" style="color: #4f46e5;"></i> Input Berita Acara Ujian
            </h3>
            <button type="button" onclick="document.getElementById('modalAddBA').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--z-text-muted);">&times;</button>
        </div>

        <form action="<?php echo \App\Core\Helper::url('/asesmen/berita-acara/save'); ?>" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Event Ujian <span style="color:red;">*</span></label>
                    <select name="event_id" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <?php foreach($events as $ev): ?>
                            <option value="<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Ruangan <span style="color:red;">*</span></label>
                    <select name="ruang_id" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <?php foreach($ruangs as $r): ?>
                            <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['nama_ruang']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Tanggal Ujian</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Jam Mulai</label>
                    <input type="time" name="jam_mulai" value="07:30" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Jam Selesai</label>
                    <input type="time" name="jam_selesai" value="09:00" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Pengawas Ruang I</label>
                    <select name="pengawas_1_id" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="">Pilih Guru Pengawas...</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Pengawas Ruang II</label>
                    <select name="pengawas_2_id" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                        <option value="">Pilih Guru Pengawas...</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Seharusnya Hadir</label>
                    <input type="number" name="jumlah_seharusnya" value="20" min="0" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Jumlah Hadir</label>
                    <input type="number" name="jumlah_hadir" value="20" min="0" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Tidak Hadir (Absen)</label>
                    <input type="number" name="jumlah_tidak_hadir" value="0" min="0" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                </div>
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Nama / No. Peserta Tidak Hadir</label>
                <input type="text" name="peserta_tidak_hadir" placeholder="Kosongkan jika nihil, atau tulis misal: Ahmad (Sakit)" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.88rem;">Catatan Kejadian Khusus Selama Ujian</label>
                <textarea name="catatan_kejadian" rows="2" placeholder="Pelaksanaan ujian berjalan tertib, aman, dan kondusif." style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('modalAddBA').style.display='none'" class="btn btn-outline" style="padding: 10px 18px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px; background: #4f46e5; border-color: #4f46e5; font-weight: 700;">Simpan Berita Acara</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
