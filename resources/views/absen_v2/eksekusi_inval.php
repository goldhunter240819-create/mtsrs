<div class="modern-page-header" style="background: linear-gradient(135deg, #059669, #047857);">
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="clipboard-edit" style="color: rgba(255,255,255,0.8);"></i> Eksekusi Tugas Inval
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">
            Absensi siswa dan jurnal kelas untuk <?php echo htmlspecialchars($namaKelas); ?> — <?php echo htmlspecialchars($namaMapel); ?>
        </p>
    </div>
</div>

<div class="z-card" style="margin: 2rem auto; max-width: 900px; padding: 1.5rem;">
    
    <!-- Info Guru yang Izin/Alpa -->
    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 14px; padding: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #92400e; text-transform: uppercase;">Guru Mata Pelajaran</div>
                <div style="font-weight: 800; color: #0f172a; font-size: 1rem;"><?php echo htmlspecialchars($namaGuruIzin); ?></div>
                <div style="font-size: 0.8rem; color: #64748b;">Status: <strong style="color: #d97706;"><?php echo htmlspecialchars($sumberIzin ?? 'Izin'); ?></strong></div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; color: #64748b;"><?php echo date('l, d M Y', strtotime($tanggal)); ?></div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #0f172a;"><?php echo $jamMulai . ' - ' . $jamSelesai; ?></div>
            </div>
        </div>
    </div>

    <?php if (!empty($materiGuru)): ?>
        <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 14px; padding: 1rem; margin-bottom: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: #0369a1; text-transform: uppercase; margin-bottom: 6px;">📝 Tugas / Materi dari Guru</div>
            <div style="font-size: 0.9rem; color: #0c4a6e; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($materiGuru)); ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo Helper::url('/absen/eksekusi-inval'); ?>" id="formInval">
        <input type="hidden" name="pengajuan_id" value="<?php echo $pengajuanId; ?>">
        <input type="hidden" name="kelas_id" value="<?php echo $kelasId; ?>">
        <input type="hidden" name="mapel_id" value="<?php echo $mapelId; ?>">
        <input type="hidden" name="tanggal" value="<?php echo $tanggal; ?>">
        <input type="hidden" name="guru_izin_id" value="<?php echo $guruIzinId; ?>">
        
        <!-- Absensi Siswa -->
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="users" style="width:18px;height:18px;color:#2563eb;"></i> Absensi Siswa
        </h3>

        <div style="overflow-x: auto; margin-bottom: 1.5rem;">
            <table class="z-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Nama Siswa</th>
                        <th style="width: 140px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswaList as $idx => $s): ?>
                        <tr>
                            <td style="text-align: center;"><?php echo ($idx + 1); ?></td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($s['nama']); ?></div>
                                <div style="font-size: 0.75rem; color: #94a3b8;"><?php echo $s['nis']; ?></div>
                            </td>
                            <td>
                                <select name="absen[<?php echo $s['id']; ?>]" style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; font-size: 0.85rem;">
                                    <option value="Hadir" selected>Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Alpha">Alpha</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Jurnal Kelas -->
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="book-open" style="width:18px;height:18px;color:#059669;"></i> Jurnal Kelas
        </h3>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">Catatan / Materi yang Diajarkan</label>
            <textarea name="jurnal_materi" rows="4" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; font-size: 0.9rem; box-sizing: border-box; resize: vertical; font-family: inherit;" placeholder="Contoh: Mengerjakan tugas dari guru..."><?php echo htmlspecialchars($materiGuru ?? ''); ?></textarea>
        </div>

        <div style="background: #f1f5f9; border-radius: 10px; padding: 12px; margin-bottom: 1.5rem; font-size: 0.8rem; color: #475569;">
            <i data-lucide="info" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;color:#3b82f6;"></i>
            Jurnal akan tercatat atas nama <strong>Anda (Guru Piket)</strong> dengan catatan inval otomatis di sistem.
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i data-lucide="check-circle" style="width:18px;height:18px;"></i> Simpan Absensi & Jurnal
        </button>
    </form>
</div>
