@extends('layouts.gudang')

@section('title', 'Label QR Produk')

@push('styles')
    <style>
        .qr-page__header {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .qr-page__header h1 {
            margin: 0 0 4px;
        }

        .qr-page__header p {
            margin: 0;
        }

        .qr-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .qr-card__grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            align-items: start;
            padding: 6px;
            justify-content: center;
            justify-items: center;
            max-width: 840px;
            margin: 0 auto;
        }

        @media (max-width: 960px) {
            .qr-card__grid {
                grid-template-columns: 1fr;
            }
        }

        .qr-label {
            background: linear-gradient(180deg, #f8fafc 0%, #e5f0ff 100%);
            border: 1px solid #d5deeb;
            border-radius: 14px;
            padding: 12px 14px;
            display: grid;
            gap: 8px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            width: 100%;
            max-width: 780px;
        }

        .qr-label__title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            }

        .qr-label__title strong {
            letter-spacing: 0.4px;
        }

        .qr-label__badge {
            display: inline-flex;
            padding: 6px 12px;
            background: #1d4ed8;
            color: #fff;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .qr-label__name {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: #0f172a;
        }

        .qr-label__meta {
            display: grid;
            grid-template-columns: repeat(4, minmax(150px, 1fr));
            gap: 10px 12px;
        }

        @media (max-width: 820px) {
            .qr-label__meta {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }

        .qr-label__meta-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 10px;
        }

        .qr-label__meta-item span {
            display: block;
            font-size: 12px;
            color: #475467;
            margin-bottom: 4px;
        }

        .qr-label__meta-item strong {
            font-size: 15px;
            color: #0f172a;
        }

        .qr-figure {
            display: grid;
            gap: 8px;
            justify-items: center;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            max-width: 380px;
            margin: 0 auto;
            width: 100%;
        }

        .qr-figure__box {
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 12px;
            box-shadow:
                inset 0 1px 0 rgba(148, 163, 184, 0.2),
                0 8px 20px rgba(15, 23, 42, 0.05);
            width: 100%;
            max-width: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-figure__box svg {
            width: 100%;
            height: auto;
            max-width: 210px;
        }

        .qr-figure__caption {
            font-size: 11px;
            color: #475467;
            margin: 0;
            text-align: center;
        }

        /* Tombol hijau untuk simpan JPEG */
        .chip-button--success {
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            color: #ffffff;
            border: 1px solid #16a34a;
            box-shadow: 0 10px 24px rgba(34, 197, 94, 0.25);
        }

        .chip-button--success:hover {
            filter: brightness(1.05);
            box-shadow: 0 12px 28px rgba(34, 197, 94, 0.28);
        }

        .chip-button--success:active {
            box-shadow: 0 6px 16px rgba(34, 197, 94, 0.24);
        }

        .qr-payload {
            width: 100%;
            max-width: 280px;
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 11px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
            margin: 0 auto;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .sidebar,
            .qr-page__header,
            .qr-actions,
            .flash {
                display: none !important;
            }

            .content {
                margin: 0;
                padding: 12px;
                width: 100%;
                max-width: 100%;
            }

            .card {
                box-shadow: none;
                border: 1px solid #cbd5e1;
            }

            .qr-payload {
                white-space: normal;
                word-break: break-all;
            }
        }
    </style>
@endpush

@section('content')
    <div class="qr-page__header">
        <div>
            <h1 class="page-title">Label QR Produk</h1>
            <p class="muted">QR Code siap dipindai untuk mempercepat transaksi dan pengecekan stok.</p>
        </div>
        <div class="qr-actions">
            <button type="button" id="btn-print-label" class="chip-button chip-button--blue">Cetak Label</button>
            <button type="button" id="btn-download-qr" class="chip-button chip-button--success">Simpan JPEG</button>
            <a href="{{ route('gudang.products.show', $product) }}" class="chip-button chip-button--gray">Kembali ke Detail</a>
            <a href="{{ route('gudang.products.index') }}" class="chip-button chip-button--yellow">Ke Daftar Produk</a>
        </div>
    </div>

    <div class="card" style="padding: 26px;">
        <div class="qr-card__grid">
            <div class="qr-label">
                <div class="qr-label__title">
                    <strong>Kode Label: {{ $labelCode }}</strong>
                    <span class="qr-label__badge">Scan Ready</span>
                </div>
                <p class="qr-label__name">{{ $product->name }}</p>
                <div class="qr-label__meta">
                    <div class="qr-label__meta-item">
                        <span>SKU/Kode</span>
                        <strong>{{ $labelCode }}</strong>
                    </div>
                    <div class="qr-label__meta-item">
                        <span>Kategori</span>
                        <strong>{{ optional($product->category)->name ?? 'Tanpa Kategori' }}</strong>
                    </div>
                    <div class="qr-label__meta-item">
                        <span>Satuan</span>
                        <strong>{{ $product->unit }}</strong>
                    </div>
                    <div class="qr-label__meta-item">
                        <span>Harga Default</span>
                        <strong>Rp{{ number_format($product->price, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <div class="qr-figure" data-product-name="{{ $product->name }}" data-label-code="{{ $labelCode }}">
                <div class="qr-figure__box" aria-label="QR Code {{ $product->name }}">
                    {!! $qrSvg !!}
                </div>
                <p class="qr-figure__caption">Nama akan ikut di JPEG: {{ $product->name }}</p>
                <div class="qr-payload" title="{{ $qrPayload }}">
                    {{ $qrPayload }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const printButton = document.getElementById('btn-print-label');
            const downloadButton = document.getElementById('btn-download-qr');
            const figure = document.querySelector('.qr-figure');
            const qrBox = document.querySelector('.qr-figure__box');

            if (printButton) {
                printButton.addEventListener('click', function () {
                    window.print();
                });
            }

            if (downloadButton && figure && qrBox) {
                downloadButton.addEventListener('click', function () {
                    const svg = qrBox.querySelector('svg');
                    if (!svg) {
                        alert('QR belum siap diekspor.');
                        return;
                    }

                    downloadButton.disabled = true;
                    downloadButton.textContent = 'Menyiapkan...';

                    const productName = figure.dataset.productName || 'Produk';
                    const labelCode = figure.dataset.labelCode || 'QR';
                    const serializer = new XMLSerializer();
                    const svgString = serializer.serializeToString(svg);
                    const svgDataUrl = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgString);
                    const img = new Image();
                    img.onload = () => {
                        const qrSize = 520;
                        const padding = 28;
                        const captionHeight = 130; // lebih tinggi agar teks jauh lebih besar
                        const width = qrSize + padding * 2;
                        const height = qrSize + padding * 2 + captionHeight;
                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        if (!ctx) {
                            alert('Canvas tidak tersedia di browser ini.');
                            downloadButton.disabled = false;
                            downloadButton.textContent = 'Simpan JPEG';
                            return;
                        }

                        // latar putih agar JPEG siap dicetak dan tidak transparan
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, width, height);

                        const qrX = (width - qrSize) / 2;
                        ctx.drawImage(img, qrX, padding, qrSize, qrSize);

                        const caption = productName.length > 60 ? productName.slice(0, 57) + '...' : productName;
                        ctx.fillStyle = '#111827';
                        ctx.font = '900 40px "Segoe UI", Arial, sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(caption, width / 2, qrSize + padding + captionHeight / 2);

                        const link = document.createElement('a');
                        link.download = `${labelCode || 'QR'}.jpg`;
                        link.href = canvas.toDataURL('image/jpeg', 0.92);
                        link.click();

                        downloadButton.disabled = false;
                        downloadButton.textContent = 'Simpan JPEG';
                    };
                    img.onerror = () => {
                        alert('Gagal memuat QR untuk diekspor.');
                        downloadButton.disabled = false;
                        downloadButton.textContent = 'Simpan JPEG';
                    };
                    img.src = svgDataUrl;
                });
            }
        });
    </script>
@endpush
