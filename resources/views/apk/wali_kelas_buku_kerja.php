<div class="apk-vector-header" style="padding-bottom: 70px; justify-content: center; text-align: center; position: relative; background-image: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div>
        <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">Program Wali Kelas</div>
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.8); margin-top: 5px;">Kelas <?= htmlspecialchars($nama_kelas_raw) ?></div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -30px; border-radius: 30px 30px 0 0; min-height: 70vh; padding: 20px;">
    
    <form action="" method="POST">
        <!-- Struktur Organisasi -->
        <div style="margin-bottom: 25px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                <i data-lucide="users" style="width: 20px; color: #0284c7;"></i>
                <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;">Struktur Organisasi</h3>
            </div>
            
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                <?php 
                $roles = [
                    'ketua_kelas' => 'Ketua Kelas',
                    'wakil_ketua' => 'Wakil Ketua',
                    'sekretaris' => 'Sekretaris',
                    'bendahara' => 'Bendahara'
                ];
                foreach ($roles as $key => $label): 
                    $selected = $meta[$key] ?? '';
                ?>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 5px;"><?= $label ?></label>
                        <select name="<?= $key ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; outline: none; background: #f8fafc; color: #1e293b; appearance: auto;">
                            <option value="">-- Pilih Siswa --</option>
                            <?php foreach ($siswa_list as $s): ?>
                                <option value="<?= htmlspecialchars($s['id']) ?>" <?= $selected == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Jadwal Piket -->
        <div style="margin-bottom: 25px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                <i data-lucide="calendar" style="width: 20px; color: #0284c7;"></i>
                <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;">Jadwal Piket</h3>
            </div>
            <p style="font-size: 0.75rem; color: #64748b; margin-top: -10px; margin-bottom: 15px;">Pilih beberapa siswa untuk setiap harinya.</p>
            
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                <?php 
                $days = ['sabtu', 'minggu', 'senin', 'selasa', 'rabu', 'kamis'];
                foreach ($days as $day): 
                    $key = 'piket_' . $day;
                    $selected_arr = isset($meta[$key]) ? explode(',', $meta[$key]) : [];
                ?>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 5px; text-transform: capitalize;">Hari <?= $day ?></label>
                        <div style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; background: #f8fafc; max-height: 150px; overflow-y: auto;">
                            <?php foreach ($siswa_list as $s): ?>
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #1e293b; margin-bottom: 6px; cursor: pointer;">
                                    <input type="checkbox" name="<?= $key ?>[]" value="<?= htmlspecialchars($s['id']) ?>" <?= in_array($s['id'], $selected_arr) ? 'checked' : '' ?> style="accent-color: #0284c7; width: 16px; height: 16px; margin: 0;">
                                    <?= htmlspecialchars($s['nama']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <button type="submit" style="width: 100%; background: #0284c7; color: #fff; border: none; border-radius: 12px; padding: 15px; font-size: 0.95rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);">
            <i data-lucide="save" style="width: 18px;"></i> Simpan Program Kelas
        </button>
    </form>
</div>
