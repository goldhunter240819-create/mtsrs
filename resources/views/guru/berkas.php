<div class="z-card" style="padding: 2rem; border-radius: 16px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; margin-bottom: 2rem;">
    <h2 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: -0.5px;">Pusat Dokumen</h2>
    <p style="font-size: 1rem; opacity: 0.9; margin: 0; max-width: 600px;">
        Arsip E-Kinerja & Berkas Pribadi. Kelola dokumen pengajaran dan berkas pribadi Anda di sini.
    </p>
</div>

<?php if(isset($_SESSION['guru_msg'])): ?>
    <div style="background: <?= $_SESSION['guru_msg_type']=='success'?'#ecfdf5':($_SESSION['guru_msg_type']=='warning'?'#fffbeb':'#fee2e2') ?>; border-left: 4px solid <?= $_SESSION['guru_msg_type']=='success'?'#10b981':($_SESSION['guru_msg_type']=='warning'?'#f59e0b':'#ef4444') ?>; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;">
        <i data-lucide="<?= $_SESSION['guru_msg_type']=='success'?'check-circle':($_SESSION['guru_msg_type']=='warning'?'alert-triangle':'alert-circle') ?>" style="color: <?= $_SESSION['guru_msg_type']=='success'?'#10b981':($_SESSION['guru_msg_type']=='warning'?'#f59e0b':'#ef4444') ?>; width: 20px;"></i>
        <p style="margin:0; color: <?= $_SESSION['guru_msg_type']=='success'?'#065f46':($_SESSION['guru_msg_type']=='warning'?'#b45309':'#b91c1c') ?>; font-size: 0.9rem; font-weight: 600; line-height: 1.5;"><?= $_SESSION['guru_msg'] ?></p>
    </div>
    <?php unset($_SESSION['guru_msg']); unset($_SESSION['guru_msg_type']); ?>
<?php endif; ?>

<div class="z-card" style="padding: 1.5rem; border-radius: 16px;">
    
    <!-- Tab Navigasi -->
    <div style="display: flex; gap: 10px; margin-bottom: 25px; padding: 5px; background: #f1f5f9; border-radius: 12px; max-width: 400px;">
        <button onclick="switchTab('perangkat')" id="tab_perangkat" style="flex: 1; padding: 8px; background: #fff; color: #1e3a8a; border: none; border-radius: 8px; font-weight: 700; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 6px;">
            <i data-lucide="folder-open" style="width: 16px;"></i> Perangkat Mengajar
        </button>
        <button onclick="switchTab('pribadi')" id="tab_pribadi" style="flex: 1; padding: 8px; background: transparent; color: #64748b; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 6px;">
            <i data-lucide="user-check" style="width: 16px;"></i> Berkas Pribadi
        </button>
    </div>

    <!-- ================= VIEW PERANGKAT ================= -->
    <div id="view_perangkat">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin: 0;">Riwayat Perangkat Pembelajaran</h3>
            <button onclick="toggleUploadForm()" class="z-btn z-btn-primary" style="display: none; align-items: center; gap: 6px;">
                <i data-lucide="plus" style="width: 16px;"></i> Upload Baru
            </button>
        </div>

        <!-- Form Upload Berkas (Hidden by default) -->
        <div id="formUploadWrapper" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="upload-cloud" style="color: #3b82f6; width: 22px;"></i> Form Unggah Perangkat
                </h3>
                <button onclick="toggleUploadForm()" style="background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 4px;">
                    <i data-lucide="x" style="width: 20px;"></i>
                </button>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data" id="formUpload" onsubmit="return submitForm('btnUpload')">
                <input type="hidden" name="tab_type" value="perangkat">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; color: #475569; font-size: 0.9rem;">Jenis Berkas <span style="color:red">*</span></label>
                        <select name="jenis_berkas" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Capaian Pembelajaran (CP) / KI-KD">Capaian Pembelajaran (CP) / KI-KD</option>
                            <option value="Silabus / ATP">Silabus / ATP</option>
                            <option value="Program Tahunan (Prota)">Program Tahunan (Prota)</option>
                            <option value="Program Semester (Promes)">Program Semester (Promes)</option>
                            <option value="RPP / Modul Ajar">RPP / Modul Ajar</option>
                            <option value="KKM / KKTP">KKM / KKTP</option>
                            <option value="Bank Soal / Evaluasi">Bank Soal / Evaluasi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; color: #475569; font-size: 0.9rem;">Judul Spesifik <span style="color:red">*</span></label>
                        <input type="text" name="judul_berkas" required placeholder="Contoh: Modul Ajar MTK Kelas 7 Sem 1" style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; color: #475569; font-size: 0.9rem;">Kelas <span style="color:red">*</span></label>
                        <select name="kelas_id" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach($kelas_mengajar as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; color: #475569; font-size: 0.9rem;">Mapel <span style="color:red">*</span></label>
                        <select name="mapel_id" required style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                            <option value="">-- Pilih Mapel --</option>
                            <?php foreach($mapel_mengajar as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama_mapel']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 6px; font-weight: 600; color: #475569; font-size: 0.9rem;">File Dokumen (Wajib berformat PDF) <span style="color:red">*</span></label>
                    <input type="file" name="file_berkas" accept=".pdf" required style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                    <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 5px;">Maksimal ukuran file: 5MB</div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" id="btnUpload" class="z-btn z-btn-primary" style="display: flex; align-items: center; gap: 8px; padding: 12px 24px;">
                        <i data-lucide="send" style="width: 18px;"></i> Simpan & Ajukan Validasi
                    </button>
                </div>
            </form>
        </div>
        
        <?php if(empty($kombinasi_mengajar)): ?>
            <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 16px;">
                <div style="background: #e2e8f0; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="folder-search" style="width: 30px; height: 30px; color: #64748b;"></i>
                </div>
                <h4 style="font-size: 1.1rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Tidak Ada Jadwal Mengajar</h4>
                <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Anda belum memiliki jadwal mengajar di tahun ajaran ini.</p>
            </div>
        <?php else: ?>
            <!-- Filter View Kelas -->
            <div style="margin-bottom: 20px; max-width: 400px;">
                <select id="filter_kelas" onchange="filterKombinasi()" style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                    <option value="all">Tampilkan Semua Kelas</option>
                    <?php 
                    $unique_kelas = [];
                    foreach($kombinasi_mengajar as $k) {
                        $unique_kelas[$k['kelas_id']] = $k['nama_kelas'];
                    }
                    foreach($unique_kelas as $k_id => $k_nama): ?>
                        <option value="<?= $k_id ?>"><?= htmlspecialchars($k_nama) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                <?php foreach($kombinasi_mengajar as $kombinasi): ?>
                    <div class="kombinasi-card" data-kelas="<?= $kombinasi['kelas_id'] ?>" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">
                            <div style="background: #e0f2fe; color: #0284c7; padding: 12px; border-radius: 12px;">
                                <i data-lucide="book-open" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0;"><?= htmlspecialchars($kombinasi['nama_kelas']) ?></h4>
                                <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;"><?= htmlspecialchars($kombinasi['nama_mapel']) ?></div>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php foreach($dokumen_wajib as $dok_wajib): 
                                $matched_berkas = [];
                                foreach($berkasList as $berkas) {
                                    if ($berkas['kelas_id'] == $kombinasi['kelas_id'] && $berkas['mapel_id'] == $kombinasi['mapel_id'] && $berkas['jenis_berkas'] == $dok_wajib) {
                                        $matched_berkas[] = $berkas;
                                    }
                                }
                            ?>
                                <?php if(!empty($matched_berkas)): ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                        <div style="font-size: 0.85rem; font-weight: 700; color: #475569;"><?= htmlspecialchars($dok_wajib) ?></div>
                                        <button onclick="document.querySelector('[name=jenis_berkas]').value = '<?= $dok_wajib ?>'; document.querySelector('[name=kelas_id]').value = '<?= $kombinasi['kelas_id'] ?>'; document.querySelector('[name=mapel_id]').value = '<?= $kombinasi['mapel_id'] ?>'; toggleUploadForm(); document.getElementById('formUploadWrapper').scrollIntoView({behavior: 'smooth'});" style="background: none; border: none; color: #3b82f6; padding: 2px 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                            <i data-lucide="plus" style="width: 12px;"></i> Tambah
                                        </button>
                                    </div>
                                    <?php foreach($matched_berkas as $berkasFound): 
                                        $statusColor = '#f59e0b'; $statusBg = '#fffbeb';
                                        if ($berkasFound['status_validasi'] == 'Disetujui') { $statusColor = '#10b981'; $statusBg = '#ecfdf5'; }
                                        elseif ($berkasFound['status_validasi'] == 'Ditolak') { $statusColor = '#ef4444'; $statusBg = '#fee2e2'; }
                                    ?>
                                        <div style="background: #f8fafc; padding: 8px 12px; border-radius: 8px; border-left: 3px solid <?= $statusColor ?>; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                                            <div style="flex: 1; min-width: 0;">
                                                <div style="font-size: 0.85rem; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($berkasFound['judul_berkas']) ?></div>
                                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;"><?= date('d/m/Y', strtotime($berkasFound['tanggal_upload'])) ?> · <span style="color: <?= $statusColor ?>; font-weight: 700;"><?= $berkasFound['status_validasi'] ?></span></div>
                                            </div>
                                            <div style="display: flex; gap: 6px; margin-left: 10px; flex-shrink: 0;">
                                                <a href="/public/uploads/berkas/<?= htmlspecialchars($berkasFound['file_nama']) ?>" target="_blank" style="background: #e0f2fe; color: #0284c7; text-decoration: none; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 4px;"><i data-lucide="external-link" style="width: 14px; height: 14px;"></i> Lihat</a>
                                                <a href="javascript:void(0)" onclick="Swal.fire({title:'Hapus berkas ini?',text:'File akan dihapus permanen.',icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonText:'Batal',confirmButtonText:'Hapus'}).then(r=>{if(r.isConfirmed) location.href='/guru/berkas/hapus/<?= $berkasFound['id'] ?>';})" style="background: #fee2e2; color: #dc2626; text-decoration: none; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 4px;"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="background: #fdf2f8; border-left: 3px solid #f43f5e; border-radius: 8px; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                        <div>
                                            <div style="font-size: 0.85rem; font-weight: 700; color: #4c1d95;"><?= htmlspecialchars($dok_wajib) ?></div>
                                            <div style="font-size: 0.75rem; color: #f43f5e; font-weight: 600; margin-top: 2px;">Belum Upload</div>
                                        </div>
                                        <button onclick="document.querySelector('[name=jenis_berkas]').value = '<?= $dok_wajib ?>'; document.querySelector('[name=kelas_id]').value = '<?= $kombinasi['kelas_id'] ?>'; document.querySelector('[name=mapel_id]').value = '<?= $kombinasi['mapel_id'] ?>'; toggleUploadForm(); document.getElementById('formUploadWrapper').scrollIntoView({behavior: 'smooth'});" style="background: transparent; border: 1px solid #f43f5e; color: #f43f5e; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                            <i data-lucide="upload" style="width: 12px;"></i> Upload
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- Dokumen "Lainnya" untuk kelas/mapel ini -->
                            <?php 
                                $adaLainnya = false;
                                foreach($berkasList as $berkas) {
                                    if ($berkas['kelas_id'] == $kombinasi['kelas_id'] && $berkas['mapel_id'] == $kombinasi['mapel_id'] && !in_array($berkas['jenis_berkas'], $dokumen_wajib)) {
                                        if(!$adaLainnya) {
                                            echo '<div style="margin-top: 15px; font-size: 0.95rem; font-weight: 700; color: #64748b; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">Dokumen Lainnya:</div>';
                                            $adaLainnya = true;
                                        }
                                        $statusColor = '#f59e0b';
                                        $statusBg = '#fffbeb';
                                        if ($berkas['status_validasi'] == 'Disetujui') {
                                            $statusColor = '#10b981';
                                            $statusBg = '#ecfdf5';
                                        } elseif ($berkas['status_validasi'] == 'Ditolak') {
                                            $statusColor = '#ef4444';
                                            $statusBg = '#fee2e2';
                                        }
                                        ?>
                                        <div style="background: #f1f5f9; border-radius: 8px; padding: 10px 12px; margin-bottom: 8px;">
                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                                <div>
                                                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 2px;"><?= htmlspecialchars($berkas['jenis_berkas']) ?></div>
                                                    <div style="font-size: 0.85rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($berkas['judul_berkas']) ?></div>
                                                </div>
                                                <div style="background: <?= $statusBg ?>; color: <?= $statusColor ?>; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">
                                                    <?= htmlspecialchars($berkas['status_validasi']) ?>
                                                </div>
                                            </div>
                                            <div style="display: flex; gap: 10px;">
                                                <a href="/public/uploads/berkas/<?= htmlspecialchars($berkas['file_nama']) ?>" target="_blank" style="font-size: 0.85rem; font-weight: 600; color: #3b82f6; text-decoration: none;"><i data-lucide="external-link" style="width: 14px; height: 14px; display:inline-block; vertical-align:middle;"></i> Lihat File</a>
                                                <a href="javascript:void(0)" onclick="Swal.fire({title:'Hapus?',text:'Yakin hapus file ini?',icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonText:'Batal',confirmButtonText:'Hapus'}).then(r=>{if(r.isConfirmed) location.href='/guru/berkas/hapus/<?= $berkas['id'] ?>';})" style="font-size: 0.85rem; font-weight: 600; color: #ef4444; text-decoration: none;"><i data-lucide="trash-2" style="width: 14px; height: 14px; display:inline-block; vertical-align:middle;"></i> Hapus</a>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ================= VIEW PRIBADI ================= -->
    <div id="view_pribadi" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin: 0;">Arsip Dokumen Pribadi</h3>
            <button onclick="togglePribadiForm()" class="z-btn" style="background: #10b981; color: white; border: none; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="plus" style="width: 16px;"></i> Upload Baru
            </button>
        </div>

        <!-- Form Upload Berkas Pribadi -->
        <div id="formUploadPribadiWrapper" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; padding: 25px; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="upload-cloud" style="color: #10b981; width: 22px;"></i> Upload Berkas Pribadi
                </h3>
                <button onclick="togglePribadiForm()" style="background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 4px;">
                    <i data-lucide="x" style="width: 20px;"></i>
                </button>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data" id="formUploadPribadi" onsubmit="return submitForm('btnUploadPribadi')">
                <input type="hidden" name="tab_type" value="pribadi">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; color: #475569; font-size: 0.9rem;">Jenis Dokumen <span style="color:red">*</span></label>
                        <select name="jenis_berkas" required style="width: 100%; padding: 10px 15px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="KTP">KTP</option>
                            <option value="Kartu Keluarga (KK)">Kartu Keluarga (KK)</option>
                            <option value="Ijazah Terakhir">Ijazah Terakhir</option>
                            <option value="Transkrip Nilai">Transkrip Nilai</option>
                            <option value="Sertifikat Pendidik">Sertifikat Pendidik</option>
                            <option value="SK Pengangkatan">SK Pengangkatan</option>
                            <option value="NUPTK / NRG">NUPTK / NRG</option>
                            <option value="Kartu BPJS">Kartu BPJS</option>
                            <option value="NPWP">NPWP</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom: 6px; font-weight: 600; color: #475569; font-size: 0.9rem;">Keterangan <span style="color:red">*</span></label>
                        <input type="text" name="judul_berkas" required placeholder="Contoh: KTP Atas Nama Ahmad" style="width: 100%; padding: 10px 15px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 6px; font-weight: 600; color: #475569; font-size: 0.9rem;">File Dokumen (PDF / Gambar) <span style="color:red">*</span></label>
                    <input type="file" name="file_berkas" accept=".pdf,.jpg,.jpeg,.png" required style="width: 100%; padding: 10px; border: 1px dashed #bbf7d0; border-radius: 8px; font-size: 0.95rem; background: #fff; color: #1e293b; outline: none;">
                    <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 5px;">Format: PDF, JPG, JPEG, PNG – Maks. 5MB</div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" id="btnUploadPribadi" class="z-btn" style="background: #10b981; color: white; border: none; display: flex; align-items: center; gap: 8px; padding: 12px 24px;">
                        <i data-lucide="send" style="width: 18px;"></i> Simpan Berkas Pribadi
                    </button>
                </div>
            </form>
        </div>

        <!-- Daftar Berkas Pribadi -->
        <?php if(empty($berkasPribadiList)): ?>
            <div style="text-align: center; padding: 50px 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 16px;">
                <div style="background: #f0fdf4; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="folder-open" style="width: 30px; height: 30px; color: #10b981;"></i>
                </div>
                <h4 style="font-size: 1.1rem; font-weight: 700; color: #334155; margin-bottom: 5px;">Belum Ada Berkas Pribadi</h4>
                <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Klik tombol <b>+ Upload Baru</b> untuk mulai mengunggah dokumen pribadi Anda.</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                <?php foreach($berkasPribadiList as $bp): 
                    $ext = strtolower(pathinfo($bp['file_nama'], PATHINFO_EXTENSION));
                ?>
                    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <div style="background: #f1f5f9; padding: 8px; border-radius: 8px; color: #64748b;">
                                <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 0.9rem; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;"><?= htmlspecialchars($bp['jenis_berkas']) ?></div>
                                <div style="font-size: 0.75rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($bp['judul_berkas']) ?></div>
                                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 4px;"><?= date('d/m/Y', strtotime($bp['tanggal_upload'])) ?> • <span style="background: #e2e8f0; color: #475569; padding: 1px 4px; border-radius: 4px; font-weight: 700; text-transform: uppercase;"><?= $ext ?></span></div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                            <a href="/public/uploads/berkas/<?= htmlspecialchars($bp['file_nama']) ?>" target="_blank" style="flex: 1; text-align: center; background: #e0f2fe; color: #0284c7; text-decoration: none; padding: 6px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 4px;"><i data-lucide="external-link" style="width: 14px; height: 14px;"></i> Lihat</a>
                            <a href="javascript:void(0)" onclick="Swal.fire({title:'Hapus berkas ini?',text:'File akan dihapus permanen.',icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonText:'Batal',confirmButtonText:'Hapus'}).then(r=>{if(r.isConfirmed) location.href='/guru/berkas-pribadi/hapus/<?= $bp['id'] ?>';})" style="flex: 1; text-align: center; background: #fee2e2; color: #dc2626; text-decoration: none; padding: 6px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 4px;"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function filterKombinasi() {
    const selectedKelas = document.getElementById('filter_kelas').value;
    const cards = document.querySelectorAll('.kombinasi-card');
    
    cards.forEach(card => {
        if (selectedKelas === 'all' || card.getAttribute('data-kelas') === selectedKelas) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function switchTab(tab) {
    const btnPerangkat = document.getElementById('tab_perangkat');
    const btnPribadi = document.getElementById('tab_pribadi');
    const viewPerangkat = document.getElementById('view_perangkat');
    const viewPribadi = document.getElementById('view_pribadi');

    if(tab === 'perangkat') {
        btnPerangkat.style.background = '#fff';
        btnPerangkat.style.color = '#1e3a8a';
        btnPerangkat.style.boxShadow = '0 4px 10px rgba(0,0,0,0.05)';
        
        btnPribadi.style.background = 'transparent';
        btnPribadi.style.color = '#64748b';
        btnPribadi.style.boxShadow = 'none';

        viewPerangkat.style.display = 'block';
        viewPribadi.style.display = 'none';
    } else {
        btnPribadi.style.background = '#fff';
        btnPribadi.style.color = '#1e3a8a';
        btnPribadi.style.boxShadow = '0 4px 10px rgba(0,0,0,0.05)';
        
        btnPerangkat.style.background = 'transparent';
        btnPerangkat.style.color = '#64748b';
        btnPerangkat.style.boxShadow = 'none';

        viewPerangkat.style.display = 'none';
        viewPribadi.style.display = 'block';
    }
}

function toggleUploadForm() {
    const formWrapper = document.getElementById('formUploadWrapper');
    if (formWrapper.style.display === 'none' || formWrapper.style.display === '') {
        formWrapper.style.display = 'block';
    } else {
        formWrapper.style.display = 'none';
    }
}

function togglePribadiForm() {
    const formWrapper = document.getElementById('formUploadPribadiWrapper');
    if (formWrapper.style.display === 'none' || formWrapper.style.display === '') {
        formWrapper.style.display = 'block';
    } else {
        formWrapper.style.display = 'none';
    }
}

function submitForm(btnId) {
    var btn = document.getElementById(btnId);
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="lucide-spin" style="width: 18px;"></i> Mengunggah...';
    lucide.createIcons();
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($active_tab) && $active_tab === 'pribadi'): ?>
        switchTab('pribadi');
    <?php endif; ?>
});
</script>
