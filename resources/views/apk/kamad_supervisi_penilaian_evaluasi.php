<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Evaluasi Penilaian</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Supervisi Penilaian</div>
        </div>
    </div>
    <a href="/apk/kamad/monitor-penilaian?guru_id=<?= $guru_id ?>" style="display:inline-flex; align-items:center; gap:5px; color:#fff; text-decoration:none; margin-top:15px; font-size:0.8rem; font-weight:700; background:rgba(255,255,255,0.2); padding:6px 12px; border-radius:20px; border:1px solid rgba(255,255,255,0.3);">
        <i data-lucide="arrow-left" style="width:14px; height:14px;"></i> Kembali
    </a>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 30px;">
    
    <div style="background:#f8fafc; border-radius:15px; padding:15px; margin-bottom:20px; display:flex; align-items:center; gap:12px; border:1px solid #e2e8f0;">
        <div style="width:45px; height:45px; background:#fdf4ff; color:#c026d3; border-radius:12px; display:flex; align-items:center; justify-content:center;">
            <i data-lucide="user-check" style="width:20px; height:20px;"></i>
        </div>
        <div>
            <div style="font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:2px;">GURU DISUPERVISI</div>
            <div style="font-weight:800; color:#1e293b; font-size:1rem;"><?= htmlspecialchars($guru['nama']) ?></div>
            <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">NIP: <?= htmlspecialchars($guru['nip'] ?? '-') ?></div>
        </div>
    </div>

    <form id="formSupervisi" onsubmit="event.preventDefault(); saveSupervisi();">
        <input type="hidden" name="guru_id" value="<?= $guru_id ?>">
        <input type="hidden" name="kelas_id" value="<?= $kelas_id ?>">
        <input type="hidden" name="mapel_id" value="<?= $mapel_id ?>">
        <input type="hidden" name="jenis_evaluasi" value="<?= htmlspecialchars($jenis_evaluasi) ?>">
        
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:15px; margin-bottom:20px;">
            <h4 style="margin: 0 0 15px; color: #1e293b; font-size: 0.95rem; font-weight: 800; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">Informasi Evaluasi</h4>
            
            <div style="display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.8rem; font-weight:700; color:#64748b;">Kelas</span>
                    <span style="font-size:0.85rem; font-weight:800; color:#1e293b;"><?= htmlspecialchars($kelas['nama_kelas']) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.8rem; font-weight:700; color:#64748b;">Mata Pelajaran</span>
                    <span style="font-size:0.85rem; font-weight:800; color:#1e293b;"><?= htmlspecialchars($mapel['nama_mapel']) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.8rem; font-weight:700; color:#64748b;">Jenis Penilaian</span>
                    <span style="font-size:0.85rem; font-weight:800; color:#c026d3;"><?= htmlspecialchars($jenis_evaluasi) ?></span>
                </div>
            </div>
        </div>

        <h4 style="margin: 0 0 10px; color: #1e293b; font-size: 0.95rem; font-weight: 800;">Kelengkapan Administrasi Penilaian</h4>
        
        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 25px;">
            <label style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:12px 15px; display:flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="ada_kisi" value="1" <?= ($supervisi['ada_kisi'] ?? 0) == 1 ? 'checked' : '' ?> style="width:20px; height:20px; accent-color:#c026d3;">
                <span style="font-size:0.85rem; font-weight:700; color:#334155;">Ada Kisi-kisi Soal</span>
            </label>
            <label style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:12px 15px; display:flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="ada_analisis" value="1" <?= ($supervisi['ada_analisis'] ?? 0) == 1 ? 'checked' : '' ?> style="width:20px; height:20px; accent-color:#c026d3;">
                <span style="font-size:0.85rem; font-weight:700; color:#334155;">Ada Analisis Butir Soal</span>
            </label>
            <label style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:12px 15px; display:flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="ada_remedial" value="1" <?= ($supervisi['ada_remedial'] ?? 0) == 1 ? 'checked' : '' ?> style="width:20px; height:20px; accent-color:#c026d3;">
                <span style="font-size:0.85rem; font-weight:700; color:#334155;">Ada Program Remedial/Pengayaan</span>
            </label>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="font-size:0.8rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Catatan (Kelebihan / Kekurangan)</label>
            <textarea name="catatan" style="width:100%; padding:12px; border-radius:12px; border:1px solid #e2e8f0; font-weight:600; color:#1e293b; background:#f8fafc; resize:vertical; font-size:0.85rem;" rows="3" placeholder="Tulis catatan..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-size:0.8rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Saran & Tindak Lanjut</label>
            <textarea name="tindak_lanjut" style="width:100%; padding:12px; border-radius:12px; border:1px solid #e2e8f0; font-weight:600; color:#1e293b; background:#f8fafc; resize:vertical; font-size:0.85rem;" rows="3" placeholder="Tulis tindak lanjut..."><?= htmlspecialchars($supervisi['tindak_lanjut'] ?? '') ?></textarea>
        </div>

        <button type="submit" id="btnSimpan" style="width:100%; background:linear-gradient(135deg, #c026d3, #a21caf); color:white; padding:15px; border:none; border-radius:12px; font-weight:800; font-size:1rem; display:flex; align-items:center; justify-content:center; gap:8px;">
            <i data-lucide="save" style="width:18px;"></i> Simpan Evaluasi
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();

    function saveSupervisi() {
        const form = document.getElementById('formSupervisi');
        const formData = new FormData(form);
        const btn = document.getElementById('btnSimpan');
        
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader" style="width:18px;" class="animate-spin"></i> Menyimpan...';
        lucide.createIcons();
        
        fetch('/apk/kamad/supervisi-penilaian/save', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/apk/kamad/monitor-penilaian?guru_id=<?= $guru_id ?>';
                });
            } else {
                Swal.fire('Gagal', res.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="save" style="width:18px;"></i> Simpan Evaluasi';
                lucide.createIcons();
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="save" style="width:18px;"></i> Simpan Evaluasi';
            lucide.createIcons();
        });
    }
</script>
