<style>
    .info-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 16px;
        padding: 20px;
        color: white;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
        position: relative;
        overflow: hidden;
    }
    .info-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .student-card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .student-info {
        flex: 1;
        min-width: 0;
    }
    .student-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .student-nis {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 2px;
    }
    .input-nilai {
        width: 70px;
        padding: 8px 10px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        text-align: center;
        font-weight: 800;
        font-size: 1.1rem;
        color: #10b981;
        outline: none;
        transition: all 0.2s ease;
    }
    .input-nilai:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .static-action {
        padding: 0 20px 30px 20px;
    }
    .btn-save {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        cursor: pointer;
    }
    .btn-save:active {
        transform: scale(0.98);
    }
</style>

<!-- Header -->
<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <a href="/apk/nilai-harian" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Input Nilai</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;"><?php echo htmlspecialchars($jenis_evaluasi_final); ?></div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0;">
    <div class="info-card">
    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Mata Pelajaran</div>
    <div style="font-size: 1.2rem; font-weight: 800; margin-bottom: 15px;"><?php echo htmlspecialchars($nama_mapel); ?></div>
    
    <div style="display: flex; gap: 20px;">
        <div>
            <div style="font-size: 0.7rem; color: #94a3b8;">Kelas</div>
            <div style="font-weight: 700; color: #cbd5e1;"><?php echo htmlspecialchars($nama_kelas); ?></div>
        </div>
        <div>
            <div style="font-size: 0.7rem; color: #94a3b8;">Evaluasi</div>
            <div style="font-weight: 700; color: #cbd5e1;"><?php echo htmlspecialchars($jenis_evaluasi_final); ?></div>
        </div>
        <div>
            <div style="font-size: 0.7rem; color: #94a3b8;">Jumlah</div>
            <div style="font-weight: 700; color: #cbd5e1;"><?php echo count($siswaList); ?> Siswa</div>
        </div>
    </div>
</div>

<form id="formNilai">
    <input type="hidden" name="kelas_id" value="<?php echo $kelas_id; ?>">
    <input type="hidden" name="mapel_id" value="<?php echo $mapel_id; ?>">
    <input type="hidden" name="jenis_evaluasi" value="<?php echo htmlspecialchars($jenis_evaluasi_final); ?>">

    <div style="padding: 0 20px 20px 20px;">
        <label style="display:block; margin-bottom: 8px; font-weight: 700; color: #475569; font-size: 0.85rem;">Materi / Topik Pembelajaran <span style="font-weight: 500; color: #e11d48;">(Wajib)</span></label>
        <textarea name="materi" rows="2" placeholder="Contoh: Bab 1 - Bilangan Bulat..." required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none; font-family: inherit; resize: vertical; box-sizing: border-box;"><?php echo htmlspecialchars($existingMateri ?? ''); ?></textarea>
    </div>

    <div style="padding-bottom: 20px;">
        <?php foreach($siswaList as $s): 
            $nilai_saat_ini = isset($existingNilai[$s['id']]) ? $existingNilai[$s['id']] : '';
        ?>
            <div class="student-card">
                <div class="student-info">
                    <div class="student-name"><?php echo htmlspecialchars($s['nama']); ?></div>
                    <div class="student-nis">NIS: <?php echo htmlspecialchars($s['nis'] ?? '-'); ?></div>
                </div>
                
                <div>
                    <input type="number" 
                           name="nilai[<?php echo $s['id']; ?>]" 
                           class="input-nilai" 
                           value="<?php echo $nilai_saat_ini; ?>" 
                           min="0" max="100" 
                           placeholder="-"
                           oninput="if(this.value > 100) this.value = 100; if(this.value < 0) this.value = 0;">
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="static-action">
        <button type="submit" class="btn-save" id="btnSimpan">
            <i data-lucide="save" style="width: 20px;"></i>
            Simpan Nilai
        </button>
    </div>
</form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('formNilai').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSimpan');
        btn.innerHTML = '<i data-lucide="loader" style="width: 20px;" class="lucide-spin"></i> Menyimpan...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch('/apk/nilai-harian/save', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.href = '/apk/nilai-harian';
                });
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                btn.innerHTML = '<i data-lucide="save" style="width: 20px;"></i> Simpan Nilai';
                btn.disabled = false;
                lucide.createIcons();
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan pada jaringan', 'error');
            btn.innerHTML = '<i data-lucide="save" style="width: 20px;"></i> Simpan Nilai';
            btn.disabled = false;
            lucide.createIcons();
        });
    });
</script>
