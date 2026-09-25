<?php
use App\Core\Helper;
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="apk-vector-header" style="padding-bottom: 70px; justify-content: center; text-align: center;">
    <a href="<?= Helper::url('/apk/profile') ?>" style="position: absolute; left: 20px; top: 25px; color: white; background: rgba(255,255,255,0.2); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
        <i data-lucide="arrow-left"></i>
    </a>
    <div style="margin-bottom: 10px;">
        <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Pengaturan Akun</div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Edit Profil</div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; display: flex; flex-direction: column; align-items: center; padding-top: 50px;">
    
    <?php if ($successMsg): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= addslashes($successMsg) ?>',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = '<?= Helper::url('/apk/profile') ?>';
        });
    </script>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= addslashes($errorMsg) ?>'
        });
    </script>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" style="width: 100%;">
        
        <!-- Upload Foto Section -->
        <div style="position: absolute; top: -55px; z-index: 30; left: 50%; transform: translateX(-50%);">
            <div style="position: relative;">
                <label for="foto_upload" style="cursor: pointer; display: block;">
                    <?php if(!empty($userProfile['foto'])): ?>
                        <img id="foto_preview" src="<?= Helper::url('/public/uploads/' . ($isGuru ? 'guru' : 'siswa') . '/' . rawurlencode($userProfile['foto'])) ?>" alt="Foto Profil" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 5px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: #f1f5f9; transition: opacity 0.3s;">
                    <?php else: ?>
                        <div id="foto_preview_container" style="width: 110px; height: 110px; border-radius: 50%; border: 5px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: linear-gradient(135deg, #e2e8f0, #cbd5e1); display: flex; align-items: center; justify-content: center; color: #64748b;">
                            <img id="foto_preview" src="" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: none;">
                            <i data-lucide="camera" id="foto_icon" style="width: 40px; height: 40px;"></i>
                        </div>
                    <?php endif; ?>
                    <div style="position: absolute; bottom: 0px; right: 0px; background: #3b82f6; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);">
                        <i data-lucide="pencil" style="width: 16px;"></i>
                    </div>
                </label>
                <input type="file" id="foto_upload" name="foto" accept="image/png, image/jpeg" style="display: none;" onchange="previewImage(this)">
            </div>
        </div>

        <!-- Data Tidak Dapat Diubah -->
        <div style="width: 100%; background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 20px; padding: 20px;">
            <h3 style="font-size: 0.9rem; color: #ef4444; margin-bottom: 15px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="lock" style="width: 18px;"></i> Data Terkunci
            </h3>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Nama Lengkap</label>
                <input type="text" value="<?= htmlspecialchars($userProfile['nama']) ?>" readonly style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; color: #94a3b8; outline: none; font-weight: 600;">
            </div>

            <?php if($isGuru): ?>
                <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">NIP / PegId</label>
                        <input type="text" value="<?= htmlspecialchars($userProfile['nip'] ?? '-') ?>" readonly style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; color: #94a3b8; outline: none; font-weight: 600;">
                    </div>
                </div>
            <?php else: ?>
                <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">NIS</label>
                        <input type="text" value="<?= htmlspecialchars($userProfile['nis']) ?>" readonly style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; color: #94a3b8; outline: none; font-weight: 600;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">NISN</label>
                        <input type="text" value="<?= htmlspecialchars($userProfile['nisn'] ?: '-') ?>" readonly style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; color: #94a3b8; outline: none; font-weight: 600;">
                    </div>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">NIK (16 Digit)</label>
                    <input type="text" name="nik" value="<?= htmlspecialchars($userProfile['nik'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; transition: border 0.2s;">
                </div>
                <?php if(!$isGuru): ?>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">No. KK</label>
                    <input type="text" name="no_kk" value="<?= htmlspecialchars($userProfile['no_kk'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; transition: border 0.2s;">
                </div>
                <?php endif; ?>
            </div>
            
            <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 10px; font-style: italic;">* Hubungi Admin Sekolah untuk merubah data terkunci.</p>
        </div>

        <!-- Data Pribadi -->
        <div style="width: 100%; background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 20px; padding: 20px;">
            <h3 style="font-size: 0.9rem; color: #3b82f6; margin-bottom: 15px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="user-pen" style="width: 18px;"></i> Data Pribadi
            </h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($userProfile['tempat_lahir'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($userProfile['tanggal_lahir'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Jenis Kelamin</label>
                    <select name="jenis_kelamin" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;">
                        <option value="L" <?= (($userProfile['jenis_kelamin'] ?? '') == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= (($userProfile['jenis_kelamin'] ?? '') == 'P') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <?php if(!$isGuru): ?>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Agama</label>
                    <select name="agama" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;">
                        <?php 
                        $agama_list = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
                        foreach($agama_list as $a): 
                        ?>
                            <option value="<?= $a ?>" <?= (($userProfile['agama'] ?? '') == $a) ? 'selected' : '' ?>><?= $a ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>

            <?php if(!$isGuru): ?>
            <!-- Field Tambahan Siswa -->
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Golongan Darah</label>
                    <select name="gol_darah" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;">
                        <option value="">- Pilih -</option>
                        <?php foreach(['A','B','AB','O'] as $gd): ?>
                            <option value="<?= $gd ?>" <?= (($userProfile['gol_darah'] ?? '') == $gd) ? 'selected' : '' ?>><?= $gd ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Anak Ke-</label>
                    <input type="number" name="anak_ke" value="<?= htmlspecialchars($userProfile['anak_ke'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;" min="1">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Kewarganegaraan</label>
                    <input type="text" name="kewarganegaraan" value="<?= htmlspecialchars($userProfile['kewarganegaraan'] ?? 'WNI') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Sekolah Asal</label>
                    <input type="text" name="sekolah_asal" value="<?= htmlspecialchars($userProfile['sekolah_asal'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
            </div>
            <?php endif; ?>

            <?php if($isGuru): ?>
            <!-- Email Guru -->
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($userProfile['email'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
            </div>
            <?php endif; ?>
            
            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">No. Telepon / WhatsApp</label>
                <div style="position: relative;">
                    <i data-lucide="phone" style="position: absolute; left: 15px; top: 12px; color: #94a3b8; width: 18px;"></i>
                    <input type="tel" name="<?= $isGuru ? 'telepon' : 'no_hp_ortu' ?>" value="<?= htmlspecialchars($userProfile[$isGuru ? 'telepon' : 'no_hp_ortu'] ?? '') ?>" style="width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
            </div>
        </div>

        <!-- Data Alamat -->
        <div style="width: 100%; background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 20px; padding: 20px;">
            <h3 style="font-size: 0.9rem; color: #10b981; margin-bottom: 15px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="map-pin" style="width: 18px;"></i> Data Alamat
            </h3>
            
            <!-- API Wilayah untuk Guru dan Siswa -->
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Provinsi</label>
                    <input type="hidden" name="provinsi" id="val_prov" value="<?= htmlspecialchars($userProfile['provinsi'] ?? '') ?>">
                    <select id="sel_prov" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;">
                        <option value=""><?= htmlspecialchars($userProfile['provinsi'] ?: '-- Pilih Provinsi --') ?></option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Kab/Kota</label>
                    <input type="hidden" name="kota" id="val_kota" value="<?= htmlspecialchars($userProfile['kota'] ?? '') ?>">
                    <select id="sel_kota" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;" disabled>
                        <option value=""><?= htmlspecialchars($userProfile['kota'] ?: '-- Pilih Kab/Kota --') ?></option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Kecamatan</label>
                    <input type="hidden" name="kecamatan" id="val_kec" value="<?= htmlspecialchars($userProfile['kecamatan'] ?? '') ?>">
                    <select id="sel_kec" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;" disabled>
                        <option value=""><?= htmlspecialchars($userProfile['kecamatan'] ?: '-- Pilih Kecamatan --') ?></option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Desa/Kelurahan</label>
                    <input type="hidden" name="desa" id="val_desa" value="<?= htmlspecialchars($userProfile['desa'] ?? '') ?>">
                    <select id="sel_desa" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;" disabled>
                        <option value=""><?= htmlspecialchars($userProfile['desa'] ?: '-- Pilih Desa --') ?></option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">RT</label>
                    <input type="text" name="rt" value="<?= htmlspecialchars($userProfile['rt'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">RW</label>
                    <input type="text" name="rw" value="<?= htmlspecialchars($userProfile['rw'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Kode Pos</label>
                    <input type="text" name="kode_pos" value="<?= htmlspecialchars($userProfile['kode_pos'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Nama Jalan / Dusun</label>
                <textarea name="alamat" rows="2" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; resize: vertical;"><?= htmlspecialchars($userProfile['alamat'] ?? '') ?></textarea>
            </div>
        </div>

        <?php if(!$isGuru): ?>
        <!-- Data Orang Tua (Siswa) -->
        <div style="width: 100%; background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 20px; padding: 20px;">
            <h3 style="font-size: 0.9rem; color: #d97706; margin-bottom: 15px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="users" style="width: 18px;"></i> Data Orang Tua & Wali
            </h3>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Nama Ayah</label>
                <input type="text" name="nama_ayah" value="<?= htmlspecialchars($userProfile['nama_ayah'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Pekerjaan Ayah</label>
                <select name="pekerjaan_ayah" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;">
                    <?php 
                    $pekerjaan_list = ['-', 'PNS', 'TNI/Polri', 'Guru/Dosen', 'Dokter/Tenaga Medis', 'Pegawai Swasta', 'Pegawai BUMN/BUMD', 'Wiraswasta / Pengusaha', 'Pedagang', 'Petani', 'Peternak', 'Nelayan', 'Buruh', 'Sopir/Driver', 'Pensiunan', 'Lainnya'];
                    foreach($pekerjaan_list as $p): 
                    ?>
                        <option value="<?= $p ?>" <?= (($userProfile['pekerjaan_ayah'] ?? '') == $p) ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Nama Ibu</label>
                <input type="text" name="nama_ibu" value="<?= htmlspecialchars($userProfile['nama_ibu'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Pekerjaan Ibu</label>
                <select name="pekerjaan_ibu" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;">
                    <?php 
                    $pekerjaan_ibu_list = ['-', 'Ibu Rumah Tangga', 'PNS', 'TNI/Polri', 'Guru/Dosen', 'Dokter/Tenaga Medis', 'Pegawai Swasta', 'Pegawai BUMN/BUMD', 'Wiraswasta / Pengusaha', 'Pedagang', 'Petani', 'Buruh', 'Pensiunan', 'Lainnya'];
                    foreach($pekerjaan_ibu_list as $p): 
                    ?>
                        <option value="<?= $p ?>" <?= (($userProfile['pekerjaan_ibu'] ?? '') == $p) ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Penghasilan Orang Tua</label>
                <input type="text" name="penghasilan_ortu" value="<?= htmlspecialchars($userProfile['penghasilan_ortu'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;" placeholder="Cth: Rp 2.000.000">
            </div>

            <!-- Bagian Wali (Opsional) -->
            <div style="margin-top: 25px; margin-bottom: 15px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                <h4 style="font-size: 0.85rem; color: #64748b; margin-bottom: 10px; font-weight: 700; text-transform: uppercase;">Data Wali (Opsional)</h4>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Nama Wali</label>
                <input type="text" name="nama_wali" value="<?= htmlspecialchars($userProfile['nama_wali'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
            </div>

            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Pekerjaan Wali</label>
                    <input type="text" name="pekerjaan_wali" value="<?= htmlspecialchars($userProfile['pekerjaan_wali'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">No HP Wali</label>
                    <input type="tel" name="no_hp_wali" value="<?= htmlspecialchars($userProfile['no_hp_wali'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($isGuru): ?>
        <!-- Data Kepegawaian & Rekening (Guru) -->
        <div style="width: 100%; background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 20px; padding: 20px;">
            <h3 style="font-size: 0.9rem; color: #d97706; margin-bottom: 15px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="briefcase" style="width: 18px;"></i> Data Kepegawaian & Rekening
            </h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">NPK</label>
                    <input type="text" name="npk" value="<?= htmlspecialchars($userProfile['npk'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">NPWP</label>
                    <input type="text" name="npwp" value="<?= htmlspecialchars($userProfile['npwp'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Nomor Rekening</label>
                <input type="text" name="no_rekening" value="<?= htmlspecialchars($userProfile['no_rekening'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
            </div>
        </div>

        <!-- Data Sertifikasi (Guru) -->
        <div style="width: 100%; background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 20px; padding: 20px;">
            <h3 style="font-size: 0.9rem; color: #8b5cf6; margin-bottom: 15px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="award" style="width: 18px;"></i> Data Sertifikasi
            </h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">NRG</label>
                    <input type="text" name="nrg" value="<?= htmlspecialchars($userProfile['nrg'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">No. Peserta</label>
                    <input type="text" name="no_peserta_sertifikasi" value="<?= htmlspecialchars($userProfile['no_peserta_sertifikasi'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">No Sertifikat</label>
                    <input type="text" name="no_sertifikat" value="<?= htmlspecialchars($userProfile['no_sertifikat'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Tgl TTD</label>
                    <input type="date" name="tgl_sertifikat" value="<?= htmlspecialchars($userProfile['tgl_sertifikat'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Jenjang Sertifikat</label>
                    <select name="jenjang_sertifikat" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none; appearance: auto;">
                        <option value="" <?= empty($userProfile['jenjang_sertifikat']) ? 'selected' : '' ?>>- Pilih -</option>
                        <option value="RA" <?= (($userProfile['jenjang_sertifikat'] ?? '') == 'RA') ? 'selected' : '' ?>>RA</option>
                        <option value="MI" <?= (($userProfile['jenjang_sertifikat'] ?? '') == 'MI') ? 'selected' : '' ?>>MI</option>
                        <option value="MTs" <?= (($userProfile['jenjang_sertifikat'] ?? '') == 'MTs') ? 'selected' : '' ?>>MTs</option>
                        <option value="MA" <?= (($userProfile['jenjang_sertifikat'] ?? '') == 'MA') ? 'selected' : '' ?>>MA</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 6px;">Mapel Sertifikat</label>
                    <input type="text" name="mapel_sertifikat" value="<?= htmlspecialchars($userProfile['mapel_sertifikat'] ?? '') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #1e293b; outline: none;">
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Submit Button -->
        <button type="submit" style="width: 100%; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 16px; border-radius: 20px; font-size: 1rem; font-weight: 800; font-family: 'Outfit', sans-serif; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;">
            <i data-lucide="save"></i> Simpan Perubahan
        </button>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        if(input.files[0].size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran Terlalu Besar',
                text: 'Ukuran foto maksimal adalah 2 MB.'
            });
            input.value = '';
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('foto_preview');
            let icon = document.getElementById('foto_icon');
            if(preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            if(icon) {
                icon.style.display = 'none';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const selProv = document.getElementById('sel_prov');
    const selKota = document.getElementById('sel_kota');
    const selKec = document.getElementById('sel_kec');
    const selDesa = document.getElementById('sel_desa');
    
    const valProv = document.getElementById('val_prov');
    const valKota = document.getElementById('val_kota');
    const valKec = document.getElementById('val_kec');
    const valDesa = document.getElementById('val_desa');

    // Fetch Provinces
    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json`)
        .then(response => response.json())
        .then(provinces => {
            let options = '<option value="">-- Pilih Provinsi --</option>';
            provinces.forEach(p => {
                options += `<option value="${p.id}" data-name="${p.name}">${p.name}</option>`;
            });
            selProv.innerHTML = options;
        });

    selProv.addEventListener('change', function() {
        selKota.innerHTML = '<option value="">-- Pilih Kab/Kota --</option>';
        selKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        selDesa.innerHTML = '<option value="">-- Pilih Desa --</option>';
        selKota.disabled = true;
        selKec.disabled = true;
        selDesa.disabled = true;
        
        let selected = this.options[this.selectedIndex];
        valProv.value = selected.getAttribute('data-name') || '';

        if(this.value) {
            selKota.disabled = false;
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.value}.json`)
                .then(response => response.json())
                .then(regencies => {
                    let options = '<option value="">-- Pilih Kab/Kota --</option>';
                    regencies.forEach(r => {
                        options += `<option value="${r.id}" data-name="${r.name}">${r.name}</option>`;
                    });
                    selKota.innerHTML = options;
                });
        } else {
            valKota.value = ''; valKec.value = ''; valDesa.value = '';
        }
    });

    selKota.addEventListener('change', function() {
        selKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        selDesa.innerHTML = '<option value="">-- Pilih Desa --</option>';
        selKec.disabled = true;
        selDesa.disabled = true;

        let selected = this.options[this.selectedIndex];
        valKota.value = selected.getAttribute('data-name') || '';

        if(this.value) {
            selKec.disabled = false;
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.value}.json`)
                .then(response => response.json())
                .then(districts => {
                    let options = '<option value="">-- Pilih Kecamatan --</option>';
                    districts.forEach(d => {
                        options += `<option value="${d.id}" data-name="${d.name}">${d.name}</option>`;
                    });
                    selKec.innerHTML = options;
                });
        } else {
            valKec.value = ''; valDesa.value = '';
        }
    });

    selKec.addEventListener('change', function() {
        selDesa.innerHTML = '<option value="">-- Pilih Desa --</option>';
        selDesa.disabled = true;

        let selected = this.options[this.selectedIndex];
        valKec.value = selected.getAttribute('data-name') || '';

        if(this.value) {
            selDesa.disabled = false;
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.value}.json`)
                .then(response => response.json())
                .then(villages => {
                    let options = '<option value="">-- Pilih Desa --</option>';
                    villages.forEach(v => {
                        options += `<option value="${v.id}" data-name="${v.name}">${v.name}</option>`;
                    });
                    selDesa.innerHTML = options;
                });
        } else {
            valDesa.value = '';
        }
    });

    selDesa.addEventListener('change', function() {
        let selected = this.options[this.selectedIndex];
        valDesa.value = selected.getAttribute('data-name') || '';
    });
});
</script>
