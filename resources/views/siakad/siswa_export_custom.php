<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="printer" style="color: #bfdbfe;"></i> Export Custom Data Siswa
        </h1>
        <p class="mph-subtitle">Pilih kolom spesifik yang ingin Anda export/cetak.</p>
    </div>
    <div class="mph-actions">
        <a href="/siakad/siswa" class="btn btn-secondary">
            <i data-lucide="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="z-card" style="padding: 2rem;">
    <form action="/siakad/siswa/export-process" method="POST" target="_blank" id="formExportCustom">
        
        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:8px; font-weight:700; color:#475569; font-size:0.85rem;">Filter Kelas</label>
            <select name="kelas_id" class="z-input" style="width: 300px; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <option value="">Semua Kelas</option>
                <?php foreach ($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($selected_kelas == $k['id']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center;">
            <label style="font-weight:700; color:#475569; font-size:0.85rem;">Pilih Data:</label>
            <button type="button" class="btn btn-sm btn-outline" onclick="checkAll(true)">Pilih Semua</button>
            <button type="button" class="btn btn-sm btn-outline" onclick="checkAll(false)">Batal Pilih Semua</button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <?php
            $kolom = [
                'nis' => 'NIS',
                'nisn' => 'NISN',
                'nik' => 'NIK',
                'nama' => 'Nama Lengkap',
                'jk' => 'Jenis Kelamin (L/P)',
                'tempat_lahir' => 'Tempat Lahir',
                'tanggal_lahir' => 'Tanggal Lahir',
                'agama' => 'Agama',
                'gol_darah' => 'Golongan Darah',
                'warga_negara' => 'Kewarganegaraan',
                'anak_ke' => 'Anak Ke-',
                'kelas' => 'Kelas Aktif',
                'pend_terakhir' => 'Pend. Terakhir',
                'sekolah_asal' => 'Sekolah Asal',
                'no_ijazah' => 'No Ijazah',
                'ayah_nama' => 'Nama Ayah',
                'ayah_pekerjaan' => 'Pekerjaan Ayah',
                'ibu_nama' => 'Nama Ibu',
                'ibu_pekerjaan' => 'Pekerjaan Ibu',
                'ortu_gaji' => 'Penghasilan Ortu',
                'ortu_hp' => 'No. HP Orang Tua',
                'wali_nama' => 'Nama Wali',
                'wali_pekerjaan' => 'Pekerjaan Wali',
                'wali_hp' => 'No. HP Wali',
                'alamat_jalan' => 'Alamat (Jalan)',
                'alamat_rt' => 'RT',
                'alamat_rw' => 'RW',
                'alamat_desa' => 'Desa/Kelurahan',
                'alamat_kecamatan' => 'Kecamatan',
                'alamat_kota' => 'Kabupaten/Kota',
                'alamat_provinsi' => 'Provinsi',
                'alamat_kodepos' => 'Kode Pos'
            ];
            foreach ($kolom as $key => $label):
            ?>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; transition: 0.2s;" class="custom-checkbox-lbl">
                <input type="checkbox" name="kolom[]" value="<?= $key ?>" checked style="width: 18px; height: 18px; accent-color: #10b981;">
                <span style="font-size: 0.85rem; font-weight: 600; color: #334155;"><?= $label ?></span>
            </label>
            <?php endforeach; ?>
        </div>
        
        <style>
            .custom-checkbox-lbl:hover { border-color: #10b981 !important; background: #fafffe !important; }
        </style>

        <div style="border-top: 1px solid #e2e8f0; padding-top: 20px; display: flex; gap: 15px;">
            <button type="submit" name="format" value="pdf" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="file-text"></i> Export PDF (Print)
            </button>
            <button type="submit" name="format" value="excel" class="btn" style="background: #10b981; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; border:none;">
                <i data-lucide="table"></i> Export Excel
            </button>
        </div>
    </form>
</div>

<script>
function checkAll(check) {
    const checkboxes = document.querySelectorAll('#formExportCustom input[type="checkbox"]');
    checkboxes.forEach(cb => {
        cb.checked = check;
    });
}
</script>
