<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="arrow-right-left" style="color: #bfdbfe;"></i> Mutasi Siswa (Masuk / Keluar)
        </h1>
        <p class="mph-subtitle">Pencatatan siswa mutasi masuk, keluar, lulus, atau DO di MTs RS.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="tambahMutasi()">
            <i data-lucide="plus-circle"></i> Catat Mutasi
        </button>
    </div>
</div>

<div class="z-panel">
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th style="width: 120px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th>Jenis Mutasi</th>
                    <th>Sekolah Asal / Tujuan</th>
                    <th>Alasan Mutasi</th>
                    <th style="width: 80px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mutasiList)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; color: var(--z-muted); padding: 2rem;">Belum ada catatan mutasi siswa.</td>
                </tr>
                <?php endif; ?>
                <?php foreach($mutasiList as $idx => $m): ?>
                <tr>
                    <td><?php echo $idx + 1; ?></td>
                    <td style="font-family:monospace;"><?php echo htmlspecialchars($m['tanggal_mutasi']); ?></td>
                    <td style="font-weight:800; color: var(--z-primary);"><?php echo htmlspecialchars($m['nis']); ?></td>
                    <td style="font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($m['nama_siswa']); ?></td>
                    <td>
                        <?php if ($m['jenis_mutasi'] === 'Masuk'): ?>
                            <span class="pill pill-green">Masuk</span>
                        <?php else: ?>
                            <span class="pill pill-amber"><?php echo htmlspecialchars($m['jenis_mutasi']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="color:var(--z-text);"><?php echo htmlspecialchars($m['sekolah_asal_tujuan'] ?: '-'); ?></td>
                    <td style="font-size:0.85rem; color: var(--z-muted);"><?php echo htmlspecialchars($m['alasan'] ?: '-'); ?></td>
                    <td style="text-align: center;">
                        <button class="btn btn-icon btn-outline" style="border-radius: 8px; padding: 6px;" title="Edit" onclick="editMutasi(<?php echo htmlspecialchars(json_encode([
                            'id' => $m['id'],
                            'siswa_id' => $m['siswa_id'],
                            'jenis' => $m['jenis_mutasi'],
                            'tanggal' => $m['tanggal_mutasi'],
                            'sekolah' => $m['sekolah_asal_tujuan'],
                            'alasan' => $m['alasan']
                        ])); ?>)">
                            <i data-lucide="edit-3" style="width:16px; height:16px;"></i>
                        </button>
                        <a href="<?php echo Helper::url('/siakad/mutasi/delete/'.$m['id']); ?>" class="btn btn-icon btn-outline" style="border-radius: 8px; padding: 6px; color: var(--z-danger); border-color: rgba(239, 68, 68, 0.2);" title="Hapus" onclick="return confirm('Yakin ingin menghapus catatan mutasi ini?');">
                            <i data-lucide="trash-2" style="width:16px; height:16px;"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalMutasi" class="z-modal-ov" style="display:none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title" id="modalMutasiTitle"><i data-lucide="arrow-right-left"></i> Catat Mutasi Siswa</div>
            <div class="z-modal-close" onclick="document.getElementById('modalMutasi').style.display='none'"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formMutasi" method="POST" action="<?php echo Helper::url('/siakad/mutasi/save'); ?>">
                <input type="hidden" name="id" id="mutasi_id" value="">
                <div class="z-form-group">
                    <label class="z-label">Pilih Siswa</label>
                    <select name="siswa_id" class="z-field" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php foreach($siswaList as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nis']); ?> - <?php echo htmlspecialchars($s['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-grid2">
                    <div class="z-form-group">
                        <label class="z-label">Jenis Mutasi</label>
                        <select name="jenis_mutasi" class="z-field" required>
                            <option value="Masuk">Mutasi Masuk</option>
                            <option value="Keluar">Mutasi Keluar</option>
                            <option value="Lulus">Lulus Sekolah</option>
                            <option value="DO">Drop Out (DO)</option>
                        </select>
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Tanggal Mutasi</label>
                        <input type="date" name="tanggal_mutasi" class="z-field" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Sekolah Asal / Tujuan</label>
                    <input type="text" name="sekolah_asal_tujuan" class="z-field" placeholder="Contoh: SMPN 1 Singojuru">
                </div>
                <div class="z-form-group">
                    <label class="z-label">Alasan Mutasi</label>
                    <textarea name="alasan" class="z-field" rows="2" placeholder="Catatan alasan pindah / mutasi..."></textarea>
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="document.getElementById('modalMutasi').style.display='none'">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formMutasi').submit()">Simpan Data</button>
        </div>
    </div>
</div>

<script>
function tambahMutasi() {
    document.getElementById('modalMutasiTitle').innerHTML = '<i data-lucide="arrow-right-left"></i> Catat Mutasi Siswa';
    document.getElementById('formMutasi').reset();
    document.getElementById('mutasi_id').value = '';
    document.getElementById('modalMutasi').style.display = 'flex';
}

function editMutasi(data) {
    document.getElementById('modalMutasiTitle').innerHTML = '<i data-lucide="edit"></i> Edit Data Mutasi';
    document.getElementById('mutasi_id').value = data.id;
    document.querySelector('select[name="siswa_id"]').value = data.siswa_id;
    document.querySelector('select[name="jenis_mutasi"]').value = data.jenis;
    document.querySelector('input[name="tanggal_mutasi"]').value = data.tanggal;
    document.querySelector('input[name="sekolah_asal_tujuan"]').value = data.sekolah;
    document.querySelector('textarea[name="alasan"]').value = data.alasan;
    
    document.getElementById('modalMutasi').style.display = 'flex';
}
</script>
