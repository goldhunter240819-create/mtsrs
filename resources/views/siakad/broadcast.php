<?php use App\Core\Helper; ?>
<div class="floating-card">
<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="radio" style="color: #bfdbfe;"></i> Broadcast Notifikasi Pesan
        </h1>
        <p class="mph-subtitle">Pengiriman pengumuman / pesan resmi massal kepada guru & siswa MTs RS.</p>
    </div>
    <div class="mph-actions">
        <button class="btn btn-primary-white" onclick="openModalBroadcast()">
            <i data-lucide="send"></i> Buat Broadcast
        </button>
    </div>
</div>

    <div class="table-responsive">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>Waktu Kirim</th>
                    <th>Judul Pesan</th>
                    <th>Pesan / Pengumuman</th>
                    <th>Target Role</th>
                    <th>Pengirim</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($broadcastList as $b): ?>
                <tr>
                    <td style="font-family:monospace;"><?php echo htmlspecialchars($b['created_at']); ?></td>
                    <td style="font-weight:700; color: var(--primary-purple);"><?php echo htmlspecialchars($b['judul']); ?></td>
                    <td style="font-size:0.88rem;"><?php echo htmlspecialchars($b['pesan']); ?></td>
                    <td><span class="badge-pill badge-primary"><?php echo htmlspecialchars($b['target_role']); ?></span></td>
                    <td style="font-weight:700;"><?php echo htmlspecialchars($b['username']); ?></td>
                    <td>
                        <a href="javascript:void(0);" 
                           onclick="confirmDelete('<?php echo Helper::url('/siakad/lain-lain/broadcast/delete?id=' . $b['id']); ?>')"
                           style="color: #ef4444; background: #fee2e2; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;"
                           onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                           <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalBroadcast" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); backdrop-filter:blur(8px); z-index:1000; align-items:center; justify-content:center; opacity:0; transition:opacity 0.3s ease;">
    <div class="floating-card" style="width:100%; max-width:500px; padding:2.5rem; transform:translateY(20px); transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position:relative;">
        
        <div style="position:absolute; top:-25px; left:50%; transform:translateX(-50%); width:60px; height:60px; background:linear-gradient(135deg, #3b82f6, #2563eb); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 10px 20px rgba(37,99,235,0.3); border:4px solid #ffffff;">
            <i data-lucide="radio" style="color:#ffffff; width:28px; height:28px;"></i>
        </div>

        <h3 style="font-weight:800; font-size:1.4rem; color:#1e293b; text-align:center; margin-top:1.5rem; margin-bottom:0.5rem;">Kirim Broadcast Notifikasi</h3>
        <p style="text-align:center; color:#64748b; font-size:0.9rem; margin-bottom:2rem;">Pesan ini akan dikirim via *Push Notification* (OneSignal) ke layar perangkat target.</p>

        <form method="POST" action="<?php echo Helper::url('/siakad/lain-lain/broadcast/save'); ?>">
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:#475569; margin-bottom:0.5rem;">Judul Pengumuman</label>
                <input type="text" name="judul" placeholder="Contoh: Info Libur Nasional" required 
                       style="width:100%; padding:0.875rem 1rem; border:2px solid #e2e8f0; border-radius:12px; font-family:inherit; font-size:0.95rem; color:#1e293b; background:#f8fafc; transition:all 0.2s; outline:none; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 4px rgba(59,130,246,0.1)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:#475569; margin-bottom:0.5rem;">Target Penerima</label>
                <div style="position:relative;">
                    <select name="target_role" 
                            style="width:100%; padding:0.875rem 1rem; border:2px solid #e2e8f0; border-radius:12px; font-family:inherit; font-size:0.95rem; color:#1e293b; background:#f8fafc; transition:all 0.2s; outline:none; appearance:none; cursor:pointer; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 4px rgba(59,130,246,0.1)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                        <option value="Semua">📡 Semua Pengguna (Guru & Siswa)</option>
                        <option value="Guru">👨‍🏫 Khusus Dewan Guru</option>
                        <option value="Siswa">🎓 Khusus Siswa / Orang Tua</option>
                    </select>
                    <i data-lucide="chevron-down" style="position:absolute; right:15px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; width:18px;"></i>
                </div>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:#475569; margin-bottom:0.5rem;">Isi Pesan Broadcast</label>
                <textarea name="pesan" rows="4" placeholder="Ketik pengumuman detail di sini..." required
                          style="width:100%; padding:1rem; border:2px solid #e2e8f0; border-radius:12px; font-family:inherit; font-size:0.95rem; color:#1e293b; background:#f8fafc; transition:all 0.2s; outline:none; resize:none; box-sizing:border-box;"
                          onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 4px rgba(59,130,246,0.1)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"></textarea>
            </div>
            
            <div style="display:flex; justify-content:center; gap:12px;">
                <button type="button" 
                        style="padding:0.75rem 1.5rem; background:#f1f5f9; color:#475569; border:none; border-radius:12px; font-weight:700; cursor:pointer; transition:all 0.2s; font-family:inherit;"
                        onmouseover="this.style.background='#e2e8f0';" onmouseout="this.style.background='#f1f5f9';"
                        onclick="closeModalBroadcast()">Batalkan</button>
                <button type="submit" 
                        style="padding:0.75rem 2rem; background:linear-gradient(135deg, #3b82f6, #2563eb); color:white; border:none; border-radius:12px; font-weight:700; cursor:pointer; transition:all 0.2s; font-family:inherit; display:flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(37,99,235,0.25);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(37,99,235,0.35)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(37,99,235,0.25)';">
                    <i data-lucide="send" style="width:18px; height:18px;"></i> Kirim Pesan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
    function openModalBroadcast() {
        const m = document.getElementById('modalBroadcast');
        const c = m.querySelector('.floating-card');
        m.style.display = 'flex';
        // force reflow
        void m.offsetWidth;
        m.style.opacity = '1';
        c.style.transform = 'translateY(0)';
    }
    function closeModalBroadcast() {
        const m = document.getElementById('modalBroadcast');
        const c = m.querySelector('.floating-card');
        m.style.opacity = '0';
        c.style.transform = 'translateY(20px)';
        setTimeout(() => { m.style.display = 'none'; }, 300);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
    function confirmDelete(url) {
        Swal.fire({
            title: 'Hapus Broadcast?',
            text: "Broadcast yang dihapus akan hilang dari layar semua pengguna!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
