<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="tags" style="color: #bfdbfe;"></i> Master Komponen Tagihan
        </h1>
        <p class="mph-subtitle">Kelola jenis tagihan administrasi sekolah (SPP, Gedung, Seragam, Komite).</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="document.getElementById('modal-komponen').style.display='flex'">
            <i data-lucide="plus-circle"></i> Tambah Komponen
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
    <?php foreach($komponenList as $k): ?>
    <div class="z-panel" style="margin-bottom:0; cursor:pointer; transition:all 0.25s;" onmouseover="this.style.borderColor='var(--z-primary)'; this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor=''; this.style.transform='';">
        <div class="z-panel-body" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <span class="pill pill-blue" style="margin-bottom:8px;"><?php echo htmlspecialchars($k['jenis']); ?></span>
                <div style="font-size: 1.25rem; font-weight: 900; color: var(--z-text);"><?php echo htmlspecialchars($k['nama_komponen']); ?></div>
                <div style="font-size: 1.15rem; font-weight: 800; color: var(--z-accent); margin-top: 4px;">Rp <?php echo number_format($k['nominal_default'], 0, ',', '.'); ?></div>
            </div>
            <div style="width:50px; height:50px; border-radius:14px; background:var(--z-primary-light); color:var(--z-primary); display:flex; align-items:center; justify-content:center;">
                <i data-lucide="tag" style="width:24px;height:24px;"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div id="modal-komponen" class="z-modal-ov" style="display: none;">
    <div class="z-modal">
        <div class="z-modal-head">
            <div class="z-modal-title"><i data-lucide="tags"></i> Tambah Komponen Baru</div>
            <div class="z-modal-close" onclick="document.getElementById('modal-komponen').style.display='none'"><i data-lucide="x"></i></div>
        </div>
        <div class="z-modal-body">
            <form id="formKomponen" method="POST" action="/keuangan/komponen/save">
                <div class="z-form-group">
                    <label class="z-label">Nama Komponen Tagihan</label>
                    <input type="text" name="nama_komponen" class="z-field" placeholder="Misal: SPP Juli 2026" required>
                </div>
                <div class="z-grid2">
                    <div class="z-form-group">
                        <label class="z-label">Kategori / Jenis</label>
                        <select name="jenis" class="z-field" required>
                            <option value="SPP">SPP Bulanan</option>
                            <option value="Gedung">Uang Gedung</option>
                            <option value="Seragam">Seragam</option>
                            <option value="Komite">Komite</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Nominal Default (Rp)</label>
                        <input type="number" name="nominal_default" class="z-field" placeholder="Misal: 50000" required>
                    </div>
                </div>
            </form>
        </div>
        <div class="z-modal-foot">
            <button class="btn btn-ghost" onclick="document.getElementById('modal-komponen').style.display='none'">Batal</button>
            <button class="btn btn-primary" onclick="document.getElementById('formKomponen').submit()">Simpan Data</button>
        </div>
    </div>
</div>
