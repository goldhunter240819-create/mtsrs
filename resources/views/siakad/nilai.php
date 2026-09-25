<?php use App\Core\Helper; ?>
<div class="floating-card">
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="award" style="color: #bfdbfe;"></i> Penilaian Akademik Siswa
        </h1>
        <p class="mph-subtitle">Input dan perbarui nilai harian siswa per mata pelajaran dan kelas.</p>
    </div>
</div>

    <form method="GET" action="<?php echo Helper::url('/siakad/nilai'); ?>" style="display:flex; gap:12px; margin-top:1rem; flex-wrap:wrap;">
        <select name="kelas_id" class="form-select" style="max-width: 220px;" onchange="this.form.submit()">
            <?php foreach($kelasList as $k): ?>
                <option value="<?php echo $k['id']; ?>" <?php echo $kelasId == $k['id'] ? 'selected' : ''; ?>>
                    Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="mapel_id" class="form-select" style="max-width: 280px;" onchange="this.form.submit()">
            <?php foreach($mapelList as $m): ?>
                <option value="<?php echo $m['id']; ?>" <?php echo $mapelId == $m['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($m['nama_mapel']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="floating-card">
    <form method="POST" action="<?php echo Helper::url('/siakad/nilai/save'); ?>">
        <input type="hidden" name="kelas_id" value="<?php echo $kelasId; ?>">
        <input type="hidden" name="mapel_id" value="<?php echo $mapelId; ?>">

        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 180px;">Nilai Harian (0-100)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($nilaiList)): ?>
                    <tr>
                        <td colspan="3" style="text-align:center; color: var(--text-muted); padding: 2rem;">Tidak ada siswa di kelas ini.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach($nilaiList as $n): ?>
                    <tr>
                        <td style="font-weight:700; color: var(--primary-blue);"><?php echo htmlspecialchars($n['nis']); ?></td>
                        <td style="font-weight:700;"><?php echo htmlspecialchars($n['nama']); ?></td>
                        <td>
                            <input type="number" step="0.01" min="0" max="100" name="nilai[<?php echo $n['siswa_id']; ?>]" class="form-input" value="<?php echo $n['nilai'] !== null ? floatval($n['nilai']) : ''; ?>" placeholder="0-100">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($nilaiList)): ?>
        <div style="display:flex; justify-content:flex-end; margin-top: 1.5rem;">
            <button type="submit" class="btn-primary"><i data-lucide="save"></i> Simpan Penilaian</button>
        </div>
        <?php endif; ?>
    </form>
</div>
