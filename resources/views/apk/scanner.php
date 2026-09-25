<div class="apk-vector-header" style="padding-bottom: 25px;">
    <div class="apk-top-logo">
        <?php if(!empty($guru['foto'])): ?>
            <img src="<?= Helper::url('/public/uploads/guru/' . rawurlencode($guru['foto'])) ?>" alt="Foto" class="apk-avatar-top">
        <?php else: ?>
            <img src="<?= htmlspecialchars($faviconSrc) ?>" alt="Logo">
        <?php endif; ?>
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;">Sistem Kehadiran</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #fff;"><?= $title ?></div>
        </div>
    </div>
</div>

<div class="apk-main-board" style="margin-top: -20px; border-radius: 25px 25px 0 0; text-align: center;">
    
    <div id="statusBadge" style="background: #e0f2fe; color: #0ea5e9; padding: 8px 15px; border-radius: 20px; font-weight: 800; font-size: 0.9rem; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 8px;">
        <i data-lucide="camera"></i> Mempersiapkan Kamera...
    </div>

    <div style="background: #f8fafc; border-radius: 24px; padding: 15px; border: 1px solid #e2e8f0; margin-bottom: 20px; box-shadow: inset 0 2px 10px rgba(0,0,0,0.03);">
        <div id="reader" style="width: 100%; border-radius: 16px; overflow: hidden; background: #000;"></div>
    </div>

    <div style="display: flex; gap: 10px; justify-content: center; margin-bottom: 20px;">
        <button id="flipBtn" style="background: #fff; color: #475569; border: 1px solid #cbd5e1; padding: 12px 20px; border-radius: 16px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; flex: 1; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <i data-lucide="camera-switch" style="width: 18px;"></i> Putar Kamera
        </button>
        <button id="testVoiceBtn" style="background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: #fff; border: none; padding: 12px 20px; border-radius: 16px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; flex: 1; box-shadow: 0 4px 15px rgba(14,165,233,0.3);">
            <i data-lucide="volume-2" style="width: 18px;"></i> Tes Suara
        </button>
    </div>

    <!-- Feedback Popup -->
    <div id="resultPopup" style="display: none; position: fixed; top: 30px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; z-index: 9999; background: white; padding: 15px 20px; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); border: 1px solid #e2e8f0; text-align: left; align-items: center; gap: 15px;">
        <div id="resultIcon" style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 24px;">
            <i data-lucide="check-circle" style="width: 28px; height: 28px;"></i>
        </div>
        <div style="flex: 1; min-width: 0;">
            <div id="resultTitle" style="font-weight: 800; font-size: 1.1rem; margin-bottom: 3px; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Nama</div>
            <div id="resultDesc" style="font-size: 0.85rem; color: #64748b;">Keterangan</div>
        </div>
    </div>
</div>

<!-- Audio Beep -->
<audio id="ttsAudioFallback" preload="none"></audio>

<script src="/public/assets/js/html5-qrcode.min.js"></script>
<script>
    // Web Audio API Beep Generator
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playBeep(type = 'success') {
        if(audioCtx.state === 'suspended') audioCtx.resume();
        
        if(type === 'success') {
            const types = ['sine', 'triangle', 'square'];
            const randType = types[Math.floor(Math.random() * types.length)];
            const notes = [
                [523.25, 659.25, 783.99, 1046.50],
                [440.00, 554.37, 659.25, 880.00],
                [587.33, 739.99, 880.00, 1174.66],
                [659.25, 830.61, 987.77, 1318.51]
            ];
            const randChord = notes[Math.floor(Math.random() * notes.length)];
            
            randChord.forEach((freq, index) => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                
                osc.type = randType;
                const maxGain = (randType === 'square') ? 0.1 : 0.3;
                
                gain.gain.setValueAtTime(maxGain, audioCtx.currentTime + (index * 0.08));
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + (index * 0.08) + 0.15);
                
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime + (index * 0.08));
                
                osc.start(audioCtx.currentTime + (index * 0.08));
                osc.stop(audioCtx.currentTime + (index * 0.08) + 0.2);
            });
        } else {
            const osc1 = audioCtx.createOscillator();
            const osc2 = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            
            osc1.connect(gainNode);
            osc2.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            osc1.type = 'sawtooth';
            osc2.type = 'square';
            
            osc1.frequency.setValueAtTime(200, audioCtx.currentTime);
            osc2.frequency.setValueAtTime(215, audioCtx.currentTime);
            
            gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.8);
            
            osc1.start(audioCtx.currentTime);
            osc2.start(audioCtx.currentTime);
            osc1.stop(audioCtx.currentTime + 0.8);
            osc2.stop(audioCtx.currentTime + 0.8);
        }
    }

    const statusBadge = document.getElementById('statusBadge');
    const resultPopup = document.getElementById('resultPopup');
    const resultIcon = document.getElementById('resultIcon');
    const resultTitle = document.getElementById('resultTitle');
    const resultDesc = document.getElementById('resultDesc');
    const flipBtn = document.getElementById('flipBtn');

    let isProcessing = false;
    let html5QrCode = null;
    let currentFacingMode = "environment";
    const mode = "<?= $mode ?>";
    
    const aiVoiceEnabled = <?= $ai_voice_enabled ? 'true' : 'false' ?>;
    const aiVoicePitch = <?= $ai_voice_pitch ?>;
    const aiVoiceRate = <?= $ai_voice_rate ?>;

    const ttsData = {
        guru_masuk: <?= json_encode($tts_guru_masuk) ?>,
        guru_pulang: <?= json_encode($tts_guru_pulang) ?>,
        guru_telat: <?= json_encode($tts_guru_telat) ?>,
        guru_sudah: <?= json_encode($tts_guru_sudah) ?>,
        siswa_masuk: <?= json_encode($tts_siswa_masuk) ?>,
        siswa_pulang: <?= json_encode($tts_siswa_pulang) ?>,
        siswa_telat: <?= json_encode($tts_siswa_telat) ?>,
        siswa_sudah: <?= json_encode($tts_siswa_sudah) ?>,
        gagal: <?= json_encode($tts_gagal) ?>
    };

    function getWaktuSapaan() {
        const hour = new Date().getHours();
        if (hour < 11) return 'pagi';
        if (hour < 15) return 'siang';
        if (hour < 18) return 'sore';
        return 'malam';
    }

    function getRandomTts(arrayName, nama, desc = '') {
        const arr = ttsData[arrayName];
        if (!arr || arr.length === 0) return '';
        let text = arr[Math.floor(Math.random() * arr.length)];
        const waktu = getWaktuSapaan();
        return text.replace(/\[nama\]/g, nama).replace(/\[desc\]/g, desc).replace(/\[waktu\]/g, waktu);
    }

    function cleanNameForTTS(name) {
        name = name.replace(/,\s*[S|M|A|D]\.[A-Za-z\.]+/g, '');
        name = name.toLowerCase();
        name = name.replace(/\bmoh\.\b/g, 'mohamad');
        name = name.replace(/\bm\.\b/g, 'muhammad');
        name = name.replace(/\ba\.\b/g, 'ahmad');
        name = name.replace(/'/g, '');
        return name.trim();
    }

    function showResult(title, desc, isSuccess, personType = 'siswa') {
        resultTitle.innerText = title;
        resultDesc.innerText = desc;
        resultPopup.style.display = 'flex';
        
        if (isSuccess) {
            resultIcon.style.background = '#dcfce7';
            resultIcon.style.color = '#15803d';
            resultIcon.innerHTML = '<i data-lucide="check-circle" style="width: 28px; height: 28px;"></i>';
        } else {
            resultIcon.style.background = '#fee2e2';
            resultIcon.style.color = '#b91c1c';
            resultIcon.innerHTML = '<i data-lucide="x-circle" style="width: 28px; height: 28px;"></i>';
        }
        lucide.createIcons();

        try {
            playBeep(isSuccess ? 'success' : 'error');
        } catch(e) {}

        try {
            let textToSpeak = "";
            let rawNama = title.split(' - ')[0] || title;
            const nama = cleanNameForTTS(rawNama);
            
            if (isSuccess) {
                if (personType === 'guru') {
                    if (desc.includes('Pulang')) textToSpeak = getRandomTts('guru_pulang', nama, desc);
                    else if (desc.includes('Terlambat')) textToSpeak = getRandomTts('guru_telat', nama, desc);
                    else if (desc.includes('Sudah')) textToSpeak = getRandomTts('guru_sudah', nama, desc);
                    else textToSpeak = getRandomTts('guru_masuk', nama, desc);
                } else {
                    if (desc.includes('Pulang')) textToSpeak = getRandomTts('siswa_pulang', nama, desc);
                    else if (desc.includes('Terlambat')) textToSpeak = getRandomTts('siswa_telat', nama, desc);
                    else if (desc.includes('Sudah')) textToSpeak = getRandomTts('siswa_sudah', nama, desc);
                    else textToSpeak = getRandomTts('siswa_masuk', nama, desc);
                }
            } else {
                textToSpeak = getRandomTts('gagal', nama, desc);
            }
            
            if (aiVoiceEnabled && textToSpeak !== "") {
                speakTextFallback(textToSpeak);
            }
        } catch(e) {}

        setTimeout(() => {
            resultPopup.style.display = 'none';
            isProcessing = false; 
            statusBadge.innerHTML = '<i data-lucide="scan"></i> Siap Scan...';
            lucide.createIcons();
        }, 2500);
    }

    function handleScanSuccess(decodedText) {
        if(isProcessing) return;
        isProcessing = true;
        
        statusBadge.innerHTML = '<i data-lucide="loader-2" class="lucide-spin"></i> Sinkronisasi...';
        lucide.createIcons();

        fetch('/apk/api/scan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nis: decodedText, mode: mode })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const personType = data.type || (mode === 'guru' ? 'guru' : 'siswa');
                showResult(data.data.nama, (data.data.kelas ? data.data.kelas + '   ' : '') + data.data.status, true, personType);
            } else {
                showResult("Gagal!", data.message, false);
            }
        })
        .catch(err => {
            showResult("Error", "Koneksi ke server terputus", false);
        });
    }

    function initScanner() {
        if(html5QrCode) {
            html5QrCode.stop().then(() => startScanning()).catch(err => startScanning());
        } else {
            html5QrCode = new Html5Qrcode("reader");
            startScanning();
        }
    }

    function startScanning() {
        html5QrCode.start(
            { facingMode: currentFacingMode },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            handleScanSuccess
        ).then(() => {
            statusBadge.innerHTML = '<i data-lucide="scan"></i> Siap Scan...';
            lucide.createIcons();
            let videoEl = document.querySelector('#reader video');
            if (videoEl) {
                if (currentFacingMode === "user") {
                    videoEl.style.transform = "scaleX(-1)";
                } else {
                    videoEl.style.transform = "scaleX(1)";
                }
            }
        }).catch(err => {
            statusBadge.innerHTML = '<i data-lucide="alert-circle"></i> Kamera gagal diakses!';
            lucide.createIcons();
        });
    }

    flipBtn.addEventListener('click', () => {
        currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
        statusBadge.innerHTML = '<i data-lucide="refresh-cw"></i> Memutar kamera...';
        lucide.createIcons();
        initScanner();
    });

    function speakTextFallback(textToSpeak) {
        const playGoogleTTS = () => {
            try {
                const url = "https://translate.googleapis.com/translate_tts?client=gtx&ie=UTF-8&tl=id&q=" + encodeURIComponent(textToSpeak);
                let audioEl = document.getElementById('ttsAudioFallback');
                if (!audioEl) {
                    audioEl = document.createElement('audio');
                    audioEl.id = 'ttsAudioFallback';
                    document.body.appendChild(audioEl);
                }
                audioEl.src = url;
                if (aiVoiceRate !== 1.0) audioEl.playbackRate = aiVoiceRate;
                audioEl.play().catch(e => {});
            } catch(e) {}
        };

        if ('speechSynthesis' in window) {
            let voices = window.speechSynthesis.getVoices();
            if (voices.length === 0 && navigator.userAgent.includes('wv')) {
                playGoogleTTS();
            } else {
                try {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(textToSpeak);
                    utterance.lang = 'id-ID';
                    utterance.rate = aiVoiceRate;
                    utterance.pitch = aiVoicePitch;
                    utterance.onerror = (e) => playGoogleTTS();
                    window.speechSynthesis.speak(utterance);
                } catch (e) {
                    playGoogleTTS();
                }
            }
        } else {
            playGoogleTTS();
        }
    }

    document.getElementById('testVoiceBtn').addEventListener('click', () => {
        playBeep('success');
        if (!aiVoiceEnabled) return;
        const texts = mode === 'guru' 
            ? ["Halo Bapak Ibu, sistem tes suara AI sudah aktif."] 
            : ["Halo ges, suara AI udah aktif nih!"];
        speakTextFallback(texts[Math.floor(Math.random() * texts.length)]);
    });

    window.onload = () => {
        initScanner();
    };
</script>
