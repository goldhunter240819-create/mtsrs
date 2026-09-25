<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 15px;">
        <a href="/apk/aplikasi-saya" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px);">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
        </a>
        <button onclick="document.getElementById('modalTambahJurnal').style.display='flex'" style="background: #fff; color: #14b8a6; border: none; padding: 8px 15px; border-radius: 20px; font-weight: 700; display: flex; align-items: center; gap: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Catat
        </button>
    </div>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #14b8a6;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #14b8a6; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="users"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Bimbingan Konseling</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Jurnal Konseling</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh; padding: 20px;">
    <?php if (empty($jurnalList)): ?>
        <div style="text-align: center; padding: 40px 20px; color: #64748b;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 15px; color: #cbd5e1;"></i>
            <h4 style="margin: 0 0 10px 0;">Belum Ada Sesi</h4>
            <p style="margin: 0; font-size: 0.9rem;">Catatan sesi bimbingan konseling dengan siswa akan tampil di sini.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($jurnalList as $j): ?>
                <div style="background: #fff; padding: 15px; border-radius: 16px; border: 1px solid rgba(20, 184, 166, 0.2); box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 3px;">
                                <i data-lucide="calendar" style="width:12px;height:12px;display:inline-block;"></i> <?= date('d M Y', strtotime($j['tanggal'])) ?>
                            </div>
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0f766e;">
                                <?= htmlspecialchars($j['nama_siswa'] ?? 'Siswa') ?>
                            </h4>
                        </div>
                        <div style="background: #f0fdfa; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; color: #0f766e; border: 1px solid rgba(20, 184, 166, 0.2);">
                            Kelas <?= htmlspecialchars($j['nama_kelas'] ?? '-') ?>
                        </div>
                    </div>
                    <div style="background: #f8fafc; padding: 10px; border-radius: 8px; margin-bottom: 8px;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Masalah/Topik</div>
                        <div style="font-size: 0.9rem; color: #334155; font-weight: 500; margin-top: 3px;">
                            <?= htmlspecialchars($j['masalah']) ?>
                        </div>
                    </div>
                    <?php if (!empty($j['tindak_lanjut'])): ?>
                    <div style="background: #f0fdfa; padding: 10px; border-radius: 8px;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #14b8a6; text-transform: uppercase;">Tindak Lanjut</div>
                        <div style="font-size: 0.9rem; color: #0f766e; font-weight: 500; margin-top: 3px;">
                            <?= htmlspecialchars($j['tindak_lanjut']) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Jurnal -->
<div id="modalTambahJurnal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: flex-end;">
    <div style="background: #fff; width: 100%; border-radius: 25px 25px 0 0; padding: 25px 20px; animation: slideUp 0.3s ease-out; max-height: 85vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; display: flex; align-items: center; gap: 8px;"><i data-lucide="edit-3" style="color: #14b8a6;"></i> Catat Sesi</h3>
            <div onclick="document.getElementById('modalTambahJurnal').style.display='none'" style="background: #f1f5f9; padding: 8px; border-radius: 50%; cursor: pointer;">
                <i data-lucide="x" style="width: 18px; height: 18px; color: #64748b;"></i>
            </div>
        </div>
        <form action="/apk/bk/jurnal/save" method="POST">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Pilih Siswa</label>
                <select name="siswa_id" required style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
                    <option value="">-- Pilih Siswa --</option>
                    <?php foreach ($siswaList as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?> (Kelas <?= htmlspecialchars($s['nama_kelas']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Tanggal Sesi</label>
                <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Masalah / Topik Konseling</label>
                <textarea name="masalah" rows="3" required placeholder="Deskripsikan masalah atau topik yang dibicarakan..." style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;"></textarea>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Tindak Lanjut / Solusi</label>
                <textarea name="tindak_lanjut" rows="3" placeholder="Apa hasil atau kesepakatan dari sesi ini?" style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;"></textarea>
            </div>
            
            <button type="submit" style="width: 100%; background: #14b8a6; color: #fff; padding: 15px; border: none; border-radius: 14px; font-weight: 800; font-size: 1rem; box-shadow: 0 4px 10px rgba(20, 184, 166, 0.3);">
                Simpan Jurnal
            </button>
        </form>
    </div>
</div>

<style>
@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
</style>
