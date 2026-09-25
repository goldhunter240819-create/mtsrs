<style>
    .cw-form-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .cw-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        margin-top: 15px;
    }
    .cw-input, .cw-select, .cw-textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.9rem;
        color: #334155;
        background: #f8fafc;
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }
    .cw-textarea {
        resize: vertical;
        min-height: 80px;
    }
    .cw-input:focus, .cw-select:focus, .cw-textarea:focus {
        border-color: #8b5cf6;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
    }
    .cw-btn {
        background: #8b5cf6;
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        width: 100%;
        justify-content: center;
        margin-top: 20px;
        transition: background 0.2s;
    }
    .cw-btn:active {
        background: #7c3aed;
    }

    .cw-history-card {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 12px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        display: flex;
        gap: 12px;
    }
    .cw-history-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .cw-history-icon.Positif { background: #dcfce7; color: #16a34a; }
    .cw-history-icon.Negatif { background: #fee2e2; color: #dc2626; }
    .cw-history-icon.Info { background: #e0f2fe; color: #0284c7; }
</style>

<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <a href="/apk/aplikasi-saya" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Wali Kelas</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Catatan Keseharian</div>
            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.9); margin-top: 2px;">
                <?= htmlspecialchars($nama_kelas) ?> &bull; SMT <?= $semester ?>
            </div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 40px;">
    
    <div style="margin-bottom: 15px; background: #f3e8ff; border: 1px solid #e9d5ff; border-radius: 12px; padding: 12px; display: flex; gap: 10px; align-items: flex-start;">
        <i data-lucide="info" style="color: #9333ea; width: 20px; flex-shrink: 0; margin-top: 2px;"></i>
        <div style="font-size: 0.8rem; color: #6b21a8; line-height: 1.4;">
            Catat keseharian siswa, baik itu apresiasi (Positif), teguran (Negatif), maupun sekadar Info.
        </div>
    </div>

    <div class="cw-form-card">
        <div style="font-size: 1rem; font-weight: 800; color: #1e293b; margin-bottom: 5px;">Tambah Catatan Baru</div>
        
        <form id="formCatatan">
            <label class="cw-label" style="margin-top: 10px;">Tanggal</label>
            <input type="date" name="tanggal" class="cw-input" value="<?= date('Y-m-d') ?>" required>

            <label class="cw-label">Siswa</label>
            <select name="siswa_id" class="cw-select" required>
                <option value="">-- Pilih Siswa --</option>
                <?php foreach ($siswaList as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?></option>
                <?php endforeach; ?>
            </select>

            <label class="cw-label">Jenis Catatan</label>
            <select name="jenis" class="cw-select" required>
                <option value="Positif">🌟 Positif (Apresiasi)</option>
                <option value="Negatif">⚠️ Negatif (Masalah/Teguran)</option>
                <option value="Info" selected>📝 Info (Lain-lain)</option>
            </select>

            <label class="cw-label">Isi Catatan</label>
            <textarea name="catatan" class="cw-textarea" placeholder="Tuliskan catatan keseharian siswa di sini..." required></textarea>

            <button type="button" class="cw-btn" onclick="simpanCatatan()">
                <i data-lucide="plus" style="width: 18px;"></i> Tambahkan
            </button>
        </form>
    </div>

    <div style="font-size: 0.95rem; font-weight: 800; color: #334155; margin-bottom: 12px; margin-top: 25px;">Riwayat Catatan Kelas</div>
    
    <?php if (empty($riwayatList)): ?>
        <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 0.85rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
            Belum ada catatan keseharian siswa.
        </div>
    <?php else: ?>
        <div id="historyContainer">
            <?php foreach ($riwayatList as $r): ?>
                <div class="cw-history-card">
                    <div class="cw-history-icon <?= htmlspecialchars($r['jenis']) ?>">
                        <?php if($r['jenis'] == 'Positif'): ?>
                            <i data-lucide="star" style="width: 20px;"></i>
                        <?php elseif($r['jenis'] == 'Negatif'): ?>
                            <i data-lucide="alert-triangle" style="width: 20px;"></i>
                        <?php else: ?>
                            <i data-lucide="message-square" style="width: 20px;"></i>
                        <?php endif; ?>
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <div style="font-size: 0.85rem; font-weight: 800; color: #1e293b;"><?= htmlspecialchars($r['nama_siswa']) ?></div>
                            <div style="font-size: 0.7rem; color: #94a3b8;"><?= date('d/m/Y', strtotime($r['tanggal'])) ?></div>
                        </div>
                        <div style="font-size: 0.8rem; color: #475569; line-height: 1.4; word-wrap: break-word;">
                            <?= nl2br(htmlspecialchars($r['catatan'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
function simpanCatatan() {
    const form = document.getElementById('formCatatan');
    if (!form.reportValidity()) return;

    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData(form);

    fetch('/apk/wali-kelas/catatan/save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: 'Tidak dapat terhubung ke server.'
        });
    });
}
</script>
