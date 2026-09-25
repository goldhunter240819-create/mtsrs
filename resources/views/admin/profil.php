<?php
use App\Core\Helper;
ob_start();
?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); margin-top: 20px;">
    <div>
        <h1 class="mph-title"><i data-lucide="user"></i> Profil Admin</h1>
        <p class="mph-subtitle">Atur nama, username, password, dan foto profil Anda</p>
    </div>
</div>

<div class="z-content-pad">
    <div class="z-card" style="padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); background: #fff;">
        <form action="<?php echo Helper::url('/admin/profil/save'); ?>" method="POST" enctype="multipart/form-data" style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
            
            <!-- Kolom Kiri: Foto -->
            <div style="flex: 0 0 auto; display: flex; flex-direction: column; align-items: center; padding: 25px 20px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; width: 220px; box-sizing: border-box;">
                <div style="position: relative; display: inline-block;">
                    <?php if (!empty($user['foto'])): ?>
                        <img id="previewFoto" src="<?php echo Helper::url('/public/uploads/profil/' . htmlspecialchars($user['foto'])); ?>" alt="Foto Profil" style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                    <?php else: ?>
                        <img id="previewFoto" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" style="display: none; width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                        <div id="noFotoPlaceholder" style="width: 140px; height: 140px; border-radius: 50%; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                            <i data-lucide="camera" style="width: 45px; height: 45px;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div style="margin-top: 20px; width: 100%; text-align: center;">
                    <label for="foto" style="cursor: pointer; background: #e0f2fe; padding: 10px 15px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; color: #0284c7; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; border: 1px solid #bae6fd; width: 100%; box-sizing: border-box;">
                        <i data-lucide="upload" style="width: 16px;"></i> Ganti Foto
                    </label>
                    <input type="file" name="foto" id="foto" accept="image/*" style="display: none;" onchange="previewImage(this)">
                </div>
                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 12px; text-align: center; line-height: 1.4;">
                    Format JPG/PNG<br>Maksimal ukuran 2MB
                </div>
            </div>

            <!-- Kolom Kanan: Input Form -->
            <div style="flex: 1; min-width: 300px;">
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px; font-size: 0.9rem;">Nama Lengkap (Tampilan)</label>
                        <input type="text" name="nama" class="z-input" value="<?php echo htmlspecialchars($user['nama'] ?? ''); ?>" required placeholder="Masukkan nama Anda" style="width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #cbd5e1; box-sizing: border-box; font-size: 0.95rem;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px; font-size: 0.9rem;">Username Login</label>
                        <input type="text" name="username" class="z-input" value="<?php echo htmlspecialchars($user['username'] ?? $_SESSION['username'] ?? ''); ?>" required placeholder="Masukkan username" style="width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #cbd5e1; box-sizing: border-box; font-size: 0.95rem;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px; font-size: 0.9rem;">Password Baru</label>
                        <div style="position: relative;">
                            <input type="password" id="inputPassword" name="password" class="z-input" placeholder="Biarkan kosong jika tidak ingin mengubah password" style="width: 100%; padding: 12px 45px 12px 15px; border-radius: 8px; border: 1px solid #cbd5e1; box-sizing: border-box; font-size: 0.95rem;">
                            <div onclick="togglePassword()" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b; display: flex; align-items: center;">
                                <i data-lucide="eye" id="eyeIcon" style="width: 20px; height: 20px;"></i>
                            </div>
                        </div>
                        <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="info" style="width: 14px; height: 14px;"></i> Isi kolom ini hanya jika Anda ingin mengganti password lama
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-start; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.95rem; border-radius: 8px;">
                        <i data-lucide="save"></i> Simpan Perubahan
                    </button>
                    <a href="<?php echo Helper::url('/portal'); ?>" class="btn btn-outline" style="padding: 12px 24px; font-weight: 700; font-size: 0.95rem; border-radius: 8px; color: #64748b; border: 1px solid #cbd5e1;">Kembali</a>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFoto').src = e.target.result;
                document.getElementById('previewFoto').style.display = 'inline-block';
                var placeholder = document.getElementById('noFotoPlaceholder');
                if(placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePassword() {
        const passwordInput = document.getElementById('inputPassword');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordInput.type = 'password';
            eyeIcon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
