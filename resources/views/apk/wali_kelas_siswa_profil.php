<div class="apk-vector-header" style="padding-bottom: 70px; justify-content: center; text-align: center; position: relative; background-image: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
    <a href="/apk/wali-kelas/siswa" style="position: absolute; left: 20px; top: 20px; color: #fff; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.2);">
        <i data-lucide="arrow-left" style="width: 20px; height: 20px;"></i>
    </a>
    <div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Profil Siswa</div>
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.8); margin-top: 5px;">Anak Wali - Kelas <?= htmlspecialchars($keterangan ? trim(str_ireplace('Kelas ', '', $keterangan)) : '') ?></div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; min-height: 70vh; padding-top: 30px;">
    
    <div style="text-align:center; margin-bottom:25px;">
        <div style="width:120px; height:160px; border-radius:16px; background:#e2e8f0; margin:0 auto 15px; overflow:hidden; display:flex; align-items:center; justify-content:center; font-size:3rem; font-weight:700; color:#64748b; border: 4px solid #eef2ff; box-shadow: 0 6px 15px rgba(0,0,0,0.08);">
            <?php if (!empty($s['foto']) && file_exists(__DIR__ . '/../../../public/uploads/siswa/' . $s['foto'])): ?>
                <img src="<?= \App\Core\Helper::url('/public/uploads/siswa/' . $s['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover; object-position: top;">
            <?php else: ?>
                <?= substr($s['nama'], 0, 2) ?>
            <?php endif; ?>
        </div>
        <div style="font-size:1.2rem; font-weight:900; color:#0f172a; line-height:1.2;"><?= htmlspecialchars($s['nama']) ?></div>
        <div style="font-size:0.85rem; color:#64748b; margin-top:6px;">NIS: <?= htmlspecialchars($s['nis'] ?? '-') ?> &bull; NISN: <?= htmlspecialchars($s['nisn'] ?? '-') ?></div>
        <div style="margin-top: 10px;">
            <?php if (strtolower($s['status']) == 'aktif'): ?>
                <span style="background: #ecfdf5; color: #10b981; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 800;">Aktif</span>
            <?php else: ?>
                <span style="background: #fef2f2; color: #ef4444; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 800;"><?= htmlspecialchars($s['status']) ?></span>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="background:#fff; border-radius:16px; padding:20px; border:1px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.02); margin-bottom: 15px;">
        <div style="font-size:0.8rem; font-weight:800; color:#4f46e5; text-transform:uppercase; margin-bottom:15px; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="user" style="width: 16px; height: 16px;"></i> Data Pribadi
        </div>
        <div style="display:flex; flex-direction:column; gap:15px;">
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">NIK / No. KK</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= htmlspecialchars($s['nik'] ?? '-') ?> / <?= htmlspecialchars($s['no_kk'] ?? '-') ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Tempat, Tanggal Lahir</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= htmlspecialchars($s['tempat_lahir'] ?? '-') ?>, <?= !empty($s['tanggal_lahir']) ? date('d M Y', strtotime($s['tanggal_lahir'])) : '-' ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Jenis Kelamin</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= (isset($s['jenis_kelamin']) && $s['jenis_kelamin'] == 'P') ? 'Perempuan' : ((isset($s['jenis_kelamin']) && $s['jenis_kelamin'] == 'L') ? 'Laki-laki' : '-') ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Agama</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= htmlspecialchars($s['agama'] ?? '-') ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Alamat Lengkap</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b; line-height:1.4;">
                    <?= htmlspecialchars($s['alamat'] ?? '-') ?> 
                    <?= (!empty($s['rt']) ? "RT " . htmlspecialchars($s['rt']) : '') ?> 
                    <?= (!empty($s['rw']) ? "RW " . htmlspecialchars($s['rw']) : '') ?><br>
                    <?= (!empty($s['desa']) ? "Ds/Kel. " . htmlspecialchars($s['desa']) : '') ?>, 
                    <?= (!empty($s['kecamatan']) ? "Kec. " . htmlspecialchars($s['kecamatan']) : '') ?><br>
                    <?= (!empty($s['kota']) ? htmlspecialchars($s['kota']) : '') ?>
                </div>
            </div>
        </div>
    </div>

    <div style="background:#fff; border-radius:16px; padding:20px; border:1px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.02); margin-bottom: 15px;">
        <div style="font-size:0.8rem; font-weight:800; color:#4f46e5; text-transform:uppercase; margin-bottom:15px; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="graduation-cap" style="width: 16px; height: 16px;"></i> Riwayat Pendidikan
        </div>
        <div style="display:flex; flex-direction:column; gap:15px;">
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Sekolah Asal</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= htmlspecialchars($s['sekolah_asal'] ?? '-') ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Pendidikan Terakhir</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= htmlspecialchars($s['pendidikan_terakhir'] ?? '-') ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">No. Ijazah</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= htmlspecialchars($s['no_ijazah'] ?? '-') ?></div>
            </div>
        </div>
    </div>

    <div style="background:#fff; border-radius:16px; padding:20px; border:1px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.02); margin-bottom: 20px;">
        <div style="font-size:0.8rem; font-weight:800; color:#4f46e5; text-transform:uppercase; margin-bottom:15px; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="users" style="width: 16px; height: 16px;"></i> Data Orang Tua / Wali
        </div>
        <div style="display:flex; flex-direction:column; gap:15px;">
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Nama Ayah</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= htmlspecialchars($s['nama_ayah'] ?? '-') ?></div>
                <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">Pekerjaan: <?= htmlspecialchars($s['pekerjaan_ayah'] ?? '-') ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Nama Ibu</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= htmlspecialchars($s['nama_ibu'] ?? '-') ?></div>
                <div style="font-size:0.75rem; color:#94a3b8; margin-top:2px;">Pekerjaan: <?= htmlspecialchars($s['pekerjaan_ibu'] ?? '-') ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">No. HP Orang Tua / Wali</div>
                <div style="font-size:0.9rem; font-weight:700; color:#1e293b;">
                    <?php 
                    $no_hp = $s['no_hp_ortu'] ?? ($s['no_hp'] ?? ''); 
                    if (!empty($no_hp)): 
                    ?>
                        <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $no_hp)) ?>" target="_blank" style="color: #10b981; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="phone" style="width: 14px; height: 14px;"></i> <?= htmlspecialchars($no_hp) ?>
                        </a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
