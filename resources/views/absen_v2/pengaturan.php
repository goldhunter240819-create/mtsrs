<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#10b981;">ABSEN V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Konfigurasi Jam</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;background:#10b981;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;"><?php echo $initial; ?></div>
                <div>
                    <div style="font-weight:800;font-size:0.9rem;color:#0f172a;"><?php echo $nama; ?></div>
                </div>
            </div>
        </header>

        <div class="z-scroll" style="padding: 2rem;">
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pengaturan berhasil disimpan!',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    }).then(() => {
                        window.history.replaceState(null, null, window.location.pathname);
                    });
                });
            </script>
            <?php endif; ?>

            <style>
                .min-card { background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 1.5rem; }
                .min-card-title { font-size: 1rem; font-weight: 800; color: #1e293b; margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }
                .min-label { display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
                .min-input { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; font-size: 0.85rem; font-weight: 500; color: #0f172a; outline: none; transition: all 0.2s; font-family: inherit; }
                .min-input:focus { background: white; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
                .min-desc { font-size: 0.7rem; color: #94a3b8; margin-top: 4px; }
                .min-checkbox-list { border: 1px solid #e2e8f0; border-radius: 8px; max-height: 200px; overflow-y: auto; background: white; }
                .min-checkbox-item { display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: 0.2s; }
                .min-checkbox-item:hover { background: #f8fafc; }
                .min-checkbox-item:last-child { border-bottom: none; }
                .min-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
            </style>

            <form action="/absen/pengaturan/save" method="POST">
                
                <div class="min-grid">
                    <!-- Kolom Kiri: Waktu & Akses -->
                    <div>
                        <div class="min-card">
                            <h2 class="min-card-title"><i data-lucide="clock" style="color:#3b82f6; width:18px;"></i> Waktu Absensi</h2>
                            
                            <div style="margin-bottom: 1.2rem;">
                                <label class="min-label">Jam Mulai Scan</label>
                                <input type="time" name="jam_masuk_mulai" value="<?php echo htmlspecialchars($settings['jam_masuk_mulai'] ?? '05:00'); ?>" required class="min-input">
                                <div class="min-desc">Waktu paling awal diperbolehkan scan absen masuk.</div>
                            </div>
                            
                            <div style="margin-bottom: 1.2rem;">
                                <label class="min-label">Jam Masuk</label>
                                <input type="time" name="jam_masuk" value="<?php echo htmlspecialchars($settings['jam_masuk'] ?? '07:00'); ?>" required class="min-input">
                            </div>
                            
                            <div style="margin-bottom: 1.2rem;">
                                <label class="min-label">Jam Pulang</label>
                                <input type="time" name="jam_pulang" value="<?php echo htmlspecialchars($settings['jam_pulang'] ?? '15:00'); ?>" required class="min-input">
                            </div>

                            <div style="margin-bottom: 1.2rem;">
                                <label class="min-label">Toleransi Terlambat (Menit)</label>
                                <input type="number" name="toleransi_terlambat" value="<?php echo htmlspecialchars($settings['toleransi_terlambat'] ?? '15'); ?>" required class="min-input" min="0">
                                <div class="min-desc">Jika waktu masuk melebihi toleransi, maka dianggap terlambat.</div>
                            </div>
                            

                        </div>

                        <div class="min-card">
                            <h2 class="min-card-title"><i data-lucide="shield-check" style="color:#10b981; width:18px;"></i> Hak Akses Scanner</h2>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label class="min-label">Admin Khusus Guru</label>
                                <?php $selectedAdminsGuru = explode(',', $settings['admin_absen_guru'] ?? ''); ?>
                                <div class="min-checkbox-list">
                                    <?php foreach($userList as $u): ?>
                                    <label class="min-checkbox-item">
                                        <input type="checkbox" name="admin_absen_guru[]" value="<?php echo $u['id']; ?>" <?php echo in_array($u['id'], $selectedAdminsGuru) ? 'checked' : ''; ?> style="accent-color: #10b981;">
                                        <div style="font-size:0.8rem; font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($u['nama']); ?> <span style="font-size:0.7rem; color:#94a3b8; font-weight:400;">(<?php echo $u['role_id']==1 ? 'Admin' : 'Guru'; ?>)</span></div>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label class="min-label">Admin Izin Piket</label>
                                <?php $selectedAdminsIzin = explode(',', $settings['admin_izin_piket'] ?? ''); ?>
                                <div class="min-checkbox-list">
                                    <?php foreach($userList as $u): ?>
                                    <label class="min-checkbox-item">
                                        <input type="checkbox" name="admin_izin_piket[]" value="<?php echo $u['id']; ?>" <?php echo in_array($u['id'], $selectedAdminsIzin) ? 'checked' : ''; ?> style="accent-color: #f59e0b;">
                                        <div style="font-size:0.8rem; font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($u['nama']); ?> <span style="font-size:0.7rem; color:#94a3b8; font-weight:400;">(<?php echo $u['role_id']==1 ? 'Admin' : 'Guru'; ?>)</span></div>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div style="margin-bottom: 0;">
                                <label class="min-label">Admin Khusus Siswa</label>
                                <?php $selectedAdminsSiswa = explode(',', $settings['admin_absen_siswa'] ?? ''); ?>
                                <div class="min-checkbox-list">
                                    <?php foreach($userList as $u): ?>
                                    <label class="min-checkbox-item">
                                        <input type="checkbox" name="admin_absen_siswa[]" value="<?php echo $u['id']; ?>" <?php echo in_array($u['id'], $selectedAdminsSiswa) ? 'checked' : ''; ?> style="accent-color: #10b981;">
                                        <div style="font-size:0.8rem; font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($u['nama']); ?> <span style="font-size:0.7rem; color:#94a3b8; font-weight:400;">(<?php echo $u['role_id']==1 ? 'Admin' : 'Guru'; ?>)</span></div>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div style="margin-top: 1.5rem; margin-bottom: 0;">
                                <label class="min-label">Admin Izin Piket</label>
                                <?php $selectedAdminsPiket = explode(',', $settings['admin_izin_piket'] ?? ''); ?>
                                <div class="min-checkbox-list">
                                    <?php foreach($userList as $u): ?>
                                    <label class="min-checkbox-item">
                                        <input type="checkbox" name="admin_izin_piket[]" value="<?php echo $u['id']; ?>" <?php echo in_array($u['id'], $selectedAdminsPiket) ? 'checked' : ''; ?> style="accent-color: #3b82f6;">
                                        <div style="font-size:0.8rem; font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($u['nama']); ?> <span style="font-size:0.7rem; color:#94a3b8; font-weight:400;">(<?php echo $u['role_id']==1 ? 'Admin' : 'Guru'; ?>)</span></div>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                                <div class="min-desc" style="margin-top: 5px;">Guru/Admin yang menerima notifikasi dan mengurus izin guru.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: TTS -->
                    <div>
                        <div class="min-card" style="height: 100%; margin-bottom: 0;">
                            <h2 class="min-card-title"><i data-lucide="mic" style="color:#f59e0b; width:18px;"></i> Suara AI (Text-to-Speech)</h2>
                            <div class="min-desc" style="margin-bottom: 1.5rem;">Gunakan Enter untuk variasi ucapan acak. Tag: [nama], [waktu], [desc].</div>
                            
                            <div style="margin-bottom: 1.2rem;">
                                <label class="min-label">Guru Masuk</label>
                                <textarea name="tts_guru_masuk" rows="2" class="min-input"><?php echo htmlspecialchars($settings['tts_guru_masuk'] ?? ''); ?></textarea>
                            </div>

                            <div style="margin-bottom: 1.2rem;">
                                <label class="min-label">Siswa Masuk</label>
                                <textarea name="tts_siswa_masuk" rows="2" class="min-input"><?php echo htmlspecialchars($settings['tts_siswa_masuk'] ?? ''); ?></textarea>
                            </div>
                            <div style="margin-bottom: 1.2rem;">
                                <label class="min-label">Siswa Pulang</label>
                                <textarea name="tts_siswa_pulang" rows="2" class="min-input"><?php echo htmlspecialchars($settings['tts_siswa_pulang'] ?? ''); ?></textarea>
                            </div>
                            <div style="margin-bottom: 1.5rem;">
                                <label class="min-label">Scan Ditolak / Gagal</label>
                                <textarea name="tts_gagal" rows="2" class="min-input"><?php echo htmlspecialchars($settings['tts_gagal'] ?? ''); ?></textarea>
                            </div>

                            <details style="font-size:0.75rem; color:#64748b; background:#f8fafc; padding:10px 14px; border-radius:8px; cursor:pointer;">
                                <summary style="font-weight:700; outline:none; user-select:none;">Tampilkan lebih banyak opsi...</summary>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:15px;">
                                    <div>
                                        <label class="min-label">Guru Terlambat</label>
                                        <textarea name="tts_guru_telat" rows="2" class="min-input"><?php echo htmlspecialchars($settings['tts_guru_telat'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="min-label">Guru Sudah Absen</label>
                                        <textarea name="tts_guru_sudah" rows="2" class="min-input"><?php echo htmlspecialchars($settings['tts_guru_sudah'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="min-label">Siswa Terlambat</label>
                                        <textarea name="tts_siswa_telat" rows="2" class="min-input"><?php echo htmlspecialchars($settings['tts_siswa_telat'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="min-label">Siswa Sudah Absen</label>
                                        <textarea name="tts_siswa_sudah" rows="2" class="min-input"><?php echo htmlspecialchars($settings['tts_siswa_sudah'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; text-align: right;">
                    <button type="submit" style="padding:12px 30px; background:#0f172a; color:white; border:none; border-radius:30px; font-weight:700; font-size:0.85rem; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition: 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                        <i data-lucide="save" style="width:16px;height:16px;"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('active');
    }
</script>
