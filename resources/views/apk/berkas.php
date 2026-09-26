<div class="apk-vector-header" style="padding-bottom: 70px; justify-content: center; text-align: center; position: relative;">
    <div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Pusat Dokumen</div>
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.8); margin-top: 5px;">Arsip E-Kinerja & Berkas Pribadi</div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; min-height: 70vh;">
    
    <!-- Tab Navigasi -->
    <div style="display: flex; gap: 10px; margin-bottom: 25px; padding: 5px; background: #f1f5f9; border-radius: 15px;">
        <button onclick="switchTab('perangkat')" id="tab_perangkat" style="flex: 1; padding: 12px; background: #fff; color: #0ea5e9; border: none; border-radius: 12px; font-weight: 800; font-size: 0.8rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05); cursor: pointer; transition: all 0.2s;">
            Perangkat Mengajar
        </button>
        <button onclick="switchTab('pribadi')" id="tab_pribadi" style="flex: 1; padding: 12px; background: transparent; color: #64748b; border: none; border-radius: 12px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: all 0.2s;">
            Berkas Pribadi
        </button>
    </div>

    <?php if(!empty($message)): ?>
        <div style="background: <?= $status=='success'?'#ecfdf5':($status=='warning'?'#fffbeb':'#fee2e2') ?>; border-left: 4px solid <?= $status=='success'?'#10b981':($status=='warning'?'#f59e0b':'#ef4444') ?>; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;">
            <i data-lucide="<?= $status=='success'?'check-circle':($status=='warning'?'alert-triangle':'alert-circle') ?>" style="color: <?= $status=='success'?'#10b981':($status=='warning'?'#f59e0b':'#ef4444') ?>; width: 20px;"></i>
            <p style="margin:0; color: <?= $status=='success'?'#065f46':($status=='warning'?'#b45309':'#b91c1c') ?>; font-size: 0.85rem; font-weight: 600; line-height: 1.5;"><?= $message ?></p>
        </div>
    <?php endif; ?>

    <!-- ================= VIEW PERANGKAT ================= -->
    <div id="view_perangkat">
        <!-- Header & Tombol Tambah -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-left: 5px;">
            <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0;">Riwayat Perangkat Anda</h3>
            <button onclick="toggleUploadForm()" style="display: none; background: linear-gradient(135deg, #0ea5e9, #2563eb); color: #fff; border: none; padding: 8px 14px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 4px 10px rgba(37,99,235,0.3);">
                <i data-lucide="plus" style="width: 14px;"></i> Upload Baru
            </button>
        </div>

        <!-- Form Upload Berkas (Modal Overlay) -->
        <div id="formUploadWrapper" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
            <div style="background: #ffffff; border-radius: 20px; padding: 25px; width: 90%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto; animation: slideUp 0.3s ease-out;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <div style="background: #e0f2fe; color: #0ea5e9; width: 36px; height: 36px; border-radius: 10px; display: flex; justify-content: center; align-items: center;">
                            <i data-lucide="upload-cloud" style="width: 20px;"></i>
                        </div>
                        Form Unggah
                    </h3>
                    <button type="button" onclick="toggleUploadForm()" style="background: #f1f5f9; border: none; color: #64748b; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; display: flex; justify-content: center; align-items: center;">
                        <i data-lucide="x" style="width: 16px;"></i>
                    </button>
                </div>
                
                <form action="<?= \App\Core\Helper::url('/apk/berkas') ?>" method="POST" enctype="multipart/form-data" id="formUpload" onsubmit="return submitForm('btnUpload')">
                    <input type="hidden" name="tab_type" value="perangkat">
                    
                    <div style="margin-bottom: 12px;">
                        <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #475569; font-size: 0.8rem;">Jenis Berkas <span style="color:red">*</span></label>
                        <select id="upload_jenis_berkas" name="jenis_berkas" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.9rem; background: #fff; color: #1e293b; outline: none;">
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
                    
                    <input type="hidden" id="upload_kelas_id" name="kelas_id" required>
                    <input type="hidden" id="upload_mapel_id" name="mapel_id" required>
                    
                    <div style="margin-bottom: 12px;">
                        <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #475569; font-size: 0.8rem;">Judul / Nama Spesifik <span style="color:red">*</span></label>
                        <input type="text" name="judul_berkas" required placeholder="Contoh: Modul Ajar MTK Kelas 7 Sem 1" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.9rem; background: #fff; color: #1e293b; outline: none;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #475569; font-size: 0.8rem;">File Dokumen (Wajib berformat PDF) <span style="color:red">*</span></label>
                        <input type="file" name="file_berkas" accept=".pdf" required style="width: 100%; padding: 10px; border: 2px dashed #cbd5e1; border-radius: 12px; font-size: 0.85rem; background: #f8fafc; color: #1e293b; outline: none; cursor: pointer;">
                        <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 5px;">Maksimal ukuran file: 5MB</div>
                    </div>

                    <button type="submit" id="btnUpload" style="width: 100%; background: linear-gradient(135deg, #0ea5e9, #2563eb); color: #fff; border: none; padding: 14px 20px; border-radius: 14px; font-size: 0.95rem; font-weight: 800; display: flex; justify-content: center; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 15px rgba(37,99,235,0.3);">
                        <i data-lucide="send" style="width: 18px;"></i> Simpan & Ajukan Validasi
                    </button>
                </form>
            </div>
        </div>
        
        <?php if(empty($kombinasi_mengajar)): ?>
            <div style="text-align: center; padding: 30px 20px; background: #fff; border: 1px dashed #cbd5e1; border-radius: 16px;">
                <div style="background: #f1f5f9; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="folder-search" style="width: 30px; height: 30px; color: #94a3b8;"></i>
                </div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Tidak Ada Jadwal Mengajar</h4>
                <p style="color: #94a3b8; font-size: 0.8rem; margin: 0;">Anda belum memiliki jadwal mengajar di tahun ajaran ini.</p>
            </div>
        <?php else: ?>
            <!-- Filter View Kelas & Mapel -->
            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <select id="filter_kelas" onchange="filterKombinasi()" style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.85rem; background: #fff; color: #1e293b; outline: none; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        <option value="" selected disabled>-- Filter Kelas --</option>
                        <option value="all">Tampilkan Semua Kelas</option>
                        <?php 
                        $unique_kelas = [];
                        foreach($kombinasi_mengajar as $k) { $unique_kelas[$k['kelas_id']] = $k['nama_kelas']; }
                        foreach($unique_kelas as $k_id => $k_nama): ?>
                            <option value="<?= $k_id ?>"><?= htmlspecialchars($k_nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <select id="filter_mapel" onchange="filterKombinasi()" style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 0.85rem; background: #fff; color: #1e293b; outline: none; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        <option value="" selected disabled>-- Filter Mapel --</option>
                        <option value="all">Tampilkan Semua Mapel</option>
                        <?php 
                        $unique_mapel = [];
                        foreach($kombinasi_mengajar as $k) { $unique_mapel[$k['mapel_id']] = $k['nama_mapel']; }
                        foreach($unique_mapel as $m_id => $m_nama): ?>
                            <option value="<?= $m_id ?>"><?= htmlspecialchars($m_nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="empty-filter-state" style="text-align: center; padding: 40px 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 16px; margin-top: 15px;">
                <div style="background: #e0f2fe; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="filter" style="width: 30px; height: 30px; color: #0ea5e9;"></i>
                </div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Pilih Kelas Dahulu</h4>
                <p style="color: #94a3b8; font-size: 0.8rem; margin: 0;">Silakan pilih kelas melalui filter di atas untuk melihat detail dokumen perangkat mengajar.</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach($kombinasi_mengajar as $kombinasi): ?>
                    <div class="kombinasi-card" data-kelas="<?= $kombinasi['kelas_id'] ?>" data-mapel="<?= $kombinasi['mapel_id'] ?>" style="display: none; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="background: #e0f2fe; color: #0284c7; padding: 8px; border-radius: 10px;">
                                    <i data-lucide="book-open" style="width: 20px; height: 20px;"></i>
                                </div>
                                <div>
                                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0;"><?= htmlspecialchars($kombinasi['nama_kelas']) ?></h4>
                                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;"><?= htmlspecialchars($kombinasi['nama_mapel']) ?></div>
                                </div>
                            </div>
                            <form method="POST" action="<?= \App\Core\Helper::url('/apk/berkas/salin-paralel') ?>" style="margin: 0;" id="form-copy-<?= $kombinasi['kelas_id'] ?>-<?= $kombinasi['mapel_id'] ?>">
                                <input type="hidden" name="kelas_id" value="<?= $kombinasi['kelas_id'] ?>">
                                <input type="hidden" name="mapel_id" value="<?= $kombinasi['mapel_id'] ?>">
                                <button type="button" onclick="Swal.fire({title:'Salin Berkas?',text:'Semua dokumen akan disalin ke kelas paralel.',icon:'question',showCancelButton:true,confirmButtonColor:'#3b82f6',cancelButtonColor:'#94a3b8',confirmButtonText:'Ya, Salin',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed) document.getElementById('form-copy-<?= $kombinasi['kelas_id'] ?>-<?= $kombinasi['mapel_id'] ?>').submit();})" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 6px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                    <i data-lucide="copy" style="width: 14px; height: 14px;"></i> Salin
                                </button>
                            </form>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <?php foreach($dokumen_wajib as $dok_wajib): 
                                $matched_berkas = [];
                                foreach($berkasList as $berkas) {
                                    if ($berkas['kelas_id'] == $kombinasi['kelas_id'] && $berkas['mapel_id'] == $kombinasi['mapel_id'] && $berkas['jenis_berkas'] == $dok_wajib) {
                                        $matched_berkas[] = $berkas;
                                    }
                                }
                            ?>
                                <?php if(!empty($matched_berkas)): ?>
                                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                        <div style="background: #f8fafc; padding: 10px 12px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                            <div style="font-size: 0.85rem; font-weight: 800; color: #334155;"><?= htmlspecialchars($dok_wajib) ?></div>
                                            <button onclick="openUploadModal('<?= $dok_wajib ?>', '<?= $kombinasi['kelas_id'] ?>', '<?= $kombinasi['mapel_id'] ?>')" style="background: #e0f2fe; color: #0284c7; border: none; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                                <i data-lucide="plus" style="width: 12px; height: 12px;"></i> Tambah
                                            </button>
                                        </div>
                                        <div style="padding: 10px 12px; display: flex; flex-direction: column; gap: 8px;">
                                            <?php foreach($matched_berkas as $berkasFound): 
                                                $statusColor = '#f59e0b'; $statusBg = '#fffbeb';
                                                if ($berkasFound['status_validasi'] == 'Disetujui') { $statusColor = '#10b981'; $statusBg = '#ecfdf5'; }
                                                elseif ($berkasFound['status_validasi'] == 'Ditolak') { $statusColor = '#ef4444'; $statusBg = '#fee2e2'; }
                                            ?>
                                                <div style="background: #f8fafc; padding: 8px 10px; border-radius: 8px; border-left: 3px solid <?= $statusColor ?>; display: flex; justify-content: space-between; align-items: center;">
                                                    <div style="flex: 1; min-width: 0;">
                                                        <div style="font-size: 0.8rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($berkasFound['judul_berkas']) ?></div>
                                                        <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 2px;"><?= date('d/m/Y', strtotime($berkasFound['tanggal_upload'])) ?> · <span style="color: <?= $statusColor ?>; font-weight: 800;"><?= $berkasFound['status_validasi'] ?></span></div>
                                                    </div>
                                                    <div style="display: flex; gap: 6px; margin-left: 8px; flex-shrink: 0;">
                                                        <a href="<?= \App\Core\Helper::url('/apk/viewer?type=perangkat&id=' . $berkasFound['id']) ?>" style="background: #e0f2fe; color: #0284c7; text-decoration: none; padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; display: flex; align-items: center; gap: 4px;"><i data-lucide="eye" style="width: 14px; height: 14px;"></i> Lihat</a>
                                                        <a href="javascript:void(0)" onclick="Swal.fire({title:'Hapus berkas ini?',text:'File akan dihapus permanen.',icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonText:'Batal',confirmButtonText:'Hapus'}).then(r=>{if(r.isConfirmed) location.href='<?= \App\Core\Helper::url('/apk/berkas/hapus/' . $berkasFound['id']) ?>';})" style="background: #fee2e2; color: #dc2626; text-decoration: none; padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; display: flex; align-items: center; gap: 4px;"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus</a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div style="background: #ffffff; border: 1px solid #fecdd3; border-radius: 12px; margin-bottom: 12px; overflow: hidden; box-shadow: 0 2px 5px rgba(244,63,94,0.05);">
                                        <div style="padding: 12px; display: flex; justify-content: space-between; align-items: center;">
                                            <div style="display: flex; gap: 10px; align-items: center;">
                                                <div style="background: #fff1f2; color: #e11d48; width: 36px; height: 36px; border-radius: 8px; display: flex; justify-content: center; align-items: center;">
                                                    <i data-lucide="file-x" style="width: 18px; height: 18px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-size: 0.85rem; font-weight: 800; color: #4c1d95;"><?= htmlspecialchars($dok_wajib) ?></div>
                                                    <div style="font-size: 0.7rem; color: #e11d48; font-weight: 600;">Belum Upload</div>
                                                </div>
                                            </div>
                                            <button onclick="openUploadModal('<?= $dok_wajib ?>', '<?= $kombinasi['kelas_id'] ?>', '<?= $kombinasi['mapel_id'] ?>')" style="background: linear-gradient(135deg, #f43f5e, #e11d48); color: white; border: none; padding: 8px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 3px 6px rgba(225,29,72,0.2);">
                                                <i data-lucide="upload-cloud" style="width: 14px; height: 14px;"></i> Upload
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <!-- Dokumen "Lainnya" untuk kelas/mapel ini -->
                            <?php 
                                $adaLainnya = false;
                                foreach($berkasList as $berkas) {
                                    if ($berkas['kelas_id'] == $kombinasi['kelas_id'] && $berkas['mapel_id'] == $kombinasi['mapel_id'] && !in_array($berkas['jenis_berkas'], $dokumen_wajib)) {
                                        if(!$adaLainnya) {
                                            echo '<div style="margin-top: 10px; font-size: 0.8rem; font-weight: 800; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px;">Dokumen Lainnya:</div>';
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
                                        <div style="background: #f1f5f9; border-radius: 8px; padding: 10px 12px;">
                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px;">
                                                <div>
                                                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 2px;"><?= htmlspecialchars($berkas['jenis_berkas']) ?></div>
                                                    <div style="font-size: 0.85rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($berkas['judul_berkas']) ?></div>
                                                </div>
                                                <div style="background: <?= $statusBg ?>; color: <?= $statusColor ?>; padding: 3px 8px; border-radius: 15px; font-size: 0.65rem; font-weight: 800;">
                                                    <?= htmlspecialchars($berkas['status_validasi']) ?>
                                                </div>
                                            </div>
                                            <a href="<?= \App\Core\Helper::url('/apk/viewer?type=perangkat&id=' . $berkas['id']) ?>" style="font-size: 0.75rem; font-weight: 700; color: #0ea5e9; text-decoration: none;"><i data-lucide="eye" style="width: 12px; height: 12px; display:inline-block; vertical-align:middle;"></i> Lihat File</a>
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
        <!-- Header + Tombol Upload -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-left: 5px;">
            <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0;">Arsip Dokumen Pribadi</h3>
            <button onclick="togglePribadiForm()" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff; border: none; padding: 8px 14px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 4px 10px rgba(16,185,129,0.3);">
                <i data-lucide="plus" style="width: 14px;"></i> Upload Baru
            </button>
        </div>

        <!-- Form Upload Berkas Pribadi -->
        <div id="formUploadPribadiWrapper" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; padding: 20px; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="upload-cloud" style="color: #10b981; width: 20px;"></i> Upload Berkas Pribadi
                </h3>
                <button onclick="togglePribadiForm()" style="background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 4px;">
                    <i data-lucide="x" style="width: 18px;"></i>
                </button>
            </div>
            
            <form action="<?= \App\Core\Helper::url('/apk/berkas') ?>" method="POST" enctype="multipart/form-data" id="formUploadPribadi" onsubmit="return submitForm('btnUploadPribadi')">
                <input type="hidden" name="tab_type" value="pribadi">
                <div style="margin-bottom: 12px;">
                    <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #475569; font-size: 0.8rem;">Nama / Jenis Berkas <span style="color:red">*</span></label>
                    <input type="text" name="jenis_berkas" required placeholder="Contoh: KTP, Ijazah S1, Sertifikat Pelatihan, dll" style="width: 100%; padding: 12px 15px; border: 1px solid #bbf7d0; border-radius: 12px; font-size: 0.9rem; background: #fff; color: #1e293b; outline: none;">
                </div>
                
                <div style="margin-bottom: 12px;">
                    <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #475569; font-size: 0.8rem;">Keterangan <span style="color:red">*</span></label>
                    <input type="text" name="judul_berkas" required placeholder="Contoh: KTP Atas Nama Ahmad" style="width: 100%; padding: 12px 15px; border: 1px solid #bbf7d0; border-radius: 12px; font-size: 0.9rem; background: #fff; color: #1e293b; outline: none;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #475569; font-size: 0.8rem;">File Dokumen (PDF / Gambar) <span style="color:red">*</span></label>
                    <input type="file" name="file_berkas" accept=".pdf,.jpg,.jpeg,.png" required style="width: 100%; padding: 10px; border: 1px dashed #bbf7d0; border-radius: 12px; font-size: 0.85rem; background: #fff; color: #1e293b; outline: none;">
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 5px;">Format: PDF, JPG, JPEG, PNG — Maks. 5MB</div>
                </div>

                <button type="submit" id="btnUploadPribadi" style="width: 100%; background: linear-gradient(135deg, #10b981, #059669); color: #fff; border: none; padding: 14px 20px; border-radius: 14px; font-size: 0.95rem; font-weight: 800; display: flex; justify-content: center; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 15px rgba(16,185,129,0.3);">
                    <i data-lucide="send" style="width: 18px;"></i> Simpan Berkas Pribadi
                </button>
            </form>
        </div>

        <!-- Daftar Berkas Pribadi -->
        <?php if(empty($berkasPribadiList)): ?>
            <div style="text-align: center; padding: 40px 20px; background: #fff; border: 1px dashed #cbd5e1; border-radius: 16px;">
                <div style="background: #f0fdf4; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="folder-open" style="width: 30px; height: 30px; color: #10b981;"></i>
                </div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: #475569; margin-bottom: 5px;">Belum Ada Berkas Pribadi</h4>
                <p style="color: #94a3b8; font-size: 0.8rem; margin: 0;">Klik tombol <b>+ Upload Baru</b> untuk mulai mengunggah dokumen pribadi Anda.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <?php foreach($berkasPribadiList as $bp): 
                    $ext = strtolower(pathinfo($bp['file_nama'], PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg','jpeg','png']);
                ?>
                    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($bp['jenis_berkas']) ?></div>
                            <div style="font-size: 0.65rem; color: #94a3b8;"><?= htmlspecialchars($bp['judul_berkas']) ?> · <?= date('d/m/Y', strtotime($bp['tanggal_upload'])) ?> · <span style="background: #f1f5f9; padding: 1px 4px; border-radius: 3px; font-weight: 700; text-transform: uppercase;"><?= $ext ?></span></div>
                        </div>
                        <div style="display: flex; gap: 8px; margin-left: 8px; flex-shrink: 0;">
                            <a href="<?= \App\Core\Helper::url('/apk/viewer?type=pribadi&id=' . $bp['id']) ?>" style="background: #e0f2fe; color: #0284c7; text-decoration: none; padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; gap: 4px;"><i data-lucide="eye" style="width: 14px; height: 14px;"></i> Lihat</a>
                            <a href="javascript:void(0)" onclick="Swal.fire({title:'Hapus berkas ini?',text:'File akan dihapus permanen.',icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonText:'Batal',confirmButtonText:'Hapus'}).then(r=>{if(r.isConfirmed) location.href='<?= \App\Core\Helper::url('/apk/berkas-pribadi/hapus/' . $bp['id']) ?>';})" style="background: #fee2e2; color: #dc2626; text-decoration: none; padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; gap: 4px;"><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus</a>
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
    const selectedMapel = document.getElementById('filter_mapel').value;
    const cards = document.querySelectorAll('.kombinasi-card');
    const emptyState = document.getElementById('empty-filter-state');
    
    // Harus pilih setidaknya satu filter (Kelas ATAU Mapel) untuk nampilin
    if ((!selectedKelas || selectedKelas === '') && (!selectedMapel || selectedMapel === '')) {
        if (emptyState) emptyState.style.display = 'block';
        cards.forEach(card => card.style.display = 'none');
        return;
    }
    
    if (emptyState) emptyState.style.display = 'none';

    cards.forEach(card => {
        const matchKelas = (selectedKelas === 'all' || selectedKelas === '' || card.getAttribute('data-kelas') === selectedKelas);
        const matchMapel = (selectedMapel === 'all' || selectedMapel === '' || card.getAttribute('data-mapel') === selectedMapel);
        
        if (matchKelas && matchMapel) {
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
        btnPerangkat.style.color = '#0ea5e9';
        btnPerangkat.style.boxShadow = '0 4px 10px rgba(0,0,0,0.05)';
        
        btnPribadi.style.background = 'transparent';
        btnPribadi.style.color = '#64748b';
        btnPribadi.style.boxShadow = 'none';

        viewPerangkat.style.display = 'block';
        viewPribadi.style.display = 'none';
    } else {
        btnPribadi.style.background = '#fff';
        btnPribadi.style.color = '#0ea5e9';
        btnPribadi.style.boxShadow = '0 4px 10px rgba(0,0,0,0.05)';
        
        btnPerangkat.style.background = 'transparent';
        btnPerangkat.style.color = '#64748b';
        btnPerangkat.style.boxShadow = 'none';

        viewPerangkat.style.display = 'none';
        viewPribadi.style.display = 'block';
    }
}

function openUploadModal(jenis, kelas, mapel) {
    document.getElementById('upload_jenis_berkas').value = jenis;
    document.getElementById('upload_kelas_id').value = kelas;
    document.getElementById('upload_mapel_id').value = mapel;
    toggleUploadForm();
}

function toggleUploadForm() {
    const formWrapper = document.getElementById('formUploadWrapper');
    if (formWrapper.style.display === 'none' || formWrapper.style.display === '') {
        formWrapper.style.display = 'flex';
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

// Auto-switch tab if needed (after pribadi upload redirect)
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($active_tab) && $active_tab === 'pribadi'): ?>
        switchTab('pribadi');
    <?php endif; ?>

    <?php if(isset($_SESSION['swal_success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= $_SESSION['swal_success'] ?>',
            confirmButtonColor: '#0ea5e9',
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['swal_success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['swal_error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= $_SESSION['swal_error'] ?>',
            confirmButtonColor: '#ef4444'
        });
        <?php unset($_SESSION['swal_error']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['swal_warning'])): ?>
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: '<?= $_SESSION['swal_warning'] ?>',
            confirmButtonColor: '#f59e0b'
        });
        <?php unset($_SESSION['swal_warning']); ?>
    <?php endif; ?>
});
</script>
