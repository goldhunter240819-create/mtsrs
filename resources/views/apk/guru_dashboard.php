<?php
use App\Core\Helper;
?>
<div class="apk-vector-header" style="background-color: #2563eb; background-image: 
    linear-gradient(30deg, rgba(255,255,255,0.1) 12%, transparent 12.5%, transparent 87%, rgba(255,255,255,0.1) 87.5%, rgba(255,255,255,0.1)),
    linear-gradient(150deg, rgba(255,255,255,0.1) 12%, transparent 12.5%, transparent 87%, rgba(255,255,255,0.1) 87.5%, rgba(255,255,255,0.1)),
    linear-gradient(30deg, rgba(255,255,255,0.1) 12%, transparent 12.5%, transparent 87%, rgba(255,255,255,0.1) 87.5%, rgba(255,255,255,0.1)),
    linear-gradient(150deg, rgba(255,255,255,0.1) 12%, transparent 12.5%, transparent 87%, rgba(255,255,255,0.1) 87.5%, rgba(255,255,255,0.1)),
    linear-gradient(60deg, rgba(255,255,255,0.1) 25%, transparent 25.5%, transparent 75%, rgba(255,255,255,0.1) 75%, rgba(255,255,255,0.1)), 
    linear-gradient(60deg, rgba(255,255,255,0.1) 25%, transparent 25.5%, transparent 75%, rgba(255,255,255,0.1) 75%, rgba(255,255,255,0.1));">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <?php $sapaan = (isset($guru['jenis_kelamin']) && strtoupper(substr($guru['jenis_kelamin'], 0, 1)) === 'P') ? 'Ibu' : 'Bapak'; ?>
            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.8; letter-spacing: 0.5px;">Selamat Datang <?= $sapaan ?>,</div>
            <div style="font-size: 1.2rem; font-weight: 800; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($guru['nama']) ?></div>
        </div>
    </div>
    
    <div style="display: flex; align-items: center; gap: 10px;">
        <button onclick="showMyQRCode()" style="background: rgba(255,255,255,0.2); border: none; width: 36px; height: 36px; border-radius: 10px; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s;">
            <i data-lucide="qr-code" style="width: 20px;"></i>
        </button>
        <div style="position: relative;">
            <?php if(!empty($guru['foto'])): ?>
                <img src="<?= Helper::url('/public/uploads/guru/' . rawurlencode($guru['foto'])) ?>" alt="Foto" class="apk-avatar-top">
            <?php else: ?>
                <div class="apk-avatar-top" style="background: rgba(255,255,255,0.2); display:flex; justify-content:center; align-items:center; color: #fff;">
                    <i data-lucide="user"></i>
                </div>
            <?php endif; ?>
            <div style="position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; background: #3b82f6; border-radius: 50%; border: 3px solid #1d4ed8;"></div>
        </div>
    </div>
</div>

<script>
function showMyQRCode() {
    <?php 
        $qrData = $guru['qr_token'] ?? ('G-'.$guru['id']);
    ?>
    const qrData = '<?= htmlspecialchars($qrData, ENT_QUOTES) ?>';
    const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=0&data=' + encodeURIComponent(qrData);

    Swal.fire({
        title: '<span style="font-size: 1.1rem; font-weight: 800; color: #1e293b;">Kartu QR Saya</span>',
        html: `
            <div style="display: flex; justify-content: center; margin-top: 15px; margin-bottom: 20px;">
                <div style="padding: 20px; background: #fff; border-radius: 20px; border: 2px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: inline-flex; align-items: center; justify-content: center;">
                    <img src="${qrUrl}" alt="QR Code" style="width: 220px; height: 220px; display: block; border-radius: 8px;">
                </div>
            </div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
                <?= htmlspecialchars($guru['nama']) ?>
            </div>
            <div style="font-size: 0.85rem; font-weight: 600; color: #64748b;">
                Tap kartu ini pada mesin Scanner Absensi
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#3b82f6',
        width: '350px',
        padding: '25px',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl px-6 py-2'
        }
    });
}
</script>

<div class="apk-main-board">
    <!-- Info Banner -->
    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; padding: 10px 15px; color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; opacity: 0.9;">Tahun Ajaran Aktif</div>
            <div style="font-size: 0.95rem; font-weight: 800;"><?= htmlspecialchars($tahun_ajaran_aktif ?? 'Semester Genap') ?></div>
        </div>
        <i data-lucide="book-open-check" style="width: 24px; opacity: 0.8;"></i>
    </div>

    <?php if (!empty($is_libur) || !empty($is_kegiatan)): ?>
        <div style="background: <?php echo $is_libur ? '#fee2e2' : '#e0e7ff'; ?>; color: <?php echo $is_libur ? '#991b1b' : '#3730a3'; ?>; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; font-weight: 600; border: 1px solid <?php echo $is_libur ? '#fecaca' : '#c7d2fe'; ?>;">
            <i data-lucide="<?php echo $is_libur ? 'calendar-off' : 'calendar-heart'; ?>" style="width: 30px; height: 30px; opacity: 0.8;"></i>
            <div>
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; margin-bottom: 2px;">Status Hari Ini</div>
                <div style="font-size: 1.05rem; font-weight: 800;"><?php echo htmlspecialchars($nama_kegiatan ?: ($is_libur ? 'Hari Libur' : 'Kegiatan Khusus')); ?></div>
                <div style="font-size: 0.85rem; font-weight: 500; margin-top: 4px; opacity: 0.9;">
                    <?php if ($is_libur): ?>
                        Anda tidak diwajibkan Absen Harian maupun Jurnal Mengajar. Selamat beristirahat!
                    <?php else: ?>
                        Jadwal mengajar dan absen kelas (Jurnal) dibebaskan hari ini.
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>



    <!-- Mading Pengumuman -->
    <?php if (count($pengumumans) > 0): ?>
    <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="megaphone" style="color: #f59e0b; width: 20px;"></i> Mading Pengumuman
    </h3>
    <div style="margin-bottom: 25px;">
        <?php foreach ($pengumumans as $p): ?>
        <div style="background: #ffffff; border-radius: 16px; padding: 15px; margin-bottom: 10px; border-left: 4px solid #3b82f6; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-top: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($p['judul']) ?></h4>
                <div style="font-size: 0.7rem; color: #94a3b8; white-space: nowrap; margin-left: 10px;"><i data-lucide="clock" style="width: 12px; height: 12px; display: inline; vertical-align: middle;"></i> <?= date('d M', strtotime($p['tanggal_dibuat'])) ?></div>
            </div>
            <p style="margin: 0; font-size: 0.85rem; color: #64748b; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($p['pesan']) ?></p>
            <?php if (!empty($p['url'])): ?>
            <div style="margin-top: 10px;">
                <a href="<?= htmlspecialchars($p['url']) ?>" style="display: inline-flex; align-items: center; gap: 4px; color: #3b82f6; font-size: 0.8rem; text-decoration: none; font-weight: 700; background: #eff6ff; padding: 4px 10px; border-radius: 8px;">Lihat Detail <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i></a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Absensi & Aktivitas Bulanan -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 25px;">
        <!-- Status Absen -->
        <?php if ($absensi_hari_ini): ?>
        <div style="background: #ffffff; border: 1px solid #a7f3d0; border-radius: 16px; padding: 15px; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 4px 15px rgba(16,185,129,0.05);">
            <div style="font-size: 0.75rem; color: #10b981; font-weight: 800; text-transform: uppercase; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;"><i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Absen Masuk</div>
            <div style="font-size: 1.4rem; font-weight: 800; color: #065f46;"><?= $absensi_hari_ini['jam_masuk'] ?: '-' ?></div>
            <div style="font-size: 0.8rem; color: #64748b; margin-top: 5px; font-weight: 600;">
                Selamat Bekerja!
            </div>
        </div>
        <?php elseif (!empty($is_libur)): ?>
        <div style="background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 800; text-transform: uppercase; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;"><i data-lucide="calendar-off" style="width: 14px; height: 14px;"></i> Status Absen</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #475569;">Hari Libur</div>
            <div style="font-size: 0.8rem; color: #64748b; margin-top: 5px; font-weight: 600;">Tidak ada jadwal hadir</div>
        </div>
        <?php else: ?>
        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 16px; padding: 15px; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 4px 15px rgba(245,158,11,0.05);">
            <div style="font-size: 0.75rem; color: #d97706; font-weight: 800; text-transform: uppercase; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;"><i data-lucide="alert-triangle" style="width: 14px; height: 14px;"></i> Status Absen</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #b45309;">Belum Absen</div>
            <div style="font-size: 0.8rem; color: #92400e; margin-top: 5px; font-weight: 600;">Silakan scan QR Anda</div>
        </div>
        <?php endif; ?>

        <!-- Aktivitas Singkat -->
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; flex: 1; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; display: flex; align-items: center; gap: 6px;"><i data-lucide="user-check" style="color:#10b981; width: 16px;"></i> Hadir (Bln)</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b;"><?= $jml_hadir ?></div>
            </div>
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; flex: 1; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; display: flex; align-items: center; gap: 6px;"><i data-lucide="book-open" style="color:#3b82f6; width: 16px;"></i> Jurnal (Bln)</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b;"><?= $jml_jurnal ?></div>
            </div>
        </div>
    </div>

    <!-- WALI KELAS MONITORING -->
    <?php if ($is_wali_kelas): ?>
    <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="users" style="color: #10b981; width: 20px;"></i> Wali Kelas <?= htmlspecialchars($nama_kelas_wali) ?>
    </h3>
    <div style="background: #ffffff; border-radius: 16px; padding: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 25px; border: 1px solid #f1f5f9; border-left: 4px solid #10b981;">
        <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 12px; font-weight: 700;">Monitoring Absensi Siswa Hari Ini</div>
        <div style="display: flex; gap: 4px; padding-bottom: 5px;">
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 6px 2px; text-align: center; cursor: pointer; flex: 1; min-width: 0;" onclick="showNames('Hadir', '<?= addslashes(implode(', ', $wali_names['Hadir'])) ?>')">
                <div style="font-size: 0.9rem; font-weight: 800; color: #059669; line-height: 1;"><?= $wali_stats['Hadir'] ?></div>
                <div style="font-size: 0.6rem; color: #047857; font-weight: 700; margin-top: 4px; letter-spacing: -0.5px;">Hadir</div>
            </div>
            <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 6px 2px; text-align: center; cursor: pointer; flex: 1; min-width: 0;" onclick="showNames('Sakit', '<?= addslashes(implode(', ', $wali_names['Sakit'])) ?>')">
                <div style="font-size: 0.9rem; font-weight: 800; color: #d97706; line-height: 1;"><?= $wali_stats['Sakit'] ?></div>
                <div style="font-size: 0.6rem; color: #b45309; font-weight: 700; margin-top: 4px; letter-spacing: -0.5px;">Sakit</div>
            </div>
            <div style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 8px; padding: 6px 2px; text-align: center; cursor: pointer; flex: 1; min-width: 0;" onclick="showNames('Izin', '<?= addslashes(implode(', ', $wali_names['Izin'])) ?>')">
                <div style="font-size: 0.9rem; font-weight: 800; color: #0284c7; line-height: 1;"><?= $wali_stats['Izin'] ?></div>
                <div style="font-size: 0.6rem; color: #0369a1; font-weight: 700; margin-top: 4px; letter-spacing: -0.5px;">Izin</div>
            </div>
            <div style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 6px 2px; text-align: center; cursor: pointer; flex: 1; min-width: 0;" onclick="showNames('Alpa', '<?= addslashes(implode(', ', $wali_names['Alpa'])) ?>')">
                <div style="font-size: 0.9rem; font-weight: 800; color: #dc2626; line-height: 1;"><?= $wali_stats['Alpa'] ?></div>
                <div style="font-size: 0.6rem; color: #b91c1c; font-weight: 700; margin-top: 4px; letter-spacing: -0.5px;">Alpa</div>
            </div>
            <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 6px 2px; text-align: center; cursor: pointer; flex: 1; min-width: 0;" onclick="showNames('Bolos', '<?= addslashes(implode(', ', $wali_names['Bolos'])) ?>')">
                <div style="font-size: 0.9rem; font-weight: 800; color: #e11d48; line-height: 1;"><?= $wali_stats['Bolos'] ?></div>
                <div style="font-size: 0.6rem; color: #be123c; font-weight: 700; margin-top: 4px; letter-spacing: -0.5px;">Bolos</div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function showNames(status, names) {
        if (!names || names.trim() === '') {
            Swal.fire({
                title: `Tidak ada siswa`,
                text: `Belum ada siswa dengan status ${status}.`,
                icon: 'info',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Tutup'
            });
            return;
        }
        Swal.fire({
            title: `Siswa ${status}`,
            html: `<div style="font-size:0.9rem; text-align:left; color:#334155; max-height:200px; overflow-y:auto; line-height:1.6;">${names.split(', ').join('<br>• ')}</div>`,
            icon: 'info',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'Tutup'
        });
    }
    </script>
    <?php endif; ?>



    <!-- Jadwal Mengajar Minimalis -->
    <div style="margin-bottom: 25px;">
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="calendar-clock" style="color: #3b82f6; width: 20px;"></i> Jadwal Mengajar
        </h3>
        <div style="display: flex; gap: 10px;">
            <button onclick="showJadwal('hari_ini', 'Hari Ini (<?= $hari_ini ?>)')" style="flex: 1; background: #fff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 12px; color: #2563eb; font-size: 0.9rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.05); transition: all 0.2s;">
                <i data-lucide="clock" style="width: 18px;"></i> Hari Ini
            </button>
            <button onclick="showJadwal('besok', 'Besok (<?= $besok ?>)')" style="flex: 1; background: #fff; border: 1px solid #ddd6fe; border-radius: 12px; padding: 12px; color: #7c3aed; font-size: 0.9rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.05); transition: all 0.2s;">
                <i data-lucide="calendar-plus" style="width: 18px;"></i> Besok
            </button>
        </div>
    </div>
    
    <script>
        const jadwalHariIni = <?= json_encode($jadwal_hari_ini ?: []) ?>;
        const jadwalBesok = <?= json_encode($jadwal_besok ?: []) ?>;

        function showJadwal(tipe, hari) {
            const data = tipe === 'hari_ini' ? jadwalHariIni : jadwalBesok;
            
            if (data.length === 0) {
                Swal.fire({
                    title: '<span style="font-size:1.1rem;font-weight:800;">Tidak ada jadwal</span>',
                    text: 'Bisa bersantai sejenak ya! 😊',
                    icon: 'info',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6 py-2'
                    }
                });
                return;
            }

            let html = '<div style="display: flex; flex-direction: column; gap: 12px; text-align: left; max-height: 350px; overflow-y: auto; padding-right: 5px;">';
            data.forEach(j => {
                html += `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px dashed #e2e8f0;">
                        <div>
                            <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem; margin-bottom: 4px;">${j.nama_mapel}</div>
                            <div style="font-size: 0.8rem; color: #64748b; display: flex; align-items: center; gap: 4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #64748b;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> 
                                ${j.jam_mulai.substring(0,5)} - ${j.jam_selesai.substring(0,5)}
                            </div>
                        </div>
                        <div style="background: #eff6ff; color: #2563eb; font-size: 0.75rem; font-weight: 800; padding: 6px 12px; border-radius: 12px; border: 1px solid #bfdbfe;">
                            Kelas ${j.nama_kelas}
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            Swal.fire({
                title: `<span style="font-size:1.1rem;font-weight:800;">Jadwal ${hari}</span>`,
                html: html,
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6 py-2'
                }
            });
        }
    </script>
</div>

<script>
    if (typeof Android !== "undefined" && typeof Android.setOneSignalUser === "function") {
        Android.setOneSignalUser("guru_<?= addslashes($guru_id) ?>");
    }
</script>
