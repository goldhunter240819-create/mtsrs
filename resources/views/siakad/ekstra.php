<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="activity" style="color: #bfdbfe;"></i> Data Ekstrakurikuler
        </h1>
        <p class="mph-subtitle">Daftar kegiatan pengembangan diri & ekstrakurikuler siswa MTs RS.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="document.getElementById('modalEkstra').style.display='flex'">
            <i data-lucide="plus-circle"></i> Tambah Ekstra
        </button>
    </div>
</div>

<div class="z-panel">
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Ekstrakurikuler</th>
                    <th>Guru Pembina</th>
                    <th>Hari Latihan</th>
                    <th>Jam Kegiatan</th>
                    <th style="text-align: center; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($ekstraList)): ?>
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--z-muted);">Belum ada data Ekstrakurikuler.</td></tr>
                <?php else: ?>
                    <?php foreach($ekstraList as $idx => $e): ?>
                    <tr>
                        <td><?php echo $idx + 1; ?></td>
                        <td style="font-weight:800; color: var(--z-accent);"><?php echo htmlspecialchars($e['nama_ekstra']); ?></td>
                        <td style="font-weight:600; color:var(--z-text);"><?php echo htmlspecialchars($e['nama_pembina'] ?: '-'); ?></td>
                        <td><span class="pill pill-blue"><?php echo htmlspecialchars($e['hari']); ?></span></td>
                        <td style="font-size:0.82rem; font-family:monospace; color:var(--z-muted);"><?php echo htmlspecialchars($e['jam_kegiatan']); ?></td>
                        <td>
                            <div class="z-action-btns">
                                <a href="#" class="action-btn delete-btn" title="Hapus" onclick="return confirm('Hapus kegiatan ekstra?')">
                                    <i data-lucide="trash-2"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Ekstra -->
<div id="modalEkstra" class="z-modal-ov" style="display:none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="activity"></i> Tambah Ekstrakurikuler Baru</div>
            <div class="z-modal-close" onclick="document.getElementById('modalEkstra').style.display='none'"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formEkstra" method="POST" action="<?php echo Helper::url('/siakad/ekstra/save'); ?>">
                <div class="z-form-group">
                    <label class="z-label">Nama Ekstrakurikuler</label>
                    <input type="text" name="nama_ekstra" class="z-field" required placeholder="Contoh: Pramuka, Hadrah, PMR">
                </div>
                <div class="z-form-group">
                    <label class="z-label">Guru Pembina</label>
                    <select name="pembina_id" class="z-field">
                        <option value="">-- Pilih Pembina --</option>
                        <?php foreach($guruList as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-grid2">
                    <div class="z-form-group">
                        <label class="z-label">Hari Latihan</label>
                        <select name="hari" class="z-field">
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Sabtu" selected>Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Jam Kegiatan</label>
                        <input type="text" name="jam_kegiatan" class="z-field" value="14:00 - 16:00">
                    </div>
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="document.getElementById('modalEkstra').style.display='none'">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formEkstra').submit()">Simpan Data</button>
        </div>
    </div>
</div>
