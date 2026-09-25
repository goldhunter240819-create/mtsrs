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
            <i data-lucide="user-check" style="color: #bfdbfe;"></i> Edit Biodata Guru & Tendik
        </h1>
        <p class="mph-subtitle">Perbarui informasi profil dan data pegawai dengan lengkap.</p>
    </div>
    <div class="mph-actions">
        <a href="<?php echo \App\Core\Helper::url('/siakad/guru'); ?>" class="btn btn-primary-white" style="text-decoration:none;">
            <i data-lucide="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<form action="<?php echo \App\Core\Helper::url('/siakad/guru/save'); ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $guru['id']; ?>">
    
    <div class="form-card">
        <div class="form-section-title"><i data-lucide="info"></i> Identitas Utama</div>
        <div class="z-form-grid-3">
            <div class="col-span-2 form-group">
                <label>Nama Lengkap & Gelar</label>
                <input type="text" name="nama" class="z-input" value="<?php echo htmlspecialchars($guru['nama']); ?>" required>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="z-input">
                    <option value="L" <?php echo $guru['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?php echo $guru['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label>NIK (16 Digit - Wajib)</label>
                <input type="text" name="nik" class="z-input" value="<?php echo htmlspecialchars($guru['nik'] ?? ''); ?>" maxlength="16" required>
            </div>
            <div class="form-group">
                <label>NIP Guru (Opsional)</label>
                <input type="text" name="nip" class="z-input" value="<?php echo htmlspecialchars($guru['nip'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>NIY / Nomor Induk Yayasan (Opsional)</label>
                <input type="text" name="niy" class="z-input" value="<?php echo htmlspecialchars($guru['niy'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="z-input" value="<?php echo htmlspecialchars($guru['tempat_lahir'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="z-input" value="<?php echo htmlspecialchars($guru['tanggal_lahir'] ?? ''); ?>">
            </div>
            <div class="col-span-2 form-group">
                <label style="display:flex; align-items:center; gap:12px; cursor:pointer; padding:12px 15px; background: linear-gradient(to right, #f0fdf4, #fff); border:1.5px solid #86efac; border-radius:12px;">
                    <input type="checkbox" name="is_kamad" value="1" style="width:22px; height:22px; accent-color:#059669;" <?php echo !empty($guru['is_kamad']) ? 'checked' : ''; ?>>
                    <div style="text-transform:none;">
                        <div style="font-size:0.95rem; font-weight:800; color:#064e3b; margin-bottom:2px;"><i data-lucide="shield-check" style="width:16px;height:16px;display:inline-block;vertical-align:-3px;"></i> Akses Kepala Madrasah (Administrator)</div>
                        <div style="font-size:0.75rem; color:#166534; font-weight:500; letter-spacing:0;">Centang ini untuk memberikan hak akses portal Admin penuh.</div>
                    </div>
                </label>
            </div>
        </div>

        <div class="form-section-title"><i data-lucide="briefcase"></i> Jabatan & Kepegawaian</div>
        <div class="z-form-grid-3">
            <div class="col-span-2 form-group">
                <label>Jabatan / Peran</label>
                <input type="text" name="jabatan" class="z-input" value="<?php echo htmlspecialchars($guru['jabatan'] ?? ''); ?>" placeholder="Contoh: Guru Matematika">
            </div>
            <div class="form-group">
                <label>Status Kepegawaian</label>
                <select name="status_kepegawaian" class="z-input">
                    <option value="Honorer" <?php echo ($guru['status_kepegawaian'] ?? '') == 'Honorer' ? 'selected' : ''; ?>>Honorer</option>
                    <option value="Sertifikasi" <?php echo ($guru['status_kepegawaian'] ?? '') == 'Sertifikasi' ? 'selected' : ''; ?>>Sertifikasi</option>
                    <option value="PNS" <?php echo ($guru['status_kepegawaian'] ?? '') == 'PNS' ? 'selected' : ''; ?>>PNS</option>
                    <option value="P3K" <?php echo ($guru['status_kepegawaian'] ?? '') == 'P3K' ? 'selected' : ''; ?>>P3K</option>
                </select>
            </div>
            <div class="form-group">
                <label>NPK</label>
                <input type="text" name="npk" class="z-input" value="<?php echo htmlspecialchars($guru['npk'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>NPWP</label>
                <input type="text" name="npwp" class="z-input" value="<?php echo htmlspecialchars($guru['npwp'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>No Rekening (BSI)</label>
                <input type="text" name="no_rekening" class="z-input" value="<?php echo htmlspecialchars($guru['no_rekening'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-section-title"><i data-lucide="phone"></i> Kontak & Alamat</div>
        <div class="z-form-grid-2">
            <div class="form-group">
                <label>Nomor HP / WhatsApp</label>
                <input type="text" name="no_hp" class="z-input" value="<?php echo htmlspecialchars($guru['telepon'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="z-input" value="<?php echo htmlspecialchars($guru['email'] ?? ''); ?>">
            </div>
        </div>
        <div class="z-form-grid-4">
            <div class="col-span-4 form-group">
                <label>Alamat Jalan / Dusun</label>
                <input type="text" name="alamat" class="z-input" value="<?php echo htmlspecialchars($guru['alamat'] ?? ''); ?>">
            </div>
            <div class="form-group"><label>RT</label><input type="text" name="rt" class="z-input" value="<?php echo htmlspecialchars($guru['rt'] ?? ''); ?>"></div>
            <div class="form-group"><label>RW</label><input type="text" name="rw" class="z-input" value="<?php echo htmlspecialchars($guru['rw'] ?? ''); ?>"></div>
            <div class="col-span-2 form-group"><label>Kelurahan / Desa</label><input type="text" name="desa" class="z-input" value="<?php echo htmlspecialchars($guru['desa'] ?? ''); ?>"></div>
            <div class="col-span-2 form-group"><label>Kecamatan</label><input type="text" name="kecamatan" class="z-input" value="<?php echo htmlspecialchars($guru['kecamatan'] ?? ''); ?>"></div>
            <div class="col-span-2 form-group"><label>Kabupaten / Kota</label><input type="text" name="kota" class="z-input" value="<?php echo htmlspecialchars($guru['kota'] ?? ''); ?>"></div>
            <div class="col-span-2 form-group"><label>Provinsi</label><input type="text" name="provinsi" class="z-input" value="<?php echo htmlspecialchars($guru['provinsi'] ?? ''); ?>"></div>
            <div class="col-span-2 form-group"><label>Kode Pos</label><input type="text" name="kode_pos" class="z-input" value="<?php echo htmlspecialchars($guru['kode_pos'] ?? ''); ?>"></div>
        </div>

        <div class="form-section-title"><i data-lucide="award"></i> Data Sertifikasi</div>
        <div class="z-form-grid-3">
            <div class="form-group">
                <label>NRG</label>
                <input type="text" name="nrg" class="z-input" value="<?php echo htmlspecialchars($guru['nrg'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>No Peserta Sertifikasi</label>
                <input type="text" name="no_peserta_sertifikasi" class="z-input" value="<?php echo htmlspecialchars($guru['no_peserta_sertifikasi'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>No Sertifikat</label>
                <input type="text" name="no_sertifikat" class="z-input" value="<?php echo htmlspecialchars($guru['no_sertifikat'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Tgl Sertifikat</label>
                <input type="date" name="tgl_sertifikat" class="z-input" value="<?php echo htmlspecialchars($guru['tgl_sertifikat'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Jenjang</label>
                <select name="jenjang_sertifikat" class="z-input">
                    <option value="">- Pilih -</option>
                    <option value="RA" <?php echo ($guru['jenjang_sertifikat'] ?? '') == 'RA' ? 'selected' : ''; ?>>RA</option>
                    <option value="MI" <?php echo ($guru['jenjang_sertifikat'] ?? '') == 'MI' ? 'selected' : ''; ?>>MI</option>
                    <option value="MTs" <?php echo ($guru['jenjang_sertifikat'] ?? '') == 'MTs' ? 'selected' : ''; ?>>MTs</option>
                    <option value="MA" <?php echo ($guru['jenjang_sertifikat'] ?? '') == 'MA' ? 'selected' : ''; ?>>MA</option>
                </select>
            </div>
            <div class="form-group">
                <label>Mapel Sertifikasi</label>
                <input type="text" name="mapel_sertifikat" class="z-input" value="<?php echo htmlspecialchars($guru['mapel_sertifikat'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-section-title"><i data-lucide="camera"></i> Foto Profil</div>
        <div class="z-form-grid-2">
            <div class="form-group" style="display: flex; align-items: center; gap: 20px;">
                <div style="width: 100px; height: 130px; border-radius: 12px; overflow: hidden; background: #f1f5f9; border: 2px solid #e2e8f0; flex-shrink: 0;">
                    <?php if(!empty($guru['foto']) && file_exists(__DIR__ . '/../../../public/uploads/guru/' . $guru['foto'])): ?>
                        <img src="<?php echo \App\Core\Helper::url('/public/uploads/guru/' . $guru['foto']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
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
            <a href="<?php echo \App\Core\Helper::url('/siakad/guru'); ?>" class="e-btn e-btn-ghost">Batal</a>
            <button type="submit" class="e-btn e-btn-primary"><i data-lucide="save"></i> SIMPAN PERUBAHAN</button>
        </div>
    </div>
</form>

<script>
    lucide.createIcons();
</script>
