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

    <div id="success-modal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(2px); z-index: 9999; align-items: center; justify-content: center; padding: 18px;">
        <div style="background: #0b1224; color: #f8fafc; border: 1px solid #1f2937; border-radius: 16px; padding: 18px 18px 14px; width: min(360px, 100%); box-shadow: 0 24px 50px rgba(0,0,0,0.35); text-align: left;">
            <div style="font-weight: 800; font-size: 16px; margin-bottom: 8px;">Scan berhasil</div>
            <div id="modal-code" style="font-weight: 700; color: #34d399; margin-bottom: 6px;"></div>
            <div id="modal-name" style="margin-bottom: 6px;"></div>
            <div id="modal-price" style="color: #facc15; font-weight: 700;"></div>
            <div style="margin-top: 14px; text-align: right;">
                <button id="modal-close" style="background: #2563eb; color: #fff; border: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; cursor: pointer;">Scan berikutnya</button>
            </div>
        </div>
    </div>

    @php
        // Gunakan path relatif agar tidak terpengaruh APP_URL di hosting.
        $scanUrl = route('kasir.scan.store', [], false);
    @endphp

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const statusMsg = document.getElementById('status-msg');
        const debugMsg = document.getElementById('debug-msg');
        const successModal = document.getElementById('success-modal');
        const modalCode = document.getElementById('modal-code');
        const modalName = document.getElementById('modal-name');
        const modalPrice = document.getElementById('modal-price');
        const modalClose = document.getElementById('modal-close');

        function updateStatus(text, color = '#f8fafc') {
            statusMsg.textContent = text;
            statusMsg.style.color = color;
        }

        const showDebug = (text) => {
            if (debugMsg) {
                debugMsg.textContent = text || '';
            }
        };

        const formatCurrency = (value) => {
            const number = Number(value || 0);
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(number);
        };

        const showSuccessModal = ({ code, name, price }) => {
            if (!successModal) return;
            modalCode.textContent = code ? `Kode: ${code}` : '';
            modalName.textContent = name ? `Nama: ${name}` : '';
            modalPrice.textContent = typeof price !== 'undefined' ? `Harga: ${formatCurrency(price)}` : '';
            successModal.style.display = 'flex';
        };

        const hideSuccessModal = () => {
            if (!successModal) return;
            successModal.style.display = 'none';
        };

        async function sendCode(decodedText) {
            let parsedCode = decodedText;
            try {
                const parsed = JSON.parse(decodedText);
                if (parsed && typeof parsed === 'object' && parsed.code) {
                    parsedCode = parsed.code;
                }
            } catch (e) {
                // biarkan parsedCode tetap decodedText jika bukan JSON
            }

            updateStatus('Mengirim: ' + parsedCode + ' ...', '#facc15');
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
                    body: JSON.stringify({ code: parsedCode }),
                });

                let rawText = '';
                try { rawText = await response.clone().text(); } catch (e) { rawText = ''; }
                lastRaw = rawText;
                let statusCode = response.status;
                lastStatus = statusCode;

                if (!response.ok) {
                    const detail = rawText.slice(0, 400);
                    if (statusCode === 422) {
                        let parsed422;
                        try { parsed422 = JSON.parse(rawText); } catch (e) { parsed422 = {}; }
                        const msg422 = parsed422 && parsed422.message ? parsed422.message : detail;
                        throw new Error('422: ' + msg422);
                    }
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

                const productName = data.product && data.product.name ? data.product.name : '';
                const productPrice = data.product && typeof data.product.price !== 'undefined' ? data.product.price : null;
                const successText = productName
                    ? `Berhasil: ${parsedCode} · ${productName}`
                    : `Berhasil: ${parsedCode}`;

                updateStatus(successText, '#4ade80');
                showDebug(productName ? `Ditambahkan: ${productName}` : ''); // info item berhasil masuk

                showSuccessModal({ code: parsedCode, name: productName, price: productPrice });

                try {
                    if (window.html5QrcodeScanner && typeof html5QrcodeScanner.pause === 'function') {
                        html5QrcodeScanner.pause();
                    }
                } catch (e) {
                    console.warn('Gagal pause scanner:', e);
                }
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

        if (modalClose) {
            modalClose.addEventListener('click', () => {
                hideSuccessModal();
                if (window.html5QrcodeScanner && typeof html5QrcodeScanner.resume === 'function') {
                    updateStatus('Siap scan...');
                    try {
                        html5QrcodeScanner.resume();
                    } catch (e) {
                        console.warn('Gagal resume scanner:', e);
                    }
                }
            });
        }
    </script>
</body>
</html>
