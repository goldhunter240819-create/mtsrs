<?php
$hideNav = true; // Sembunyikan bottom nav di halaman login
?>
<div style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2rem; background: linear-gradient(135deg, var(--apk-bg) 0%, #e2e8f0 100%);">
    
    <div style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-radius: 30px; padding: 2.5rem 1.5rem; box-shadow: 0 20px 40px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.02); border: 2px solid #f1f5f9; text-align: center; display: flex; flex-direction: column; align-items: center; width: 100%;">

        <div style="width: 80px; height: 80px; background: white; border-radius: 20px; display: flex; justify-content: center; align-items: center; box-shadow: 0 10px 20px rgba(0,0,0,0.05); margin-bottom: 15px; border: 3px solid #e2e8f0;">
            <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo" style="width: 55px; height: 55px; object-fit: contain;">
        </div>
        
        <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; text-transform: uppercase; margin-bottom: 2rem; letter-spacing: 0.5px;">MTs Roudlotus Sholihin</h3>

        <div style="margin-bottom: 1.5rem;">
            <h2 style="color: var(--apk-primary); font-weight: 800; font-size: 1.4rem; margin-bottom: 6px; margin-top:0;">Assalamu'alaikum 👋</h2>
            <p style="font-size: 0.95rem; color: #64748b; line-height: 1.5; font-weight: 600; margin:0;">Yuk, arahkan <b>Kartu Pintar</b> ke kamera untuk masuk.</p>
        </div>

        <?php if(!empty($error_msg)): ?>
            <div style="background: #fef2f2; border: 2px solid #fca5a5; color: #ef4444; padding: 12px; border-radius: 15px; margin-bottom: 15px; width: 100%; font-weight: 600; font-size: 0.9rem;">
                <i data-lucide="triangle-alert"></i> <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <div style="width: 100%; max-width: 320px; border-radius: 30px; overflow: hidden; background: #fff; border: 6px solid var(--apk-primary); box-shadow: 0 15px 35px rgba(16, 185, 129, 0.2); position: relative;">
            <div id="qr-reader" style="width: 100%; border-radius: 24px; overflow:hidden;"></div>
        </div>
        
        <div id="qr-status" style="margin-top: 1.5rem; font-weight: 700; color: var(--apk-primary); font-size: 1.1rem; min-height: 2rem; background: #ecfdf5; padding: 8px 20px; border-radius: 20px; display: inline-block;">Memulai kamera...</div>

        <!-- Form tersembunyi -->
        <form id="login-form" method="POST" action="/apk/login" style="display: none;">
            <input type="text" name="qr_data" id="qr_data_input">
        </form>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode;

    window.onload = () => {
        startQRScanner();
    };

    async function startQRScanner() {
        html5QrCode = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        try {
            await html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
            document.getElementById('qr-status').innerText = 'Siap memindai...';
        } catch (err) {
            document.getElementById('qr-status').style.color = '#ef4444';
            document.getElementById('qr-status').innerHTML = 'Gagal mengakses kamera.';
        }
    }

    async function onScanSuccess(decodedText, decodedResult) {
        document.getElementById('qr-status').innerText = 'QR Terdeteksi! Memproses...';
        if (html5QrCode) {
            await html5QrCode.stop().catch(() => {});
        }
        document.getElementById('qr_data_input').value = decodedText;
        document.getElementById('login-form').submit();
    }
</script>
