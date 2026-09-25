<?php use App\Core\Helper; ?>
<div class="floating-card">
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="edit-3" style="color: #bfdbfe;"></i> Form Input Nilai Pengajar Guru
        </h1>
        <p class="mph-subtitle">Form khusus guru mata pelajaran untuk penginputan nilai harian siswa.</p>
    </div>
    <div class="mph-actions">
        <a href="<?php echo Helper::url('/siakad/nilai/template'); ?>" class="btn btn-primary-white" style="text-decoration:none;">
            <i data-lucide="download"></i> Download Template Excel
        </a>
    </div>
</div>

    <form method="GET" action="<?php echo Helper::url('/siakad/nilai/input'); ?>" style="display:flex; gap:12px; margin-bottom: 1.25rem; flex-wrap:wrap;">
        <select name="kelas_id" class="form-select" style="max-width: 220px;" onchange="this.form.submit()">
            <?php foreach($kelasList as $k): ?>
                <option value="<?php echo $k['id']; ?>" <?php echo $kelasId == $k['id'] ? 'selected' : ''; ?>>
                    Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="mapel_id" class="form-select" style="max-width: 260px;" onchange="this.form.submit()">
            <?php foreach($mapelList as $m): ?>
                <option value="<?php echo $m['id']; ?>" <?php echo $mapelId == $m['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($m['nama_mapel']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <form method="POST" action="<?php echo Helper::url('/siakad/nilai/save'); ?>">
        <input type="hidden" name="kelas_id" value="<?php echo $kelasId; ?>">
        <input type="hidden" name="mapel_id" value="<?php echo $mapelId; ?>">

        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 150px;">Nilai Harian / Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($nilaiList as $n): ?>
                    <tr>
                        <td style="font-weight:700; color: var(--primary-blue);"><?php echo htmlspecialchars($n['nis']); ?></td>
                        <td style="font-weight:700;"><?php echo htmlspecialchars($n['nama']); ?></td>
                        <td>
                            <input type="number" step="0.01" name="nilai[<?php echo $n['siswa_id']; ?>]" class="form-input" value="<?php echo $n['nilai'] ?? ''; ?>" placeholder="0.00" min="0" max="100" style="text-align:center; font-weight:800;">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn-primary">
                <i data-lucide="save"></i> Simpan Penilaian Guru
            </button>
        </div>
    </form>
</div>
