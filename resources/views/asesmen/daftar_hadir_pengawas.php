<?php
$title = $title ?? 'Daftar Hadir Pengawas Ruang | MTs RS';
$activeMenu = $activeMenu ?? 'asesmen_pengawas';

ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #4f46e5, #3730a3); margin-bottom: 2rem;">
    <div>
        <h1 class="mph-title" style="color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="user-check" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Kehadiran Pengawas Ujian
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Format presensi tanda tangan harian bagi Bapak/Ibu guru pengawas ruang ujian madrasah.
        </p>
    </div>
</div>

<div class="z-card" style="padding: 1.5rem; border-radius: 16px; margin-bottom: 1.5rem;">
    <form method="GET" action="<?php echo \App\Core\Helper::url('/asesmen/daftar-hadir-pengawas/cetak'); ?>" target="_blank" style="display: flex; flex-wrap: wrap; gap: 1.25rem; align-items: flex-end;">
        <div style="flex: 1; min-width: 220px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Pilih Event Ujian <span style="color:red;">*</span></label>
            <select name="event_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <?php foreach($events as $ev): ?>
                    <option value="<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['nama']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="width: 180px;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--z-text); margin-bottom: 6px;">Tanggal Pelaksanaan</label>
            <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 22px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Cetak Presensi Pengawas
            </button>
        </div>
    </form>
</div>

<div class="z-card" style="padding: 1.75rem; border-radius: 16px;">
    <div style="display: flex; gap: 16px; align-items: flex-start;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i data-lucide="check-circle" style="width: 22px; height: 22px;"></i>
        </div>
        <div>
            <h3 style="margin: 0 0 6px 0; font-size: 1.05rem; font-weight: 800; color: var(--z-text);">Tentang Lembar Presensi Pengawas Ruang</h3>
            <p style="font-size: 0.88rem; color: var(--z-text-muted); line-height: 1.6; margin: 0;">
                Dokumen presensi pengawas digunakan oleh Panitia Ujian Madrasah untuk merekap kehadiran pengawas ruang di setiap sesi ujian, lengkap dengan nomor ruang tugas dan jam bertugas.
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
