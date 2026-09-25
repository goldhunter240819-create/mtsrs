<?php use App\Core\Helper; ?>
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="calendar" style="color: #bfdbfe;"></i> Jadwal Pelajaran
        </h1>
        <p class="mph-subtitle">Kelola alokasi jadwal pelajaran harian per rombel kelas.</p>
    </div>
    <div class="mph-actions">
        <a href="<?php echo Helper::url('/siakad/jadwal/waktu'); ?>" class="btn btn-secondary" style="background:#f1f5f9; color:#475569; border:none;">
            <i data-lucide="clock"></i> Atur Waktu (Global)
        </a>
    </div>
</div>

<style>
    .schedule-wrapper {
        width: 100%;
        overflow-x: auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
        /* Custom scrollbar */
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8fafc;
    }
    .schedule-wrapper::-webkit-scrollbar { height: 10px; }
    .schedule-wrapper::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    .schedule-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid #f8fafc; }
    
    .master-schedule-table {
        width: max-content;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 100%;
    }
    .master-schedule-table th, .master-schedule-table td {
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 12px;
        vertical-align: middle;
    }
    .master-schedule-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 1px 0 #e2e8f0;
    }
    .hari-header {
        background: var(--primary-blue) !important;
        color: #fff !important;
        font-size: 0.9rem !important;
        padding: 12px !important;
        border-right: 1px solid rgba(255,255,255,0.2) !important;
    }
    .kelas-header {
        width: 160px;
        min-width: 160px;
        max-width: 160px;
        background: #f1f5f9 !important;
    }
    .kelas-name { margin-bottom: 4px; font-weight: 900; color: #1e293b; }

    /* Sticky columns */
    .sticky-col-1, .sticky-col-2 {
        position: sticky;
        left: 0;
        background: #f8fafc;
        z-index: 20;
    }
    .sticky-col-2 { left: 40px; border-right: 2px solid #cbd5e1 !important; }
    
    .master-schedule-table thead .sticky-col-1, 
    .master-schedule-table thead .sticky-col-2 {
        z-index: 30; /* Above regular headers and other sticky cols */
    }

    .cell-empty {
        background: #fff;
        text-align: center;
        color: #cbd5e1;
    }
    .cell-filled {
        background: #f0fdf4;
        border-left: 3px solid #10b981;
        transition: 0.2s;
        cursor: default;
        width: 160px;
        max-width: 160px;
        overflow: hidden;
    }
    .cell-filled:hover {
        background: #dcfce7;
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        z-index: 5;
        position: relative;
    }
    
    .mapel-name {
        display: block;
        font-weight: 800;
        color: #0f172a;
        font-size: 0.75rem;
        line-height: 1.2;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .guru-name {
        display: block;
        font-size: 0.65rem;
        color: #64748b;
        font-weight: 600;
        line-height: 1.1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Colors based on Hari just for visual variation */
    .hari-Senin .cell-filled { background: #f0f9ff; border-left-color: #3b82f6; }
    .hari-Selasa .cell-filled { background: #fdf4ff; border-left-color: #d946ef; }
    .hari-Rabu .cell-filled { background: #fffbeb; border-left-color: #f59e0b; }
    .hari-Kamis .cell-filled { background: #f0fdf4; border-left-color: #10b981; }
    .hari-Ahad .cell-filled { background: #fff1f2; border-left-color: #f43f5e; }
</style>

<?php foreach($hari_list as $h): ?>
<div style="background: var(--primary-blue); padding: 12px 20px; border-radius: 12px 12px 0 0; color: white; font-weight: 800; font-size: 1.1rem; text-transform: uppercase; margin-top: 2rem; display: flex; align-items: center; gap: 8px;">
    <i data-lucide="calendar"></i> HARI <?php echo strtoupper($h); ?>
</div>
<div class="schedule-wrapper" style="border-radius: 0 0 12px 12px; margin-bottom: 0;">
    <table class="master-schedule-table">
        <thead>
            <tr>
                <th class="sticky-col-1" style="width:40px;">NO</th>
                <th class="sticky-col-2" style="width:120px;">WAKTU</th>
                <?php foreach($kelas as $k): ?>
                    <th class="kelas-header">
                        <div class="kelas-name"><?php echo htmlspecialchars($k['nama_kelas']); ?></div>
                        <a href="<?php echo Helper::url('/siakad/jadwal/edit/' . $k['id']); ?>" class="btn-atur" title="Edit Jadwal Kelas Ini" style="display:inline-block; background:#e0e7ff; color:#4f46e5; font-size:0.65rem; padding:2px 8px; border-radius:10px; text-decoration:none;">[Atur]</a>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($jam_list)): ?>
                <tr><td colspan="<?php echo 2 + count($kelas); ?>" class="text-center" style="padding: 3rem; color: #94a3b8; text-align: center;">Belum ada jadwal yang diatur.</td></tr>
            <?php else: ?>
                <?php $no = 1; foreach($jam_list as $jam): ?>
                <tr>
                    <td class="sticky-col-1 text-center" style="font-size: 0.8rem; font-weight: 800; color:#475569; text-align:center;"><?php echo $no++; ?></td>
                    <td class="sticky-col-2 text-center" style="font-size: 0.75rem; font-weight: 800; font-family: monospace; color:var(--primary-blue); text-align:center;">
                        <?php echo htmlspecialchars($jam); ?><br>
                    </td>
                    
                    <?php foreach($kelas as $k): ?>
                        <?php 
                        $cell = $jadwal_master[$h][$jam][$k['id']] ?? null;
                        if($cell): 
                        ?>
                        <td class="cell-filled hari-<?php echo str_replace("'", '', $h); ?>" title="<?php echo htmlspecialchars($cell['nama_mapel'] . ' - ' . $cell['nama_guru']); ?>">
                            <span class="mapel-name"><?php echo htmlspecialchars($cell['nama_mapel']); ?></span>
                            <span class="guru-name"><i data-lucide="user" style="width:10px;height:10px;display:inline;"></i> <?php echo htmlspecialchars($cell['nama_guru']); ?></span>
                        </td>
                        <?php else: ?>
                        <td class="cell-empty">-</td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<script>
    <?php if(isset($_GET['err']) && $_GET['err'] == 'no_waktu'): ?>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            showAppModal({
                title: 'Pengaturan Waktu Kosong',
                message: 'Harap mengatur <strong>Slot Waktu Pelajaran (Master Time Slots)</strong> terlebih dahulu sebelum Anda dapat mengatur jadwal untuk masing-masing kelas.',
                type: 'error',
                buttons: [
                    { text: 'Tutup', class: 'btn btn-secondary' },
                    { text: '<i data-lucide="clock"></i> Atur Waktu Sekarang', class: 'btn btn-primary', href: '<?php echo Helper::url("/siakad/jadwal/waktu"); ?>' }
                ]
            });
        }, 100);
    });
    <?php endif; ?>
</script>
