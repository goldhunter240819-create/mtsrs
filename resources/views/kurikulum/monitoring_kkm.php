<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="target" style="color: #bfdbfe;"></i> Monitoring KKM
        </h1>
        <p class="mph-subtitle">Kelola dan pantau Kriteria Ketuntasan Minimal tiap mata pelajaran.</p>
    </div>
</div>

<div class="z-card">
    <form id="formKkm" method="POST" action="<?= \App\Core\Helper::url('/kurikulum/monitoring-kkm/save') ?>">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; align-items: center; padding: 20px 20px 0 20px; flex-wrap: wrap; gap: 15px;">
            <p style="color: #64748b; font-size: 0.9rem; font-weight: 500; margin: 0;">Tentukan nilai KKM dan Bobot Nilai Rapor secara global.</p>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 5px; font-size: 0.85rem; background: #eff6ff; padding: 6px 12px; border-radius: 8px; border: 1px solid #bfdbfe;">
                    <label style="color: #1e3a8a; font-weight: 600;">Bobot Harian (PH+PTS) %:</label>
                    <input type="number" name="bobot_harian" value="<?= $institusi['bobot_harian'] ?? 50 ?>" style="width: 60px; padding: 4px; border: 1px solid #93c5fd; border-radius: 4px; text-align: center;">
                    <label style="color: #1e3a8a; font-weight: 600; margin-left: 10px;">Sumatif (PAS) %:</label>
                    <input type="number" name="bobot_pas" value="<?= $institusi['bobot_pas'] ?? 50 ?>" style="width: 60px; padding: 4px; border: 1px solid #93c5fd; border-radius: 4px; text-align: center;">
                </div>
                <button type="submit" class="z-btn z-btn-primary" id="btnSimpanKkm" style="padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; height: 38px;">
                    <i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Pengaturan
                </button>
            </div>
        </div>

        <div class="z-table-wrap">
            <table class="z-table">
                <thead>
                    <tr>
                        <th style="padding-left: 20px;">Kelompok</th>
                        <th>Mata Pelajaran</th>
                        <th style="width: 120px; text-align: center;">KKM Kelas 7</th>
                        <th style="width: 120px; text-align: center;">KKM Kelas 8</th>
                        <th style="width: 120px; text-align: center; padding-right: 20px;">KKM Kelas 9</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mapels)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 2rem; color: #64748b;">Tidak ada data mata pelajaran.</td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $current_kelompok = '';
                        foreach ($mapels as $mapel): 
                            if ($current_kelompok != $mapel['kelompok']) {
                                $current_kelompok = $mapel['kelompok'];
                                echo "<tr><td colspan='5' style='background: #f8fafc; font-weight: bold; color: #475569; padding-left: 20px;'>Kelompok " . htmlspecialchars($current_kelompok) . "</td></tr>";
                            }
                        ?>
                        <tr>
                            <td style="padding-left: 20px; vertical-align: middle;">
                                <span style="background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Kel. <?= htmlspecialchars($mapel['kelompok']) ?></span>
                            </td>
                            <td style="font-weight: 600; color: #334155; vertical-align: middle;">
                                <?= htmlspecialchars($mapel['nama_mapel']) ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <input type="number" name="kkm[<?= $mapel['id'] ?>][7]" class="z-input" style="width: 80px; text-align: center;" min="0" max="100" value="<?= $kkm_data[$mapel['id']][7] ?? '' ?>" placeholder="-">
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <input type="number" name="kkm[<?= $mapel['id'] ?>][8]" class="z-input" style="width: 80px; text-align: center;" min="0" max="100" value="<?= $kkm_data[$mapel['id']][8] ?? '' ?>" placeholder="-">
                            </td>
                            <td style="text-align: center; vertical-align: middle; padding-right: 20px;">
                                <input type="number" name="kkm[<?= $mapel['id'] ?>][9]" class="z-input" style="width: 80px; text-align: center;" min="0" max="100" value="<?= $kkm_data[$mapel['id']][9] ?? '' ?>" placeholder="-">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script>
document.getElementById('formKkm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const bobotHarian = document.querySelector('input[name="bobot_harian"]').value;
    const bobotPas = document.querySelector('input[name="bobot_pas"]').value;
    
    if (parseInt(bobotHarian) + parseInt(bobotPas) !== 100) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Total persentase bobot Harian + PAS harus 100%!'
        });
        return;
    }

    const btn = document.getElementById('btnSimpanKkm');
    btn.disabled = true;
    btn.innerHTML = '<i class="lucide-loader" style="width: 16px; height: 16px; animation: spin 1s linear infinite;"></i> Menyimpan...';

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Semua KKM';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    })
    .catch(error => {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: 'Gagal menyimpan data ke server.'
        });
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Semua KKM';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
});

// CSS for spinner
const style = document.createElement('style');
style.innerHTML = `
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
`;
document.head.appendChild(style);
</script>
