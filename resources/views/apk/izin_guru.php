<div class="apk-vector-header" style="padding-bottom: 25px; background-color: #2563eb;">
    <div class="apk-top-logo">
        <a href="/apk/rekap-absensi" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Mau Izin?</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Ajukan Izin</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding: 20px;">
    
    <form id="formIzin" method="POST" action="/apk/izin-guru">
        <!-- Input Tanggal (Hidden) -->
        <input type="hidden" id="inputTanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>">
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">TANGGAL IZIN</label>
            <div style="background: #f1f5f9; padding: 12px; border-radius: 10px; font-weight: 700; color: #1e293b;">
                <?php 
                    $hariList = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                    echo $hariList[date('l')] . ', ' . date('d M Y'); 
                ?> (Hari Ini)
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">KATEGORI IZIN</label>
            <select id="selectJenis" name="jenis_izin" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-size: 0.95rem; color: #1e293b; outline: none; appearance: none;">
                <option value="">-- Pilih Kategori --</option>
                <option value="Sakit">Sakit</option>
                <option value="Izin">Izin</option>
            </select>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">KETERANGAN / ALASAN DETAIL</label>
            <textarea name="alasan_detail" required placeholder="Contoh: Sakit demam dan batuk, atau Izin ada acara keluarga..." style="width: 100%; min-height: 80px; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box; resize: vertical; font-family: inherit;"></textarea>
        </div>

        <!-- Tugas & Materi Section -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Tugas & Materi</h3>
            <p style="font-size: 0.75rem; color: #64748b; line-height: 1.5; margin-bottom: 15px;">
                Silakan berikan tugas atau materi untuk kelas yang akan Anda tinggalkan hari ini. <strong style="color: #ef4444;">Wajib diisi jika ada kelas.</strong>
            </p>
            
            <div id="jadwalContainer">
                <div style="text-align: center; padding: 25px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px;">
                    <i data-lucide="loader" style="width: 24px; height: 24px; color: #94a3b8; animation: spin 1s linear infinite;"></i>
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; margin-top: 8px;">Memuat jadwal mengajar...</div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="btnSubmit" disabled style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px; background: #94a3b8; color: white; border: none; border-radius: 14px; font-weight: 700; font-size: 0.95rem; cursor: not-allowed; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.2s;">
            <i data-lucide="send" style="width: 18px; height: 18px;"></i> Kirim Pengajuan
        </button>
    </form>
</div>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadJadwal();
    validateForm();
    
    document.getElementById('formIzin').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const jenis = document.getElementById('selectJenis').value;
        if (!jenis) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih keterangan izin terlebih dahulu.' });
            return;
        }
        
        // Check all required textareas
        const textareas = document.querySelectorAll('#jadwalContainer textarea[required]');
        let allFilled = true;
        textareas.forEach(function(ta) {
            if (!ta.value.trim()) {
                allFilled = false;
                ta.style.border = '2px solid #ef4444';
            } else {
                ta.style.border = '1px solid #e2e8f0';
            }
        });
        
        if (!allFilled) {
            Swal.fire({ icon: 'warning', title: 'Tugas Belum Lengkap', text: 'Semua tugas/materi untuk setiap kelas wajib diisi!' });
            return;
        }
        
        Swal.fire({
            title: 'Kirim Pengajuan Izin?',
            text: 'Pastikan tugas dan materi sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('btnSubmit').disabled = true;
                document.getElementById('btnSubmit').innerHTML = '<i data-lucide="loader" style="width:18px;height:18px;animation:spin 1s linear infinite;"></i> Mengirim...';
                e.target.submit();
            }
        });
    });
});

function loadJadwal() {
    const tanggal = document.getElementById('inputTanggal').value;
    const container = document.getElementById('jadwalContainer');
    
    container.innerHTML = '<div style="text-align: center; padding: 25px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px;"><i data-lucide="loader" style="width: 24px; height: 24px; color: #94a3b8; animation: spin 1s linear infinite;"></i><div style="font-size: 0.8rem; color: #64748b; font-weight: 600; margin-top: 8px;">Memuat jadwal mengajar...</div></div>';
    lucide.createIcons();
    
    fetch('/apk/api/jadwal-harian?tanggal=' + tanggal)
        .then(r => r.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = '<div style="text-align: center; padding: 25px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px;"><i data-lucide="calendar-off" style="width: 28px; height: 28px; color: #94a3b8;"></i><div style="font-size: 0.85rem; font-weight: 700; color: #475569; margin-top: 8px;">Tidak ada jadwal kelas pada tanggal ini.</div></div>';
                lucide.createIcons();
                document.getElementById('btnSubmit').disabled = true;
                document.getElementById('btnSubmit').style.background = '#94a3b8';
                document.getElementById('btnSubmit').style.cursor = 'not-allowed';
                return;
            }
            
            let html = '';
            data.forEach(function(j, idx) {
                html += '<div style="background: white; border: 1px solid #e2e8f0; border-radius: 14px; padding: 15px; margin-bottom: 12px;">';
                html += '  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">';
                html += '    <div>';
                html += '      <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem;">' + j.nama_kelas + '</div>';
                html += '      <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">' + j.nama_mapel + '</div>';
                html += '    </div>';
                html += '    <div style="font-size: 0.7rem; color: #3b82f6; font-weight: 700; background: #eff6ff; padding: 4px 10px; border-radius: 8px;">';
                html += '      <i data-lucide="clock" style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:3px;"></i>' + j.jam_mulai.substring(0,5) + ' - ' + j.jam_selesai.substring(0,5);
                html += '    </div>';
                html += '  </div>';
                html += '  <input type="hidden" name="jadwal_ids[]" value="' + j.id + '">';
                html += '  <input type="hidden" name="kelas_ids[]" value="' + j.kelas_id + '">';
                html += '  <input type="hidden" name="mapel_ids[]" value="' + j.mapel_id + '">';
                html += '  <textarea name="tugas[]" required placeholder="Tulis tugas atau materi untuk kelas ini..." style="width: 100%; min-height: 70px; padding: 10px 12px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.85rem; color: #1e293b; outline: none; box-sizing: border-box; resize: vertical; font-family: inherit;"></textarea>';
                html += '</div>';
            });
            
            container.innerHTML = html;
            lucide.createIcons();
            validateForm(); // initial check after load
        })
        .catch(err => {
            container.innerHTML = '<div style="text-align: center; padding: 25px; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px;"><div style="font-size: 0.85rem; font-weight: 700; color: #ef4444;">Gagal memuat jadwal.</div></div>';
            disableSubmit();
        });
}

function disableSubmit() {
    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('btnSubmit').style.background = '#94a3b8';
    document.getElementById('btnSubmit').style.cursor = 'not-allowed';
}

function enableSubmit() {
    document.getElementById('btnSubmit').disabled = false;
    document.getElementById('btnSubmit').style.background = 'linear-gradient(135deg, #2563eb, #1d4ed8)';
    document.getElementById('btnSubmit').style.cursor = 'pointer';
}

function validateForm() {
    let isValid = true;
    const form = document.getElementById('formIzin');
    const requiredElements = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    // Pastikan semua field wajib terisi
    requiredElements.forEach(el => {
        if (!el.value || el.value.trim() === '') {
            isValid = false;
        }
    });

    // Pastikan minimal ada 1 jadwal/tugas
    const tugasElements = form.querySelectorAll('textarea[name="tugas[]"]');
    if (tugasElements.length === 0) {
        isValid = false;
    }

    if (isValid) {
        enableSubmit();
    } else {
        disableSubmit();
    }
}

document.getElementById('formIzin').addEventListener('input', validateForm);
document.getElementById('formIzin').addEventListener('change', validateForm);
</script>
