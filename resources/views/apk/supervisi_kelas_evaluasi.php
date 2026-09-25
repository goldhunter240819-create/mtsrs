<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Evaluasi KBM</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Supervisi Kelas</div>
        </div>
    </div>
    <a href="/apk/supervisi-kelas" style="display:inline-flex; align-items:center; gap:5px; color:#fff; text-decoration:none; margin-top:15px; font-size:0.8rem; font-weight:700; background:rgba(255,255,255,0.2); padding:6px 12px; border-radius:20px; border:1px solid rgba(255,255,255,0.3);">
        <i data-lucide="arrow-left" style="width:14px; height:14px;"></i> Kembali
    </a>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 30px;">
    
    <div style="background:#f8fafc; border-radius:15px; padding:15px; margin-bottom:20px; display:flex; align-items:center; gap:12px; border:1px solid #e2e8f0;">
        <div style="width:45px; height:45px; background:#fef3c7; color:#d97706; border-radius:12px; display:flex; align-items:center; justify-content:center;">
            <i data-lucide="user" style="width:20px; height:20px;"></i>
        </div>
        <div>
            <div style="font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:2px;">GURU DISUPERVISI</div>
            <div style="font-weight:800; color:#1e293b; font-size:1rem;"><?= htmlspecialchars($guru['nama']) ?></div>
            <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">NIP: <?= htmlspecialchars($guru['nip'] ?? '-') ?></div>
        </div>
    </div>

    <form id="formSupervisi" onsubmit="event.preventDefault(); saveSupervisi();">
        <input type="hidden" name="guru_id" value="<?= $guru['id'] ?>">
        
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:15px; margin-bottom:20px;">
            <h4 style="margin: 0 0 15px; color: #1e293b; font-size: 0.95rem; font-weight: 800; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">Informasi KBM</h4>
            
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div>
                    <label style="font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Tanggal Supervisi</label>
                    <input type="date" name="tanggal_supervisi" style="width:100%; padding:10px; border-radius:10px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;" required value="<?= $supervisi['tanggal_supervisi'] ?? date('Y-m-d') ?>">
                </div>
                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label style="font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Kelas</label>
                        <input type="text" name="kelas" style="width:100%; padding:10px; border-radius:10px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;" required placeholder="Cth: VII A" value="<?= htmlspecialchars($supervisi['kelas'] ?? '') ?>">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Jam Ke-</label>
                        <input type="text" name="jam_ke" style="width:100%; padding:10px; border-radius:10px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;" placeholder="Cth: 1-2" value="<?= htmlspecialchars($supervisi['jam_ke'] ?? '') ?>">
                    </div>
                </div>
                <div>
                    <label style="font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Mata Pelajaran</label>
                    <input type="text" name="mata_pelajaran" style="width:100%; padding:10px; border-radius:10px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;" required placeholder="Cth: Matematika" value="<?= htmlspecialchars($supervisi['mata_pelajaran'] ?? '') ?>">
                </div>
                <div>
                    <label style="font-size:0.75rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Materi Pokok</label>
                    <input type="text" name="materi_pokok" style="width:100%; padding:10px; border-radius:10px; border:1px solid #e2e8f0; font-weight:700; color:#1e293b; background:#f8fafc;" required placeholder="Materi yang diajarkan..." value="<?= htmlspecialchars($supervisi['materi_pokok'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div style="background: #eff6ff; padding: 12px 15px; border-radius: 10px; border-left: 4px solid #3b82f6; margin-bottom: 20px;">
            <div style="font-size: 0.75rem; font-weight: 800; color: #1e40af; margin-bottom: 5px;">Keterangan Skor:</div>
            <div style="display: flex; gap: 10px; font-size: 0.75rem; color: #1e3a8a; flex-wrap: wrap;">
                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">4</strong> Sangat Baik</div>
                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">3</strong> Baik</div>
                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">2</strong> Cukup</div>
                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">1</strong> Kurang</div>
            </div>
        </div>

        <h4 style="margin: 0 0 10px; color: #1e293b; font-size: 0.95rem; font-weight: 800;">Instrumen Penilaian</h4>
        
        <div style="display:flex; flex-direction:column; gap:15px; margin-bottom: 25px;">
            <?php
            $instrumen_items = [
                'A. PENDAHULUAN' => [
                    'p1' => 'Menyiapkan peserta didik secara psikis dan fisik',
                    'p2' => 'Memberi motivasi belajar kontekstual',
                    'p3' => 'Mengaitkan materi sebelumnya dengan yang akan dipelajari',
                    'p4' => 'Menjelaskan tujuan pembelajaran',
                    'p5' => 'Menyampaikan cakupan materi sesuai RPP'
                ],
                'B. KEGIATAN INTI' => [
                    'i1' => 'Penguasaan materi pembelajaran',
                    'i2' => 'Mengaitkan materi dengan pengetahuan lain',
                    'i3' => 'Menyajikan pembahasan secara tepat',
                    'i4' => 'Melaksanakan sesuai alokasi waktu',
                    'i5' => 'Menerapkan pembelajaran aktif',
                    'i6' => 'Pemanfaatan media/sumber belajar',
                    'i7' => 'Melibatkan siswa dalam pemanfaatan media',
                    'i8' => 'Menumbuhkan partisipasi aktif siswa',
                    'i9' => 'Merespon positif partisipasi siswa'
                ],
                'C. PENILAIAN' => [
                    'n1' => 'Melaksanakan penilaian proses',
                    'n2' => 'Melakukan penilaian akhir'
                ],
                'D. PENUTUP' => [
                    't1' => 'Melakukan refleksi/rangkuman bersama siswa',
                    't2' => 'Memberikan tes (lisan/tulis)',
                    't3' => 'Mengumpulkan portofolio',
                    't4' => 'Memberikan tugas pengayaan/tindak lanjut'
                ]
            ];
            foreach ($instrumen_items as $kategori => $items):
            ?>
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                <div style="padding:10px 15px; background:#f8fafc; font-weight:800; color:#0f172a; font-size:0.8rem; border-bottom:1px solid #e2e8f0;">
                    <?= $kategori ?>
                </div>
                <div style="display:flex; flex-direction:column;">
                    <?php 
                    $no = 1; 
                    foreach ($items as $key => $label): 
                        $val = $supervisi['instrumen'][$key] ?? '';
                    ?>
                    <div style="padding:12px 15px; border-bottom:1px solid #f1f5f9; display:flex; flex-direction:column; gap:8px;">
                        <div style="font-size:0.8rem; color:#334155; font-weight:600; line-height:1.4;">
                            <span style="color:#64748b; font-weight:800; margin-right:5px;"><?= $no++ ?>.</span>
                            <?= $label ?>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <?php for($i=1; $i<=4; $i++): ?>
                            <label style="flex:1; text-align:center; position:relative;">
                                <input type="radio" name="instrumen[<?= $key ?>]" value="<?= $i ?>" required <?= $val == $i ? 'checked' : '' ?> style="position:absolute; opacity:0; width:0; height:0;">
                                <div class="radio-btn-<?= $key ?>" style="padding:8px 0; border-radius:8px; border:1px solid <?= $val == $i ? '#3b82f6' : '#cbd5e1' ?>; background:<?= $val == $i ? '#eff6ff' : '#fff' ?>; color:<?= $val == $i ? '#1d4ed8' : '#64748b' ?>; font-weight:800; font-size:0.9rem; transition:all 0.2s;">
                                    <?= $i ?>
                                </div>
                            </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="font-size:0.8rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Catatan Observasi</label>
            <textarea name="catatan" style="width:100%; padding:12px; border-radius:12px; border:1px solid #e2e8f0; font-weight:600; color:#1e293b; background:#f8fafc; resize:vertical; font-size:0.85rem;" rows="3" placeholder="Tulis catatan (kelebihan/kekurangan)..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-size:0.8rem; font-weight:800; color:#64748b; margin-bottom:5px; display:block;">Saran & Tindak Lanjut</label>
            <textarea name="tindak_lanjut" style="width:100%; padding:12px; border-radius:12px; border:1px solid #e2e8f0; font-weight:600; color:#1e293b; background:#f8fafc; resize:vertical; font-size:0.85rem;" rows="3" placeholder="Tulis tindak lanjut..."><?= htmlspecialchars($supervisi['tindak_lanjut'] ?? '') ?></textarea>
        </div>

        <button type="submit" id="btnSimpan" style="width:100%; background:linear-gradient(135deg, #f59e0b, #d97706); color:white; padding:15px; border:none; border-radius:12px; font-weight:800; font-size:1rem; display:flex; align-items:center; justify-content:center; gap:8px;">
            <i data-lucide="save" style="width:18px;"></i> Simpan Observasi
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();

    // Script to style custom radio buttons
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const groupName = this.name;
            const container = this.closest('div[style*="display:flex; gap:8px;"]');
            
            // Reset all in this group
            container.querySelectorAll('div[class^="radio-btn-"]').forEach(div => {
                div.style.borderColor = '#cbd5e1';
                div.style.background = '#fff';
                div.style.color = '#64748b';
            });
            
            // Highlight selected
            const selectedDiv = this.nextElementSibling;
            selectedDiv.style.borderColor = '#3b82f6';
            selectedDiv.style.background = '#eff6ff';
            selectedDiv.style.color = '#1d4ed8';
        });
    });

    function saveSupervisi() {
        const form = document.getElementById('formSupervisi');
        const formData = new FormData(form);
        const btn = document.getElementById('btnSimpan');
        
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader" style="width:18px;" class="animate-spin"></i> Menyimpan...';
        lucide.createIcons();
        
        fetch('/apk/supervisi-kelas/save', {
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
                    window.location.href = '/apk/supervisi-kelas';
                });
            } else {
                Swal.fire('Gagal', res.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="save" style="width:18px;"></i> Simpan Observasi';
                lucide.createIcons();
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="save" style="width:18px;"></i> Simpan Observasi';
            lucide.createIcons();
        });
    }
</script>
