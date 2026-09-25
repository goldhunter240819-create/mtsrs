<?php
use App\Core\Helper;
use App\Core\Branding;

$institusi = Branding::getInstitusi();
$logoSrc = !empty($institusi['logo']) ? Helper::url('/uploads/logo/' . $institusi['logo']) : Helper::url('/assets/images/logo.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MTs Roudlotus Sholihin</title>
    <!-- Tab Favicon -->
    <link rel="shortcut icon" href="<?php echo $logoSrc; ?>?v=<?php echo time(); ?>" type="image/png">
    <link rel="icon" href="<?php echo $logoSrc; ?>?v=<?php echo time(); ?>" type="image/png">

    <link rel="stylesheet" href="<?php echo Helper::url('/assets/css/style.css'); ?>?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(-45deg, #0f172a, #1e1b4b, #312e81, #1e1b4b);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glowing Animated Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.45;
            animation: orbFloat 10s ease-in-out infinite alternate;
        }
        .orb-1 {
            top: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            background: #2563eb;
        }
        .orb-2 {
            bottom: -100px;
            right: -100px;
            width: 450px;
            height: 450px;
            background: #7c3aed;
        }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 30px) scale(1.1); }
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 28px;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
            color: white;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            width: 72px;
            height: 72px;
            margin: 0 auto 1.25rem;
            background: #ffffff;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
        }

        .login-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-subtitle {
            font-size: 0.88rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
            color: #cbd5e1;
            font-weight: 700;
            font-size: 0.9rem;
            margin-left: 4px;
        }

        .form-label-wrap i {
            width: 18px;
            height: 18px;
            color: #94a3b8;
        }

        .input-wrap {
            position: relative;
        }

        .form-control-glass {
            width: 100%;
            box-sizing: border-box;
            padding: 0.9rem 1.2rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control-glass::placeholder {
            color: #64748b;
        }

        .form-control-glass:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.12);
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            color: #94a3b8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: #e2e8f0;
        }

        .btn-login-submit {
            width: 100%;
            padding: 0.9rem;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            border: none;
            color: white;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 0.5rem;
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(124, 58, 237, 0.5);
        }

        .alert-error {
            background: rgba(244, 63, 94, 0.2);
            border: 1px solid rgba(244, 63, 94, 0.4);
            color: #fecdd3;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

    </style>
</head>
<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <img src="<?php echo $logoSrc; ?>" alt="Logo MTs RS">
            </div>
            <h1 class="login-title">MTs Roudlotus Sholihin</h1>
            <p class="login-subtitle">Sistem Informasi Akademik & Keuangan Terpadu</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <i data-lucide="alert-circle" style="width:18px;height:18px;"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo Helper::url('/login'); ?>" method="POST" id="loginForm">
            <input type="hidden" name="qr_data" id="qr_data">


            <div id="qr-reader-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.75); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
                <div id="qr-reader-container" style="background: white; padding: 25px; border-radius: 24px; width: 90%; max-width: 380px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #1e293b; font-weight: 800; font-size: 1.25rem;">Scan QR Code</h3>
                    <div id="qr-reader" style="width: 100%; overflow: hidden; border-radius: 16px; border: 2px solid #e2e8f0;"></div>
                    <button type="button" class="btn-login-submit" style="background: #ef4444; margin-top: 20px; box-shadow: none; border-radius: 12px;" onclick="stopQRScanner()">
                        <i data-lucide="x"></i> Batal Scan
                    </button>
                </div>
            </div>

            <div class="form-group" id="group-username">
                <div class="form-label-wrap">
                    <i data-lucide="user"></i> Username
                </div>
                <div class="input-wrap">
                    <input type="text" name="username" id="input-username" class="form-control-glass" placeholder="Ketik username..." required autofocus>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <div class="form-label-wrap">
                    <i data-lucide="lock"></i> Password
                </div>
                <div class="input-wrap">
                    <input type="password" name="password" id="input-password" class="form-control-glass" placeholder="Ketik password..." style="padding-right: 2.8rem;" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()" title="Tampilkan/Sembunyikan Password">
                        <i data-lucide="eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login-submit">
                Masuk Sistem <i data-lucide="arrow-right" style="width:18px;height:18px;"></i>
            </button>

            <div style="text-align: center; margin-top: 1rem;">
                <p style="color: rgba(255,255,255,0.6); font-size: 0.8rem; margin-bottom: 0.5rem;">atau</p>
                <button type="button" class="btn-login-submit" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white;" onclick="toggleQRScanner()">
                    <i data-lucide="qr-code"></i> Login dengan QR Code
                </button>
            </div>
        </form>

    </div>

    <script>
        lucide.createIcons();

        function togglePassword() {
            const input = document.getElementById('input-password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        let html5QrCode = null;

        function toggleQRScanner() {
            const modal = document.getElementById('qr-reader-modal');
            if (modal.style.display === 'none') {
                modal.style.display = 'flex';
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("qr-reader");
                }
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                
                html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
                .catch(err => {
                    alert("Kamera tidak dapat diakses atau tidak ditemukan.");
                    stopQRScanner();
                });
            } else {
                stopQRScanner();
            }
        }

        function stopQRScanner() {
            const modal = document.getElementById('qr-reader-modal');
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    modal.style.display = 'none';
                }).catch(err => {
                    console.error("Gagal menghentikan kamera", err);
                    modal.style.display = 'none';
                });
            } else {
                modal.style.display = 'none';
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('qr_data').value = decodedText;
            stopQRScanner();
            document.getElementById('loginForm').submit();
        }
    </script>
</body>
</html>
