<style>
    /* ─── Layout ─── */
    .z-main {
        margin-left: calc(var(--z-sidebar-w) + var(--z-gap) * 2);
        flex: 1; background: #f4f7f6; min-height: 100vh; padding: 1.5rem;
        display: flex; flex-direction: column;
        background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.02) 1px, transparent 0);
        background-size: 24px 24px;
    }
    
    /* ─── Elite Page Header ─── */
    .elite-page-header {
        position: relative; padding: 2rem; margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px; overflow: hidden; color: #fff;
        box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.3);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .header-bg-shapes {
        position: absolute; top: 0; right: 0; bottom: 0; left: 0;
        background-image: radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.15) 0%, transparent 40%),
                          radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 40%);
        z-index: 1;
    }
    .header-content-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; }
    .header-badge {
        display: inline-block; padding: 4px 10px; background: rgba(16, 185, 129, 0.2);
        color: #10b981; font-size: 0.6rem; font-weight: 800; border-radius: 20px;
        border: 1px solid rgba(16, 185, 129, 0.3); margin-bottom: 0.75rem; letter-spacing: 0.5px;
    }
    .header-text-premium h1 { font-size: 1.6rem; font-weight: 900; margin: 0; letter-spacing: -1px; line-height: 1.2; }
    .header-text-premium p { font-size: 0.85rem; opacity: 0.8; margin-top: 0.5rem; max-width: 500px; font-weight: 500; line-height: 1.5; }

    /* ─── Form Elements ─── */
    .form-card {
        background: #fff; border-radius: 24px; padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
    }
    .form-section-title {
        font-size: 1.1rem; font-weight: 800; color: #0f172a;
        margin-bottom: 1.5rem; padding-bottom: 0.5rem;
        border-bottom: 2px solid #f1f5f9; display: flex; align-items: center; gap: 10px;
    }
    .form-section-title i { color: #10b981; }
    
    .z-form-grid-3, .z-form-grid-2, .z-form-grid-4 { display: grid; gap: 1.5rem; margin-bottom: 2rem; }
    .z-form-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .z-form-grid-2 { grid-template-columns: repeat(2, 1fr); }
    .z-form-grid-4 { grid-template-columns: repeat(4, 1fr); }

    .form-group label {
        display: block; font-size: 0.75rem; font-weight: 800;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;
        margin-bottom: 10px;
    }
    .z-input {
        width: 100%; padding: 14px 18px; border-radius: 16px;
        border: 2px solid #f1f5f9; background: #f8fafc;
        font-weight: 700; color: #1e293b; transition: 0.3s;
        font-size: 0.95rem !important;
        box-sizing: border-box;
    }
    .z-input:focus { border-color: #10b981; box-shadow: 0 0 0 5px rgba(16,185,129,0.1); background: #fff; outline: none; }
    
    .col-span-2 { grid-column: span 2; }
    .col-span-3 { grid-column: span 3; }
    .col-span-4 { grid-column: span 4; }

    .e-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px; 
        padding: 14px 24px; border-radius: 14px; font-weight: 800; font-size: 0.9rem; 
        cursor: pointer; border: none; transition: 0.2s ease; text-decoration: none;
    }
    .e-btn:hover { transform: translateY(-3px); }
    .e-btn-primary { background: #10b981; color: #fff; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); }
    .e-btn-ghost { background: #f1f5f9; color: #64748b; }
    .e-btn-ghost:hover { background: #e2e8f0; color: #0f172a; }
</style>

<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="edit-3" style="color: #bfdbfe;"></i> Edit Biodata Siswa
        </h1>
        <p class="mph-subtitle">Perbarui informasi profil dan data peserta didik dengan lengkap.</p>
    </div>
    <div class="mph-actions">
        <a href="/siakad/siswa" class="btn btn-primary-white" style="text-decoration:none;">
            <i data-lucide="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<form action="/siakad/siswa/save" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $siswa['id']; ?>">
    
    <div class="form-card">
        <div class="form-section-title"><i data-lucide="user"></i> Identitas Diri</div>
        <div class="z-form-grid-3">
            <div class="col-span-2 form-group">
                <label>Nama Lengkap (Sesuai Akta/KK)</label>
                <input type="text" name="nama" class="z-input" value="<?php echo htmlspecialchars($siswa['nama']); ?>" required>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="z-input">
                    <option value="L" <?php echo $siswa['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?php echo $siswa['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label>NIK (16 Digit)</label>
                <input type="text" name="nik" class="z-input" value="<?php echo htmlspecialchars($siswa['nik'] ?? ''); ?>" maxlength="16">
            </div>
            <div class="form-group">
                <label>No. Kartu Keluarga (KK)</label>
                <input type="text" name="no_kk" class="z-input" value="<?php echo htmlspecialchars($siswa['no_kk'] ?? ''); ?>" maxlength="16">
            </div>
            <div class="form-group">
                <label>Agama</label>
                <select name="agama" class="z-input">
                    <?php $agamas = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha']; foreach($agamas as $ag): ?>
                        <option value="<?php echo $ag; ?>" <?php echo ($siswa['agama'] ?? '') == $ag ? 'selected' : ''; ?>><?php echo $ag; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="z-input" value="<?php echo htmlspecialchars($siswa['tempat_lahir'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="z-input" value="<?php echo htmlspecialchars($siswa['tanggal_lahir'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-section-title"><i data-lucide="graduation-cap"></i> Data Sekolah</div>
        <div class="z-form-grid-3">
            <div class="form-group">
                <label>NIS (Nomor Induk Sekolah)</label>
                <input type="text" name="nis" class="z-input" value="<?php echo htmlspecialchars($siswa['nis'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>NISN (Nasional)</label>
                <input type="text" name="nisn" class="z-input" value="<?php echo htmlspecialchars($siswa['nisn'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Kelas Saat Ini</label>
                <select name="kelas_id" class="z-input">
                    <option value="">Pilih Kelas</option>
                    <?php foreach($kelas as $k): ?>
                        <option value="<?php echo $k['id']; ?>" <?php echo ($siswa['kelas_id'] ?? '') == $k['id'] ? 'selected' : ''; ?>><?php echo $k['nama_kelas']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Pendidikan Terakhir</label>
                <input type="text" name="pendidikan_terakhir" class="z-input" value="<?php echo htmlspecialchars($siswa['pendidikan_terakhir'] ?? ''); ?>" placeholder="SD/MI">
            </div>
            <div class="form-group">
                <label>Sekolah Asal</label>
                <input type="text" name="sekolah_asal" class="z-input" value="<?php echo htmlspecialchars($siswa['sekolah_asal'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>No. Ijazah</label>
                <input type="text" name="no_ijazah" class="z-input" value="<?php echo htmlspecialchars($siswa['no_ijazah'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-section-title"><i data-lucide="users"></i> Data Orang Tua / Wali</div>
        <div class="z-form-grid-2">
            <div class="form-group">
                <label>Nama Ayah</label>
                <input type="text" name="nama_ayah" class="z-input" value="<?php echo htmlspecialchars($siswa['nama_ayah'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Pekerjaan Ayah</label>
                <input type="text" name="pekerjaan_ayah" class="z-input" value="<?php echo htmlspecialchars($siswa['pekerjaan_ayah'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Nama Ibu</label>
                <input type="text" name="nama_ibu" class="z-input" value="<?php echo htmlspecialchars($siswa['nama_ibu'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Pekerjaan Ibu</label>
                <input type="text" name="pekerjaan_ibu" class="z-input" value="<?php echo htmlspecialchars($siswa['pekerjaan_ibu'] ?? ''); ?>">
            </div>
            <div class="col-span-2 form-group">
                <label>No. HP Orang Tua</label>
                <input type="text" name="no_hp_ortu" class="z-input" value="<?php echo htmlspecialchars($siswa['no_hp_ortu'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-section-title"><i data-lucide="map-pin"></i> Alamat Lengkap</div>
        <div class="z-form-grid-4">
            <div class="col-span-4 form-group">
                <label>Alamat Jalan / Dusun</label>
                <input type="text" name="alamat" class="z-input" value="<?php echo htmlspecialchars($siswa['alamat'] ?? ''); ?>">
            </div>
            <div class="form-group"><label>RT</label><input type="text" name="rt" class="z-input" value="<?php echo htmlspecialchars($siswa['rt'] ?? ''); ?>"></div>
            <div class="form-group"><label>RW</label><input type="text" name="rw" class="z-input" value="<?php echo htmlspecialchars($siswa['rw'] ?? ''); ?>"></div>
            <div class="col-span-2 form-group"><label>Kelurahan / Desa</label><input type="text" name="desa" class="z-input" value="<?php echo htmlspecialchars($siswa['desa'] ?? ''); ?>"></div>
            <div class="col-span-2 form-group"><label>Kecamatan</label><input type="text" name="kecamatan" class="z-input" value="<?php echo htmlspecialchars($siswa['kecamatan'] ?? ''); ?>"></div>
            <div class="col-span-2 form-group"><label>Kabupaten / Kota</label><input type="text" name="kota" class="z-input" value="<?php echo htmlspecialchars($siswa['kota'] ?? ''); ?>"></div>
        </div>

        <div class="form-section-title"><i data-lucide="camera"></i> Pas Foto</div>
        <div class="z-form-grid-2">
            <div class="form-group" style="display: flex; align-items: center; gap: 20px;">
                <div style="width: 100px; height: 130px; border-radius: 12px; overflow: hidden; background: #f1f5f9; border: 2px solid #e2e8f0; flex-shrink: 0;">
                    <?php if(!empty($siswa['foto'])): ?>
                        <img src="/public/uploads/siswa/<?php echo $siswa['foto']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i data-lucide="user" style="width: 40px; height: 40px;"></i></div>
                    <?php endif; ?>
                </div>
                <div style="flex: 1;">
                    <label>Upload Foto Baru (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" class="z-input" style="padding: 10px;">
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 5px;">Format: JPG, PNG. Abaikan jika tidak ingin mengubah foto.</p>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 3rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 2px solid #f1f5f9;">
            <a href="/siakad/siswa" class="e-btn e-btn-ghost">Batal</a>
            <button type="submit" class="e-btn e-btn-primary"><i data-lucide="save"></i> SIMPAN PERUBAHAN</button>
        </div>
    </div>
</form>

<script>
    lucide.createIcons();
</script>
