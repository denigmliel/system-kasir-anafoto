<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mobile Scanner</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            text-align: center;
        }
        h3 { margin: 0 0 12px; }
        .instruction { margin: 0 0 16px; color: #cbd5e1; font-size: 14px; }
        #reader { width: 100%; max-width: 520px; margin: 0 auto; border-radius: 14px; overflow: hidden; border: 2px solid #f8fafc; }
        .status { margin-top: 16px; font-size: 16px; font-weight: 700; color: #4ade80; min-height: 24px; }
        .muted { color: #cbd5e1; font-size: 13px; margin-top: 10px; }
    </style>
</head>
<body>
    <h3>ANA FOTOCOPY SCANNER</h3>
    <p class="instruction">Login dengan akun kasir yang sama di HP & komputer.</p>

    <div id="reader"></div>
    <div id="status-msg" class="status">Siap scan...</div>
    <div id="debug-msg" class="muted" style="word-break: break-all;"></div>
    <div class="muted">Pastikan kamera diizinkan.</div>

    @php
        // Gunakan path relatif agar tidak terpengaruh APP_URL di hosting.
        $scanUrl = route('kasir.scan.store', [], false);
    @endphp

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const statusMsg = document.getElementById('status-msg');
        const debugMsg = document.getElementById('debug-msg');

        function updateStatus(text, color = '#f8fafc') {
            statusMsg.textContent = text;
            statusMsg.style.color = color;
        }

        const showDebug = (text) => {
            if (debugMsg) {
                debugMsg.textContent = text || '';
            }
        };

        async function sendCode(decodedText) {
            updateStatus('Mengirim: ' + decodedText + ' ...', '#facc15');
            showDebug('');
            let lastStatus = null;
            let lastRaw = '';
            try {
                const response = await fetch("{{ $scanUrl }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ code: decodedText }),
                });

                let rawText = '';
                try { rawText = await response.clone().text(); } catch (e) { rawText = ''; }
                lastRaw = rawText;
                let statusCode = response.status;
                lastStatus = statusCode;

                if (!response.ok) {
                    const detail = rawText.slice(0, 400);
                    if (statusCode === 404) throw new Error('404: scan-item tidak ditemukan. Deploy kode terbaru & route:clear. Detail: ' + detail);
                    if (statusCode === 401) throw new Error('401: Belum login. Login ulang di domain yang sama.');
                    if (statusCode === 419) throw new Error('419: Sesi/CSRF kedaluwarsa. Refresh halaman & login ulang.');
                    if (statusCode === 500) {
                        let parsed;
                        try { parsed = JSON.parse(rawText); } catch (e) { parsed = {}; }
                        throw new Error(parsed.message || ('500: Error server. ' + detail));
                    }
                    throw new Error('HTTP ' + statusCode + '. ' + detail);
                }

                let data = {};
                try {
                    data = await response.json();
                } catch (e) {
                    showDebug('Raw response: ' + rawText.slice(0, 400));
                    throw new Error('Respons bukan JSON. Periksa log server / Network tab.');
                }

                if (!data || data.status !== 'success') {
                    showDebug('Raw response: ' + rawText.slice(0, 400));
                    throw new Error((data && data.message) || 'Gagal mengirim scan (respons tidak success).');
                }

                updateStatus('Berhasil: ' + decodedText, '#4ade80');
                html5QrcodeScanner.pause();
                setTimeout(() => {
                    updateStatus('Siap scan...');
                    html5QrcodeScanner.resume();
                }, 1200);
            } catch (error) {
                console.error(error);
                const rawSnippet = (lastRaw || '').slice(0, 400);
                const msg = error && error.message ? error.message : 'Terjadi kesalahan saat mengirim. Lihat debug di bawah.';
                updateStatus('Gagal: ' + msg, '#f87171');
                showDebug('Debug: ' + msg + (lastStatus ? ' | HTTP ' + lastStatus : '') + (rawSnippet ? ' | Raw: ' + rawSnippet : ''));
                alert(msg + (lastStatus ? '\nStatus: ' + lastStatus : '') + (rawSnippet ? '\nRaw: ' + rawSnippet : ''));
                setTimeout(() => updateStatus('Siap scan...'), 1500);
            }
        }

        function onScanSuccess(decodedText) {
            if (!decodedText) {
                return;
            }
            sendCode(decodedText);
        }

        const html5QrcodeScanner = new Html5QrcodeScanner('reader', {
            fps: 10,
            qrbox: 250,
        });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>
