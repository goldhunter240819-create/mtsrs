<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="piggy-bank" style="color: #bfdbfe;"></i> Tabungan Siswa
        </h1>
        <p class="mph-subtitle">Pengelolaan saldo, setoran, dan penarikan tabungan santri/siswa MTs RS.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="openMutasiModal()">
            <i data-lucide="arrow-left-right"></i> Transaksi Setor / Tarik
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">
    <!-- Saldo Siswa Table -->
    <div class="z-panel" style="margin:0;">
        <div class="z-panel-head">
            <div class="z-panel-title"><i data-lucide="wallet"></i> Daftar Saldo Tabungan</div>
        </div>
        
        <div class="z-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 100px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 80px;">Kelas</th>
                        <th>Saldo Tabungan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tabunganList)): ?>
                    <tr><td colspan="4" style="text-align:center; color:var(--z-muted); padding:2rem;">Belum ada data tabungan.</td></tr>
                    <?php endif; ?>
                    <?php foreach($tabunganList as $tb): ?>
                    <tr>
                        <td style="font-weight:800; color: var(--z-primary);"><?php echo htmlspecialchars($tb['nis']); ?></td>
                        <td style="font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($tb['nama_siswa']); ?></td>
                        <td><span class="pill pill-blue"><?php echo htmlspecialchars($tb['nama_kelas'] ?? '-'); ?></span></td>
                        <td style="font-weight:800; color: var(--z-accent);">Rp <?php echo number_format($tb['saldo'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Log Mutasi Terbaru -->
    <div class="z-panel" style="margin:0;">
        <div class="z-panel-head">
            <div class="z-panel-title"><i data-lucide="history"></i> Mutasi Terakhir</div>
        </div>

        <div class="z-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 90px;">Waktu</th>
                        <th>Siswa</th>
                        <th>Jenis</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentMutasi)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color: var(--z-muted); padding: 2rem;">Belum ada riwayat mutasi tabungan.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach($recentMutasi as $m): ?>
                    <tr>
                        <td style="font-size:0.75rem; color: var(--z-muted);"><?php echo date('d/m/y H:i', strtotime($m['created_at'])); ?></td>
                        <td style="font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($m['nama_siswa']); ?></td>
                        <td>
                            <?php if ($m['jenis'] === 'Setor'): ?>
                                <span class="pill pill-green">+ Setor</span>
                            <?php else: ?>
                                <span class="pill" style="background:rgba(244,63,94,0.12); color:var(--z-danger); border:1px solid rgba(244,63,94,0.3);">- Tarik</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight:800; color: <?php echo $m['jenis'] === 'Setor' ? 'var(--z-accent)' : 'var(--z-danger)'; ?>;">
                            Rp <?php echo number_format($m['nominal'], 0, ',', '.'); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Setor / Tarik Tabungan -->
<div id="modalMutasi" class="z-modal-ov" style="display:none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="arrow-left-right"></i> Transaksi Tabungan Siswa</div>
            <div class="z-modal-close" onclick="closeMutasiModal()"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formMutasi" action="/keuangan/tabungan/mutasi" method="POST">
                <div class="z-form-group">
                    <label class="z-label">Pilih Siswa</label>
                    <select name="siswa_id" class="z-field" required>
                        <?php foreach($tabunganList as $s): ?>
                            <option value="<?php echo $s['siswa_id']; ?>">[<?php echo htmlspecialchars($s['nis']); ?>] <?php echo htmlspecialchars($s['nama_siswa']); ?> (Saldo: Rp <?php echo number_format($s['saldo'],0,',','.'); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="z-grid2">
                    <div class="z-form-group">
                        <label class="z-label">Jenis Transaksi</label>
                        <select name="jenis" class="z-field">
                            <option value="Setor">📥 Setor Tabungan (+)</option>
                            <option value="Tarik">📤 Penarikan Tabungan (-)</option>
                        </select>
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="z-field" placeholder="Masukkan jumlah nominal" required min="1000">
                    </div>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Keterangan / Catatan</label>
                    <input type="text" name="keterangan" class="z-field" placeholder="Misal: Setoran mingguan">
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="closeMutasiModal()">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formMutasi').submit()">Proses Transaksi</button>
        </div>
    </div>
</div>

<script>
    function openMutasiModal() {
        document.getElementById('modalMutasi').style.display = 'flex';
    }
    function closeMutasiModal() {
        document.getElementById('modalMutasi').style.display = 'none';
    }
</script>
