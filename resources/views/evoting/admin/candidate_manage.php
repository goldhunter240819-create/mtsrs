<div class="modern-page-header" style="background: linear-gradient(135deg, #0f172a, #334155);">
    <a href="<?php echo \App\Core\Helper::url('/evoting/admin'); ?>" style="color: rgba(255,255,255,0.8); display: inline-flex; align-items: center; gap: 5px; text-decoration: none; font-size: 13px; margin-bottom: 10px;">
        <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Kembali ke Dashboard
    </a>
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="users" style="color: rgba(255,255,255,0.8);"></i> Kelola Kandidat
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Event: <strong><?= htmlspecialchars($eventData['nama_event']) ?></strong></p>
    </div>
</div>

<div class="z-card" style="margin-top: 2rem; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Daftar Kandidat</h2>
        <button onclick="document.getElementById('modal-add-candidate').style.display='flex'; lucide.createIcons();" class="btn btn-primary">
            <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i> Tambah Kandidat
        </button>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php if(empty($candidatesData)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b;">
                <i data-lucide="users" style="width: 48px; height: 48px; margin-bottom: 10px; opacity: 0.5;"></i>
                <p style="margin: 0;">Belum ada kandidat untuk event ini.</p>
            </div>
        <?php else: ?>
            <?php foreach($candidatesData as $c): ?>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
                <div style="height: 120px; background: linear-gradient(135deg, #e2e8f0, #f8fafc); position: relative; display: flex; align-items: center; justify-content: center;">
                    <div style="position: absolute; top: 10px; right: 10px; width: 35px; height: 35px; border-radius: 50%; background: var(--z-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                        <?= $c['no_urut'] ?>
                    </div>
                    <?php if(!empty($c['foto'])): ?>
                        <img src="<?= \App\Core\Helper::url('/public/uploads/evoting/'.$c['foto']) ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transform: translateY(20px);">
                    <?php else: ?>
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: #cbd5e1; display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transform: translateY(20px); color: white;">
                            <i data-lucide="user" style="width: 40px; height: 40px;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div style="padding: 30px 20px 20px; text-align: center; flex: 1;">
                    <h3 style="margin: 0 0 5px 0; font-size: 1.1rem; color: #0f172a; font-weight: 800;"><?= htmlspecialchars($c['nama_kandidat']) ?></h3>
                    <div style="font-size: 13px; color: #64748b; margin-top: 15px; text-align: left; background: #f1f5f9; padding: 10px; border-radius: 8px;">
                        <strong>Visi:</strong><br>
                        <?= nl2br(htmlspecialchars($c['visi'] ?: '-')) ?>
                    </div>
                </div>
                <div style="padding: 15px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px;">
                    <a href="<?php echo \App\Core\Helper::url('/evoting/admin/delete-candidate?id='.$c['id'].'&event_id='.$eventId); ?>" class="btn btn-sm btn-danger" style="flex: 1; justify-content: center;" onclick="return confirm('Yakin ingin menghapus kandidat ini?')">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Kandidat -->
<div id="modal-add-candidate" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Kandidat</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').style.display='none'">&times;</button>
        </div>
        <div class="modal-body">
            <form action="<?php echo \App\Core\Helper::url('/evoting/admin/store-candidate'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="event_id" value="<?= $eventId ?>">
                
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="width: 100px;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">No. Urut</label>
                        <input type="number" name="no_urut" class="z-input" required min="1">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">Nama Kandidat</label>
                        <input type="text" name="nama_kandidat" class="z-input" placeholder="Contoh: Budi Santoso" required>
                    </div>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">Foto Kandidat (Opsional)</label>
                    <input type="file" name="foto" class="z-input" accept="image/*">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">Visi</label>
                    <textarea name="visi" class="z-input" rows="3" placeholder="Visi kandidat..."></textarea>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;">Misi</label>
                    <textarea name="misi" class="z-input" rows="4" placeholder="Misi kandidat..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-outline" onclick="this.closest('.modal-overlay').style.display='none'">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kandidat</button>
                </div>
            </form>
        </div>
    </div>
</div>
