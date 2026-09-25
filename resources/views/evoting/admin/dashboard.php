<div class="modern-page-header" style="background: linear-gradient(135deg, #e11d48, #be123c);">
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="box-select" style="color: rgba(255,255,255,0.8);"></i> E-Voting Administrator
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Kelola event pemilihan, kandidat, dan pantau perolehan suara secara langsung.</p>
    </div>
</div>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 12px; margin-top: 2rem; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
        <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: 12px; margin-top: 2rem; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
        <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
    </div>
<?php endif; ?>

<div class="z-card" style="margin-top: 2rem; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Daftar Event Pemilihan</h2>
        <button onclick="document.getElementById('modal-add-event').style.display='flex'; lucide.createIcons();" class="btn btn-primary">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Buat Event Baru
        </button>
    </div>

    <div class="z-table-responsive">
        <table class="z-table">
            <thead>
                <tr>
                    <th>Nama Event</th>
                    <th>Jadwal</th>
                    <th>Status</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($events)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: #64748b;">
                        Belum ada event pemilihan yang dibuat.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($events as $e): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($e['nama_event']) ?></div>
                            <div style="font-size: 12px; color: #64748b;"><?= htmlspecialchars($e['deskripsi']) ?></div>
                        </td>
                        <td>
                            <div style="font-size: 12px; color: #475569;">
                                <strong>Mulai:</strong> <?= date('d M Y H:i', strtotime($e['tgl_mulai'])) ?><br>
                                <strong>Selesai:</strong> <?= date('d M Y H:i', strtotime($e['tgl_selesai'])) ?>
                            </div>
                        </td>
                        <td>
                            <?php if($e['status'] === 'Aktif'): ?>
                                <span class="pill pill-green">Aktif</span>
                            <?php elseif($e['status'] === 'Selesai'): ?>
                                <span class="pill pill-gray">Selesai</span>
                            <?php else: ?>
                                <span class="pill pill-yellow">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 5px;">
                                <a href="<?php echo \App\Core\Helper::url('/evoting/admin/candidates?event_id='.$e['id']); ?>" class="btn btn-sm btn-outline" title="Kelola Kandidat">
                                    <i data-lucide="users" style="width: 14px; height: 14px;"></i> Kandidat
                                </a>
                                <a href="<?php echo \App\Core\Helper::url('/evoting/admin/voters?event_id='.$e['id']); ?>" class="btn btn-sm btn-outline" title="Kelola Pemilih & Token">
                                    <i data-lucide="key" style="width: 14px; height: 14px;"></i> Pemilih
                                </a>
                                <a href="<?php echo \App\Core\Helper::url('/evoting/admin/live?event_id='.$e['id']); ?>" class="btn btn-sm btn-primary" title="Live Count Hasil Suara">
                                    <i data-lucide="bar-chart-2" style="width: 14px; height: 14px;"></i> Live Count
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

<style>
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,23,42,0.8); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; padding: 20px; }
    .modal-content { background: white; border-radius: 20px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
    .modal-header { padding: 20px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .modal-title { margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; }
    .modal-close { background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; transition: 0.2s; font-size: 18px; font-weight: bold; }
    .modal-close:hover { background: #e2e8f0; color: #0f172a; }
    .modal-body { padding: 25px; }
</style>
<!-- Modal Tambah Event -->
<div id="modal-add-event" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">Buat Event Pemilihan</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').style.display='none'">&times;</button>
        </div>
        <div class="modal-body">
            <form action="<?php echo \App\Core\Helper::url('/evoting/admin/store-event'); ?>" method="POST">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">Nama Event</label>
                    <input type="text" name="nama_event" class="z-input" placeholder="Contoh: Pemilihan Ketua OSIM 2026/2027" style="width: 100%; box-sizing: border-box;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">Deskripsi Singkat</label>
                    <textarea name="deskripsi" class="z-input" rows="2" placeholder="Deskripsi opsional..." style="width: 100%; box-sizing: border-box;"></textarea>
                </div>
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">Tanggal & Waktu Mulai</label>
                        <input type="datetime-local" name="tgl_mulai" class="z-input" style="width: 100%; box-sizing: border-box;" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">Tanggal & Waktu Selesai</label>
                        <input type="datetime-local" name="tgl_selesai" class="z-input" style="width: 100%; box-sizing: border-box;" required>
                    </div>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">Status Awal</label>
                    <select name="status" class="z-input" style="width: 100%; box-sizing: border-box;">
                        <option value="Draft">Draft (Belum Aktif)</option>
                        <option value="Aktif">Aktif (Bisa Memilih)</option>
                    </select>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-outline" onclick="this.closest('.modal-overlay').style.display='none'">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Event</button>
                </div>
            </form>
        </div>
    </div>
</div>
