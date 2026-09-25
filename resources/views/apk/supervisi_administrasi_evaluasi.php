<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Evaluasi Adm.</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Supervisi Guru</div>
        </div>
    </div>
    <a href="/apk/supervisi-administrasi" style="display:inline-flex; align-items:center; gap:5px; color:#fff; text-decoration:none; margin-top:15px; font-size:0.8rem; font-weight:700; background:rgba(255,255,255,0.2); padding:6px 12px; border-radius:20px; border:1px solid rgba(255,255,255,0.3);">
        <i data-lucide="arrow-left" style="width:14px; height:14px;"></i> Kembali
    </a>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 30px;">
    
    <div style="background:#f8fafc; border-radius:15px; padding:15px; margin-bottom:20px; display:flex; align-items:center; gap:12px; border:1px solid #e2e8f0;">
        <div style="width:45px; height:45px; background:#e0f2fe; color:#0284c7; border-radius:12px; display:flex; align-items:center; justify-content:center;">
            <i data-lucide="user" style="width:20px; height:20px;"></i>
        </div>
        <div>
            <div style="font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:2px;">NAMA GURU</div>
            <div style="font-weight:800; color:#1e293b; font-size:1rem;"><?= htmlspecialchars($guru['nama']) ?></div>
            <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">NIP: <?= htmlspecialchars($guru['nip'] ?? '-') ?></div>
        </div>
    </div>

    <form id="formSupervisi" onsubmit="event.preventDefault(); saveSupervisi();">
        <input type="hidden" name="guru_id" value="<?= $guru['id'] ?>">
        
        <div style="margin-bottom: 20px;">
            <label style="font-size:0.8rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Tanggal Supervisi</label>
            <input type="date" name="tanggal_supervisi" style="width:100%; padding:12px; border-radius:12px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;" required value="<?= $supervisi ? $supervisi['tanggal_supervisi'] : date('Y-m-d') ?>">
        </div>

        <h4 style="margin: 0 0 10px; color: #1e293b; font-size: 0.95rem; font-weight: 800;">Instrumen Penilaian</h4>
        
        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom: 25px;">
            <?php
            $instrumen_items = [
                'silabus' => ['label' => 'Silabus / ATP', 'db_jenis' => 'Silabus / ATP'],
                'rpp' => ['label' => 'RPP / Modul Ajar', 'db_jenis' => 'RPP / Modul Ajar'],
                'prota' => ['label' => 'Program Tahunan', 'db_jenis' => 'Program Tahunan (Prota)'],
                'promes' => ['label' => 'Program Semester', 'db_jenis' => 'Program Semester (Promes)'],
                'kkm' => ['label' => 'KKM / KKTP', 'db_jenis' => 'KKM / KKTP'],
                'jurnal' => ['label' => 'Bank Soal / Evaluasi', 'db_jenis' => 'Bank Soal / Evaluasi'],
                'absen' => ['label' => 'Absen Siswa', 'db_jenis' => 'Daftar Hadir Siswa'],
                'nilai' => ['label' => 'Buku Nilai', 'db_jenis' => 'Buku Nilai']
            ];
            foreach ($instrumen_items as $key => $item):
                $val = $supervisi['instrumen'][$key] ?? '';
                $berkasTerkait = $berkas_guru[$item['db_jenis']] ?? [];
            ?>
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                <div style="padding:12px 15px; background:#f8fafc; border-bottom:1px solid #f1f5f9;">
                    <div style="font-weight:700; color:#334155; font-size:0.85rem; margin-bottom:5px;"><?= $item['label'] ?></div>
                    <?php if (!empty($berkasTerkait)): ?>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <?php foreach ($berkasTerkait as $berkas): ?>
                                <a href="/public/uploads/berkas/<?= htmlspecialchars($berkas['file_nama']) ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.7rem; color: #0ea5e9; text-decoration: none; background: #e0f2fe; padding: 4px 8px; border-radius: 6px; width: fit-content; max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <i data-lucide="external-link" style="width: 10px; height: 10px; flex-shrink: 0;"></i> 
                                    <span style="overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($berkas['judul_berkas']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="font-size: 0.7rem; color: #94a3b8; font-style: italic;">Tidak ada file dilampirkan</div>
                    <?php endif; ?>
                </div>
                <div style="padding:10px 15px;">
                    <select name="instrumen[<?= $key ?>]" style="width: 100%; padding: 10px; font-size: 0.85rem; border-radius: 8px; border:1px solid #cbd5e1; color:#334155; font-weight:600; outline:none; background:#fff;">
                        <option value="">- Pilih Nilai -</option>
                        <option value="Ada" <?= $val === 'Ada' ? 'selected' : '' ?>>Ada Lengkap</option>
                        <option value="Tidak Lengkap" <?= $val === 'Tidak Lengkap' ? 'selected' : '' ?>>Tidak Lengkap</option>
                        <option value="Tidak Ada" <?= $val === 'Tidak Ada' ? 'selected' : '' ?>>Tidak Ada</option>
                    </select>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="font-size:0.8rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Catatan (Opsional)</label>
            <textarea name="catatan" style="width:100%; padding:12px; border-radius:12px; border:1px solid #e2e8f0; font-weight:600; color:#1e293b; background:#f8fafc; resize:vertical; font-size:0.85rem;" rows="3" placeholder="Tulis catatan..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-size:0.8rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Tindak Lanjut (Opsional)</label>
            <textarea name="tindak_lanjut" style="width:100%; padding:12px; border-radius:12px; border:1px solid #e2e8f0; font-weight:600; color:#1e293b; background:#f8fafc; resize:vertical; font-size:0.85rem;" rows="3" placeholder="Tulis tindak lanjut..."><?= htmlspecialchars($supervisi['tindak_lanjut'] ?? '') ?></textarea>
        </div>

        <button type="submit" id="btnSimpan" style="width:100%; background:linear-gradient(135deg, #0ea5e9, #0284c7); color:white; padding:15px; border:none; border-radius:12px; font-weight:800; font-size:1rem; display:flex; align-items:center; justify-content:center; gap:8px;">
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
        
        fetch('/apk/supervisi-administrasi/save', {
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
                    window.location.href = '/apk/supervisi-administrasi';
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
