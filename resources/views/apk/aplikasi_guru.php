<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Admin Delegasi</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;">Aplikasi Saya</div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; padding-bottom: 30px;">
    
    <style>
        .app-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 25px; }
        .app-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px 5px; text-align: center; text-decoration: none; color: inherit; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; align-items: center; justify-content: center; transition: all 0.2s; }
        .app-card:active { transform: scale(0.95); }
        .app-icon { width: 45px; height: 45px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; }
        .app-title { font-size: 0.75rem; font-weight: 800; color: #1e293b; line-height: 1.2; text-align: center; }
        .cat-title { font-size: 0.85rem; font-weight: 800; color: #475569; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; padding-left: 5px; }
    </style>

    <!-- Kategori Pribadi (Default Akses Tanpa Delegasi) -->
    <div class="cat-title"><i data-lucide="user" style="width: 16px; color: #94a3b8;"></i> Pribadi (Default)</div>
    <div class="app-grid">
        <a href="/apk/nilai-harian" class="app-card">
            <div class="app-icon" style="background: #e0f2fe; color: #0ea5e9;"><i data-lucide="file-spreadsheet" style="width: 22px;"></i></div>
            <div class="app-title">Penilaian</div>
        </a>
        <a href="/apk/rekap-absensi" class="app-card">
            <div class="app-icon" style="background: #ecfdf5; color: #10b981;"><i data-lucide="clock" style="width: 22px;"></i></div>
            <div class="app-title">Absensi Saya</div>
        </a>
        <a href="/apk/e-rapor" class="app-card">
            <div class="app-icon" style="background: #fdf4ff; color: #d946ef;"><i data-lucide="book-check" style="width: 22px;"></i></div>
            <div class="app-title">E-Rapor</div>
        </a>
    </div>

    <!-- Keuangan & Tabungan -->
    <?php if(in_array('kasir_tabungan', $custom_apk_access ?? []) || in_array('kasir_bendahara', $custom_apk_access ?? []) || in_array('bku_bendahara', $custom_apk_access ?? [])): ?>
    <div class="cat-title"><i data-lucide="wallet" style="width: 16px; color: #94a3b8;"></i> Keuangan & Tabungan</div>
    <div class="app-grid">
        <?php if(in_array('kasir_tabungan', $custom_apk_access ?? [])): ?>
        <a href="#" class="app-card">
            <div class="app-icon" style="background: #fffbeb; color: #f59e0b;"><i data-lucide="wallet" style="width: 22px;"></i></div>
            <div class="app-title">Kasir Tab.</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('kasir_bendahara', $custom_apk_access ?? [])): ?>
        <a href="javascript:void(0)" onclick="openKasirModal()" class="app-card">
            <div class="app-icon" style="background: #ecfdf5; color: #10b981;"><i data-lucide="banknote" style="width: 22px;"></i></div>
            <div class="app-title">Kasir Bend.</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('bku_bendahara', $custom_apk_access ?? [])): ?>
        <a href="/apk/bku" class="app-card">
            <div class="app-icon" style="background: #eff6ff; color: #3b82f6;"><i data-lucide="book-text" style="width: 22px;"></i></div>
            <div class="app-title">BKU Bend.</div>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Absensi & Scanner -->
    <?php if(in_array('scan_siswa', $custom_apk_access ?? []) || in_array('scan_guru', $custom_apk_access ?? []) || in_array('persetujuan_izin', $custom_apk_access ?? [])): ?>
    <div class="cat-title"><i data-lucide="scan-line" style="width: 16px; color: #94a3b8;"></i> Absensi & Perizinan</div>
    <div class="app-grid">
        <?php if(in_array('scan_siswa', $custom_apk_access ?? [])): ?>
        <a href="/apk/absen?mode=siswa" class="app-card">
            <div class="app-icon" style="background: #e0f2fe; color: #0ea5e9;"><i data-lucide="scan" style="width: 22px;"></i></div>
            <div class="app-title">Scan Siswa</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('scan_guru', $custom_apk_access ?? [])): ?>
        <a href="/apk/absen?mode=guru" class="app-card">
            <div class="app-icon" style="background: #eff6ff; color: #3b82f6;"><i data-lucide="scan-face" style="width: 22px;"></i></div>
            <div class="app-title">Scan Guru</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('persetujuan_izin', $custom_apk_access ?? [])): ?>
        <a href="/apk/admin-izin-piket" class="app-card">
            <div class="app-icon" style="background: #fffbeb; color: #f59e0b;"><i data-lucide="clipboard-check" style="width: 22px;"></i></div>
            <div class="app-title">Izin Guru</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('izin_siswa', $custom_apk_access ?? [])): ?>
        <a href="/apk/izin-siswa" class="app-card">
            <div class="app-icon" style="background: #ecfdf5; color: #10b981;"><i data-lucide="user-check" style="width: 22px;"></i></div>
            <div class="app-title">Izin Siswa</div>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>



    <!-- Supervisi & Monitoring: KAMAD -->
    <?php if(in_array('mon_keuangan', $custom_apk_access ?? []) || in_array('qr_absen_guru', $custom_apk_access ?? [])): ?>
    <div class="cat-title"><i data-lucide="activity" style="width: 16px; color: #94a3b8;"></i> Kepala Madrasah</div>
    <div class="app-grid">
        <?php if(in_array('mon_keuangan', $custom_apk_access ?? [])): ?>
        <a href="/apk/monitor-keuangan" class="app-card">
            <div class="app-icon" style="background: #eff6ff; color: #3b82f6;"><i data-lucide="line-chart" style="width: 22px;"></i></div>
            <div class="app-title">Mon. Keuangan</div>
        </a>
        <?php endif; ?>
        <a href="/apk/kamad/realtime-guru" class="app-card">
            <div class="app-icon" style="background: #e0e7ff; color: #4f46e5;"><i data-lucide="scan-line" style="width: 22px;"></i></div>
            <div class="app-title">QR Absen Guru</div>
        </a>
    </div>
    <?php endif; ?>

    <!-- Waka Kurikulum -->
    <?php if(in_array('wakakur_jadwal', $custom_apk_access ?? []) || in_array('mon_jurnal', $custom_apk_access ?? []) || in_array('mon_penilaian', $custom_apk_access ?? []) || in_array('val_ekinerja', $custom_apk_access ?? []) || in_array('supervisi_kelas', $custom_apk_access ?? [])): ?>
    <div class="cat-title"><i data-lucide="book-open-check" style="width: 16px; color: #94a3b8;"></i> Waka Kurikulum</div>
    <div class="app-grid">
        <?php if(in_array('wakakur_jadwal', $custom_apk_access ?? [])): ?>
        <a href="/apk/waka-kurikulum/jadwal-kbm" class="app-card">
            <div class="app-icon" style="background: #fdf4ff; color: #d946ef;"><i data-lucide="calendar-check" style="width: 22px;"></i></div>
            <div class="app-title">Jadwal KBM</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('mon_jurnal', $custom_apk_access ?? [])): ?>
        <a href="/apk/kamad/monitoring-jurnal" class="app-card">
            <div class="app-icon" style="background: #e0f2fe; color: #0ea5e9;"><i data-lucide="book-open" style="width: 22px;"></i></div>
            <div class="app-title">Monitor Jurnal</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('val_ekinerja', $custom_apk_access ?? [])): ?>
        <a href="/apk/supervisi-administrasi" class="app-card">
            <div class="app-icon" style="background: #fdf4ff; color: #d946ef;"><i data-lucide="clipboard-check" style="width: 22px;"></i></div>
            <div class="app-title">Supervisi Adm.</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('supervisi_kelas', $custom_apk_access ?? [])): ?>
        <a href="/apk/supervisi-kelas" class="app-card">
            <div class="app-icon" style="background: #fffbeb; color: #d97706;"><i data-lucide="presentation" style="width: 22px;"></i></div>
            <div class="app-title">Supervisi Kelas</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('mon_penilaian', $custom_apk_access ?? [])): ?>
        <a href="/apk/kamad/monitor-penilaian" class="app-card">
            <div class="app-icon" style="background: #fdf4ff; color: #c026d3;"><i data-lucide="clipboard-list" style="width: 22px;"></i></div>
            <div class="app-title">Supervisi Penilaian</div>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Waka Kesiswaan -->
    <?php if(in_array('wakasis_poin', $custom_apk_access ?? []) || in_array('wakasis_kehadiran', $custom_apk_access ?? []) || in_array('absen_qr', $custom_apk_access ?? []) || in_array('absen_mapel', $custom_apk_access ?? [])): ?>
    <div class="cat-title"><i data-lucide="shield-alert" style="width: 16px; color: #94a3b8;"></i> Waka Kesiswaan</div>
    <div class="app-grid">
        <?php if(in_array('wakasis_poin', $custom_apk_access ?? [])): ?>
        <a href="/apk/waka-kesiswaan/poin" class="app-card">
            <div class="app-icon" style="background: #fef2f2; color: #ef4444;"><i data-lucide="clipboard-list" style="width: 22px;"></i></div>
            <div class="app-title">Poin Log</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('wakasis_kehadiran', $custom_apk_access ?? [])): ?>
        <a href="/apk/waka-kesiswaan/statistik-kehadiran" class="app-card">
            <div class="app-icon" style="background: #e0e7ff; color: #4338ca;"><i data-lucide="pie-chart" style="width: 22px;"></i></div>
            <div class="app-title">Kehadiran</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('absen_qr', $custom_apk_access ?? [])): ?>
        <a href="/apk/monitor-realtime" class="app-card">
            <div class="app-icon" style="background: #f3e8ff; color: #9333ea;"><i data-lucide="scan-line" style="width: 22px;"></i></div>
            <div class="app-title">QR Absen Siswa</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('absen_mapel', $custom_apk_access ?? [])): ?>
        <a href="/apk/monitor-permapel" class="app-card">
            <div class="app-icon" style="background: #ecfdf5; color: #10b981;"><i data-lucide="book-check" style="width: 22px;"></i></div>
            <div class="app-title">Absen Mapel</div>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Bimbingan Konseling -->
    <?php if(in_array('bk_jurnal', $custom_apk_access ?? []) || in_array('bk_poin', $custom_apk_access ?? [])): ?>
    <div class="cat-title"><i data-lucide="heart-handshake" style="width: 16px; color: #94a3b8;"></i> Bimbingan Konseling</div>
    <div class="app-grid">
        <?php if(in_array('bk_jurnal', $custom_apk_access ?? [])): ?>
        <a href="/apk/bk/jurnal" class="app-card">
            <div class="app-icon" style="background: #eef2ff; color: #6366f1;"><i data-lucide="book-user" style="width: 22px;"></i></div>
            <div class="app-title">Jurnal Konseling</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('bk_poin', $custom_apk_access ?? [])): ?>
        <a href="/apk/bk/poin" class="app-card">
            <div class="app-icon" style="background: #fef2f2; color: #ef4444;"><i data-lucide="alert-triangle" style="width: 22px;"></i></div>
            <div class="app-title">Poin Kedisiplinan</div>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Wali Kelas -->
    <?php if(in_array('wali_siswa', $custom_apk_access ?? []) || in_array('wali_jurnal', $custom_apk_access ?? []) || in_array('wali_absen', $custom_apk_access ?? []) || in_array('wali_catatan', $custom_apk_access ?? []) || in_array('wali_buku', $custom_apk_access ?? []) || in_array('wali_poin', $custom_apk_access ?? [])): ?>
    <div class="cat-title"><i data-lucide="users" style="width: 16px; color: #94a3b8;"></i> Wali Kelas</div>
    <div class="app-grid">
        <?php if(in_array('wali_siswa', $custom_apk_access ?? [])): ?>
        <a href="/apk/wali-kelas/siswa" class="app-card">
            <div class="app-icon" style="background: #eef2ff; color: #6366f1;"><i data-lucide="users" style="width: 22px;"></i></div>
            <div class="app-title">Data Siswa</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('wali_jurnal', $custom_apk_access ?? [])): ?>
        <a href="/apk/wali-kelas/jurnal" class="app-card">
            <div class="app-icon" style="background: #fffbeb; color: #f59e0b;"><i data-lucide="clipboard-list" style="width: 22px;"></i></div>
            <div class="app-title">Jurnal Kelas</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('wali_absen', $custom_apk_access ?? [])): ?>
          <a href="/apk/wali-kelas/monitor-absen" class="app-card">
              <div class="app-icon" style="background: #ecfdf5; color: #10b981;"><i data-lucide="user-check" style="width: 22px;"></i></div>
              <div class="app-title">Absensi Kelas</div>
          </a>
        <?php endif; ?>
        <?php if(in_array('wali_catatan', $custom_apk_access ?? [])): ?>
          <a href="/apk/wali-kelas/catatan" class="app-card">
              <div class="app-icon" style="background: #f3e8ff; color: #8b5cf6;"><i data-lucide="notebook-pen" style="width: 22px;"></i></div>
              <div class="app-title">Catatan Wali Kelas</div>
          </a>
        <?php endif; ?>
        <?php if(in_array('wali_buku', $custom_apk_access ?? [])): ?>
        <a href="/apk/wali-kelas/buku-kerja" class="app-card">
            <div class="app-icon" style="background: #e0f2fe; color: #0ea5e9;"><i data-lucide="list-todo" style="width: 22px;"></i></div>
            <div class="app-title">Buku Kerja</div>
        </a>
        <?php endif; ?>
        <?php if(in_array('wali_poin', $custom_apk_access ?? [])): ?>
        <a href="/apk/wali-kelas/poin" class="app-card">
            <div class="app-icon" style="background: #fef2f2; color: #ef4444;"><i data-lucide="alert-circle" style="width: 22px;"></i></div>
            <div class="app-title">Log Kedisiplinan</div>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Empty State -->
    <?php if(empty($custom_apk_access)): ?>
    <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
        <i data-lucide="package-open" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 15px;"></i>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 600;">Belum ada aplikasi yang didelegasikan ke akun Anda.</p>
    </div>
    <?php endif; ?>

</div>

<!-- Modal Kasir Bendahara -->
<div id="modalKasirBend" class="apk-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s; padding:20px;" onclick="if(event.target===this) { closeKasirModal(); }">
    <div class="apk-modal-content" style="background:#fff; width:100%; max-width:400px; margin:0 auto; border-radius:24px; padding:25px; transform:scale(0.95); transition:transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
        <h3 style="margin:0 0 15px 0; font-size:1.2rem; color:#1e293b; font-weight:800; text-align:center;">Pilih Mode Kasir</h3>
        
        <a href="/apk/kasir/kolektif" class="app-card" style="flex-direction:row; justify-content:flex-start; padding:15px; margin-bottom:12px; border:2px solid #e0f2fe; background:#f0f9ff; box-shadow:none; text-decoration:none;">
            <div class="app-icon" style="background:#bae6fd; color:#0284c7; margin-bottom:0; margin-right:15px; flex-shrink:0;"><i data-lucide="layers" style="width:24px;height:24px;"></i></div>
            <div style="text-align:left;">
                <div style="font-size:1rem; font-weight:800; color:#0369a1; margin-bottom:4px;">Pembayaran Kolektif (Auto-Split)</div>
                <div style="font-size:0.8rem; color:#0ea5e9;">Bayar uang gelondongan, sistem memecah otomatis.</div>
            </div>
        </a>

        <a href="/apk/kasir/manual" class="app-card" style="flex-direction:row; justify-content:flex-start; padding:15px; margin-bottom:20px; border:1px solid #e2e8f0; box-shadow:none; text-decoration:none;">
            <div class="app-icon" style="background:#f1f5f9; color:#64748b; margin-bottom:0; margin-right:15px; flex-shrink:0;"><i data-lucide="hand-coins" style="width:24px;height:24px;"></i></div>
            <div style="text-align:left;">
                <div style="font-size:1rem; font-weight:800; color:#334155; margin-bottom:4px;">Pembayaran Manual</div>
                <div style="font-size:0.8rem; color:#64748b;">Bayar tagihan satu-per-satu secara manual.</div>
            </div>
        </a>

        <button type="button" class="btn btn-outline" style="width:100%; padding:14px; border-radius:12px; font-weight:700; color:#64748b; border:1px solid #e2e8f0; background:transparent;" onclick="closeKasirModal();">Batal</button>
    </div>
</div>

<script>
    function openKasirModal() {
        var modal = document.getElementById('modalKasirBend');
        var content = modal.querySelector('.apk-modal-content');
        modal.style.display = 'flex';
        // Trigger reflow
        void modal.offsetWidth;
        modal.style.opacity = '1';
        setTimeout(function() {
            content.style.transform = 'scale(1)';
        }, 10);
    }

    function closeKasirModal() {
        var modal = document.getElementById('modalKasirBend');
        var content = modal.querySelector('.apk-modal-content');
        content.style.transform = 'scale(0.95)';
        setTimeout(function() {
            modal.style.opacity = '0';
            setTimeout(function() {
                modal.style.display = 'none';
            }, 200);
        }, 100);
    }
</script>
<br><br><br>
