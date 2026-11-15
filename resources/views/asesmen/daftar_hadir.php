<?php
$title = $title ?? 'Daftar Hadir Peserta Ujian | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_daftar_hadir';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="clipboard-check" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Daftar Hadir Peserta Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Format presensi tanda tangan peserta ujian per ruang dan mata pelajaran siap cetak resmi kop madrasah.
        </p>
    </div>
</div>

<!-- Filter Box -->
<div class="z-card" style="padding: 1.5rem; border-radius: 16px; margin-bottom: 1.5rem;">
    <form method="GET" action="<?php echo \App\Core\Helper::url('/asesmen/daftar-hadir/cetak'); ?>" target="_blank" style="display: flex; flex-wrap: wrap; gap: 1.25rem; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Pilih Event Ujian <span style="color:red;">*</span></label>
            <select name="event_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <?php foreach($events as $ev): ?>
                    <option value="<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['nama']); ?> (<?php echo htmlspecialchars($ev['kode']); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 160px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Pilih Ruangan <span style="color:red;">*</span></label>
            <select name="ruang_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <?php foreach($ruangs as $r): ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['nama_ruang']); ?> (Kapasitas: <?php echo $r['kapasitas']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Mata Pelajaran <span style="color:red;">*</span></label>
            <select name="mapel_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <?php foreach($mapels as $m): ?>
                    <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="width: 150px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Tanggal Ujian</label>
            <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 22px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Cetak Lembar Presensi
            </button>
        </div>
    </form>
</div>

<!-- Info Guide Card -->
<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="display: flex; gap: 16px; align-items: flex-start;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="info" style="width: 22px; height: 22px;"></i>
        </div>
        <div>
            <h3 style="margin: 0 0 6px 0; font-size: 1.05rem; font-weight: 800; color: var(--z-text);">Petunjuk Cetak Daftar Hadir Peserta</h3>
            <p style="font-size: 0.88rem; color: var(--z-text-muted); line-height: 1.6; margin: 0;">
                1. Pastikan siswa telah terdistribusi ke dalam ruangan melalui menu <strong>Distribusi Peserta</strong>.<br>
                2. Pilih Event Ujian, Ruangan yang ingin dicetak, dan Mata Pelajaran yang diujikan.<br>
                3. Klik tombol <strong>Cetak Lembar Presensi</strong> untuk mencetak dokumen format resmi A4 lengkap dengan kolom tanda tangan zig-zag (1..20), kop surat madrasah, dan tanda tangan pengawas ruang.
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
