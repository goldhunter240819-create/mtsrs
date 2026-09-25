<div class="modern-page-header">
    <div>
        <h1 class="mph-title">
            <i data-lucide="settings" style="color: #bfdbfe;"></i> Pengaturan Aplikasi
        </h1>
        <p class="mph-subtitle">Konfigurasi tema, warna utama, dan pendelegasian akses kustom fitur Aplikasi Mobile kepada masing-masing guru.</p>
    </div>
</div>

<div class="z-card" style="margin-bottom: 2rem;">
    <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">Delegasi Hak Akses Aplikasi</h3>
        <input type="text" id="searchGuru" placeholder="Cari nama guru..." style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; width: 250px;">
    </div>
    
    <div style="padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; color: #64748b;">
        <i data-lucide="info" style="width:14px; height:14px; vertical-align:middle;"></i> Pilih aplikasi yang ingin ditampilkan pada menu <b>"Aplikasi Saya"</b> di akun APK masing-masing guru.
    </div>

    <div style="max-height: 600px; overflow-y: auto;">
        <?php foreach ($gurus as $g): ?>
            <?php 
                $akses = $akses_map[$g['id']] ?? []; 
                $hasAny = count($akses) > 0;
            ?>
            <div class="guru-row" style="border-bottom: 1px solid #f1f5f9; padding: 15px 20px;" data-nama="<?= strtolower(htmlspecialchars($g['nama'])) ?>">
                <div style="display:flex; justify-content:space-between; align-items:center; cursor:pointer;" onclick="toggleAccordion(<?= $g['id'] ?>)">
                    <div style="display:flex; align-items:center; gap: 15px;">
                        <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg, #e0f2fe, #bae6fd); color:#0369a1; display:flex; align-items:center; justify-content:center; font-weight:700;">
                            <?= substr(htmlspecialchars($g['nama']), 0, 2) ?>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #334155; font-size: 1.05rem;"><?= htmlspecialchars($g['nama']) ?></div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top:2px;">
                                NIP: <?= empty($g['nip']) ? '-' : htmlspecialchars($g['nip']) ?> &bull; 
                                <span style="color:<?= $g['is_kamad'] ? '#b91c1c' : '#0ea5e9' ?>; font-weight:600;"><?= $g['is_kamad'] ? 'Kepala Madrasah' : 'Guru' ?></span>
                                &bull; <?= htmlspecialchars($g['jabatan']) ?>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:15px;">
                        <?php if($hasAny): ?>
                            <span class="pill pill-green"><?= count($akses) ?> Akses Aktif</span>
                        <?php else: ?>
                            <span class="pill" style="background:#f1f5f9; color:#94a3b8;">Default</span>
                        <?php endif; ?>
                        <i data-lucide="chevron-down" id="icon-<?= $g['id'] ?>" style="color:#94a3b8; transition:0.3s;"></i>
                    </div>
                </div>

                <div id="acc-<?= $g['id'] ?>" class="acc-body" data-akses='<?= json_encode($akses) ?>' style="display:none; margin-top:20px; padding:20px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                    <!-- Content will be loaded lazily -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Template for Apps Grid (rendered once, cloned via JS) -->
<div id="apps-template" style="display:none;">
    <?php foreach ($apk_apps as $catName => $apps): ?>
        <div style="margin-bottom: 20px;">
            <div style="font-size: 0.85rem; font-weight: 800; color: #475569; margin-bottom: 12px; text-transform:uppercase; letter-spacing:0.5px;">
                <?= $catName ?>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:15px;">
                <?php foreach ($apps as $code => $app): ?>
                    <label class="app-toggle-card" data-code="<?= $code ?>" data-color="<?= $app['color'] ?>" style="display:flex; align-items:center; gap:12px; padding:12px; background:#fff; border:1px solid #cbd5e1; border-radius:10px; cursor:pointer; transition:0.2s; box-shadow: none;">
                        <div style="width:36px; height:36px; border-radius:8px; background:<?= $app['bg'] ?>; color:<?= $app['color'] ?>; display:flex; align-items:center; justify-content:center;">
                            <i data-lucide="<?= $app['icon'] ?>" style="width:18px;"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:0.9rem; font-weight:700; color:#1e293b;"><?= $app['name'] ?></div>
                        </div>
                        <div>
                            <input type="checkbox" class="z-switch app-checkbox" data-code="<?= $code ?>">
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function toggleAccordion(id) {
    const acc = document.getElementById('acc-' + id);
    const icon = document.getElementById('icon-' + id);
    if (acc.style.display === 'none') {
        if (!acc.hasAttribute('data-loaded')) {
            const tmpl = document.getElementById('apps-template').cloneNode(true);
            tmpl.style.display = 'block';
            tmpl.id = ''; // clear id to avoid duplicates
            
            const aksesStr = acc.getAttribute('data-akses');
            let akses = [];
            if(aksesStr) {
                try { akses = JSON.parse(aksesStr); } catch(e) {}
            }
            
            // Set up checkboxes and styles
            tmpl.querySelectorAll('.app-toggle-card').forEach(card => {
                const code = card.getAttribute('data-code');
                const color = card.getAttribute('data-color');
                const checkbox = card.querySelector('.app-checkbox');
                
                if (akses.includes(code)) {
                    checkbox.checked = true;
                    card.classList.add('active');
                    card.style.borderColor = color;
                    card.style.boxShadow = '0 4px 10px rgba(0,0,0,0.05)';
                }
                
                checkbox.addEventListener('change', function() {
                    toggleAkses(id, code, this.checked, card, color);
                });
            });
            
            acc.appendChild(tmpl);
            acc.setAttribute('data-loaded', 'true');
        }
        acc.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        acc.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

function toggleAkses(guruId, appCode, isActive, cardElem, activeColor) {
    // Update styling instantly
    if(isActive) {
        cardElem.style.borderColor = activeColor;
        cardElem.style.boxShadow = '0 4px 10px rgba(0,0,0,0.05)';
    } else {
        cardElem.style.borderColor = '#cbd5e1';
        cardElem.style.boxShadow = 'none';
    }

    // Send AJAX request
    fetch('<?= \App\Core\Helper::url("/pengaturan-apk/aplikasi/save-akses") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `guru_id=${guruId}&app_code=${appCode}&is_active=${isActive ? 1 : 0}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status !== 'success') {
            alert('Gagal menyimpan perubahan: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan jaringan.');
    });
}

// Simple search filter
document.getElementById('searchGuru').addEventListener('input', function(e) {
    const val = e.target.value.toLowerCase();
    document.querySelectorAll('.guru-row').forEach(row => {
        const nama = row.getAttribute('data-nama');
        if(nama.includes(val)) {
            row.style.display = 'block';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
