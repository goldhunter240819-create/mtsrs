<style>
    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 0.95rem;
        color: #1e293b;
        outline: none;
        transition: all 0.2s ease;
        appearance: none;
    }
    .form-control:focus {
        border-color: #10b981;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .select-wrapper {
        position: relative;
    }
    .select-wrapper::after {
        content: '\25BC';
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        font-size: 0.7rem;
        color: #64748b;
        pointer-events: none;
    }
    .btn-primary {
        display: block;
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        text-align: center;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        border: none;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        transition: all 0.2s ease;
    }
    .btn-primary:active {
        transform: scale(0.98);
    }
    .tabs {
        display: flex;
        background: #f1f5f9;
        padding: 5px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .tab {
        flex: 1;
        text-align: center;
        padding: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .tab.active {
        background: white;
        color: #10b981;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .history-card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .history-title {
        font-weight: 800;
        color: #1e293b;
        font-size: 0.95rem;
    }
    .history-meta {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 4px;
    }
    .btn-edit {
        background: #eff6ff;
        color: #3b82f6;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
</style>

<!-- Header -->
<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <a href="/apk/aplikasi-saya" style="color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-right: 15px;">
            <i data-lucide="arrow-left"></i>
        </a>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Menu Guru</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Nilai Harian</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0;">
    <div class="tabs">
        <div class="tab active" id="tabInput" onclick="switchTab('input')">Input Baru</div>
        <div class="tab" id="tabRiwayat" onclick="switchTab('riwayat')">Riwayat Nilai</div>
    </div>

    <!-- TAB INPUT BARU -->
    <div id="contentInput">
        <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
            <p style="font-size: 0.85rem; color: #64748b; margin-top: 0; margin-bottom: 20px;">
                Pilih parameter di bawah ini untuk memulai input nilai. Sistem akan otomatis menentukan urutan Penilaian Harian (PH).
            </p>
            
            <form action="/apk/nilai-harian/input" method="GET">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Pilih Kelas</label>
                    <div class="select-wrapper">
                        <select name="kelas_id" id="kelas_id" class="form-control" required onchange="updateMapelDropdown()">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach($kelasList as $k): ?>
                                <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Pilih Mata Pelajaran</label>
                    <div class="select-wrapper">
                        <select name="mapel_id" id="mapel_id" class="form-control" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            <?php foreach($mapelList as $m): ?>
                                <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label>Jenis Evaluasi</label>
                    <div class="select-wrapper">
                        <select name="jenis_evaluasi_tipe" class="form-control" required>
                            <option value="PH">Penilaian Harian (Auto PH 1, PH 2, dst)</option>
                            <option value="PTS">PTS (Penilaian Tengah Semester)</option>
                            <option value="PAS">PAS (Penilaian Akhir Semester)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    Lanjutkan Input Nilai
                    <i data-lucide="arrow-right" style="width: 18px; height: 18px; vertical-align: middle; margin-left: 5px;"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- TAB RIWAYAT -->
    <div id="contentRiwayat" style="display: none;">
        <?php if(empty($historyGrouped)): ?>
            <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 16px; border: 1px solid #f1f5f9;">
                <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                    <i data-lucide="folder-open" style="width: 28px; height: 28px; color: #94a3b8;"></i>
                </div>
                <div style="font-weight: 700; color: #475569;">Belum Ada Riwayat</div>
                <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 5px;">Anda belum memasukkan nilai apa pun di semester ini.</div>
            </div>
        <?php else: ?>
            <?php foreach($historyGrouped as $namaKelas => $mapelGroup): ?>
                <div style="margin-top: 20px; margin-bottom: 10px;">
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px;">
                        <?php echo htmlspecialchars($namaKelas); ?>
                    </h3>
                </div>
                
                <?php foreach($mapelGroup as $namaMapel => $historyList): ?>
                    <div style="margin-top: 15px; margin-bottom: 8px;">
                        <div style="font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i data-lucide="book" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                            <?php echo htmlspecialchars($namaMapel); ?>
                        </div>
                    </div>
                    
                    <?php foreach($historyList as $idx => $h): 
                        $cardId = 'eval_' . md5($h['kelas_id'] . $h['mapel_id'] . $h['jenis_evaluasi'] . $idx);
                    ?>
                        <div class="history-card" style="margin-bottom: 12px; overflow: hidden;">
                            <div onclick="toggleDropdown('<?php echo $cardId; ?>')" style="cursor: pointer; display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div class="history-title"><?php echo htmlspecialchars($h['jenis_evaluasi']); ?></div>
                                    <div class="history-meta">
                                        <span style="font-weight: 700; color: <?php echo $h['persen_ikut'] == 100 ? '#10b981' : '#f59e0b'; ?>;">
                                            <?php echo $h['persen_ikut']; ?>% Ikut (<?php echo count(array_filter($h['details'], function($d){ return $d['nilai'] > 0; })); ?> dari <?php echo $h['jml_siswa']; ?> Siswa)
                                        </span>
                                    </div>
                                    <?php if (!empty($h['keterangan'])): ?>
                                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 6px; padding-top: 6px; border-top: 1px dashed #e2e8f0;">
                                            <i data-lucide="book-open" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;"></i> <?php echo htmlspecialchars($h['keterangan']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <i data-lucide="chevron-down" id="icon_<?php echo $cardId; ?>" style="width: 20px; height: 20px; color: #94a3b8; transition: transform 0.3s;"></i>
                                </div>
                            </div>
                            
                            <div id="detail_<?php echo $cardId; ?>" style="display: none; margin-top: 15px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                                <div style="max-height: 250px; overflow-x: hidden; overflow-y: auto; padding-right: 5px; margin-bottom: 15px;">
                                    <div style="display: flex; background: #f8fafc; padding: 8px; border-bottom: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                        <div style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Nama Siswa</div>
                                        <div style="width: 50px; text-align: center;">Nilai</div>
                                    </div>
                                    <?php foreach($h['details'] as $d): ?>
                                        <div style="display: flex; padding: 8px; border-bottom: 1px dashed #e2e8f0; font-size: 0.75rem; align-items: center;">
                                            <div style="flex: 1; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding-right: 10px;" title="<?php echo htmlspecialchars($d['nama']); ?>">
                                                <?php echo htmlspecialchars($d['nama']); ?>
                                            </div>
                                            <div style="width: 50px; text-align: center; font-weight: 800; color: <?php echo empty($d['nilai']) ? '#94a3b8' : ($d['tuntas'] ? '#10b981' : '#ef4444'); ?>;">
                                                <?php echo empty($d['nilai']) ? 'Belum' : $d['nilai']; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div style="display: flex; gap: 8px; margin-top: 10px;">
                                    <a href="/apk/nilai-harian/input?kelas_id=<?php echo $h['kelas_id']; ?>&mapel_id=<?php echo $h['mapel_id']; ?>&jenis_evaluasi_tipe=EXACT&jenis_evaluasi_exact=<?php echo urlencode($h['jenis_evaluasi']); ?>" class="btn-edit" style="flex: 1; text-align: center; justify-content: center; padding: 8px 12px; font-size: 0.75rem;">
                                        <i data-lucide="edit-3" style="width: 14px;"></i> Edit Nilai
                                    </a>
                                    <button onclick="hapusRiwayat('<?php echo $h['kelas_id']; ?>', '<?php echo $h['mapel_id']; ?>', '<?php echo addslashes($h['jenis_evaluasi']); ?>')" class="btn-edit" style="flex: 1; text-align: center; justify-content: center; background: #fee2e2; color: #ef4444; border: none; cursor: pointer; padding: 8px 12px; font-size: 0.75rem;">
                                        <i data-lucide="trash-2" style="width: 14px;"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const mengajarData = <?php echo json_encode($mengajarData ?? []); ?>;

    function hapusRiwayat(kelas_id, mapel_id, jenis_evaluasi) {
        Swal.fire({
            title: 'Hapus Riwayat?',
            text: "Seluruh nilai siswa pada " + jenis_evaluasi + " ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('kelas_id', kelas_id);
                formData.append('mapel_id', mapel_id);
                formData.append('jenis_evaluasi', jenis_evaluasi);
                
                fetch('/apk/nilai-harian/delete', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Terhapus!', data.message, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error!', 'Terjadi kesalahan jaringan', 'error');
                });
            }
        });
    }

    function updateMapelDropdown() {
        var kelas_id = document.getElementById('kelas_id').value;
        var mapelSelect = document.getElementById('mapel_id');
        var currentVal = mapelSelect.value;
        
        mapelSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
        
        if (!kelas_id) return;
        
        var filteredMapel = mengajarData.filter(function(item) {
            return item.kelas_id == kelas_id;
        });
        
        var uniqueMapels = {};
        filteredMapel.forEach(function(item) {
            if(!uniqueMapels[item.mapel_id]) {
                var option = document.createElement('option');
                option.value = item.mapel_id;
                option.text = item.nama_mapel;
                mapelSelect.appendChild(option);
                uniqueMapels[item.mapel_id] = true;
            }
        });
    }

    function switchTab(tab) {
        document.getElementById('tabInput').classList.remove('active');
        document.getElementById('tabRiwayat').classList.remove('active');
        document.getElementById('contentInput').style.display = 'none';
        document.getElementById('contentRiwayat').style.display = 'none';

        if(tab === 'input') {
            document.getElementById('tabInput').classList.add('active');
            document.getElementById('contentInput').style.display = 'block';
        } else {
            document.getElementById('tabRiwayat').classList.add('active');
            document.getElementById('contentRiwayat').style.display = 'block';
        }
    }

    function toggleDropdown(id) {
        var detail = document.getElementById('detail_' + id);
        var icon = document.getElementById('icon_' + id);
        if (detail.style.display === 'none') {
            detail.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        } else {
            detail.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }
</script>
