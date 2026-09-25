<div class="modern-page-header">
    <div class="mph-left">
        <h1 class="mph-title"><i data-lucide="layers"></i> Komponen Biaya Sekolah</h1>
        <p class="mph-subtitle">Kelola jenis dan nominal biaya resmi sekolah
    Tambah Biaya


    Daftar Komponen Biaya</p>
    </div>
</div>
<div class="z-content-pad">


    <div class="z-table-wrap"><table>
        <thead><tr><th>Jenis Biaya</th><th>Nominal</th><th>Periode</th><th>Berlaku Untuk</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($biaya_list as $b): ?>
            <tr>
                <td style="font-weight:700;"><?php echo $b['nama']; ?></td>
                <td class="z-amount">Rp <?php echo number_format($b['nominal'],0,',','.'); ?></td>
                <td><span class="pill pill-blue"><?php echo $b['periode']; ?></span></td>
                <td style="font-size:.82rem;"><?php echo $b['berlaku_untuk']; ?></td>
                <td><span class="pill <?php echo $b['status']==='Aktif'?'pill-green':'pill-gray'; ?>"><?php echo $b['status']; ?></span></td>
                <td>
                    <div style="display:flex;gap:5px;">
                        <button class="btn btn-outline btn-sm" onclick="alert('Edit belum tersedia!')"><i data-lucide="pencil" style="width:12px;height:12px;"></i></button>
                        <a href="/admin/finance/biaya/delete/<?php echo $b['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus komponen biaya ini?');"><i data-lucide="trash-2" style="width:12px;height:12px;"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div>
</div>
</div>