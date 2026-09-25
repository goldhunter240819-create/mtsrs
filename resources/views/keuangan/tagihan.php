<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="receipt" style="color: #bfdbfe;"></i> Daftar Tagihan Siswa
        </h1>
        <p class="mph-subtitle">Generate dan monitor tagihan bulanan / insidental siswa MTs RS.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="openGenerateModal()">
            <i data-lucide="sparkles"></i> Generate Tagihan Massal
        </button>
    </div>
</div>

<div class="z-panel">
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 120px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 100px;">Kelas</th>
                    <th>Komponen Tagihan</th>
                    <th>Periode / Bulan</th>
                    <th>Nominal</th>
                    <th>Terbayar</th>
                    <th style="width: 100px;">Status</th>
                    <th style="text-align: center; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tagihanList)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; color: var(--z-muted); padding: 2rem;">Belum ada data tagihan.</td>
                </tr>
                <?php endif; ?>
                <?php foreach($tagihanList as $t): ?>
                <tr>
                    <td style="font-weight:800; color: var(--z-primary); font-family:monospace;"><?php echo htmlspecialchars($t['nis']); ?></td>
                    <td style="font-weight:700; color:var(--z-text);"><?php echo htmlspecialchars($t['nama_siswa']); ?></td>
                    <td><span class="pill pill-blue"><?php echo htmlspecialchars($t['nama_kelas'] ?? '-'); ?></span></td>
                    <td style="font-weight:600; color:var(--z-text);"><?php echo htmlspecialchars($t['nama_komponen']); ?></td>
                    <td style="color:var(--z-muted); font-size:0.85rem;"><?php echo htmlspecialchars($t['bulan'] ?: '-'); ?></td>
                    <td style="font-weight:700; color:var(--z-text);">Rp <?php echo number_format($t['nominal'], 0, ',', '.'); ?></td>
                    <td style="font-weight:800; color: var(--z-accent);">Rp <?php echo number_format($t['terbayar'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if ($t['status'] === 'Lunas'): ?>
                            <span class="pill pill-green">Lunas</span>
                        <?php elseif ($t['status'] === 'Sebagian'): ?>
                            <span class="pill pill-amber">Sebagian</span>
                        <?php else: ?>
                            <span class="pill" style="background:rgba(244,63,94,0.12); color:var(--z-danger); border:1px solid rgba(244,63,94,0.3);">Belum Bayar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="z-action-btns">
                            <?php if ($t['status'] !== 'Lunas'): ?>
                                <a href="/keuangan/pembayaran?tagihan_id=<?php echo $t['id']; ?>" class="btn btn-outline btn-sm" style="padding: 4px 8px;" title="Bayar">
                                    <i data-lucide="credit-card"></i> Bayar
                                </a>
                            <?php else: ?>
                                <span style="font-size:0.75rem; color: var(--z-muted); font-weight:700;">Selesai</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Generate Tagihan Massal -->
<div id="modalGenerate" class="z-modal-ov" style="display:none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="sparkles"></i> Generate Tagihan Massal</div>
            <div class="z-modal-close" onclick="closeGenerateModal()"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formGenerate" action="/keuangan/tagihan/generate" method="POST">
                <div class="z-form-group">
                    <label class="z-label">Pilih Komponen Tagihan</label>
                    <select name="komponen_id" class="z-field" required>
                        <?php foreach($komponenList as $k): ?>
                            <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama_komponen']); ?> (Rp <?php echo number_format($k['nominal_default'], 0, ',', '.'); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Target Kelas (Atau Semua Siswa)</label>
                    <select name="kelas_id" class="z-field">
                        <option value="0">-- Semua Siswa Aktif --</option>
                        <?php foreach($kelasList as $kl): ?>
                            <option value="<?php echo $kl['id']; ?>">Khusus Kelas <?php echo htmlspecialchars($kl['nama_kelas']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-form-group">
                    <label class="z-label">Periode / Keterangan Bulan</label>
                    <input type="text" name="bulan" class="z-field" placeholder="Misal: Agustus 2026" required>
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="closeGenerateModal()">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formGenerate').submit()">Generate Sekarang</button>
        </div>
    </div>
</div>

<script>
    function openGenerateModal() {
        document.getElementById('modalGenerate').style.display = 'flex';
    }
    function closeGenerateModal() {
        document.getElementById('modalGenerate').style.display = 'none';
    }
</script>
