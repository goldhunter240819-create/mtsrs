<?php
$title = $title ?? 'Cetak Laporan BK | MTs RS';
$activeMenu = $activeMenu ?? 'bk_laporan';

ob_start();
?>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title"><i data-lucide="printer"></i> Cetak Laporan BK</h1>
        <p class="mph-subtitle">Pilih jenis laporan yang ingin Anda cetak (Rekap per Kelas atau Detail per Siswa).</p>
    </div>
</div>

<div class="z-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
    <!-- Cetak Kelas -->
    <div class="z-card" style="padding: 2rem;">
        <h3 style="margin-top:0; font-size: 1.25rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="users" style="color: #4f46e5;"></i> Rekap Kedisiplinan Kelas
        </h3>
        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">
            Laporan ini berisi daftar total poin seluruh siswa dalam satu kelas terpilih. Sangat cocok digunakan untuk laporan bulanan atau akhir semester ke wali kelas.
        </p>
        <form action="<?= \App\Core\Helper::url('/bk/laporan/cetak-kelas') ?>" method="GET" target="_blank">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Pilih Kelas</label>
                <select name="kelas_id" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); outline: none;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px; font-weight: 600; padding: 12px;">
                <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Cetak Rekap Kelas
            </button>
        </form>
    </div>

    <!-- Cetak Surat Panggilan -->
    <div class="z-card" style="padding: 2rem;">
        <h3 style="margin-top:0; font-size: 1.25rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="user" style="color: #e11d48;"></i> Surat Panggilan / Detail
        </h3>
        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">
            Laporan ini mencetak riwayat pelanggaran lengkap secara spesifik untuk satu siswa berserta format Surat Panggilan Orang Tua (SP).
        </p>
        <form action="<?= \App\Core\Helper::url('/bk/laporan/cetak-siswa') ?>" method="GET" target="_blank">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Pilih Siswa</label>
                <select name="siswa_id" id="select-siswa" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); outline: none;">
                    <option value="">-- Cari dan Pilih Siswa --</option>
                    <?php foreach($siswaList as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_siswa']) ?> (<?= htmlspecialchars($s['nama_kelas']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px; font-weight: 600; padding: 12px; background: #e11d48; color: white; border: none;">
                <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Cetak Surat Panggilan
            </button>
        </form>
    </div>
</div>

<!-- Sertakan jQuery dan Select2 untuk pencarian dropdown siswa yang mudah -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    lucide.createIcons();
    $(document).ready(function() {
        $('#select-siswa').select2({
            placeholder: "-- Cari dan Pilih Siswa --",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php'; 
?>
