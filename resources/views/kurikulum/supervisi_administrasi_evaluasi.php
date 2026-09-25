<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="mph-title" style="color: white; margin-bottom: 5px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="clipboard-edit" style="color: rgba(255,255,255,0.8); width: 24px; height: 24px;"></i> Form Supervisi Administrasi
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9); margin: 0;">Evaluasi kelengkapan berkas mengajar guru secara komprehensif.</p>
    </div>
    <a href="<?= \App\Core\Helper::url('/kurikulum/supervisi-administrasi') ?>" style="display: inline-flex; align-items: center; gap: 6px; color: #fff; text-decoration: none; font-size: 0.85rem; font-weight: 600; background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 20px; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.3); transition: all 0.2s;">
        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Kembali ke Daftar
    </a>
</div>

<div class="z-card" style="margin-top: 2rem; padding: 2rem;">
    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
        <div style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="user" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 4px;">NAMA GURU</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #0f172a;"><?= htmlspecialchars($guru['nama']) ?></div>
            <?php if (!empty($guru['nip'])): ?>
                <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">NIP: <?= htmlspecialchars($guru['nip']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <form id="formSupervisi" onsubmit="event.preventDefault(); saveSupervisi();">
        <input type="hidden" name="guru_id" value="<?= $guru['id'] ?>">
        
        <div style="margin-bottom: 25px;">
            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Tanggal Supervisi</label>
            <input type="date" name="tanggal_supervisi" class="z-input" style="max-width: 200px;" required value="<?= $supervisi ? $supervisi['tanggal_supervisi'] : date('Y-m-d') ?>">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Instrumen Administrasi Pembelajaran</label>
            <table class="z-table" style="border: 1px solid var(--z-border);">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 12px 15px;">Nama Dokumen</th>
                        <th style="width: 180px; text-align: center;">Penilaian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $instrumen_items = [
                        'silabus' => ['label' => 'Silabus / Alur Tujuan Pembelajaran (ATP)', 'db_jenis' => 'Silabus / ATP'],
                        'rpp' => ['label' => 'RPP / Modul Ajar', 'db_jenis' => 'RPP / Modul Ajar'],
                        'prota' => ['label' => 'Program Tahunan (Prota)', 'db_jenis' => 'Program Tahunan (Prota)'],
                        'promes' => ['label' => 'Program Semester (Promes)', 'db_jenis' => 'Program Semester (Promes)'],
                        'kkm' => ['label' => 'KKM / KKTP', 'db_jenis' => 'KKM / KKTP'],
                        'jurnal' => ['label' => 'Bank Soal / Evaluasi', 'db_jenis' => 'Bank Soal / Evaluasi'],
                        'absen' => ['label' => 'Daftar Hadir Siswa', 'db_jenis' => 'Daftar Hadir Siswa'],
                        'nilai' => ['label' => 'Buku Nilai / Rekap Nilai', 'db_jenis' => 'Buku Nilai']
                    ];
                    foreach ($instrumen_items as $key => $item):
                        $val = $supervisi['instrumen'][$key] ?? '';
                        $berkasTerkait = $berkas_guru[$item['db_jenis']] ?? [];
                    ?>
                    <tr>
                        <td style="padding: 12px 15px;">
                            <div style="font-weight: 600; color: #334155; margin-bottom: 5px;"><?= $item['label'] ?></div>
                            <?php if (!empty($berkasTerkait)): ?>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <?php foreach ($berkasTerkait as $berkas): ?>
                                        <a href="/public/uploads/berkas/<?= htmlspecialchars($berkas['file_nama']) ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #0ea5e9; text-decoration: none; background: #e0f2fe; padding: 4px 8px; border-radius: 6px; width: fit-content; max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($berkas['judul_berkas']) ?>">
                                            <i data-lucide="file-text" style="width: 12px; height: 12px; flex-shrink: 0;"></i> 
                                            <span style="overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($berkas['judul_berkas']) ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">Belum ada berkas diunggah via E-Kinerja</div>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px; text-align: center; vertical-align: top;">
                            <select name="instrumen[<?= $key ?>]" class="z-input" style="width: 100%; padding: 8px; font-size: 0.9rem; border-radius: 8px;">
                                <option value="">- Pilih -</option>
                                <option value="Ada" <?= $val === 'Ada' ? 'selected' : '' ?>>Ada</option>
                                <option value="Tidak Lengkap" <?= $val === 'Tidak Lengkap' ? 'selected' : '' ?>>Tidak Lengkap</option>
                                <option value="Tidak Ada" <?= $val === 'Tidak Ada' ? 'selected' : '' ?>>Tidak Ada</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Catatan Tambahan (Feedback)</label>
            <textarea name="catatan" class="z-input" style="width: 100%; resize: vertical;" rows="4" placeholder="Tuliskan catatan apresiasi atau perbaikan di sini..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Tindak Lanjut</label>
            <textarea name="tindak_lanjut" class="z-input" style="width: 100%; resize: vertical;" rows="4" placeholder="Tuliskan langkah tindak lanjut yang harus dilakukan guru..."><?= htmlspecialchars($supervisi['tindak_lanjut'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 20px; border-top: 1px solid var(--z-border);">
            <a href="<?= \App\Core\Helper::url('/kurikulum/supervisi-administrasi') ?>" class="btn btn-ghost" style="text-decoration: none;">
                Batal
            </a>
            <button type="submit" class="btn btn-primary" id="btnSaveSupervisi" style="padding-left: 25px; padding-right: 25px;">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i> Simpan Supervisi
            </button>
        </div>
    </form>
</div>

<script>
function saveSupervisi() {
    const form = document.getElementById('formSupervisi');
    const formData = new FormData(form);

    const btn = document.getElementById('btnSaveSupervisi');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Menyimpan...';

    fetch(`<?= \App\Core\Helper::url('/kurikulum/supervisi-administrasi/save') ?>`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = `<?= \App\Core\Helper::url('/kurikulum/supervisi-administrasi') ?>`;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: res.message
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
            lucide.createIcons();
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: 'Gagal menghubungi server'
        });
        btn.disabled = false;
        btn.innerHTML = originalText;
        lucide.createIcons();
    });
}
</script>
