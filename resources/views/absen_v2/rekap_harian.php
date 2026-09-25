<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#10b981;">ABSEN V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Rekap Harian</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;background:#10b981;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;"><?php echo $initial; ?></div>
                <div>
                    <div style="font-weight:800;font-size:0.9rem;color:#0f172a;"><?php echo $nama; ?></div>
                </div>
            </div>
        </header>

        <div class="z-scroll" style="padding: 2rem;">
            <!-- MONITORING KELAS -->
            <?php if (!empty($monitoring_kelas)): ?>
            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom:1.5rem;">
                <h3 style="font-size:1rem; font-weight:700; color:#475569; margin-bottom:15px; display:flex; align-items:center; gap:8px;"><i data-lucide="activity" style="width:18px; color:#3b82f6;"></i> Monitoring per Kelas</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                    <?php foreach ($monitoring_kelas as $mk): ?>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 15px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
                        <div style="font-weight:800; color:#1e293b; font-size:0.9rem;">Kls <?php echo htmlspecialchars($mk['nama_kelas']); ?></div>
                        <div style="display:flex; gap:6px;">
                            <div onclick="showAdminModal('<?php echo htmlspecialchars($mk['nama_kelas']); ?>', 'Hadir')" style="background:#dcfce7; padding:4px 8px; border-radius:6px; font-size:0.8rem; font-weight:700; color:#166534; cursor:pointer; transition:transform 0.1s;" onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='none'" onmouseleave="this.style.transform='none'" title="Hadir">H: <?php echo $mk['Hadir']; ?></div>
                            <div onclick="showAdminModal('<?php echo htmlspecialchars($mk['nama_kelas']); ?>', 'SakitIzin')" style="background:#fef3c7; padding:4px 8px; border-radius:6px; font-size:0.8rem; font-weight:700; color:#92400e; cursor:pointer; transition:transform 0.1s;" onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='none'" onmouseleave="this.style.transform='none'" title="Sakit/Izin">S/I: <?php echo $mk['SakitIzin']; ?></div>
                            <div onclick="showAdminModal('<?php echo htmlspecialchars($mk['nama_kelas']); ?>', 'Alpa')" style="background:#fee2e2; padding:4px 8px; border-radius:6px; font-size:0.8rem; font-weight:700; color:#991b1b; cursor:pointer; transition:transform 0.1s;" onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='none'" onmouseleave="this.style.transform='none'" title="Alpa">A: <?php echo $mk['Alpa']; ?></div>
                            <div onclick="showAdminModal('<?php echo htmlspecialchars($mk['nama_kelas']); ?>', 'Bolos')" style="background:#fce7f3; padding:4px 8px; border-radius:6px; font-size:0.8rem; font-weight:700; color:#be185d; cursor:pointer; transition:transform 0.1s;" onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='none'" onmouseleave="this.style.transform='none'" title="Bolos">B: <?php echo $mk['Bolos']; ?></div>
                            <div onclick="showAdminModal('<?php echo htmlspecialchars($mk['nama_kelas']); ?>', 'Belum')" style="background:#f1f5f9; padding:4px 8px; border-radius:6px; font-size:0.8rem; font-weight:700; color:#334155; cursor:pointer; transition:transform 0.1s;" onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='none'" onmouseleave="this.style.transform='none'" title="Belum Scan">?: <?php echo $mk['Belum']; ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h2 style="margin:0; font-size:1.2rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="calendar" style="color:#10b981;"></i> Rekap Harian Absensi
                    </h2>
                    <form method="GET" style="display:flex; gap:10px;">
                        <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;">
                        <select name="kelas_id" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="padding:8px 16px; background:#10b981; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Filter</button>
                    </form>
                </div>
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:800px;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">No</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">NIS</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Nama Siswa</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Kelas</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Waktu Absen</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Status</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($absensi)): ?>
                            <tr>
                                <td colspan="6" style="padding:24px; text-align:center; color:#94a3b8; font-weight:600;">Belum ada data absensi untuk tanggal ini.</td>
                            </tr>
                            <?php else: ?>
                                <?php $no=1; foreach($absensi as $a): ?>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:12px 16px; color:#475569;"><?php echo $no++; ?></td>
                                    <td style="padding:12px 16px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($a['nis']); ?></td>
                                    <td style="padding:12px 16px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($a['nama']); ?></td>
                                    <td style="padding:12px 16px; color:#475569;"><?php echo htmlspecialchars($a['nama_kelas']); ?></td>
                                    <td style="padding:12px 16px; color:#475569;"><?php echo date('H:i:s', strtotime($a['created_at'])); ?></td>
                                    <td style="padding:12px 16px;">
                                        <?php if ($a['status'] == 'Hadir'): ?>
                                            <span style="background:#dcfce7; color:#15803d; padding:4px 10px; border-radius:100px; font-size:0.75rem; font-weight:700;">Hadir</span>
                                        <?php elseif ($a['status'] == 'Terlambat'): ?>
                                            <span style="background:#fef3c7; color:#b45309; padding:4px 10px; border-radius:100px; font-size:0.75rem; font-weight:700;">Terlambat</span>
                                        <?php elseif ($a['status'] == 'Bolos'): ?>
                                            <span style="background:#fce7f3; color:#be185d; padding:4px 10px; border-radius:100px; font-size:0.75rem; font-weight:700;">Bolos</span>
                                        <?php else: ?>
                                            <span style="background:#fee2e2; color:#b91c1c; padding:4px 10px; border-radius:100px; font-size:0.75rem; font-weight:700;"><?php echo htmlspecialchars($a['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:12px 16px; text-align:center;">
                                        <button onclick="openEditModal(<?php echo $a['id']; ?>, '<?php echo htmlspecialchars($a['nama']); ?>', '<?php echo $a['status']; ?>')" style="background:#3b82f6; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:0.8rem; font-weight:600; display:inline-flex; align-items:center; gap:5px;">
                                            <i data-lucide="edit-3" style="width:14px; height:14px;"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Admin Monitoring -->
<div id="adminModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(2px);">
    <div style="background: white; border-radius: 15px; width: 90%; max-width: 400px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <h3 id="adminModalTitle" style="margin: 0; font-size: 1.1rem; color: #1e293b; display:flex; align-items:center; gap:8px;"><i data-lucide="users" style="width:18px;"></i> <span>Daftar Siswa</span></h3>
            <button onclick="closeAdminModal()" style="background: #f1f5f9; border: none; width: 30px; height: 30px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1rem; color: #94a3b8; cursor: pointer;"><i data-lucide="x" style="width:18px;"></i></button>
        </div>
        <div id="adminModalList" style="max-height: 50vh; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; color: #475569;">
        </div>
    </div>
</div>

<!-- Modal Edit Status Absensi -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(2px);">
    <div style="background: white; border-radius: 15px; width: 90%; max-width: 400px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; display:flex; align-items:center; gap:8px;"><i data-lucide="edit" style="width:18px;"></i> <span>Edit Status Absen</span></h3>
            <button onclick="closeEditModal()" style="background: #f1f5f9; border: none; width: 30px; height: 30px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1rem; color: #94a3b8; cursor: pointer;"><i data-lucide="x" style="width:18px;"></i></button>
        </div>
        <div style="margin-bottom: 15px; color:#475569; font-size:0.9rem;">
            Siswa: <strong id="editModalSiswaNama"></strong>
        </div>
        <form id="formEditAbsen">
            <input type="hidden" id="editAbsenId" name="id">
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600; font-size:0.9rem; color:#1e293b;">Status Kehadiran</label>
                <select id="editAbsenStatus" name="status" style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1; outline:none; font-size:0.95rem;">
                    <option value="Hadir">Hadir</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpa">Alpa</option>
                    <option value="Bolos">Bolos</option>
                </select>
            </div>
            <button type="submit" style="width:100%; background:#10b981; color:white; border:none; padding:10px; border-radius:8px; font-weight:700; cursor:pointer;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    const monitoringData = <?php echo json_encode(array_values($monitoring_kelas ?? [])); ?>;
    
    function showAdminModal(namaKelas, tipe) {
        let title = '';
        let color = '';
        if (tipe === 'Hadir') { title = 'Hadir'; color = '#10b981'; }
        else if (tipe === 'SakitIzin') { title = 'Sakit/Izin'; color = '#f59e0b'; }
        else if (tipe === 'Alpa') { title = 'Alpa'; color = '#ef4444'; }
        else if (tipe === 'Bolos') { title = 'Bolos'; color = '#be185d'; }
        else if (tipe === 'Belum') { title = 'Belum Scan'; color = '#64748b'; }
        
        document.querySelector('#adminModalTitle span').innerHTML = '<span style="color:'+color+'; font-weight:800;">' + title + '</span> - Kls ' + namaKelas;
        
        const listDiv = document.getElementById('adminModalList');
        listDiv.innerHTML = '';
        
        const classData = monitoringData.find(c => c.nama_kelas === namaKelas);
        const dataList = classData ? classData['list_' + tipe] : [];
        
        if (!dataList || dataList.length === 0) {
            listDiv.innerHTML = '<div style="text-align:center; padding: 20px; color:#94a3b8; font-style:italic;">Tidak ada data</div>';
        } else {
            dataList.forEach((nama, idx) => {
                listDiv.innerHTML += '<div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-weight: 500;">' + (idx+1) + '. ' + nama + '</div>';
            });
        }
        
        document.getElementById('adminModal').style.display = 'flex';
    }
    
    function closeAdminModal() {
        document.getElementById('adminModal').style.display = 'none';
    }
    
    document.getElementById('adminModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAdminModal();
        }
    });

    // Edit Modal Logic
    function openEditModal(id, nama, status) {
        document.getElementById('editAbsenId').value = id;
        document.getElementById('editModalSiswaNama').textContent = nama;
        document.getElementById('editAbsenStatus').value = status;
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    document.getElementById('formEditAbsen').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/absen/update-status-siswa', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Berhasil diperbarui!');
                location.reload();
            } else {
                alert('Gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem.');
        });
    });

    lucide.createIcons();
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('active');
    }
</script>
