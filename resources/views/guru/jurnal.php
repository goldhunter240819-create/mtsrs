<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="book-open" style="color: #bfdbfe;"></i> Jurnal Mengajar & Absensi Kelas
        </h1>
        <p class="mph-subtitle">Catat materi harian yang diajarkan dan rekap siswa yang tidak hadir.</p>
    </div>
</div>


<div class="z-card" style="padding: 0;">
    <div style="padding: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <h3 style="font-size: 1.2rem; font-weight: 700; margin: 0;">Riwayat Jurnal Anda</h3>
        
        <form method="GET" style="margin: 0; width: 100%; max-width: 600px; display: flex; gap: 10px;">
            <select name="kelas_id" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; width: 100%; font-size: 0.9rem;" onchange="this.form.mapel_id ? this.form.mapel_id.value='' : null; this.form.submit()">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_mengajar as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($filter_kelas_id == $k['id']) ? 'selected' : '' ?>>
                        Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="mapel_id" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; width: 100%; font-size: 0.9rem;" onchange="this.form.submit()" <?= empty($mapel_mengajar) ? 'disabled' : '' ?>>
                <option value="">-- Pilih Mata Pelajaran --</option>
                <?php foreach ($mapel_mengajar as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($filter_mapel_id == $m['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nama_mapel']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($filter_kelas_id > 0 && $filter_mapel_id > 0): ?>
        <div style="overflow-x: auto; padding: 0 1.5rem 1.5rem 1.5rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; margin-top: 1rem;">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(0,0,0,0.1); color: var(--z-muted);">
                        <th style="padding: 1rem 0.5rem;">Tanggal</th>
                        <th style="padding: 1rem 0.5rem;">Kelas</th>
                        <th style="padding: 1rem 0.5rem;">Mapel</th>
                        <th style="padding: 1rem 0.5rem;">Materi</th>
                        <th style="padding: 1rem 0.5rem;">Siswa Absen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jurnals)): ?>
                        <tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--z-muted);">Belum ada riwayat jurnal untuk kelas & mapel ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($jurnals as $j): ?>
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <td style="padding: 1rem 0.5rem; font-weight: 600;"><?php echo date('d/m/Y', strtotime($j['tanggal'])); ?></td>
                                <td style="padding: 1rem 0.5rem;"><span style="background: rgba(37,99,235,0.1); color: var(--z-primary); padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.85rem;"><?php echo htmlspecialchars($j['nama_kelas']); ?></span></td>
                                <td style="padding: 1rem 0.5rem; font-weight: 600;"><?php echo htmlspecialchars($j['nama_mapel']); ?></td>
                                <td style="padding: 1rem 0.5rem;"><?php echo nl2br(htmlspecialchars($j['materi'])); ?></td>
                                <td style="padding: 1rem 0.5rem; color: #ef4444; font-size: 0.9rem;"><?php echo htmlspecialchars($j['siswa_absen']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
            <i data-lucide="filter" style="width: 40px; height: 40px; color: #94a3b8; margin-bottom: 10px;"></i>
            <p style="margin: 0; color: #64748b; font-weight: 500;">Silakan pilih Kelas dan Mata Pelajaran untuk melihat riwayat jurnal.</p>
        </div>
    <?php endif; ?>
</div>
