<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Data Gaji Karyawan</h1>
        <p class="mph-subtitle">Kelola penggajian guru dan staf sekolah</p>
    </div>
    <div class="mph-right" style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Data</button>
    </div>
</div>
<div class="z-content-pad">

<?php
$sudah = 0;
$totalGaji = 0;
foreach($gaji as $g) {
    if($g['status'] === 'Sudah Dibayar') $sudah++;
    $totalGaji += $g['gaji_pokok'] + $g['tunjangan'];
}
?>
<div class="z-stats" style="grid-template-columns: repeat(3, 1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Karyawan</div><div class="z-stat-value"><?php echo count($gaji); ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="users"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Sudah Dibayar</div><div class="z-stat-value" style="color:#16a34a;"><?php echo $sudah; ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="check-circle"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Penggajian</div><div class="z-stat-value" style="font-size:1.05rem;">Rp <?php echo number_format($totalGaji,0,',','.'); ?></div></div><div class="z-stat-icon zi-purple"><i data-lucide="wallet"></i></div></div>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="users"></i>Daftar Gaji</div></div>
    <div class="z-table-wrap"><table>
        <thead><tr><th>Karyawan</th><th>Jabatan</th><th>Gaji Pokok</th><th>Tunjangan</th><th>Total</th><th>Bulan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($gaji as $g): ?>
            <tr>
                <td><div class="z-uc"><div class="z-uc-av"><?php echo strtoupper(substr($g['nama'],0,2)); ?></div><div class="z-uc-name"><?php echo $g['nama']; ?></div></div></td>
                <td style="font-size:.82rem;"><?php echo $g['jabatan']; ?></td>
                <td class="z-amount">Rp <?php echo number_format($g['gaji_pokok'],0,',','.'); ?></td>
                <td style="font-size:.82rem;">Rp <?php echo number_format($g['tunjangan'],0,',','.'); ?></td>
                <td class="z-amount">Rp <?php echo number_format($g['gaji_pokok']+$g['tunjangan'],0,',','.'); ?></td>
                <td style="font-size:.81rem;color:var(--z-muted);"><?php echo $g['bulan']; ?></td>
                <td><span class="pill <?php echo $g['status']==='Sudah Dibayar'?'pill-green':'pill-amber'; ?>"><?php echo $g['status']; ?></span></td>
                <td>
                    <?php if($g['status']!=='Sudah Dibayar'): ?>
                        <form action="/admin/finance/gaji/bayar" method="POST" style="display:inline;">
                            <input type="hidden" name="nip" value="<?php echo $g['nip']; ?>">
                            <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Konfirmasi pembayaran gaji untuk karyawan ini?');">Bayar</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div>
</div>
</div>