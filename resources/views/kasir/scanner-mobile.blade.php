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
    <div class="muted">Pastikan kamera diizinkan.</div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const statusMsg = document.getElementById('status-msg');

        function updateStatus(text, color = '#f8fafc') {
            statusMsg.textContent = text;
            statusMsg.style.color = color;
        }

        async function sendCode(decodedText) {
            updateStatus('Mengirim: ' + decodedText + ' ...', '#facc15');
            try {
                const response = await fetch("{{ route('kasir.scan.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ code: decodedText }),
                });

                if (!response.ok) {
                    if (response.status === 404) throw new Error('Route /scan-item tidak ditemukan (404). Pastikan web.php sudah terdeploy.');
                    if (response.status === 401) throw new Error('Belum login (401). Silakan login ulang.');
                    if (response.status === 419) throw new Error('Sesi/CSRF kedaluwarsa (419). Refresh halaman dan login ulang.');
                    if (response.status === 500) {
                        const errData = await response.json().catch(() => ({}));
                        throw new Error(errData.message || 'Error server (500).');
                    }
                    throw new Error('Gagal: HTTP ' + response.status);
                }

                const data = await response.json().catch(() => ({}));
                if (data.status !== 'success') {
                    throw new Error(data.message || 'Gagal mengirim scan (respons tidak success).');
                }

                updateStatus('Berhasil: ' + decodedText, '#4ade80');
                html5QrcodeScanner.pause();
                setTimeout(() => {
                    updateStatus('Siap scan...');
                    html5QrcodeScanner.resume();
                }, 1200);
            } catch (error) {
                console.error(error);
                updateStatus('Gagal: ' + error.message, '#f87171');
                alert(error.message);
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
