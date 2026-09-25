<div class="apk-vector-header" style="padding-bottom: 70px; justify-content: center; text-align: center; position: relative; background-image: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
    <div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Data Siswa</div>
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.8); margin-top: 5px;">Anak Wali - Kelas <?= htmlspecialchars($nama_kelas_raw) ?></div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; min-height: 70vh;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-left: 5px;">
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;">Daftar Siswa</h3>
        <div style="font-size: 0.75rem; font-weight: 800; background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 12px;"><?= count($siswas) ?> Siswa</div>
    </div>

    <?php if (empty($siswas)): ?>
        <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
            <i data-lucide="users" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 15px;"></i>
            <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 600;">Belum ada siswa yang dimasukkan ke kelas ini.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <style>
                @keyframes slideUp {
                    from { transform: translateY(100%); }
                    to { transform: translateY(0); }
                }
            </style>
            <?php foreach ($siswas as $idx => $s): ?>
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 45px; height: 45px; border-radius: 50%; background: #e2e8f0; overflow: hidden; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; color: #64748b;">
                            <?php if (!empty($s['foto']) && file_exists(__DIR__ . '/../../../public/uploads/siswa/' . $s['foto'])): ?>
                                <img src="<?= \App\Core\Helper::url('/public/uploads/siswa/' . $s['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?= substr($s['nama'], 0, 2) ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 800; color: #1e293b; line-height: 1.3;"><?= htmlspecialchars($s['nama']) ?></div>
                            <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 2px;">NIS: <?= htmlspecialchars($s['nis'] ?? '-') ?></div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <?php if (strtolower($s['status']) == 'aktif'): ?>
                            <span style="background: #ecfdf5; color: #10b981; padding: 4px 8px; border-radius: 8px; font-size: 0.65rem; font-weight: 800;">Aktif</span>
                        <?php else: ?>
                            <span style="background: #fef2f2; color: #ef4444; padding: 4px 8px; border-radius: 8px; font-size: 0.65rem; font-weight: 800;"><?= htmlspecialchars($s['status']) ?></span>
                        <?php endif; ?>
                        
                        <a href="/apk/wali-kelas/siswa/profil/<?= $s['id'] ?>" style="padding: 4px 8px; background: #eef2ff; color: #4f46e5; border: none; border-radius: 8px; font-weight: 700; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; gap: 4px; text-decoration: none;">
                            <i data-lucide="user" style="width: 12px; height: 12px;"></i> Profil
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
