@extends('layouts.app')

@section('title', 'Point of Sale')

@push('styles')
    <style>
        .form-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .form-control,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            background-color: #fff;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-control:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #1e3c72;
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .input-error {
            margin-top: 6px;
            font-size: 13px;
            color: #b42318;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.1s, box-shadow 0.1s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            box-shadow: 0 8px 16px rgba(30, 60, 114, 0.2);
        }

        .btn-secondary {
            background-color: #e4e7ec;
            color: #1f2937;
        }

        .btn-danger {
            background-color: #f97066;
            color: #fff;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
        }

        .low-stock-alert {
            display: none;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid #facc15;
            background-color: #fef9c3;
            color: #854d0e;
        }

        .low-stock-alert.is-visible {
            display: flex;
        }

        .low-stock-alert__icon {
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #facc15;
            color: #854d0e;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .low-stock-alert__title {
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .low-stock-alert__list {
            margin: 0;
            padding-left: 18px;
            list-style: disc;
            font-size: 13px;
            line-height: 1.5;
        }

        .editing-notice {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1px solid #fcd34d;
            background-color: #fffbeb;
            color: #92400e;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .editing-notice__actions {
            display: inline-flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .items-table-wrapper {
            position: relative;
            overflow: visible;
        }

        .items-list {
            width: 100%;
        }

        .items-header,
        .item-row {
            display: grid;
            grid-template-columns: 220px minmax(260px, 1fr) 140px 110px 110px 64px;
            gap: 8px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .items-header,
            .item-row {
                grid-template-columns: minmax(180px, 1fr);
            }
            .items-header > .items-header-cell:not(:first-child) {
                display: none;
            }
        .item-row {
            gap: 12px;
        }
        .item-row .item-cell--unit,
        .item-row .item-cell--quantity,
        .item-row .item-cell--subtotal,
        .item-row .item-cell--actions {
            display: flex;
        }
        .item-row .item-cell--unit,
        .item-row .item-cell--quantity,
        .item-row .item-cell--subtotal,
        .item-row .item-cell--actions {
            margin-top: 4px;
        }
        }

        .items-header {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 14px 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .items-header-cell {
            font-size: 14px;
        }

        .items-header-cell--right {
            text-align: right;
        }

        .items-body-scroll {
            margin-top: 12px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background-color: #fff;
            max-height: 360px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 6px;
        }

        .items-body {
            display: flex;
            flex-direction: column;
            border-radius: 12px;
            background-color: #fff;
            overflow: visible;
        }

        .item-row {
            padding: 18px;
            background-color: #fff;
            border-bottom: 1px solid #eef2f7;
            position: relative;
        }

        .item-row:first-child {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .item-row:last-child {
            border-bottom: none;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .item-cell {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .item-cell-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 0.03em;
        }

        @media (min-width: 1025px) {
            .item-cell-label {
                display: none;
            }
            .item-cell {
                justify-content: center;
            }
            .item-cell--subtotal,
            .item-cell--actions {
                justify-content: flex-end;
            }
        }

        .item-cell select,
        .item-cell input {
            width: 100%;
        }

        .item-cell--product .product-select-wrapper {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .item-cell--quantity {
            max-width: 150px;
        }

        .item-cell--unit {
            max-width: 160px;
        }

        .item-cell--subtotal {
            align-items: center;
            justify-content: flex-end;
            font-weight: 600;
            color: #1f2937;
            text-align: right;
        }

        .item-cell--actions {
            align-items: flex-end;
            justify-content: center;
            text-align: right;
        }

        .unit-select:disabled {
            background-color: #f5f7fb;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .item-subtotal {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            min-height: 44px;
        }

        @media (max-width: 1024px) {
            .item-cell--quantity,
            .item-cell--subtotal,
            .item-cell--actions {
                max-width: none;
                text-align: left;
            }

            .item-cell--subtotal {
                align-items: flex-start;
            }

            .item-cell--actions {
                align-items: flex-start;
            }
        }

        .product-select-wrapper,
        .category-select-wrapper {
            position: relative;
        }

        .product-select-wrapper .product-search-input,
        .category-select-wrapper .category-search-input {
            display: none;
            margin-bottom: 8px;
        }

        .product-select-wrapper--enhanced .product-search-input,
        .category-select-wrapper--enhanced .category-search-input {
            display: block;
        }

        .product-select-wrapper--enhanced .product-select,
        .category-select-wrapper--enhanced .category-select {
            display: none;
        }

        .product-suggestions,
        .category-suggestions {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            display: none;
            max-height: 240px;
            overflow-y: auto;
            padding: 6px;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            background-color: #fff;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
            z-index: 30;
        }

        .product-suggestions.is-visible,
        .category-suggestions.is-visible {
            display: block;
        }

        .product-suggestion,
        .category-suggestion {
            width: 100%;
            padding: 8px 10px;
            background: transparent;
            border: none;
            border-radius: 6px;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.1s ease;
        }

        .product-suggestion:hover,
        .product-suggestion.is-active,
        .category-suggestion:hover,
        .category-suggestion.is-active {
            background-color: #f1f5ff;
        }

        .product-suggestion-label,
        .category-suggestion-label {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
        }

        .product-suggestion-meta,
        .category-suggestion-meta {
            margin-top: 2px;
            font-size: 12px;
            color: #6b7280;
        }

        .product-suggestion-empty,
        .category-suggestion-empty {
            font-size: 13px;
            color: #6b7280;
            padding: 8px 10px;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .btn-icon svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        .items-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            gap: 12px;
        }

        .subtotal-display {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }

        .currency-input {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border: 1px solid #d0d5dd;
            border-radius: 8px;
            background-color: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .currency-input:focus-within {
            border-color: #1e3c72;
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
        }

        .currency-prefix {
            font-weight: 600;
            color: #475467;
        }

        .currency-input input {
            border: none;
            outline: none;
            background: transparent;
            flex: 1;
            font-size: 14px;
        }

        .items-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .items-toolbar__actions {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .smart-scan-layer {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            z-index: 1500;
        }

        .smart-scan-layer.is-visible {
            display: flex;
        }

        .smart-scan-layer__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(3px);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .smart-scan-layer.is-visible .smart-scan-layer__backdrop {
            opacity: 1;
        }

        .smart-scan {
            position: relative;
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 14px;
            width: min(460px, 100%);
            max-width: 460px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 70px rgba(15, 23, 42, 0.16);
            border-radius: 20px;
            padding: 16px 18px 18px;
            z-index: 1;
            animation: floatIn 0.22s ease;
        }

        @keyframes floatIn {
            from {
                transform: translateY(12px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .smart-scan.is-visible {
            display: grid;
        }

        .smart-scan__header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .smart-scan__title-group {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .smart-scan__title {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
        }

        .smart-scan__desc {
            margin: 2px 0 0;
            color: #475467;
            font-size: 13px;
        }

        .smart-scan__actions {
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }

        .smart-scan__body {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 12px;
            margin-top: 4px;
        }

        .smart-scan__video {
            position: relative;
            width: 100%;
            min-height: 260px;
            border-radius: 16px;
            overflow: hidden;
            background: radial-gradient(circle at 30% 30%, #eef2ff 0%, #e2e8f0 45%, #d7dde5 100%);
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9), inset 0 0 0 1px rgba(226, 232, 240, 0.5);
        }

        .smart-scan__video video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .smart-scan__video video.smart-scan__video--unmirror {
            transform: scaleX(-1);
        }

        .smart-scan__frame {
            position: absolute;
            inset: 14%;
            border-radius: 18px;
            box-shadow:
                0 0 0 1px rgba(16, 185, 129, 0.45),
                0 0 0 9px rgba(16, 185, 129, 0.08);
            pointer-events: none;
            z-index: 2;
        }

        .smart-scan__frame-corner {
            position: absolute;
            width: 34px;
            height: 34px;
            border-radius: 12px;
            border: 3px solid #22c55e;
            filter: drop-shadow(0 6px 14px rgba(34, 197, 94, 0.25));
        }

        .smart-scan__frame-corner--tl { top: -3px; left: -3px; border-right: 0; border-bottom: 0; }
        .smart-scan__frame-corner--tr { top: -3px; right: -3px; border-left: 0; border-bottom: 0; }
        .smart-scan__frame-corner--bl { bottom: -3px; left: -3px; border-right: 0; border-top: 0; }
        .smart-scan__frame-corner--br { bottom: -3px; right: -3px; border-left: 0; border-top: 0; }

        .smart-scan__pulse {
            position: absolute;
            inset: 10%;
            border: 1px solid rgba(16, 185, 129, 0.35);
            border-radius: 18px;
            pointer-events: none;
            animation: pulse 2s infinite;
            z-index: 1;
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 0.15; }
            100% { opacity: 0.6; }
        }

        .smart-scan__status-block {
            display: grid;
            gap: 6px;
        }

        .smart-scan__status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .smart-scan__status[data-variant="error"] {
            background: #fef2f2;
            color: #b42318;
            border: 1px solid #fecdd3;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .smart-scan__status[data-variant="success"] {
            background: #ecfdf3;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .smart-scan__hint {
            font-size: 12px;
            color: #526274;
            margin: 0;
        }

        .smart-scan__badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 12px;
            background: #e0f2fe;
            color: #0b4f6c;
            border-radius: 999px;
            font-size: 12px;
            letter-spacing: 0.2px;
            font-weight: 700;
            border: 1px solid #bae6fd;
            box-shadow: 0 6px 16px rgba(14, 116, 144, 0.12);
        }

        .smart-scan__badge svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        .smart-scan__status-icon {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .smart-scan__status-icon svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        @media (max-width: 640px) {
            .smart-scan {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <h1 class="page-title">Point of Sale</h1>

    <div class="grid" style="gap: 24px; grid-template-columns: minmax(0, 1fr);">
        <div class="card">
            <h2 style="margin: 0 0 12px; font-size: 20px;">
                {{ $editingTransaction ? 'Perbarui Transaksi' : 'Transaksi Baru' }}
            </h2>
            <p class="muted" style="margin-bottom: 20px;">
                @if ($editingTransaction)
                    Anda sedang mengubah transaksi <strong>{{ $editingTransaction['code'] ?? '' }}</strong>. Sesuaikan item atau pembayaran lalu simpan untuk memperbarui data.
                @endif
            </p>

            @if ($editingTransaction)
                <div class="editing-notice">
                    <div>
                        <div style="font-weight: 600; margin-bottom: 4px;">
                            Mengedit transaksi {{ $editingTransaction['code'] ?? '' }}
                        </div>
                        <div style="font-size: 14px;">
                            Tambahkan produk atau ubah jumlah, kemudian simpan untuk memperbarui struk.
                        </div>
                    </div>
                    <div class="editing-notice__actions">
                        <a
                            href="{{ route('kasir.transaction.print', $editingTransaction['id']) }}"
                            class="btn btn-secondary"
                            target="_blank"
                        >
                            Cetak Terakhir
                        </a>
                        <a href="{{ route('kasir.pos') }}" class="btn btn-danger">
                            Batalkan Edit
                        </a>
                    </div>
                </div>
            @endif

            @php
                $defaultPaymentMethod = $editingTransaction['payment_method'] ?? 'cash';
                $paymentSeed = $editingTransaction['payment_amount'] ?? null;
                $prefilledItems = $editingTransaction['items'] ?? null;
                $formHasOldInput = old('_token') ? true : false;
            @endphp

            <form
                method="POST"
                action="{{ route('kasir.transaction.create') }}"
                id="pos-form"
                data-has-old-input="{{ ($formHasOldInput || $editingTransaction) ? '1' : '0' }}"
                data-editing="{{ $editingTransaction ? '1' : '0' }}"
            >
                @csrf
                <input
                    type="hidden"
                    name="transaction_id"
                    value="{{ old('transaction_id', $editingTransaction['id'] ?? '') }}"
                >

                @php
                    $oldPaymentAmount = old('payment_amount', $paymentSeed);
                    if (is_numeric($oldPaymentAmount)) {
                        $oldPaymentNumeric = (string) (int) $oldPaymentAmount;
                    } elseif (is_string($oldPaymentAmount)) {
                        $oldPaymentNumeric = preg_replace('/[^\d]/', '', $oldPaymentAmount);
                    } else {
                    $oldPaymentNumeric = '';
                }
            @endphp

                @php
                    $defaultItems = [
                        ['category_id' => null, 'product_id' => null, 'product_unit_id' => null, 'quantity' => 1],
                    ];
                    $oldItems = old('items', $prefilledItems ?: $defaultItems);
                @endphp

                <div class="card" style="padding: 32px; border: 1px dashed #d0d5dd; background-color: #f9fbff;">
                    <div class="items-toolbar">
                        <h3 style="margin: 0; font-size: 18px;">Daftar Item</h3>
                        <div class="items-toolbar__actions">
                            <button
                                type="button"
                                class="btn btn-secondary btn-icon"
                                id="smart-scan-toggle"
                                title="Scan QR produk dengan kamera"
                                aria-label="Scan QR produk dengan kamera"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="M7 5a2 2 0 0 0-2 2v2H3V7a4 4 0 0 1 4-4h2v2H7Zm8-2h2a4 4 0 0 1 4 4v2h-2V7a2 2 0 0 0-2-2h-2V3Zm-8 16h2v2H7a4 4 0 0 1-4-4v-2h2v2a2 2 0 0 0 2 2Zm12-2a2 2 0 0 1-2 2h-2v2h2a4 4 0 0 0 4-4v-2h-2v2ZM8 9.5A1.5 1.5 0 1 1 9.5 11 1.5 1.5 0 0 1 8 9.5ZM12 12a4 4 0 1 1 4-4 4 4 0 0 1-4 4Zm0-2a2 2 0 1 0-2-2 2 2 0 0 0 2 2Zm-3 4h6v2H9Zm8 0h2v2h-2Zm-10 0h2v2H7Z"/>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-secondary" id="add-item-btn">Tambah Item</button>
                        </div>
                    </div>

                    <div id="smart-scan-layer" class="smart-scan-layer" hidden>
                        <div class="smart-scan-layer__backdrop" id="smart-scan-backdrop" aria-hidden="true"></div>
                        <div
                            id="smart-scan-panel"
                            class="smart-scan"
                            aria-live="polite"
                            role="dialog"
                            aria-modal="true"
                            aria-label="Smart Scanner"
                            hidden
                        >
                            <div class="smart-scan__header">
                                <div class="smart-scan__title-group">
                                    <div class="smart-scan__badge">
                                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                            <path d="M4 7a3 3 0 0 1 3-3h1a1 1 0 0 1 0 2H7a1 1 0 0 0-1 1v1a1 1 0 0 1-2 0Zm11-4h1a3 3 0 0 1 3 3v1a1 1 0 0 1-2 0V6a1 1 0 0 0-1-1h-1a1 1 0 0 1 0-2ZM6 16v1a1 1 0 0 0 1 1h1a1 1 0 0 1 0 2H7a3 3 0 0 1-3-3v-1a1 1 0 0 1 2 0Zm12-2a1 1 0 0 1 1 1v1a3 3 0 0 1-3 3h-1a1 1 0 0 1 0-2h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1ZM9 9.5A1.5 1.5 0 1 1 10.5 11 1.5 1.5 0 0 1 9 9.5Zm3 2.5a4 4 0 1 1 4-4 4 4 0 0 1-4 4Zm0-2a2 2 0 1 0-2-2 2 2 0 0 0 2 2Zm-3 4h6a1 1 0 0 1 0 2H9a1 1 0 0 1 0-2Zm8 0h2a1 1 0 0 1 0 2h-2a1 1 0 0 1 0-2Zm-10 0h2a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2Z"/>
                                        </svg>
                                        <span>Smart Scanner</span>
                                    </div>
                                    <div>
                                        <p class="smart-scan__title">Scan produk lebih cepat</p>
                                        <p class="smart-scan__desc">Arahkan QR ke bingkai hijau, kamera dibuka mini di depan.</p>
                                    </div>
                                </div>
                                <div class="smart-scan__actions">
                                    <button
                                        type="button"
                                        class="btn btn-secondary btn-icon"
                                        id="smart-scan-close"
                                        title="Tutup pemindaian"
                                        aria-label="Tutup pemindaian"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                            <path d="M18.3 5.7a1 1 0 0 0-1.4-1.4L12 9.59 7.1 4.7A1 1 0 1 0 5.7 6.1L10.6 11 5.7 15.9a1 1 0 1 0 1.4 1.4L12 12.41l4.9 4.89a1 1 0 1 0 1.4-1.4L13.41 11z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="smart-scan__body">
                                <div class="smart-scan__video">
                                    <video id="smart-scan-video" autoplay playsinline muted></video>
                                    <div class="smart-scan__frame" aria-hidden="true">
                                        <span class="smart-scan__frame-corner smart-scan__frame-corner--tl"></span>
                                        <span class="smart-scan__frame-corner smart-scan__frame-corner--tr"></span>
                                        <span class="smart-scan__frame-corner smart-scan__frame-corner--bl"></span>
                                        <span class="smart-scan__frame-corner smart-scan__frame-corner--br"></span>
                                    </div>
                                    <div class="smart-scan__pulse" aria-hidden="true"></div>
                                    <canvas id="smart-scan-canvas" hidden></canvas>
                                </div>
                                <div class="smart-scan__status-block">
                                    <div id="smart-scan-status" class="smart-scan__status" data-variant="info">
                                        <span class="smart-scan__status-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" focusable="false">
                                                <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 15a1 1 0 1 1 1-1 1 1 0 0 1-1 1Zm1-4a1 1 0 0 1-2 0V7a1 1 0 0 1 2 0Z"/>
                                            </svg>
                                        </span>
                                        <span>Tekan ikon kamera untuk memulai pemindaian.</span>
                                    </div>
                                    <p class="smart-scan__hint" id="smart-scan-hint">Tips: gunakan kamera belakang di HP untuk fokus lebih cepat dan jaga jarak 10-20 cm dari label.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="low-stock-alert" class="low-stock-alert" role="status" aria-live="polite">
                        <div class="low-stock-alert__icon">!</div>
                        <div>
                            <div class="low-stock-alert__title">Stok produk hampir habis</div>
                            <ul id="low-stock-alert-list" class="low-stock-alert__list"></ul>
                        </div>
                    </div>

                    <div class="items-table-wrapper">
                        <div class="items-list">
                            <div class="items-header">
                                <div class="items-header-cell">Kategori</div>
                                <div class="items-header-cell">Produk</div>
                                <div class="items-header-cell">Satuan</div>
                                <div class="items-header-cell">Jumlah</div>
                                <div class="items-header-cell items-header-cell--right">Subtotal</div>
                                <div class="items-header-cell items-header-cell--right"></div>
                            </div>
                            <div class="items-body-scroll">
                                <div class="items-body" id="items-body">
                                    @foreach ($oldItems as $index => $item)
                                        @php
                                            $unitId = $item['product_unit_id'] ?? null;
                                            $quantity = old("items.$index.quantity", $item['quantity'] ?? 1);
                                            $selectedProduct = null;
                                            $selectedUnit = null;
                                            if ($unitId) {
                                                foreach ($products as $candidateProduct) {
                                                    $candidateUnit = $candidateProduct->units->firstWhere('id', (int) $unitId);
                                                    if ($candidateUnit) {
                                                        $selectedProduct = $candidateProduct;
                                                        $selectedUnit = $candidateUnit;
                                                        break;
                                                    }
                                                }
                                            }
                                            $unitPrice = $selectedUnit ? $selectedUnit->price : 0;
                                            $lineSubtotal = max($unitPrice * $quantity, 0);
                                            $displayCode = $selectedProduct ? ($selectedProduct->code ?? ('#' . $selectedProduct->id)) : '';
                        $productLabel = $selectedProduct ? $displayCode . ' - ' . $selectedProduct->name : '';
                                            $productValue = $selectedProduct ? (string) $selectedProduct->id : '';
                                            $productValue = old("items.$index.product_id", $productValue);
                                            $selectedCategoryId = null;
                                            if ($selectedProduct) {
                                                $selectedCategoryId = $selectedProduct->category_id !== null
                                                    ? (string) $selectedProduct->category_id
                                                    : 'uncategorized';
                                            }
                                            $rawCategoryValue = old("items.$index.category_id", $item['category_id'] ?? $selectedCategoryId);
                                            $categoryValue = $rawCategoryValue === null ? '' : (string) $rawCategoryValue;
                                        @endphp
                                    <div class="item-row">
                                        <div class="item-cell item-cell--category">
                                            <div class="item-cell-label">Kategori</div>
                                            @php
                                                $categoryLabel = '';
                                                if ($categoryValue === 'uncategorized') {
                                                    $categoryLabel = 'Tanpa Kategori';
                                                } else {
                                                    $matchedCategory = collect($categories)->firstWhere('id', (int) $categoryValue);
                                                    $categoryLabel = $matchedCategory ? $matchedCategory->name : '';
                                                }
                                            @endphp
                                            <div class="category-select-wrapper">
                                                <input
                                                    type="search"
                                                    class="form-control category-search-input"
                                                    placeholder="Cari kategori..."
                                                    autocomplete="off"
                                                    value="{{ $categoryLabel }}"
                                                    @if ($categoryLabel !== '')
                                                        title="{{ $categoryLabel }}"
                                                    @endif
                                                >
                                                <select
                                                    name="items[{{ $index }}][category_id]"
                                                    class="form-select category-select"
                                                >
                                                    <option value="">Semua Kategori</option>
                                                    <option value="uncategorized" @selected($categoryValue === 'uncategorized')>Tanpa Kategori</option>
                                                    @foreach ($categories as $category)
                                                        <option
                                                            value="{{ $category->id }}"
                                                            @selected($categoryValue === (string) $category->id)
                                                        >
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="category-suggestions"></div>
                                            </div>
                                        </div>
                                        <div class="item-cell item-cell--product">
                                            <div class="item-cell-label">Produk</div>
                                            <div class="product-select-wrapper">
                                                <input
                                                    type="search"
                                                    class="form-control product-search-input"
                                                    placeholder="Cari produk..."
                                                    autocomplete="off"
                                                    value="{{ $productLabel }}"
                                                    @if ($productLabel !== '')
                                                        title="{{ $productLabel }}"
                                                    @endif
                                                >
                                                <select
                                                    name="items[{{ $index }}][product_id]"
                                                    class="form-select product-select"
                                                >
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($products as $product)
                                                        @php
                                                            $unitPayload = $product->units->map(function ($unit) use ($product) {
                                                                return [
                                                                    'id' => $unit->id,
                                                                    'name' => $unit->name,
                                                                    'price' => $unit->price,
                                                                    'stock' => $product->is_stock_unlimited ? '' : $product->stock,
                                                                    'stockLabel' => $product->display_stock,
                                                                    'isUnlimited' => $product->is_stock_unlimited,
                                                                ];
                                                            });
                                                            $unitNames = $product->units->pluck('name')->filter()->implode(', ');
                                                            $unitPrices = $product->units->pluck('price')->filter(fn ($price) => $price !== null);
                                                            $minPrice = $unitPrices->min();
                                                            $maxPrice = $unitPrices->max();
                                                        @endphp
                                                        <option
                                                            value="{{ $product->id }}"
                                                            data-product="{{ $product->name }}"
                                                            data-product-id="{{ $product->id }}"
                                                            data-product-code="{{ $product->code ?? ('#' . $product->id) }}"
                                                            data-category-id="{{ $product->category_id ?? 'uncategorized' }}"
                                                            data-category-name="{{ optional($product->category)->name ?? 'Tanpa Kategori' }}"
                                                            data-stock="{{ $product->is_stock_unlimited ? '' : $product->stock }}"
                                                            data-is-unlimited="{{ $product->is_stock_unlimited ? '1' : '0' }}"
                                                            data-stock-label="{{ $product->display_stock }}"
                                                            data-units='@json($unitPayload)'
                                                            data-units-text="{{ $unitNames }}"
                                                            data-min-price="{{ $minPrice === null ? '' : $minPrice }}"
                                                            data-max-price="{{ $maxPrice === null ? '' : $maxPrice }}"
                                                            @selected($productValue === (string) $product->id)
                                                        >
                                                            {{ $product->code ?? ('#' . $product->id) }} - {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="product-suggestions"></div>
                                        </div>
                                        </div>
                                        <div class="item-cell item-cell--unit">
                                            <div class="item-cell-label">Satuan</div>
                                            <select
                                                name="items[{{ $index }}][product_unit_id]"
                                                class="form-select unit-select"
                                                {{ $selectedProduct ? '' : 'disabled' }}
                                            >
                                                <option value="">
                                                    {{ $selectedProduct ? 'Pilih satuan' : 'Pilih produk terlebih dahulu' }}
                                                </option>
                                                @if ($selectedProduct)
                                                    @foreach ($selectedProduct->units as $unit)
                                                        <option
                                                            value="{{ $unit->id }}"
                                                            data-price="{{ $unit->price }}"
                                                            data-unit="{{ $unit->name }}"
                                                            data-stock="{{ $selectedProduct->is_stock_unlimited ? '' : $selectedProduct->stock }}"
                                                            data-is-unlimited="{{ $selectedProduct->is_stock_unlimited ? '1' : '0' }}"
                                                            data-stock-label="{{ $selectedProduct->display_stock }}"
                                                            @selected((int) $unitId === $unit->id)
                                                        >
                                                            {{ $unit->name }} - Rp{{ number_format($unit->price, 0, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error("items.$index.product_unit_id")
                                                <div class="input-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="item-cell item-cell--quantity">
                                            <div class="item-cell-label">Jumlah</div>
                                            <input
                                                type="number"
                                                name="items[{{ $index }}][quantity]"
                                                min="0"
                                                class="form-control quantity-input"
                                                value="{{ $quantity }}"
                                            >
                                            @error("items.$index.quantity")
                                                <div class="input-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="item-cell item-cell--subtotal">
                                            <div class="item-cell-label">Subtotal</div>
                                            <div class="item-subtotal" data-value="{{ $lineSubtotal }}">Rp{{ number_format($lineSubtotal, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="item-cell item-cell--actions">
                                            <div class="item-cell-label">Aksi</div>
                                            <button type="button" class="btn btn-danger btn-icon remove-item" title="Hapus baris" aria-label="Hapus item">
                                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                                    <path d="M9 3a1 1 0 0 0-1 1v1H5.5a1 1 0 0 0 0 2H6v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7h.5a1 1 0 0 0 0-2H16V4a1 1 0 0 0-1-1H9zm1 3h4V5h-4v1zm-1 4a1 1 0 0 1 2 0v8a1 1 0 0 1-2 0V10zm6-1a1 1 0 0 0-1 1v8a1 1 0 0 0 2 0V10a1 1 0 0 0-1-1z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="items-footer">
                        <div class="muted" style="font-size: 13px;">
                            Subtotal dihitung otomatis berdasarkan harga dan jumlah.
                        </div>
                        <div class="subtotal-display">
                            Estimasi Total: <span id="estimated-total">Rp0</span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: center;">
                    <div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); width: min(820px, 100%);">
                        <div style="max-width: 320px;">
                            <label class="form-label" for="payment_method">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="form-select">
                                @foreach ($paymentMethods as $value => $label)
                                    <option value="{{ $value }}" @selected(old('payment_method', $defaultPaymentMethod) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="max-width: 340px;">
                            <label class="form-label" for="payment_amount_display">Nominal Pembayaran</label>
                            <div class="currency-input">
                                <span class="currency-prefix">Rp</span>
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    id="payment_amount_display"
                                    placeholder="0"
                                    autocomplete="off"
                                    value="{{ $oldPaymentNumeric !== '' ? number_format((int) $oldPaymentNumeric, 0, ',', '.') : '' }}"
                                >
                            </div>
                            <input type="hidden" name="payment_amount" id="payment_amount" value="{{ $oldPaymentNumeric }}">
                            @error('payment_amount')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Simpan &amp; Proses</button>
                </div>
            </form>
        </div>

    </div>

    <template id="item-row-template">
        <div class="item-row">
            <div class="item-cell item-cell--category">
                <div class="item-cell-label">Kategori</div>
                <div class="category-select-wrapper">
                    <input
                        type="search"
                        class="form-control category-search-input"
                        placeholder="Cari kategori..."
                        autocomplete="off"
                        value=""
                    >
                    <select data-name="items[INDEX][category_id]" class="form-select category-select">
                        <option value="">Semua Kategori</option>
                        <option value="uncategorized">Tanpa Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <div class="category-suggestions"></div>
                </div>
            </div>
            <div class="item-cell item-cell--product">
                <div class="item-cell-label">Produk</div>
                <div class="product-select-wrapper">
                    <input
                        type="search"
                        class="form-control product-search-input"
                        placeholder="Cari produk..."
                        autocomplete="off"
                    >
                    <select data-name="items[INDEX][product_id]" class="form-select product-select">
                        <option value="">Pilih Produk</option>
                        @foreach ($products as $product)
                            @php
                                $unitPayload = $product->units->map(function ($unit) use ($product) {
                                    return [
                                        'id' => $unit->id,
                                        'name' => $unit->name,
                                        'price' => $unit->price,
                                        'stock' => $product->is_stock_unlimited ? '' : $product->stock,
                                        'stockLabel' => $product->display_stock,
                                        'isUnlimited' => $product->is_stock_unlimited,
                                    ];
                                });
                                $unitNames = $product->units->pluck('name')->filter()->implode(', ');
                                $unitPrices = $product->units->pluck('price')->filter(fn ($price) => $price !== null);
                                $minPrice = $unitPrices->min();
                                $maxPrice = $unitPrices->max();
                            @endphp
                            <option
                                value="{{ $product->id }}"
                                data-product="{{ $product->name }}"
                                data-product-id="{{ $product->id }}"
                                data-product-code="{{ $product->code ?? ('#' . $product->id) }}"
                                data-category-id="{{ $product->category_id ?? 'uncategorized' }}"
                                data-category-name="{{ optional($product->category)->name ?? 'Tanpa Kategori' }}"
                                data-stock="{{ $product->is_stock_unlimited ? '' : $product->stock }}"
                                data-is-unlimited="{{ $product->is_stock_unlimited ? '1' : '0' }}"
                                data-stock-label="{{ $product->display_stock }}"
                                data-units='@json($unitPayload)'
                                data-units-text="{{ $unitNames }}"
                                data-min-price="{{ $minPrice === null ? '' : $minPrice }}"
                                data-max-price="{{ $maxPrice === null ? '' : $maxPrice }}"
                            >
                                {{ $product->code ?? ('#' . $product->id) }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="product-suggestions"></div>
                </div>
            </div>
            <div class="item-cell item-cell--unit">
                <div class="item-cell-label">Satuan</div>
                <select data-name="items[INDEX][product_unit_id]" class="form-select unit-select" disabled>
                    <option value="">Pilih produk terlebih dahulu</option>
                </select>
            </div>
                <div class="item-cell item-cell--quantity">
                    <div class="item-cell-label">Jumlah</div>
                    <input
                        type="number"
                        min="0"
                        value="1"
                        data-name="items[INDEX][quantity]"
                        class="form-control quantity-input"
                    >
                </div>
            <div class="item-cell item-cell--subtotal">
                <div class="item-cell-label">Subtotal</div>
                <div class="item-subtotal" data-value="0">Rp0</div>
            </div>
            <div class="item-cell item-cell--actions">
                <div class="item-cell-label">Aksi</div>
                <button type="button" class="btn btn-danger btn-icon remove-item" title="Hapus baris" aria-label="Hapus item">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M9 3a1 1 0 0 0-1 1v1H5.5a1 1 0 0 0 0 2H6v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7h.5a1 1 0 0 0 0-2H16V4a1 1 0 0 0-1-1H9zm1 3h4V5h-4v1zm-1 4a1 1 0 0 1 2 0v8a1 1 0 0 1-2 0V10zm6-1a1 1 0 0 0-1 1v8a1 1 0 0 0 2 0V10a1 1 0 0 0-1-1z"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemsBody = document.getElementById('items-body');
            const addItemBtn = document.getElementById('add-item-btn');
            const template = document.getElementById('item-row-template');
            const estimatedTotalEl = document.getElementById('estimated-total');
            const paymentMethodSelect = document.getElementById('payment_method');
            const paymentAmountHidden = document.getElementById('payment_amount');
            const paymentAmountDisplay = document.getElementById('payment_amount_display');
            const posForm = document.getElementById('pos-form');
            const lowStockAlert = document.getElementById('low-stock-alert');
            const lowStockList = document.getElementById('low-stock-alert-list');
            const smartScanToggle = document.getElementById('smart-scan-toggle');
            const smartScanLayer = document.getElementById('smart-scan-layer');
            const smartScanBackdrop = document.getElementById('smart-scan-backdrop');
            const smartScanPanel = document.getElementById('smart-scan-panel');
            const smartScanVideo = document.getElementById('smart-scan-video');
            const smartScanStatus = document.getElementById('smart-scan-status');
            const smartScanHint = document.getElementById('smart-scan-hint');
            const smartScanCanvas = document.getElementById('smart-scan-canvas');
            const smartScanClose = document.getElementById('smart-scan-close');
            const smartScanStatusText = smartScanStatus ? smartScanStatus.querySelector('span:last-child') : null;
            const LOW_STOCK_THRESHOLD = 3;
            const POS_DRAFT_STORAGE_KEY = 'pos_form_draft_v1';
            const hasOldInput = posForm ? posForm.dataset.hasOldInput === '1' : false;
            const isEditing = posForm ? posForm.dataset.editing === '1' : false;
            const canUseDraftStorage = (() => {
                try {
                    if (typeof window === 'undefined' || !window.localStorage) {
                        return false;
                    }
                    const testKey = '__pos_draft_test__';
                    window.localStorage.setItem(testKey, '1');
                    window.localStorage.removeItem(testKey);
                    return true;
                } catch (error) {
                    return false;
                }
            })();
            let isRestoringDraft = false;
            let smartScanStream = null;
            let smartScanActive = false;
            let smartScanFrameHandle = null;
            let barcodeDetector = null;
            let jsqrLoader = null;
            let lastScanValue = '';
            let lastScanAt = 0;

            let rowIndex = itemsBody.querySelectorAll('.item-row').length;

            if (isEditing) {
                clearPersistedFormState();
            }

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(value);
            }

            function formatWithGrouping(digits) {
                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function syncPaymentInputs(rawValue) {
                if (!paymentAmountHidden || !paymentAmountDisplay) {
                    return;
                }

                const digitsOnly = (rawValue || '').replace(/\D/g, '');

                if (digitsOnly === '') {
                    paymentAmountHidden.value = '';
                    paymentAmountDisplay.value = '';
                    return;
                }

                paymentAmountHidden.value = digitsOnly;
                paymentAmountDisplay.value = formatWithGrouping(digitsOnly);
            }

            function setPaymentAmountFromDigits(digits) {
                if (!paymentAmountHidden || !paymentAmountDisplay) {
                    return;
                }

                if (!digits) {
                    paymentAmountHidden.value = '';
                    paymentAmountDisplay.value = '';
                    return;
                }

                paymentAmountHidden.value = digits;
                paymentAmountDisplay.value = formatWithGrouping(digits);
            }

            function serializeFormState() {
                if (!itemsBody) {
                    return null;
                }

                const items = Array.from(itemsBody.querySelectorAll('.item-row')).map((row) => {
                    const categorySelect = row.querySelector('.category-select');
                    const productSelect = row.querySelector('.product-select');
                    const unitSelect = row.querySelector('.unit-select');
                    const quantityInput = row.querySelector('.quantity-input');

                    return {
                        category_id: categorySelect ? categorySelect.value : '',
                        product_id: productSelect ? productSelect.value : '',
                        product_unit_id: unitSelect ? unitSelect.value : '',
                        quantity: quantityInput ? quantityInput.value : '',
                    };
                });

                return {
                    payment_method: paymentMethodSelect ? paymentMethodSelect.value : '',
                    payment_amount: paymentAmountHidden ? paymentAmountHidden.value : '',
                    items,
                };
            }

            function persistFormState() {
                if (!canUseDraftStorage || isRestoringDraft) {
                    return;
                }

                const state = serializeFormState();
                if (!state) {
                    return;
                }

                try {
                    window.localStorage.setItem(POS_DRAFT_STORAGE_KEY, JSON.stringify(state));
                } catch (error) {
                    console.warn('Gagal menyimpan draft POS', error);
                }
            }

            function clearPersistedFormState() {
                if (!canUseDraftStorage) {
                    return;
                }

                try {
                    window.localStorage.removeItem(POS_DRAFT_STORAGE_KEY);
                } catch (error) {
                    console.warn('Gagal menghapus draft POS', error);
                }
            }

            function parseUnitsFromOption(option) {
                if (!option || !option.dataset.units) {
                    return [];
                }

                try {
                    const parsed = JSON.parse(option.dataset.units);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    console.warn('Gagal membaca data satuan produk', error);
                    return [];
                }
            }

            function buildProductIndex() {
                const referenceSelect = document.querySelector('.product-select');
                const byId = new Map();
                const byCode = new Map();

                if (!referenceSelect) {
                    return { byId, byCode };
                }

                Array.from(referenceSelect.options)
                    .filter((option) => option.value)
                    .forEach((option) => {
                        const units = parseUnitsFromOption(option);
                        const defaultUnitId = units.length ? String(units[0].id) : '';
                        const payload = {
                            id: String(option.value),
                            code: option.dataset.productCode || '',
                            label: (option.textContent || '').trim(),
                            categoryId: option.dataset.categoryId || '',
                            units,
                            defaultUnitId,
                        };

                        byId.set(payload.id, payload);
                        if (payload.code) {
                            byCode.set(payload.code, payload);
                        }
                    });

                return { byId, byCode };
            }

            const productIndex = buildProductIndex();

            function updateSmartScanStatus(message, variant = 'info') {
                if (!smartScanStatus) {
                    return;
                }
                smartScanStatus.dataset.variant = variant;
                if (smartScanStatusText) {
                    smartScanStatusText.textContent = message;
                } else {
                    smartScanStatus.textContent = message;
                }
            }

            function showSmartScanPanel() {
                if (smartScanLayer) {
                    smartScanLayer.hidden = false;
                    smartScanLayer.classList.add('is-visible');
                }
                if (smartScanPanel) {
                    smartScanPanel.hidden = false;
                    smartScanPanel.classList.add('is-visible');
                }
            }

            function hideSmartScanPanel() {
                if (smartScanPanel) {
                    smartScanPanel.classList.remove('is-visible');
                    smartScanPanel.hidden = true;
                }
                if (smartScanLayer) {
                    smartScanLayer.classList.remove('is-visible');
                    smartScanLayer.hidden = true;
                }
            }

            function stopSmartScan() {
                smartScanActive = false;
                if (smartScanFrameHandle) {
                    window.cancelAnimationFrame(smartScanFrameHandle);
                    smartScanFrameHandle = null;
                }
                if (smartScanStream) {
                    smartScanStream.getTracks().forEach((track) => track.stop());
                    smartScanStream = null;
                }
                if (smartScanVideo) {
                    smartScanVideo.pause();
                    smartScanVideo.srcObject = null;
                    smartScanVideo.classList.remove('smart-scan__video--unmirror');
                }
            }

            async function ensureBarcodeDetector() {
                if (!('BarcodeDetector' in window)) {
                    return null;
                }
                if (barcodeDetector) {
                    return barcodeDetector;
                }
                try {
                    const supportedFormats = await window.BarcodeDetector.getSupportedFormats();
                    const formats = supportedFormats.includes('qr_code') ? ['qr_code'] : supportedFormats;
                    if (!formats.length) {
                        return null;
                    }
                    barcodeDetector = new window.BarcodeDetector({ formats });
                    return barcodeDetector;
                } catch (error) {
                    console.warn('BarcodeDetector tidak tersedia', error);
                    return null;
                }
            }

            async function detectWithBarcodeDetector() {
                if (!smartScanVideo) {
                    return null;
                }
                const detector = await ensureBarcodeDetector();
                if (!detector) {
                    return null;
                }
                if (smartScanVideo.readyState < 2) {
                    return null;
                }
                try {
                    const results = await detector.detect(smartScanVideo);
                    if (results && results.length) {
                        const match = results[0];
                        return match.rawValue || (match.rawData ? new TextDecoder().decode(match.rawData) : null);
                    }
                } catch (error) {
                    console.warn('Deteksi BarcodeDetector gagal', error);
                }
                return null;
            }

            async function ensureJsqr() {
                if (window.jsQR) {
                    return window.jsQR;
                }
                if (jsqrLoader) {
                    try {
                        return await jsqrLoader;
                    } catch (error) {
                        return null;
                    }
                }

                jsqrLoader = new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
                    script.async = true;
                    script.onload = () => resolve(window.jsQR || null);
                    script.onerror = () => reject(new Error('Gagal memuat jsQR'));
                    document.head.appendChild(script);
                });

                try {
                    return await jsqrLoader;
                } catch (error) {
                    console.warn('jsQR tidak dapat dimuat', error);
                    return null;
                }
            }

            async function detectWithJsqr() {
                if (!smartScanVideo || !smartScanCanvas) {
                    return null;
                }
                if (smartScanVideo.readyState < 2) {
                    return null;
                }

                const jsqr = await ensureJsqr();
                if (!jsqr) {
                    return null;
                }

                const videoWidth = smartScanVideo.videoWidth;
                const videoHeight = smartScanVideo.videoHeight;
                if (!videoWidth || !videoHeight) {
                    return null;
                }

                const targetWidth = 480;
                const scale = Math.min(1, targetWidth / videoWidth);
                const canvasWidth = Math.max(1, Math.floor(videoWidth * scale));
                const canvasHeight = Math.max(1, Math.floor(videoHeight * scale));

                smartScanCanvas.width = canvasWidth;
                smartScanCanvas.height = canvasHeight;

                const ctx = smartScanCanvas.getContext('2d', { willReadFrequently: true });
                if (!ctx) {
                    return null;
                }

                ctx.drawImage(smartScanVideo, 0, 0, canvasWidth, canvasHeight);
                const imageData = ctx.getImageData(0, 0, canvasWidth, canvasHeight);
                const result = jsqr(imageData.data, imageData.width, imageData.height);
                return result ? result.data : null;
            }

            function parseProductPayload(rawValue) {
                if (rawValue === undefined || rawValue === null) {
                    return null;
                }
                const text = typeof rawValue === 'string' ? rawValue.trim() : String(rawValue).trim();
                if (!text) {
                    return null;
                }

                try {
                    const parsed = JSON.parse(text);
                    if (parsed && typeof parsed === 'object') {
                        return {
                            id: parsed.id !== undefined && parsed.id !== null ? String(parsed.id) : '',
                            code: parsed.code !== undefined && parsed.code !== null ? String(parsed.code) : '',
                            type: parsed.type || '',
                            raw: text,
                        };
                    }
                } catch (error) {
                    // bukan JSON, lanjutkan.
                }

                if (/^\d+$/.test(text)) {
                    return { id: text, code: '', raw: text };
                }

                return { id: '', code: text, raw: text };
            }

            function resolveScannedProduct(parsedPayload) {
                if (!parsedPayload) {
                    return null;
                }

                if (parsedPayload.id && productIndex.byId.has(parsedPayload.id)) {
                    return productIndex.byId.get(parsedPayload.id);
                }

                if (parsedPayload.code && productIndex.byCode.has(parsedPayload.code)) {
                    return productIndex.byCode.get(parsedPayload.code);
                }

                if (parsedPayload.code) {
                    const normalized = parsedPayload.code.trim().toUpperCase();
                    for (const [code, product] of productIndex.byCode.entries()) {
                        if (code.toUpperCase() === normalized) {
                            return product;
                        }
                    }
                }

                return null;
            }

            function findRowByProductAndUnit(productId, unitId) {
                const rows = itemsBody ? Array.from(itemsBody.querySelectorAll('.item-row')) : [];
                return rows.find((row) => {
                    const productSelect = row.querySelector('.product-select');
                    const unitSelect = row.querySelector('.unit-select');
                    if (!productSelect || !unitSelect) {
                        return false;
                    }
                    const sameProduct = productSelect.value === String(productId);
                    const sameUnit = unitId ? unitSelect.value === String(unitId) : true;
                    return sameProduct && sameUnit;
                }) || null;
            }

            function findEmptyRow() {
                const rows = itemsBody ? Array.from(itemsBody.querySelectorAll('.item-row')) : [];
                return rows.find((row) => {
                    const productSelect = row.querySelector('.product-select');
                    return productSelect && !productSelect.value;
                }) || null;
            }

            function addProductFromScan(product) {
                if (!product) {
                    updateSmartScanStatus('QR terbaca, tetapi produk tidak ada di daftar POS.', 'error');
                    return;
                }

                const unitId = product.defaultUnitId || (product.units.length ? String(product.units[0].id) : '');
                if (!unitId) {
                    updateSmartScanStatus('Produk belum memiliki satuan yang dapat dipilih.', 'error');
                    return;
                }

                const existingRow = findRowByProductAndUnit(product.id, unitId);
                if (existingRow) {
                    const qtyInput = existingRow.querySelector('.quantity-input');
                    if (qtyInput) {
                        const current = Number(qtyInput.value || 0);
                        qtyInput.value = Math.max(0, current) + 1;
                        qtyInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    updateSmartScanStatus(`Jumlah ditambah untuk ${product.label}`, 'success');
                    existingRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                const emptyRow = findEmptyRow();
                if (emptyRow) {
                    hydrateRowFromData(emptyRow, {
                        category_id: product.categoryId || '',
                        product_id: product.id,
                        product_unit_id: unitId,
                        quantity: 1,
                    });
                    emptyRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    updateSmartScanStatus(`Ditambahkan: ${product.label}`, 'success');
                    return;
                }

                const newRow = addRow({
                    category_id: product.categoryId || '',
                    product_id: product.id,
                    product_unit_id: unitId,
                    quantity: 1,
                }, { prepend: true });
                if (newRow) {
                    newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                updateSmartScanStatus(`Ditambahkan: ${product.label}`, 'success');
            }

            function handleSmartScanResult(rawValue) {
                const now = Date.now();
                const text = typeof rawValue === 'string' ? rawValue.trim() : String(rawValue).trim();
                if (!text) {
                    return;
                }
                if (text === lastScanValue && now - lastScanAt < 1500) {
                    return;
                }
                lastScanValue = text;
                lastScanAt = now;

                const parsedPayload = parseProductPayload(text);
                const product = resolveScannedProduct(parsedPayload);
                if (!product) {
                    updateSmartScanStatus('QR terbaca, tetapi tidak cocok dengan produk manapun.', 'error');
                    return;
                }
                addProductFromScan(product);
                // Tutup popup setelah scan berhasil supaya layar POS terlihat.
                updateSmartScanStatus(`Ditambahkan: ${product.label}. Menutup pemindaian...`, 'success');
                stopSmartScan();
                hideSmartScanPanel();
                persistFormState();
            }

            async function processSmartScanFrame() {
                if (!smartScanActive) {
                    return;
                }

                let value = await detectWithBarcodeDetector();
                if (!value) {
                    value = await detectWithJsqr();
                }

                if (value) {
                    handleSmartScanResult(value);
                }

                smartScanFrameHandle = window.requestAnimationFrame(processSmartScanFrame);
            }

            async function startSmartScan() {
                if (!smartScanToggle || !smartScanPanel) {
                    return;
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    updateSmartScanStatus('Browser tidak mendukung akses kamera.', 'error');
                    return;
                }

                showSmartScanPanel();
                updateSmartScanStatus('Mengaktifkan kamera...', 'info');

                try {
                    smartScanStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'environment' } },
                        audio: false,
                    });

                    if (smartScanVideo) {
                        smartScanVideo.srcObject = smartScanStream;
                        await smartScanVideo.play();

                        const track = smartScanStream.getVideoTracks()[0];
                        const facing = track && track.getSettings ? track.getSettings().facingMode : null;
                        const shouldUnmirror = facing === 'user' || facing === undefined;
                        smartScanVideo.classList.toggle('smart-scan__video--unmirror', !!shouldUnmirror);
                    }

                    smartScanActive = true;
                    lastScanValue = '';
                    lastScanAt = 0;
                    updateSmartScanStatus('Arahkan QR produk ke area kamera.', 'info');
                    processSmartScanFrame();
                } catch (error) {
                    console.error('Gagal memulai pemindaian', error);
                    updateSmartScanStatus('Tidak bisa mengakses kamera. Izinkan akses atau coba perangkat lain.', 'error');
                    stopSmartScan();
                }
            }

            function hydrateRowFromData(row, data = {}) {
                if (!row || !data || typeof data !== 'object') {
                    return;
                }

                const toValue = (value) => {
                    if (value === undefined || value === null) {
                        return '';
                    }
                    return String(value);
                };

                const categorySelect = row.querySelector('.category-select');
                if (categorySelect && Object.prototype.hasOwnProperty.call(data, 'category_id')) {
                    categorySelect.value = toValue(data.category_id);
                    categorySelect.dispatchEvent(new Event('change', { bubbles: true }));
                }

                const productSelect = row.querySelector('.product-select');
                if (productSelect && Object.prototype.hasOwnProperty.call(data, 'product_id')) {
                    productSelect.value = toValue(data.product_id);
                    productSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }

                const unitSelect = row.querySelector('.unit-select');
                if (unitSelect && Object.prototype.hasOwnProperty.call(data, 'product_unit_id')) {
                    unitSelect.value = toValue(data.product_unit_id);
                    unitSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }

                const quantityInput = row.querySelector('.quantity-input');
                if (quantityInput && Object.prototype.hasOwnProperty.call(data, 'quantity')) {
                    quantityInput.value = data.quantity ?? '';
                    quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
                }

                updateRow(row);
            }

            function restorePersistedFormState() {
                if (!canUseDraftStorage) {
                    return false;
                }

                let rawState = null;
                try {
                    rawState = window.localStorage.getItem(POS_DRAFT_STORAGE_KEY);
                } catch (error) {
                    console.warn('Gagal membaca draft POS', error);
                    return false;
                }

                if (!rawState) {
                    return false;
                }

                let state = null;
                try {
                    state = JSON.parse(rawState);
                } catch (error) {
                    console.warn('Format draft POS tidak valid, menghapus data lama');
                    clearPersistedFormState();
                    return false;
                }

                if (!state || !Array.isArray(state.items)) {
                    return false;
                }

                isRestoringDraft = true;
                try {
                    if (itemsBody) {
                        itemsBody.innerHTML = '';
                        rowIndex = 0;
                        const rowsData = state.items.length ? state.items : [{}];
                        rowsData.forEach((itemData) => {
                            addRow(itemData);
                        });
                    }

                    if (paymentMethodSelect && state.payment_method) {
                        paymentMethodSelect.value = state.payment_method;
                        paymentMethodSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    if (state.payment_amount !== undefined && state.payment_amount !== null) {
                        setPaymentAmountFromDigits(String(state.payment_amount));
                    }
                } finally {
                    isRestoringDraft = false;
                }

                updateTotals();
                return true;
            }

            function calculateCurrentTotal() {
                let total = 0;
                itemsBody.querySelectorAll('.item-row').forEach((row) => {
                    const subtotalText = row.querySelector('.item-subtotal').dataset.value;
                    total += Number(subtotalText || 0);
                });
                return total;
            }

            function collectLowStockSelections() {
                const warnings = new Map();

                itemsBody.querySelectorAll('.item-row').forEach((row) => {
                    const select = row.querySelector('.product-select');
                    if (!select || !select.value) {
                        return;
                    }

                    const option = select.options[select.selectedIndex];
                    if (!option || !option.value) {
                        return;
                    }

                    if (option.dataset.isUnlimited === '1') {
                        return;
                    }

                    const rawStock = option.dataset.stock;
                    if (rawStock === undefined || rawStock === null || rawStock === '') {
                        return;
                    }

                    const stockValue = Number(rawStock);
                    if (!Number.isFinite(stockValue) || stockValue > LOW_STOCK_THRESHOLD) {
                        return;
                    }

                    const productKey = option.dataset.productId || option.value;
                    if (warnings.has(productKey)) {
                        return;
                    }

                    warnings.set(productKey, {
                        label: option.dataset.product || (option.textContent || 'Produk'),
                        stock: stockValue,
                        stockLabel: option.dataset.stockLabel || '',
                    });
                });

                return Array.from(warnings.values());
            }

            function updateLowStockAlert() {
                if (!lowStockAlert || !lowStockList) {
                    return;
                }

                const warningItems = collectLowStockSelections();

                if (!warningItems.length) {
                    lowStockAlert.classList.remove('is-visible');
                    lowStockList.innerHTML = '';
                    return;
                }

                lowStockList.innerHTML = '';
                warningItems.forEach((item) => {
                    const li = document.createElement('li');
                    const label = item.stockLabel || `${item.stock} pcs`;
                    li.textContent = `${item.label} tersisa ${label}`;
                    lowStockList.appendChild(li);
                });

                lowStockAlert.classList.add('is-visible');
            }

            function autoFillPaymentForMethod(total) {
                if (!paymentMethodSelect || !paymentAmountDisplay) {
                    return;
                }

                const method = paymentMethodSelect.value;
                const autoFillMethods = ['qris', 'transfer'];
                const readOnlyMethods = ['qris'];

                paymentAmountDisplay.readOnly = readOnlyMethods.includes(method);

                if (!autoFillMethods.includes(method)) {
                    return;
                }

                const sanitized = Math.max(0, Math.round(Number(total) || 0));
                if (sanitized > 0) {
                    setPaymentAmountFromDigits(String(sanitized));
                } else {
                    setPaymentAmountFromDigits('');
                }
            }

            function updateTotals() {
                const total = calculateCurrentTotal();
                estimatedTotalEl.textContent = formatCurrency(total);
                autoFillPaymentForMethod(total);
                updateLowStockAlert();
                return total;
            }

            function filterProductsByCategory(row) {
                if (!row) {
                    return;
                }

                const select = row.querySelector('.product-select');
                const categorySelect = row.querySelector('.category-select');

                if (!select) {
                    return;
                }

                const selectedCategory = categorySelect ? categorySelect.value : '';

                Array.from(select.options).forEach((option) => {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }

                    const optionCategory = option.dataset.categoryId || '';
                    const isVisible = !selectedCategory || optionCategory === selectedCategory;
                    option.hidden = !isVisible;
                });

                if (select.value) {
                    const selectedOption = select.options[select.selectedIndex];
                    const optionCategory = selectedOption ? (selectedOption.dataset.categoryId || '') : '';
                    if (selectedCategory && optionCategory !== selectedCategory) {
                        select.value = '';
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            }

            function getSelectedProductOption(row) {
                if (!row) {
                    return null;
                }

                const select = row.querySelector('.product-select');
                if (!select) {
                    return null;
                }

                return select.options[select.selectedIndex] || null;
            }

            function parseUnitPayload(option) {
                if (!option || !option.dataset.units) {
                    return [];
                }

                try {
                    return JSON.parse(option.dataset.units);
                } catch (error) {
                    console.warn('Gagal membaca data satuan produk', error);
                    return [];
                }
            }

            function populateUnitSelect(row, { preserveSelection = true } = {}) {
                if (!row) {
                    return false;
                }

                const unitSelect = row.querySelector('.unit-select');
                const productOption = getSelectedProductOption(row);

                if (!unitSelect) {
                    return false;
                }

                const previousValue = preserveSelection ? unitSelect.value : '';
                const unitsData = parseUnitPayload(productOption);

                unitSelect.innerHTML = '';
                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = productOption ? 'Pilih satuan' : 'Pilih produk terlebih dahulu';
                unitSelect.appendChild(placeholderOption);

                if (!productOption || !unitsData.length) {
                    unitSelect.value = '';
                    unitSelect.disabled = true;
                    return previousValue !== unitSelect.value;
                }

                unitsData.forEach((unit) => {
                    const optionEl = document.createElement('option');
                    optionEl.value = String(unit.id);
                    const hasPrice = unit.price !== undefined && unit.price !== null && unit.price !== '';
                    const priceNumber = Number(unit.price || 0);
                    optionEl.textContent = hasPrice ? `${unit.name} - ${formatCurrency(priceNumber)}` : unit.name;
                    optionEl.dataset.price = unit.price || 0;
                    optionEl.dataset.unit = unit.name || '';
                    optionEl.dataset.stock = unit.stock || '';
                    optionEl.dataset.isUnlimited = unit.isUnlimited ? '1' : '0';
                    optionEl.dataset.stockLabel = unit.stockLabel || '';
                    unitSelect.appendChild(optionEl);
                });

                unitSelect.disabled = false;

                let nextValue = '';
                if (preserveSelection && unitsData.some((unit) => String(unit.id) === previousValue)) {
                    nextValue = previousValue;
                } else if (unitsData.length === 1) {
                    nextValue = String(unitsData[0].id);
                }

                unitSelect.value = nextValue;
                return previousValue !== unitSelect.value;
            }

            function enableSelectTypeahead(select) {
                if (!select || select.dataset.typeaheadInitialized === '1') {
                    return;
                }

                let searchBuffer = '';
                let debounceTimer = null;

                const resetBuffer = () => {
                    searchBuffer = '';
                    if (debounceTimer) {
                        window.clearTimeout(debounceTimer);
                        debounceTimer = null;
                    }
                };

                const findMatch = () => {
                    const lowerBuffer = searchBuffer.toLowerCase();

                    return Array.from(select.options).find((option) => {
                        if (!option.value || option.hidden) {
                            return false;
                        }

                        const label = (option.textContent || '').toLowerCase();
                        const name = (option.dataset.product || '').toLowerCase();
                        const code = (option.dataset.productCode || '').toLowerCase();
                        const units = (option.dataset.unitsText || '').toLowerCase();

                        return (
                            label.includes(lowerBuffer) ||
                            name.includes(lowerBuffer) ||
                            code.includes(lowerBuffer) ||
                            units.includes(lowerBuffer)
                        );
                    });
                };

                select.addEventListener('keydown', (event) => {
                    const { key } = event;

                    if (key === 'Backspace') {
                        if (searchBuffer.length > 0) {
                            searchBuffer = searchBuffer.slice(0, -1);
                        }
                        event.preventDefault();
                        return;
                    }

                    if (key === 'Escape') {
                        resetBuffer();
                        return;
                    }

                    if (key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
                        searchBuffer += key.toLowerCase();

                        if (debounceTimer) {
                            window.clearTimeout(debounceTimer);
                        }
                        debounceTimer = window.setTimeout(resetBuffer, 700);

                        const match = findMatch();
                        if (match) {
                            select.value = match.value;
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }

                        event.preventDefault();
                    } else if (!['ArrowUp', 'ArrowDown', 'Tab', 'Enter'].includes(key)) {
                        resetBuffer();
                    }
                });

                select.addEventListener('blur', resetBuffer);

                select.dataset.typeaheadInitialized = '1';
            }

            function enableCategorySearch(row, select) {
                if (!row || !select) {
                    return;
                }

                const wrapper = row.querySelector('.category-select-wrapper');
                const searchInput = wrapper ? wrapper.querySelector('.category-search-input') : null;
                const suggestions = wrapper ? wrapper.querySelector('.category-suggestions') : null;

                if (!wrapper || !searchInput || !suggestions || searchInput.dataset.searchInitialized === '1') {
                    return;
                }

                wrapper.classList.add('category-select-wrapper--enhanced');
                searchInput.dataset.searchInitialized = '1';
                row._categorySuggestions = suggestions;

                let suggestionButtons = [];
                let highlightedIndex = -1;
                let repositionActive = false;

                const ensureSuggestionsPortal = () => {
                    if (!suggestions || suggestions.dataset.portaled === '1') {
                        return;
                    }
                    suggestions.dataset.portaled = '1';
                    document.body.appendChild(suggestions);
                    suggestions.style.position = 'absolute';
                };

                const positionSuggestions = () => {
                    if (!suggestions || suggestions.dataset.isVisible !== '1') {
                        return;
                    }
                    const rect = searchInput.getBoundingClientRect();
                    suggestions.style.width = `${rect.width}px`;
                    suggestions.style.left = `${rect.left + window.scrollX}px`;
                    suggestions.style.top = `${rect.bottom + 4 + window.scrollY}px`;
                };

                const handleViewportChange = () => {
                    positionSuggestions();
                };

                const enableReposition = () => {
                    if (repositionActive) {
                        return;
                    }
                    repositionActive = true;
                    window.addEventListener('resize', handleViewportChange);
                    window.addEventListener('scroll', handleViewportChange, true);
                };

                const disableReposition = () => {
                    if (!repositionActive) {
                        return;
                    }
                    repositionActive = false;
                    window.removeEventListener('resize', handleViewportChange);
                    window.removeEventListener('scroll', handleViewportChange, true);
                };

                const formatOptionLabel = (option) => (option ? (option.textContent || '').trim() : '');

                const closeSuggestions = () => {
                    suggestions.innerHTML = '';
                    suggestions.classList.remove('is-visible');
                    suggestions.dataset.isVisible = '0';
                    suggestions.style.display = 'none';
                    highlightedIndex = -1;
                    suggestionButtons = [];
                    disableReposition();
                };

                const highlightSuggestion = (index) => {
                    if (!suggestionButtons.length) {
                        highlightedIndex = -1;
                        return;
                    }

                    const maxIndex = suggestionButtons.length - 1;
                    let nextIndex = index;

                    if (index < 0) {
                        nextIndex = maxIndex;
                    } else if (index > maxIndex) {
                        nextIndex = 0;
                    }

                    suggestionButtons.forEach((button) => button.classList.remove('is-active'));

                    const button = suggestionButtons[nextIndex];
                    if (button) {
                        button.classList.add('is-active');
                        button.scrollIntoView({ block: 'nearest' });
                        highlightedIndex = nextIndex;
                    }
                };

                const selectOption = (option) => {
                    if (!option) {
                        return;
                    }

                    if (select.value !== option.value) {
                        select.value = option.value;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    const label = option.value ? formatOptionLabel(option) : '';
                    searchInput.value = label;
                    if (label) {
                        searchInput.title = label;
                    } else {
                        searchInput.removeAttribute('title');
                    }
                    closeSuggestions();
                };

                const renderSuggestions = (query) => {
                    ensureSuggestionsPortal();
                    const normalized = query.trim().toLowerCase();
                    const options = Array.from(select.options).filter((option) => !option.disabled);

                    const matches = options.filter((option) => {
                        if (!normalized) {
                            return true;
                        }
                        return formatOptionLabel(option).toLowerCase().includes(normalized);
                    });

                    suggestions.innerHTML = '';

                    if (!matches.length) {
                        const empty = document.createElement('div');
                        empty.className = 'category-suggestion-empty';
                        empty.textContent = 'Kategori tidak ditemukan';
                        suggestions.appendChild(empty);
                        suggestionButtons = [];
                        highlightedIndex = -1;
                    } else {
                        matches.forEach((option) => {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'category-suggestion';
                            button.dataset.optionIndex = option.index;
                            button.innerHTML = `
                                <div class="category-suggestion-label">${formatOptionLabel(option)}</div>
                            `;

                            button.addEventListener('mousedown', (event) => {
                                event.preventDefault();
                                selectOption(option);
                            });

                            suggestions.appendChild(button);
                        });
                        suggestionButtons = Array.from(suggestions.querySelectorAll('.category-suggestion'));
                        highlightedIndex = -1;
                    }

                    suggestions.classList.add('is-visible');
                    suggestions.dataset.isVisible = '1';
                    suggestions.style.display = 'block';
                    positionSuggestions();
                    enableReposition();
                };

                const syncInputWithSelection = () => {
                    const selectedOption = select.options[select.selectedIndex];
                    const label = selectedOption && selectedOption.value ? formatOptionLabel(selectedOption) : '';
                    if (searchInput.value !== label) {
                        searchInput.value = label;
                    }
                    if (label) {
                        searchInput.title = label;
                    } else {
                        searchInput.removeAttribute('title');
                    }
                };

                searchInput.addEventListener('focus', () => {
                    renderSuggestions(searchInput.value);
                });

                searchInput.addEventListener('input', () => {
                    renderSuggestions(searchInput.value);
                });

                searchInput.addEventListener('keydown', (event) => {
                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        if (!suggestions.classList.contains('is-visible')) {
                            renderSuggestions(searchInput.value);
                        }
                        if (suggestionButtons.length) {
                            const nextIndex = highlightedIndex === -1 ? 0 : highlightedIndex + 1;
                            highlightSuggestion(nextIndex);
                        }
                    } else if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        if (!suggestions.classList.contains('is-visible')) {
                            renderSuggestions(searchInput.value);
                        }
                        if (suggestionButtons.length) {
                            const nextIndex = highlightedIndex === -1 ? suggestionButtons.length - 1 : highlightedIndex - 1;
                            highlightSuggestion(nextIndex);
                        }
                    } else if (event.key === 'Enter') {
                        if (highlightedIndex >= 0 && suggestionButtons[highlightedIndex]) {
                            event.preventDefault();
                            const optionIndex = Number(suggestionButtons[highlightedIndex].dataset.optionIndex);
                            const option = select.options[optionIndex];
                            selectOption(option);
                            return;
                        }

                        const directMatch = Array.from(select.options).find((option) => {
                            return formatOptionLabel(option).toLowerCase() === searchInput.value.trim().toLowerCase();
                        });

                        if (directMatch) {
                            event.preventDefault();
                            selectOption(directMatch);
                        }
                    } else if (event.key === 'Escape') {
                        if (suggestions.classList.contains('is-visible')) {
                            event.preventDefault();
                            closeSuggestions();
                        }
                    }
                });

                searchInput.addEventListener('blur', () => {
                    window.setTimeout(() => {
                        closeSuggestions();
                        const trimmed = searchInput.value.trim();
                        if (!trimmed) {
                            searchInput.value = '';
                            select.value = '';
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        } else {
                            syncInputWithSelection();
                        }
                    }, 120);
                });

                select.addEventListener('change', () => {
                    syncInputWithSelection();
                });

                syncInputWithSelection();
                row._closeCategorySuggestions = closeSuggestions;
            }

            function enableProductSearch(row, select) {
                if (!row || !select) {
                    return;
                }

                const wrapper = row.querySelector('.product-select-wrapper');
                const searchInput = wrapper ? wrapper.querySelector('.product-search-input') : null;
                const suggestions = wrapper ? wrapper.querySelector('.product-suggestions') : null;
                const categorySelect = row.querySelector('.category-select');

                if (!wrapper || !searchInput || !suggestions || searchInput.dataset.searchInitialized === '1') {
                    return;
                }

                wrapper.classList.add('product-select-wrapper--enhanced');
                searchInput.dataset.searchInitialized = '1';
                row._productSuggestions = suggestions;

                let suggestionButtons = [];
                let highlightedIndex = -1;
                let repositionActive = false;

                const ensureSuggestionsPortal = () => {
                    if (!suggestions || suggestions.dataset.portaled === '1') {
                        return;
                    }
                    suggestions.dataset.portaled = '1';
                    document.body.appendChild(suggestions);
                    suggestions.style.position = 'absolute';
                };

                const positionSuggestions = () => {
                    if (!suggestions || suggestions.dataset.isVisible !== '1') {
                        return;
                    }
                    const rect = searchInput.getBoundingClientRect();
                    suggestions.style.width = `${rect.width}px`;
                    suggestions.style.left = `${rect.left + window.scrollX}px`;
                    suggestions.style.top = `${rect.bottom + 4 + window.scrollY}px`;
                };

                const handleViewportChange = () => {
                    positionSuggestions();
                };

                const enableReposition = () => {
                    if (repositionActive) {
                        return;
                    }
                    repositionActive = true;
                    window.addEventListener('resize', handleViewportChange);
                    window.addEventListener('scroll', handleViewportChange, true);
                };

                const disableReposition = () => {
                    if (!repositionActive) {
                        return;
                    }
                    repositionActive = false;
                    window.removeEventListener('resize', handleViewportChange);
                    window.removeEventListener('scroll', handleViewportChange, true);
                };

                const formatOptionLabel = (option) => (option ? (option.textContent || '').trim() : '');

                const formatOptionMeta = (option) => {
                    if (!option) {
                        return '';
                    }

                    const rawMinPrice = option.dataset.minPrice ?? '';
                    const rawMaxPrice = option.dataset.maxPrice ?? '';
                    const hasMinPrice = rawMinPrice !== '';
                    const hasMaxPrice = rawMaxPrice !== '';
                    let priceLabel = '';

                    if (hasMinPrice && hasMaxPrice && rawMinPrice !== rawMaxPrice) {
                        priceLabel = `Harga: ${formatCurrency(Number(rawMinPrice))} - ${formatCurrency(Number(rawMaxPrice))}`;
                    } else if (hasMinPrice) {
                        priceLabel = `Harga: ${formatCurrency(Number(rawMinPrice))}`;
                    }

                    const isUnlimited = option.dataset.isUnlimited === '1';
                    const stockLabel = isUnlimited
                        ? 'Stok: Tidak terbatas'
                        : (option.dataset.stockLabel ? `Stok: ${option.dataset.stockLabel}` : '');

                    const unitNames = option.dataset.unitsText || '';
                    const unitLabel = unitNames ? `Satuan: ${unitNames}` : '';
                    const categoryName = option.dataset.categoryName || '';
                    const categoryLabel = categoryName ? `Kategori: ${categoryName}` : '';
                    const parts = [priceLabel, unitLabel, stockLabel, categoryLabel].filter(Boolean);

                    return parts.join(' | ');
                };

                const closeSuggestions = () => {
                    suggestions.innerHTML = '';
                    suggestions.classList.remove('is-visible');
                    suggestions.dataset.isVisible = '0';
                    suggestions.style.display = 'none';
                    highlightedIndex = -1;
                    suggestionButtons = [];
                    disableReposition();
                };

                const highlightSuggestion = (index) => {
                    if (!suggestionButtons.length) {
                        highlightedIndex = -1;
                        return;
                    }

                    const maxIndex = suggestionButtons.length - 1;
                    let nextIndex = index;

                    if (index < 0) {
                        nextIndex = maxIndex;
                    } else if (index > maxIndex) {
                        nextIndex = 0;
                    }

                    suggestionButtons.forEach((btn) => btn.classList.remove('is-active'));

                    const button = suggestionButtons[nextIndex];
                    if (button) {
                        button.classList.add('is-active');
                        button.scrollIntoView({ block: 'nearest' });
                        highlightedIndex = nextIndex;
                    }
                };

                const selectOption = (option) => {
                    if (!option) {
                        return;
                    }

                    if (select.value !== option.value) {
                        select.value = option.value;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    searchInput.value = formatOptionLabel(option);
                    closeSuggestions();
                };

                const renderSuggestions = (query) => {
                    const normalized = query.trim().toLowerCase();
                    const options = Array.from(select.options).filter((option) => option.value && !option.hidden);
                    ensureSuggestionsPortal();

                    const matches = options
                        .filter((option) => {
                            if (!normalized) {
                                return true;
                            }

                            const haystack = [
                                option.textContent,
                                option.dataset.product,
                                option.dataset.productCode,
                                option.dataset.unitsText,
                            ]
                                .join(' ')
                                .toLowerCase();

                            return haystack.includes(normalized);
                        });

                    suggestions.innerHTML = '';

                    if (!matches.length) {
                        const empty = document.createElement('div');
                        empty.className = 'product-suggestion-empty';
                        empty.textContent = 'Produk tidak ditemukan';
                        suggestions.appendChild(empty);
                        suggestions.classList.add('is-visible');
                        highlightedIndex = -1;
                        suggestionButtons = [];
                        return;
                    }

                    matches.forEach((option) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'product-suggestion';
                        button.dataset.optionIndex = option.index;
                        button.innerHTML = `
                            <div class="product-suggestion-label">${formatOptionLabel(option)}</div>
                            <div class="product-suggestion-meta">${formatOptionMeta(option)}</div>
                        `;

                        button.addEventListener('mousedown', (event) => {
                            event.preventDefault();
                            selectOption(option);
                        });

                        suggestions.appendChild(button);
                    });

                    suggestionButtons = Array.from(suggestions.querySelectorAll('.product-suggestion'));
                    highlightedIndex = -1;
                    suggestions.classList.add('is-visible');
                    suggestions.dataset.isVisible = '1';
                    suggestions.style.display = 'block';
                    positionSuggestions();
                    enableReposition();
                };

                const syncInputWithSelection = () => {
                    const selectedOption = select.options[select.selectedIndex];
                    const label = selectedOption && selectedOption.value ? formatOptionLabel(selectedOption) : '';
                    if (searchInput.value !== label) {
                        searchInput.value = label;
                    }
                    if (label) {
                        searchInput.title = label;
                    } else {
                        searchInput.removeAttribute('title');
                    }
                };

                searchInput.addEventListener('focus', () => {
                    renderSuggestions(searchInput.value);
                });

                searchInput.addEventListener('input', () => {
                    renderSuggestions(searchInput.value);
                });

                searchInput.addEventListener('keydown', (event) => {
                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        if (!suggestions.classList.contains('is-visible')) {
                            renderSuggestions(searchInput.value);
                        }
                        if (suggestionButtons.length) {
                            const nextIndex = highlightedIndex === -1 ? 0 : highlightedIndex + 1;
                            highlightSuggestion(nextIndex);
                        }
                    } else if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        if (!suggestions.classList.contains('is-visible')) {
                            renderSuggestions(searchInput.value);
                        }
                        if (suggestionButtons.length) {
                            const nextIndex = highlightedIndex === -1 ? suggestionButtons.length - 1 : highlightedIndex - 1;
                            highlightSuggestion(nextIndex);
                        }
                    } else if (event.key === 'Enter') {
                        if (highlightedIndex >= 0 && suggestionButtons[highlightedIndex]) {
                            event.preventDefault();
                            const optionIndex = Number(suggestionButtons[highlightedIndex].dataset.optionIndex);
                            const option = select.options[optionIndex];
                            selectOption(option);
                            return;
                        }

                        const directMatch = Array.from(select.options).find((option) => {
                            if (!option.value) {
                                return false;
                            }
                            return formatOptionLabel(option).toLowerCase() === searchInput.value.trim().toLowerCase();
                        });

                        if (directMatch) {
                            event.preventDefault();
                            selectOption(directMatch);
                        }
                    } else if (event.key === 'Escape') {
                        if (suggestions.classList.contains('is-visible')) {
                            event.preventDefault();
                            closeSuggestions();
                        }
                    }
                });

                searchInput.addEventListener('blur', () => {
                    window.setTimeout(() => {
                        closeSuggestions();
                        syncInputWithSelection();
                    }, 120);
                });

                select.addEventListener('change', () => {
                    syncInputWithSelection();
                });

                if (categorySelect) {
                    categorySelect.addEventListener('change', () => {
                        if (document.activeElement === searchInput) {
                            renderSuggestions(searchInput.value);
                        } else {
                            closeSuggestions();
                        }
                    });
                }

                syncInputWithSelection();
                row._closeProductSuggestions = closeSuggestions;
            }

            function updateRow(row) {
                const productSelect = row.querySelector('.product-select');
                const unitSelect = row.querySelector('.unit-select');
                const quantityInput = row.querySelector('.quantity-input');
                const subtotalCell = row.querySelector('.item-subtotal');
                const searchInput = row.querySelector('.product-search-input');
                const categorySelect = row.querySelector('.category-select');
                const categorySearchInput = row.querySelector('.category-search-input');

                const productOption = productSelect ? productSelect.options[productSelect.selectedIndex] : null;
                const unitOption = unitSelect ? unitSelect.options[unitSelect.selectedIndex] : null;
                const price = unitOption ? Number(unitOption.dataset.price || 0) : 0;
                const quantityValue = quantityInput ? Number(quantityInput.value || 0) : 0;
                const quantity = Math.max(quantityValue, 0);
                const net = Math.max(price * quantity, 0);

                subtotalCell.textContent = formatCurrency(net);
                subtotalCell.dataset.value = net;

                if (productOption && productOption.value) {
                    if (categorySelect && !categorySelect.value) {
                        const optionCategory = productOption.dataset.categoryId || '';
                        if (optionCategory && categorySelect.value !== optionCategory) {
                            categorySelect.value = optionCategory;
                            filterProductsByCategory(row);
                        }
                    }

                    const label = (productOption.textContent || '').trim();
                    if (searchInput && searchInput.value !== label) {
                        searchInput.value = label;
                    }
                    if (searchInput) {
                        searchInput.title = label;
                    }
                } else {
                    if (searchInput) {
                        searchInput.removeAttribute('title');
                    }
                    if (searchInput && searchInput.value !== '') {
                        searchInput.value = '';
                    }
                }

                if (categorySelect) {
                    const selectedCategoryOption = categorySelect.options[categorySelect.selectedIndex];
                    const categoryValue = selectedCategoryOption ? selectedCategoryOption.value : '';
                    const categoryLabel =
                        selectedCategoryOption && categoryValue
                            ? (selectedCategoryOption.textContent || '').trim()
                            : '';
                    if (categorySearchInput && categorySearchInput.value !== categoryLabel) {
                        categorySearchInput.value = categoryLabel;
                    }
                    if (categorySearchInput) {
                        if (categoryLabel) {
                            categorySearchInput.title = categoryLabel;
                        } else {
                            categorySearchInput.removeAttribute('title');
                        }
                    }
                } else if (categorySearchInput) {
                    categorySearchInput.value = '';
                    categorySearchInput.removeAttribute('title');
                }

                updateTotals();
            }

            function attachRowEvents(row) {
                const productSelect = row.querySelector('.product-select');
                const unitSelect = row.querySelector('.unit-select');
                const quantityInput = row.querySelector('.quantity-input');
                const categorySelect = row.querySelector('.category-select');

                if (quantityInput) {
                    quantityInput.addEventListener('input', () => updateRow(row));
                    quantityInput.addEventListener('change', () => updateRow(row));
                }

                if (unitSelect) {
                    unitSelect.addEventListener('change', () => updateRow(row));
                }

                if (productSelect) {
                    productSelect.addEventListener('change', () => {
                        populateUnitSelect(row, { preserveSelection: false });
                        updateRow(row);
                    });
                }

                if (categorySelect) {
                    categorySelect.addEventListener('change', () => {
                        filterProductsByCategory(row);
                        updateRow(row);
                    });
                }

                filterProductsByCategory(row);
                enableSelectTypeahead(productSelect);
                enableProductSearch(row, productSelect);
                enableSelectTypeahead(categorySelect);
                enableCategorySearch(row, categorySelect);

                row.querySelector('.remove-item').addEventListener('click', () => {
                    if (itemsBody.querySelectorAll('.item-row').length === 1) {
                        updateRow(row);
                        return;
                    }
                    if (typeof row._closeProductSuggestions === 'function') {
                        row._closeProductSuggestions();
                    }
                    if (row._productSuggestions) {
                        row._productSuggestions.remove();
                        row._productSuggestions = null;
                    }
                    if (typeof row._closeCategorySuggestions === 'function') {
                        row._closeCategorySuggestions();
                    }
                    if (row._categorySuggestions) {
                        row._categorySuggestions.remove();
                        row._categorySuggestions = null;
                    }
                    row.remove();
                    updateTotals();
                    persistFormState();
                });

                updateRow(row);
            }

            function addRow(prefillData = null, { prepend = false } = {}) {
                const fragment = template.content.cloneNode(true);

                fragment.querySelectorAll('[data-name]').forEach((input) => {
                    const fieldName = input.dataset.name.replace('INDEX', rowIndex);
                    input.name = fieldName;
                });

                const row = fragment.querySelector('.item-row');
                if (prepend && itemsBody.firstChild) {
                    itemsBody.insertBefore(row, itemsBody.firstChild);
                } else {
                    itemsBody.appendChild(row);
                }
                attachRowEvents(row);
                if (prefillData) {
                    hydrateRowFromData(row, prefillData);
                }

                rowIndex += 1;
                persistFormState();
                return row;
            }

            if (addItemBtn) {
                addItemBtn.addEventListener('click', function () {
                    addRow();
                });
            }

            if (smartScanToggle) {
                smartScanToggle.addEventListener('click', () => {
                    const isActive = smartScanActive && smartScanPanel && !smartScanPanel.hidden;
                    if (isActive) {
                        stopSmartScan();
                        hideSmartScanPanel();
                        updateSmartScanStatus('Pemindaian dihentikan. Tekan ikon kamera untuk memulai lagi.', 'info');
                    } else {
                        startSmartScan();
                    }
                });
            }

            if (smartScanClose) {
                smartScanClose.addEventListener('click', () => {
                    stopSmartScan();
                    hideSmartScanPanel();
                    updateSmartScanStatus('Pemindaian ditutup.', 'info');
                });
            }

            if (smartScanBackdrop) {
                smartScanBackdrop.addEventListener('click', () => {
                    stopSmartScan();
                    hideSmartScanPanel();
                    updateSmartScanStatus('Pemindaian ditutup.', 'info');
                });
            }

            let restoredFromDraft = false;
            if (!hasOldInput && !isEditing) {
                restoredFromDraft = restorePersistedFormState();
            }

            if (!restoredFromDraft) {
                itemsBody.querySelectorAll('.item-row').forEach((row) => {
                    attachRowEvents(row);
                });
            }

            if (paymentMethodSelect) {
                paymentMethodSelect.addEventListener('change', () => {
                    const total = calculateCurrentTotal();
                    autoFillPaymentForMethod(total);
                });
            }

            if (paymentAmountDisplay) {
                paymentAmountDisplay.addEventListener('input', (event) => {
                    syncPaymentInputs(event.target.value);
                });

                paymentAmountDisplay.addEventListener('blur', (event) => {
                    syncPaymentInputs(event.target.value);
                });

                syncPaymentInputs(paymentAmountDisplay.value);
            }

            if (posForm) {
                const handleFormMutation = () => {
                    persistFormState();
                };

                posForm.addEventListener('input', handleFormMutation);
                posForm.addEventListener('change', handleFormMutation);

                posForm.addEventListener('submit', () => {
                    if (paymentAmountDisplay) {
                        syncPaymentInputs(paymentAmountDisplay.value);
                    }
                    stopSmartScan();
                    hideSmartScanPanel();
                    clearPersistedFormState();
                });

                posForm.addEventListener('reset', () => {
                    window.requestAnimationFrame(() => {
                        if (paymentAmountDisplay) {
                            syncPaymentInputs(paymentAmountDisplay.value);
                        }
                        stopSmartScan();
                        hideSmartScanPanel();
                        clearPersistedFormState();
                    });
                });
            }

            window.addEventListener('pagehide', () => {
                stopSmartScan();
            });

            updateTotals();
        });
    </script>
@endpush
