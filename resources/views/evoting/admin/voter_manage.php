<div class="modern-page-header" style="background: linear-gradient(135deg, #0f172a, #334155);">
    <a href="<?php echo \App\Core\Helper::url('/evoting/admin'); ?>" style="color: rgba(255,255,255,0.8); display: inline-flex; align-items: center; gap: 5px; text-decoration: none; font-size: 13px; margin-bottom: 10px;">
        <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Kembali ke Dashboard
    </a>
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="key" style="color: rgba(255,255,255,0.8);"></i> Kelola Pemilih & Token
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Event: <strong><?= htmlspecialchars($eventData['nama_event']) ?></strong></p>
    </div>
</div>

<div class="z-card" style="margin-top: 2rem; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 5px 0;">Daftar Pemilih</h2>
            <div style="font-size: 13px; color: #64748b;">Total Pemilih: <strong><?= count($votersData) ?></strong></div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="document.getElementById('modal-generate-tokens').style.display='flex'; lucide.createIcons();" class="btn btn-outline">
                <i data-lucide="refresh-cw" style="width: 18px; height: 18px;"></i> Generate Pemilih
            </button>
            <?php if(!empty($votersData)): ?>
            <a href="<?php echo \App\Core\Helper::url('/evoting/admin/print-tokens?event_id='.$eventId); ?>" target="_blank" class="btn btn-primary">
                <i data-lucide="printer" style="width: 18px; height: 18px;"></i> Cetak Kartu Token
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="z-table-responsive">
        <table class="z-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pemilih</th>
                    <th>Tipe / Keterangan</th>
                    <th>Token (PIN)</th>
                    <th>Status</th>
                    <th>Waktu Memilih</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($votersData)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">
                        Belum ada data pemilih. Silakan klik "Generate Pemilih" untuk membuat data pemilih secara massal dari data Siswa/Guru.
                    </td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($votersData as $v): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($v['nama']) ?></div>
                        </td>
                        <td>
                            <span class="pill pill-gray"><?= $v['user_type'] ?></span>
                            <?php if($v['user_type'] === 'Siswa' && !empty($v['keterangan'])): ?>
                                <span style="font-size: 12px; color: #64748b; margin-left: 5px;"><?= htmlspecialchars($v['keterangan']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-family: monospace; font-size: 16px; font-weight: 800; color: var(--z-primary); letter-spacing: 2px;">
                                <?= $v['token'] ?>
                            </div>
                        </td>
                        <td>
                            <?php if($v['has_voted']): ?>
                                <span class="pill pill-green"><i data-lucide="check-circle" style="width: 12px; height: 12px; margin-right: 3px;"></i> Sudah Memilih</span>
                            <?php else: ?>
                                <span class="pill pill-yellow">Belum Memilih</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 12px; color: #475569;">
                            <?= $v['voted_at'] ? date('d M Y H:i', strtotime($v['voted_at'])) : '-' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Generate Tokens -->
<div id="modal-generate-tokens" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 450px;">
        <div class="modal-header">
            <h3 class="modal-title">Generate Data Pemilih</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').style.display='none'">&times;</button>
        </div>
        <div class="modal-body">
            <form action="<?php echo \App\Core\Helper::url('/evoting/admin/generate-tokens'); ?>" method="POST">
                <input type="hidden" name="event_id" value="<?= $eventId ?>">
                
                <div style="margin-bottom: 20px; font-size: 13px; color: #475569; line-height: 1.5; background: #f1f5f9; padding: 15px; border-radius: 8px;">
                    Fitur ini akan mengambil data dari master data dan membuatkan Token secara otomatis. Data yang sudah ada tidak akan digandakan (aman untuk digenerate berulang jika ada siswa baru).
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">Pilih Target Pemilih</label>
                    <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer;">
                        <input type="checkbox" name="target[]" value="siswa" checked>
                        <span>Seluruh Siswa (Status Aktif)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="target[]" value="guru">
                        <span>Seluruh Guru & Karyawan</span>
                    </label>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;">
                    <button type="button" class="btn btn-outline" onclick="this.closest('.modal-overlay').style.display='none'">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
