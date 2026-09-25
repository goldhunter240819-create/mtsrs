<?php
$title = $title ?? 'Catat Poin Siswa | MTs RS';
$activeMenu = $activeMenu ?? 'bk_pelanggaran';

ob_start();
?>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title"><i data-lucide="clipboard-list"></i> Catat Poin Siswa</h1>
        <p class="mph-subtitle">Input pelanggaran atau prestasi siswa untuk tahun ajaran ini.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="document.getElementById('modalAdd').style.display='flex'">
            <i data-lucide="plus"></i> Input Poin Baru
        </button>
    </div>
</div>

<?php \App\Core\Helper::showFlash(); ?>

<div class="z-panel">
    <table class="z-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Poin</th>
                <th>Pencatat</th>
                <th style="width: 80px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($riwayat as $r): ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                <td>
                    <div style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($r['nama_siswa']); ?></div>
                    <div style="font-size:0.75rem; color:#64748b;"><?php echo htmlspecialchars($r['nama_kelas']); ?></div>
                </td>
                <td><?php echo htmlspecialchars($r['nama_kategori']); ?></td>
                <td><?php echo htmlspecialchars($r['keterangan']); ?></td>
                <td style="font-weight: bold; color: <?php echo $r['tipe'] == 'pelanggaran' ? '#b91c1c' : '#15803d'; ?>;">
                    <?php echo $r['tipe'] == 'pelanggaran' ? '+' : '-'; ?><?php echo $r['poin']; ?>
                </td>
                <td style="font-size:0.85rem; color:#64748b;"><?php echo htmlspecialchars($r['nama_guru']); ?></td>
                <td>
                    <a href="<?php echo \App\Core\Helper::url('/bk/pelanggaran/delete/' . $r['id']); ?>" class="z-btn" style="background:#fee2e2; color:#dc2626; padding: 4px 8px; font-size:0.8rem;" onclick="return confirm('Hapus riwayat poin ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($riwayat)): ?>
                <tr><td colspan="7" style="text-align:center;">Belum ada catatan poin.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Form -->
<div id="modalAdd" class="z-modal" style="display: none; position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div style="background: white; border-radius: 12px; width: 500px; padding: 1.5rem; max-height: 90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:1rem; margin-bottom:1rem;">
            <h3 style="margin:0; font-size:1.1rem;">Input Poin Baru</h3>
            <button onclick="document.getElementById('modalAdd').style.display='none'" style="background:none; border:none; cursor:pointer;"><i data-lucide="x"></i></button>
        </div>
        <form action="<?php echo \App\Core\Helper::url('/bk/pelanggaran/save'); ?>" method="POST">
            
            <div class="z-form-group">
                <label class="z-label">Tanggal</label>
                <input type="date" name="tanggal" class="z-input" required value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="z-form-group">
                <label class="z-label">Pilih Siswa</label>
                <!-- Dalam aplikasi nyata, gunakan select2/selectize untuk search -->
                <select name="siswa_id" class="z-input" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php foreach($siswaList as $s): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nama_siswa']) . ' (' . htmlspecialchars($s['nama_kelas']) . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="z-form-group">
                <label class="z-label">Kategori Poin</label>
                <select name="kategori_id" class="z-input" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach($kategoriList as $k): ?>
                        <option value="<?php echo $k['id']; ?>">
                            [<?php echo $k['tipe'] == 'pelanggaran' ? 'Pelanggaran' : 'Prestasi'; ?>] <?php echo htmlspecialchars($k['nama_kategori']); ?> (Poin: <?php echo $k['poin']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="z-form-group">
                <label class="z-label">Keterangan Tambahan (Opsional)</label>
                <textarea name="keterangan" class="z-input" rows="3" placeholder="Misal: Terlambat 15 menit, dipulangkan dll"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1.5rem;">
                <button type="button" class="z-btn z-btn-secondary" onclick="document.getElementById('modalAdd').style.display='none'">Batal</button>
                <button type="submit" class="z-btn z-btn-primary">Simpan Poin</button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php'; 
?>
