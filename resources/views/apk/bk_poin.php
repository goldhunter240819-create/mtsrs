<div class="apk-header" style="background: transparent; padding-top: 30px; position: absolute; width: 100%; top: 0; z-index: 100;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 15px;">
        <a href="/apk/aplikasi-saya" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 700; background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px);">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Kembali
        </a>
        <button onclick="document.getElementById('modalTambahPoin').style.display='flex'" style="background: #fff; color: #8b5cf6; border: none; padding: 8px 15px; border-radius: 20px; font-weight: 700; display: flex; align-items: center; gap: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Tambah
        </button>
    </div>
</div>

<div class="apk-vector-header" style="padding-bottom: 25px; padding-top: 90px; background-color: #8b5cf6;">
    <div class="apk-top-logo">
        <div style="width: 45px; height: 45px; border-radius: 12px; background: #fff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <i data-lucide="award"></i>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Bimbingan Konseling</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Poin Kedisiplinan</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; min-height: 70vh; padding: 20px;">
    <?php if (empty($poinList)): ?>
        <div style="text-align: center; padding: 40px 20px; color: #64748b;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 15px; color: #cbd5e1;"></i>
            <h4 style="margin: 0 0 10px 0;">Belum Ada Data</h4>
            <p style="margin: 0; font-size: 0.9rem;">Belum ada pelanggaran atau prestasi yang tercatat saat ini.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($poinList as $p): ?>
                <?php 
                $isPelanggaran = ($p['tipe'] === 'pelanggaran'); 
                $bgColor = $isPelanggaran ? '#fef2f2' : '#f0fdf4';
                $borderColor = $isPelanggaran ? 'rgba(239,68,68,0.2)' : 'rgba(34,197,94,0.2)';
                $textColor = $isPelanggaran ? '#ef4444' : '#22c55e';
                $icon = $isPelanggaran ? 'alert-circle' : 'award';
                ?>
                <div style="background: <?= $bgColor ?>; padding: 15px; border-radius: 16px; border: 1px solid <?= $borderColor ?>; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 3px;">
                                <?= date('d M Y', strtotime($p['tanggal'])) ?> &middot; Oleh: <?= htmlspecialchars($p['nama_guru'] ?? '-') ?>
                            </div>
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #1e293b;">
                                <?= htmlspecialchars($p['nama_siswa'] ?? 'Siswa Tidak Diketahui') ?>
                            </h4>
                        </div>
                        <div style="background: #fff; padding: 5px 10px; border-radius: 8px; font-weight: 800; font-size: 0.9rem; color: <?= $textColor ?>; border: 1px solid <?= $borderColor ?>; display: flex; align-items: center; gap: 5px;">
                            <i data-lucide="<?= $icon ?>" style="width: 14px; height: 14px;"></i> <?= htmlspecialchars($p['poin']) ?>
                        </div>
                    </div>
                    <div style="font-size: 0.85rem; color: #475569; margin-bottom: 5px;">
                        <strong><?= htmlspecialchars($p['nama_kategori'] ?? 'Kategori') ?>:</strong> <?= htmlspecialchars($p['keterangan'] ?? '-') ?>
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: #94a3b8; font-weight: 600;">
                        <i data-lucide="users" style="width: 12px; height: 12px;"></i> Kelas <?= htmlspecialchars($p['nama_kelas'] ?? '-') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Poin -->
<div id="modalTambahPoin" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="hide-scroll" style="background: #fff; width: 95%; max-width: 480px; max-height: 85vh; overflow-y: auto; border-radius: 25px; padding: 25px 20px; animation: zoomIn 0.3s ease-out;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; position: sticky; top: -25px; background: #fff; padding-top: 25px; z-index: 10; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; display: flex; align-items: center; gap: 8px;"><i data-lucide="award" style="color: #8b5cf6;"></i> Tambah Poin</h3>
            <div onclick="document.getElementById('modalTambahPoin').style.display='none'" style="background: #f1f5f9; padding: 8px; border-radius: 50%; cursor: pointer;">
                <i data-lucide="x" style="width: 18px; height: 18px; color: #64748b;"></i>
            </div>
        </div>
        <form action="/apk/bk/poin/save" method="POST">
            <input type="hidden" name="tahun_ajaran_id" value="<?= $ta_id ?>">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Pilih Kelas</label>
                <select id="kelas_id" required onchange="loadSiswa()" style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>">Kelas <?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Pilih Siswa</label>
                <select id="siswa_id" name="siswa_id" required style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
                    <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Tanggal Kejadian</label>
                <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Tingkat Pelanggaran</label>
                <select id="tingkat" required onchange="loadKategori()" style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
                    <option value="">-- Pilih Tingkat --</option>
                    <option value="ringan">Pelanggaran Ringan</option>
                    <option value="sedang">Pelanggaran Sedang</option>
                    <option value="berat">Pelanggaran Berat</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Jenis Pelanggaran</label>
                <select id="kategori_id" name="kategori_id" required onchange="setDetailPoin()" style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
                    <option value="">-- Pilih Tingkat Terlebih Dahulu --</option>
                </select>
                <div id="poinReminder" style="display: none; margin-top: 6px; font-size: 0.8rem; color: #8b5cf6; font-weight: 700; background: #f5f3ff; padding: 6px 10px; border-radius: 6px; border: 1px dashed #ddd6fe;">
                    <i data-lucide="info" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;"></i>
                    <span style="vertical-align: middle;">Poin standar untuk pelanggaran ini: <b id="textPoinDefault">0</b></span>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Input Poin Pelanggaran</label>
                <input type="number" id="poin_input" name="poin" required placeholder="Masukkan angka poin (misal: 3)" style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; font-size: 0.95rem; outline: none; border: 2px solid #ef4444;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan opsional..." style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;"></textarea>
            </div>
            
            <button type="submit" style="width: 100%; background: #8b5cf6; color: #fff; padding: 15px; border: none; border-radius: 14px; font-weight: 800; font-size: 1rem; box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);">
                Simpan Pelanggaran
            </button>
        </form>
    </div>
</div>

<script>
const siswaByKelas = <?= json_encode($siswaByKelas) ?>;
const kategoriList = <?= json_encode($kategoriList) ?>;

function loadSiswa() {
    const kelasId = document.getElementById('kelas_id').value;
    const selectSiswa = document.getElementById('siswa_id');
    
    selectSiswa.innerHTML = '<option value="">-- Pilih Siswa --</option>';
    
    if (kelasId && siswaByKelas[kelasId]) {
        siswaByKelas[kelasId].forEach(s => {
            selectSiswa.innerHTML += `<option value="${s.id}">${s.nama}</option>`;
        });
    } else {
        selectSiswa.innerHTML = '<option value="">-- Pilih Kelas Terlebih Dahulu --</option>';
    }
}

function loadKategori() {
    const tingkat = document.getElementById('tingkat').value;
    const selectKategori = document.getElementById('kategori_id');
    
    selectKategori.innerHTML = '<option value="">-- Pilih Jenis Pelanggaran --</option>';
    
    if (tingkat) {
        kategoriList.filter(k => k.tingkat === tingkat).forEach(k => {
            selectKategori.innerHTML += `<option value="${k.id}" data-poin="${k.poin}">${k.nama_kategori}</option>`;
        });
    } else {
        selectKategori.innerHTML = '<option value="">-- Pilih Tingkat Terlebih Dahulu --</option>';
    }
    setDetailPoin(); // reset text & input when tingkat changes
}

function setDetailPoin() {
    const select = document.getElementById('kategori_id');
    const reminderBox = document.getElementById('poinReminder');
    const textPoin = document.getElementById('textPoinDefault');
    const inputPoin = document.getElementById('poin_input');
    
    if (select.selectedIndex > 0) {
        const selectedOption = select.options[select.selectedIndex];
        const poin = selectedOption.getAttribute('data-poin');
        
        if (poin) {
            textPoin.innerText = poin;
            inputPoin.value = poin; // Auto fill the input!
            reminderBox.style.display = 'block';
        } else {
            reminderBox.style.display = 'none';
        }
    } else {
        reminderBox.style.display = 'none';
        inputPoin.value = '';
    }
}
</script>

<style>
.hide-scroll::-webkit-scrollbar {
    display: none;
}
.hide-scroll {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
@keyframes zoomIn {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
</style>
