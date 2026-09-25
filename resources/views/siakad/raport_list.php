<?php use App\Core\Helper; ?>
<div class="floating-card">
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="printer" style="color: #bfdbfe;"></i> Cetak Leger & Rapor Siswa
        </h1>
        <p class="mph-subtitle">Hasil evaluasi hasil belajar siswa dan pencetakan Rapor Akademik.</p>
    </div>
</div>

    <form method="GET" action="<?php echo Helper::url('/siakad/raport'); ?>" style="display:flex; gap:12px; margin-bottom: 1.25rem;">
        <select name="kelas_id" class="form-select" style="max-width: 220px;" onchange="this.form.submit()">
            <?php foreach($kelasList as $k): ?>
                <option value="<?php echo $k['id']; ?>" <?php echo $kelasId == $k['id'] ? 'selected' : ''; ?>>
                    Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="table-responsive">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Nilai Rata-rata</th>
                    <th>Aksi Cetak</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($siswaList as $s): ?>
                <tr>
                    <td style="font-weight:700; color: var(--primary-blue);"><?php echo htmlspecialchars($s['nis']); ?></td>
                    <td style="font-weight:700;"><?php echo htmlspecialchars($s['nama']); ?></td>
                    <td><span class="badge-pill badge-primary">Kelas <?php echo htmlspecialchars($s['nama_kelas']); ?></span></td>
                    <td style="font-weight:800; color: var(--accent-mint);"><?php echo round($s['rata_nilai'] ?: 0); ?></td>
                    <td>
                        <a href="<?php echo Helper::url('/siakad/raport/cetak/' . $s['id']); ?>" target="_blank" class="btn-secondary" style="padding:4px 12px; font-size:0.8rem;">
                            <i data-lucide="printer" style="width:14px;height:14px;"></i> Cetak Rapor
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
