<div class="z-card" style="padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Jurnal Mengajar & Absensi Kelas</h2>
    <p style="color: var(--z-muted); font-size: 0.95rem;">Catat materi harian yang diajarkan dan rekap siswa yang tidak hadir.</p>
</div>

<div class="z-card" style="margin-bottom: 2rem; padding: 1.5rem;">
    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem;">Isi Jurnal Baru</h3>
    <form action="<?php echo \App\Core\Helper::url('/guru/jurnal/save'); ?>" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Tanggal</label>
                <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" class="form-control" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Kelas</label>
                <select name="kelas_id" class="form-control" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <option value="">Pilih Kelas</option>
                    <?php foreach ($kelas_mengajar as $k): ?>
                        <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Mata Pelajaran</label>
                <select name="mapel_id" class="form-control" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <option value="">Pilih Mapel</option>
                    <?php foreach ($mapel_mengajar as $m): ?>
                        <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Materi Pokok yang Diajarkan</label>
            <textarea name="materi" class="form-control" rows="3" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #cbd5e1;" placeholder="Contoh: Bab 1: Sistem Persamaan Linear Dua Variabel"></textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Catatan Siswa Absen / Hambatan (Opsional)</label>
            <textarea name="siswa_absen" class="form-control" rows="2" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #cbd5e1;" placeholder="Contoh: Budi (Sakit), Andi (Izin)"></textarea>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 700; border-radius: 8px; background: var(--z-primary); color: white; border: none; cursor: pointer;">Simpan Jurnal</button>
        </div>
    </form>
</div>

<div class="z-card" style="padding: 0;">
    <div style="padding: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05);">
        <h3 style="font-size: 1.2rem; font-weight: 700;">Riwayat Jurnal Anda</h3>
    </div>
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
                    <tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--z-muted);">Belum ada riwayat jurnal.</td></tr>
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
</div>
