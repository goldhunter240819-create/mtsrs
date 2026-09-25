<?php use App\Core\Helper; ?>
<div class="floating-card">
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="file-spreadsheet" style="color: #bfdbfe;"></i> Template Export & Import Nilai Excel
        </h1>
        <p class="mph-subtitle">Format penginputan nilai massal melalui file MS Excel / CSV untuk dewan guru.</p>
    </div>
</div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:1.5rem;">
        <div class="floating-card" style="background:#ffffff; border:1px solid var(--border-light);">
            <div class="mod-icon-box icon-blue" style="margin-bottom:1rem;">
                <i data-lucide="download"></i>
            </div>
            <h3 style="font-weight:800; margin-bottom:6px;">Download Template Blank Excel</h3>
            <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1.25rem;">Unduh format file Excel (.xlsx / .csv) berisi daftar nama siswa rombel terisi otomatis.</p>

            <form method="GET" action="<?php echo Helper::url('/siakad/nilai/input'); ?>">
                <div class="form-group">
                    <label class="form-label">Pilih Rombel Kelas</label>
                    <select name="kelas_id" class="form-select">
                        <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo $k['id']; ?>">Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Pilih Mata Pelajaran</label>
                    <select name="mapel_id" class="form-select">
                        <?php foreach($mapelList as $m): ?>
                            <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">
                    <i data-lucide="file-spread-sheet"></i> Unduh Format Blank
                </button>
            </form>
        </div>

        <div class="floating-card" style="background:#ffffff; border:1px solid var(--border-light);">
            <div class="mod-icon-box icon-purple" style="margin-bottom:1rem;">
                <i data-lucide="upload"></i>
            </div>
            <h3 style="font-weight:800; margin-bottom:6px;">Upload File Nilai Terisi</h3>
            <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1.25rem;">Unggah file Excel / CSV yang sudah terisi nilai siswa untuk diimpor otomatis ke database.</p>

            <form method="POST" action="<?php echo Helper::url('/siakad/nilai/save'); ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Pilih File Excel / CSV (.csv / .xlsx)</label>
                    <input type="file" name="file_nilai" class="form-input" required accept=".csv, .xlsx">
                </div>
                <button type="submit" class="btn-primary" style="background:var(--primary-gradient-hover); width:100%; justify-content:center; margin-top:2rem;">
                    <i data-lucide="upload-cloud"></i> Impor Nilai Ke Database
                </button>
            </form>
        </div>
    </div>
</div>
