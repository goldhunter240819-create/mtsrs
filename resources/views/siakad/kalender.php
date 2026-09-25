<?php use App\Core\Helper; ?>
<div class="floating-card">
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="calendar-days" style="color: #bfdbfe;"></i> Kalender Pendidikan Akademik
        </h1>
        <p class="mph-subtitle">Agenda kegiatan, jadwal ujian, dan hari libur madrasah MTs RS.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="document.getElementById('modalKalender').style.display='flex'">
            <i data-lucide="plus"></i> Agenda Baru
        </button>
    </div>
</div>

    <div class="table-responsive">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Rentang Tanggal</th>
                    <th>Nama Agenda / Kegiatan</th>
                    <th>Kategori Agenda</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($kalenderList as $idx => $k): ?>
                <tr>
                    <td><?php echo $idx + 1; ?></td>
                    <td style="font-family:monospace; font-weight:700; color: var(--primary-blue);">
                        <?php echo htmlspecialchars($k['tanggal_mulai']); ?> s/d <?php echo htmlspecialchars($k['tanggal_selesai']); ?>
                    </td>
                    <td style="font-weight:700;">
                        <?php echo htmlspecialchars($k['kegiatan']); ?>
                        <?php if ($k['kategori'] === 'Pulang Dipercepat' && !empty($k['jam_pulang'])): ?>
                            <br><small style="color: #64748b;">Jam: <?= substr($k['jam_pulang'], 0, 5) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($k['kategori'] === 'Ujian'): ?>
                            <span class="badge-pill badge-amber">Ujian</span>
                        <?php elseif ($k['kategori'] === 'Libur'): ?>
                            <span class="badge-pill" style="background:rgba(244,63,94,0.12); color:var(--accent-rose); border:1px solid rgba(244,63,94,0.3);">Libur</span>
                        <?php else: ?>
                            <span class="badge-pill badge-live"><?php echo htmlspecialchars($k['kategori']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <button class="btn btn-sm" style="padding:6px; background:#f1f5f9; color:#3b82f6; border:1px solid #cbd5e1; border-radius:6px;" 
                                onclick="editKalender(<?= $k['id'] ?>, '<?= $k['tanggal_mulai'] ?>', '<?= $k['tanggal_selesai'] ?>', '<?= htmlspecialchars(addslashes($k['kegiatan'])) ?>', '<?= $k['kategori'] ?>', '<?= $k['jam_pulang'] ? substr($k['jam_pulang'], 0, 5) : '' ?>')">
                                <i data-lucide="edit" style="width:16px; height:16px;"></i>
                            </button>
                            <button class="btn btn-sm" style="padding:6px; background:#fef2f2; color:#ef4444; border:1px solid #fecaca; border-radius:6px;" 
                                onclick="deleteKalender(<?= $k['id'] ?>)">
                                <i data-lucide="trash-2" style="width:16px; height:16px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalKalender" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(8px); z-index:1000; align-items:center; justify-content:center;">
    <div class="floating-card" style="width:100%; max-width:480px;">
        <h3 style="font-weight:800; margin-bottom:1rem;">Tambah Agenda Kalender Pendidikan</h3>
        <form method="POST" action="<?php echo Helper::url('/siakad/kalender/save'); ?>">
            <div class="form-group">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Kegiatan / Agenda</label>
                <input type="text" name="kegiatan" class="form-input" placeholder="Contoh: Penilaian Tengah Semester (PTS)" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="kategori" id="selectKategori" class="form-select" onchange="toggleJamPulang()">
                    <option value="KBM">KBM (Kegiatan Belajar)</option>
                    <option value="Ujian">Ujian / Evaluasi</option>
                    <option value="Kegiatan" selected>Kegiatan Madrasah</option>
                    <option value="Pulang Dipercepat">Pulang Dipercepat / Kegiatan</option>
                    <option value="Libur">Hari Libur</option>
                </select>
            </div>
            <div class="form-group" id="jamPulangGroup" style="display:none;">
                <label class="form-label">Jam Pulang Dipercepat</label>
                <input type="time" name="jam_pulang" class="form-input">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:1.5rem;">
                <button type="button" class="btn" style="background:#f1f5f9; color:#64748b;" onclick="document.getElementById('modalKalender').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEditKalender" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(8px); z-index:1000; align-items:center; justify-content:center;">
    <div class="floating-card" style="width:100%; max-width:480px;">
        <h3 style="font-weight:800; margin-bottom:1rem;">Edit Agenda Kalender Pendidikan</h3>
        <form method="POST" action="<?php echo Helper::url('/siakad/kalender/update'); ?>">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" id="edit_mulai" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" id="edit_selesai" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Kegiatan / Agenda</label>
                <input type="text" name="kegiatan" id="edit_kegiatan" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="kategori" id="edit_kategori" class="form-select" onchange="toggleEditJamPulang()">
                    <option value="KBM">KBM (Kegiatan Belajar)</option>
                    <option value="Ujian">Ujian / Evaluasi</option>
                    <option value="Kegiatan">Kegiatan Madrasah</option>
                    <option value="Pulang Dipercepat">Pulang Dipercepat / Kegiatan</option>
                    <option value="Libur">Hari Libur</option>
                </select>
            </div>
            <div class="form-group" id="edit_jamPulangGroup" style="display:none;">
                <label class="form-label">Jam Pulang Dipercepat</label>
                <input type="time" name="jam_pulang" id="edit_jam_pulang" class="form-input">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:1.5rem;">
                <button type="button" class="btn" style="background:#f1f5f9; color:#64748b;" onclick="document.getElementById('modalEditKalender').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleJamPulang() {
        const select = document.getElementById('selectKategori');
        const group = document.getElementById('jamPulangGroup');
        group.style.display = (select.value === 'Pulang Dipercepat') ? 'block' : 'none';
    }
    
    function toggleEditJamPulang() {
        const select = document.getElementById('edit_kategori');
        const group = document.getElementById('edit_jamPulangGroup');
        group.style.display = (select.value === 'Pulang Dipercepat') ? 'block' : 'none';
    }

    function editKalender(id, mulai, selesai, kegiatan, kategori, jam) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_mulai').value = mulai;
        document.getElementById('edit_selesai').value = selesai;
        document.getElementById('edit_kegiatan').value = kegiatan;
        document.getElementById('edit_kategori').value = kategori;
        document.getElementById('edit_jam_pulang').value = jam;
        toggleEditJamPulang();
        document.getElementById('modalEditKalender').style.display = 'flex';
    }

    function deleteKalender(id) {
        Swal.fire({
            title: 'Hapus Agenda?',
            text: "Agenda ini akan dihapus permanen dari kalender.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= Helper::url('/siakad/kalender/delete') ?>?id=' + id;
            }
        });
    }
</script>
