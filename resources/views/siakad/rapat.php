<?php use App\Core\Helper; ?>
<div class="floating-card">
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="users-round" style="color: #bfdbfe;"></i> Daftar Hadir & Agenda Rapat Guru
        </h1>
        <p class="mph-subtitle">Pencatatan presensi kehadiran rapat dewan guru MTs Roudlotus Sholihin.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="document.getElementById('modalRapat').style.display='flex'">
            <i data-lucide="plus"></i> Agenda Rapat Baru
        </button>
    </div>
</div>

    <div class="table-responsive">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal & Waktu</th>
                    <th>Nama Rapat / Agenda</th>
                    <th>Tempat Pelaksanaan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rapatList as $idx => $r): ?>
                <tr>
                    <td><?php echo $idx + 1; ?></td>
                    <td style="font-family:monospace; font-weight:700; color: var(--primary-blue);">
                        <?php echo htmlspecialchars($r['tanggal']); ?> (<?php echo htmlspecialchars($r['waktu']); ?>)
                    </td>
                    <td style="font-weight:700;"><?php echo htmlspecialchars($r['nama_rapat']); ?></td>
                    <td><span class="badge-pill badge-primary"><?php echo htmlspecialchars($r['tempat']); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalRapat" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(8px); z-index:1000; align-items:center; justify-content:center;">
    <div class="floating-card" style="width:100%; max-width:480px;">
        <h3 style="font-weight:800; margin-bottom:1rem;">Tambah Agenda Rapat Guru</h3>
        <form method="POST" action="<?php echo Helper::url('/siakad/lain-lain/rapat/save'); ?>">
            <div class="form-group">
                <label class="form-label">Nama Rapat / Agenda</label>
                <input type="text" name="nama_rapat" class="form-input" placeholder="Contoh: Rapat Evaluasi Bulanan & Persiapan PTS" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Waktu Pelaksanaan</label>
                <input type="text" name="waktu" class="form-input" value="09:00 WIB">
            </div>
            <div class="form-group">
                <label class="form-label">Tempat</label>
                <input type="text" name="tempat" class="form-input" value="Ruang Guru MTs RS">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:1.5rem;">
                <button type="button" class="btn-secondary" onclick="document.getElementById('modalRapat').style.display='none'">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
