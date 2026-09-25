<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
            <h1 class="mph-title" style="color: white;">
                <i data-lucide="clipboard-check" style="color: rgba(255,255,255,0.8);"></i> Instrumen Supervisi Kelas
            </h1>
            <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Penilaian Pelaksanaan Pembelajaran di Kelas</p>
        </div>
        <a href="<?= \App\Core\Helper::url('/kurikulum/supervisi-kelas') ?>" class="btn btn-primary" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); text-decoration: none;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Kembali
        </a>
    </div>
</div>

<div class="z-card" style="margin-top: 2rem; padding: 2rem;">
    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
        <div style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="user" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">GURU YANG DISUPERVISI</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b;"><?= htmlspecialchars($guru['nama']) ?></div>
            <div style="font-size: 0.85rem; color: #64748b;">NIP/NUPTK: <?= htmlspecialchars($guru['nip'] ?: '-') ?></div>
        </div>
    </div>

    <form method="POST" action="<?= \App\Core\Helper::url('/kurikulum/supervisi-kelas/save') ?>">
        <input type="hidden" name="guru_id" value="<?= $guru['id'] ?>">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div>
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Tanggal Supervisi</label>
                <input type="date" name="tanggal_supervisi" class="z-input" style="width: 100%;" required value="<?= $supervisi['tanggal_supervisi'] ?? date('Y-m-d') ?>">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" class="z-input" style="width: 100%;" required placeholder="Contoh: Matematika" value="<?= htmlspecialchars($supervisi['mata_pelajaran'] ?? '') ?>">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Kelas</label>
                <input type="text" name="kelas" class="z-input" style="width: 100%;" required placeholder="Contoh: VII A" value="<?= htmlspecialchars($supervisi['kelas'] ?? '') ?>">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Jam Ke-</label>
                <input type="text" name="jam_ke" class="z-input" style="width: 100%;" placeholder="Contoh: 1-2" value="<?= htmlspecialchars($supervisi['jam_ke'] ?? '') ?>">
            </div>
            <div style="grid-column: 1 / -1;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Materi Pokok</label>
                <input type="text" name="materi_pokok" class="z-input" style="width: 100%;" required placeholder="Tuliskan materi pokok yang diajarkan..." value="<?= htmlspecialchars($supervisi['materi_pokok'] ?? '') ?>">
            </div>
        </div>

        <h3 style="font-size: 1rem; font-weight: 800; color: #1e293b; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;">Instrumen Penilaian Pelaksanaan Pembelajaran</h3>
        
        <div style="background: #eff6ff; padding: 12px 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-bottom: 20px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #1e40af; margin-bottom: 5px;">Keterangan Skor:</div>
            <div style="display: flex; gap: 15px; font-size: 0.8rem; color: #1e3a8a;">
                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">4</strong> Sangat Baik</div>
                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">3</strong> Baik</div>
                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">2</strong> Cukup</div>
                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">1</strong> Kurang</div>
            </div>
        </div>

        <div class="z-table-wrap" style="margin-bottom: 25px;">
            <table class="z-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">No</th>
                        <th>Komponen Penilaian</th>
                        <th style="width: 150px; text-align: center;">Nilai (1-4)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $instrumen_items = [
                        'A. KEGIATAN PENDAHULUAN' => [
                            'p1' => 'Guru menyiapkan peserta didik secara psikis dan fisik untuk mengikuti proses pembelajaran',
                            'p2' => 'Guru memberi motivasi belajar peserta didik secara kontekstual',
                            'p3' => 'Guru mengajukan pertanyaan-pertanyaan yang mengaitkan pengetahuan sebelumnya dengan materi yang akan dipelajari',
                            'p4' => 'Guru menjelaskan tujuan pembelajaran atau kompetensi dasar yang akan dicapai',
                            'p5' => 'Guru menyampaikan cakupan materi dan penjelasan uraian kegiatan sesuai silabus/RPP'
                        ],
                        'B. KEGIATAN INTI' => [
                            'i1' => 'Penguasaan materi pembelajaran (kemampuan menyesuaikan materi dengan tujuan pembelajaran)',
                            'i2' => 'Kemampuan mengaitkan materi dengan pengetahuan lain yang relevan',
                            'i3' => 'Menyajikan pembahasan materi pembelajaran dengan tepat',
                            'i4' => 'Melaksanakan pembelajaran sesuai dengan alokasi waktu yang direncanakan',
                            'i5' => 'Penerapan strategi pembelajaran yang mendidik (melaksanakan pembelajaran aktif)',
                            'i6' => 'Pemanfaatan media/sumber belajar dalam pembelajaran (menunjukkan keterampilan dalam penggunaan media)',
                            'i7' => 'Melibatkan peserta didik dalam pemanfaatan media/sumber belajar',
                            'i8' => 'Menumbuhkan partisipasi aktif peserta didik melalui interaksi guru, peserta didik, sumber belajar',
                            'i9' => 'Merespon positif partisipasi peserta didik'
                        ],
                        'C. PENILAIAN PEMBELAJARAN' => [
                            'n1' => 'Memantau kemajuan belajar (melaksanakan penilaian proses)',
                            'n2' => 'Melakukan penilaian akhir sesuai dengan tujuan'
                        ],
                        'D. KEGIATAN PENUTUP' => [
                            't1' => 'Melakukan refleksi atau membuat rangkuman dengan melibatkan peserta didik',
                            't2' => 'Memberikan tes lisan atau tulisan',
                            't3' => 'Mengumpulkan hasil kerja sebagai bahan portofolio',
                            't4' => 'Melaksanakan tindak lanjut dengan memberikan arahan kegiatan berikutnya dan tugas pengayaan'
                        ]
                    ];

                    foreach ($instrumen_items as $kategori => $items):
                    ?>
                    <tr>
                        <td colspan="3" style="background: #f8fafc; font-weight: 800; color: #0f172a; padding: 10px 15px; border-bottom: 1px solid #e2e8f0;"><?= $kategori ?></td>
                    </tr>
                        <?php 
                        $no = 1; 
                        foreach ($items as $key => $label): 
                            $val = $supervisi['instrumen'][$key] ?? '';
                        ?>
                        <tr>
                            <td style="text-align: center; color: #64748b; padding: 12px 15px; border-bottom: 1px solid #f1f5f9;"><?= $no++ ?></td>
                            <td style="padding: 12px 15px; color: #334155; line-height: 1.4; border-bottom: 1px solid #f1f5f9;"><?= $label ?></td>
                            <td style="padding: 12px; text-align: center; vertical-align: middle; border-bottom: 1px solid #f1f5f9;">
                                <select name="instrumen[<?= $key ?>]" class="z-input" required style="width: 100%; padding: 8px; font-size: 0.95rem; border-radius: 8px; text-align: center; font-weight: 700;">
                                    <option value="">-</option>
                                    <option value="4" <?= $val === '4' ? 'selected' : '' ?>>4</option>
                                    <option value="3" <?= $val === '3' ? 'selected' : '' ?>>3</option>
                                    <option value="2" <?= $val === '2' ? 'selected' : '' ?>>2</option>
                                    <option value="1" <?= $val === '1' ? 'selected' : '' ?>>1</option>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Catatan Tambahan (Kelebihan / Kekurangan)</label>
            <textarea name="catatan" class="z-input" style="width: 100%; resize: vertical;" rows="4" placeholder="Tuliskan catatan observasi di sini..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="font-size: 0.85rem; font-weight: 700; color: var(--z-muted); display: block; margin-bottom: 8px;">Saran & Tindak Lanjut</label>
            <textarea name="tindak_lanjut" class="z-input" style="width: 100%; resize: vertical;" rows="4" placeholder="Tuliskan saran perbaikan untuk guru..."><?= htmlspecialchars($supervisi['tindak_lanjut'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 20px; border-top: 1px solid var(--z-border);">
            <a href="<?= \App\Core\Helper::url('/kurikulum/supervisi-kelas') ?>" class="btn btn-ghost" style="text-decoration: none;">
                Batal
            </a>
            <button type="submit" class="btn btn-primary" id="btnSaveSupervisi" style="padding-left: 25px; padding-right: 25px;">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i> Simpan Evaluasi
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.querySelector('form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSaveSupervisi');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="loader" class="spin" style="width: 18px; height: 18px;"></i> Menyimpan...';
            btn.disabled = true;
            lucide.createIcons();

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Tersimpan!', text: result.message, timer: 1500, showConfirmButton: false }).then(() => {
                        window.location.href = "<?= \App\Core\Helper::url('/kurikulum/supervisi-kelas') ?>";
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message });
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                    lucide.createIcons();
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem' });
                btn.innerHTML = originalContent;
                btn.disabled = false;
                lucide.createIcons();
            }
        });
    });
</script>
