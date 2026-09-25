<div class="z-card" style="padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <a href="/guru/nilai-harian" class="btn" style="margin-bottom: 10px; display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; color: #475569; text-decoration: none;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Kembali
        </a>
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Input Nilai: <?= htmlspecialchars($jenis_evaluasi) ?></h2>
        <p style="color: var(--z-muted); font-size: 0.95rem; margin: 0;">
            Kelas: <b><?= htmlspecialchars($nama_kelas) ?></b> | Mapel: <b><?= htmlspecialchars($nama_mapel) ?></b>
        </p>
    </div>
    
    <div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('formNilai').submit();" style="padding: 0.75rem 1.5rem; font-weight: 600; border-radius: 8px; background: var(--z-primary); color: white; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="save"></i> Simpan Semua Nilai
        </button>
    </div>
</div>

<div class="z-card" style="padding: 1.5rem;">
    <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;">
        <p style="margin: 0; color: #1d4ed8; font-size: 0.85rem;">
            <b>Tips:</b> Gunakan tombol <kbd style="background: #fff; padding: 2px 6px; border-radius: 4px; border: 1px solid #bfdbfe; font-size: 0.8rem;">Tab</kbd> pada keyboard Anda untuk pindah ke baris nilai siswa berikutnya secara cepat (seperti Excel).
        </p>
    </div>

    <form action="/guru/nilai-harian/save" method="POST" id="formNilai">
        <input type="hidden" name="kelas_id" value="<?= $kelas_id ?>">
        <input type="hidden" name="mapel_id" value="<?= $mapel_id ?>">
        <input type="hidden" name="jenis_evaluasi" value="<?= htmlspecialchars($jenis_evaluasi) ?>">
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; color: var(--z-text);">Materi Pokok / Judul Evaluasi (Berlaku untuk 1 kelas) <span style="color:red">*</span></label>
            <input type="text" name="materi" class="z-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;" placeholder="Contoh: Bab 1: Sistem Persamaan Linear Dua Variabel" value="<?= htmlspecialchars($materi_exist) ?>" required>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="z-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 120px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 80px;">L/P</th>
                        <th style="width: 150px;">Nilai (0-100)</th>
                        <th>Catatan / Keterangan (Opsional)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($siswa_list)): ?>
                        <tr><td colspan="6" style="text-align: center;">Belum ada data siswa di kelas ini.</td></tr>
                    <?php else: ?>
                        <?php $no=1; foreach($siswa_list as $s): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($s['nis']) ?></td>
                            <td><span style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($s['nama']) ?></span></td>
                            <td><?= $s['jenis_kelamin'] == 'L' ? 'L' : 'P' ?></td>
                            <td>
                                <input type="number" name="nilai[<?= $s['id'] ?>]" min="0" max="100" class="z-input" style="width: 100%; text-align: center; font-weight: 700; padding: 8px;" placeholder="0-100" value="<?= $nilai_exist[$s['id']] ?? '' ?>">
                            </td>
                            <td>
                                <input type="text" name="keterangan[<?= $s['id'] ?>]" class="z-input" style="width: 100%; padding: 8px;" placeholder="Catatan khusus..." value="<?= htmlspecialchars($keterangan_exist[$s['id']] ?? '') ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 700; border-radius: 8px; background: var(--z-primary); color: white; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem;">
                <i data-lucide="save"></i> Simpan Semua Nilai
            </button>
        </div>
    </form>
</div>
