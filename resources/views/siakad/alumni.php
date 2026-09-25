<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="graduation-cap" style="color: #bfdbfe;"></i> Data Alumni Siswa
        </h1>
        <p class="mph-subtitle">Arsip rekam jejak siswa lulusan MTs Roudlotus Sholihin.</p>
    </div>
</div>

<div class="z-panel">
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">NIS</th>
                    <th style="width: 120px;">NISN</th>
                    <th>Nama Alumni</th>
                    <th>Jenis Kelamin</th>
                    <th>Kelas Terakhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($alumniList)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; color: var(--z-muted); padding: 2rem;">Belum ada data alumni tersimpan.</td>
                </tr>
                <?php endif; ?>
                <?php foreach($alumniList as $idx => $a): ?>
                <tr>
                    <td><?php echo $idx + 1; ?></td>
                    <td style="font-weight:800; color: var(--z-primary);"><?php echo htmlspecialchars($a['nis']); ?></td>
                    <td style="font-family:monospace;"><?php echo htmlspecialchars($a['nisn'] ?: '-'); ?></td>
                    <td style="font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($a['nama']); ?></td>
                    <td><?php echo $a['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                    <td><span class="pill pill-blue"><?php echo htmlspecialchars($a['nama_kelas'] ?? '-'); ?></span></td>
                    <td><span class="pill pill-green"><?php echo htmlspecialchars($a['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
