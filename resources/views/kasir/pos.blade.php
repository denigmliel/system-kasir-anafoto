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
                @else
                    Pilih produk dan tentukan jumlah. Sistem akan otomatis menghitung subtotal dan kembalian.
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

                <div class="form-grid" style="margin-bottom: 20px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
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

                @php
                    $defaultItems = [
                        ['category_id' => null, 'product_id' => null, 'product_unit_id' => null, 'quantity' => 1],
                    ];
                    $oldItems = old('items', $prefilledItems ?: $defaultItems);
                @endphp

                <div class="card" style="padding: 32px; border: 1px dashed #d0d5dd; background-color: #f9fbff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h3 style="margin: 0; font-size: 18px;">Daftar Item</h3>
                        <button type="button" class="btn btn-secondary" id="add-item-btn">Tambah Item</button>
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
                    value="0"
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
            const LOW_STOCK_THRESHOLD = 5;
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

                const isQris = paymentMethodSelect.value === 'qris';
                paymentAmountDisplay.readOnly = isQris;

                if (!isQris) {
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

            function addRow(prefillData = null) {
                const fragment = template.content.cloneNode(true);

                fragment.querySelectorAll('[data-name]').forEach((input) => {
                    const fieldName = input.dataset.name.replace('INDEX', rowIndex);
                    input.name = fieldName;
                });

                const row = fragment.querySelector('.item-row');
                itemsBody.appendChild(row);
                attachRowEvents(row);
                if (prefillData) {
                    hydrateRowFromData(row, prefillData);
                }

                rowIndex += 1;
                persistFormState();
            }

            addItemBtn.addEventListener('click', function () {
                addRow();
            });

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
                    clearPersistedFormState();
                });

                posForm.addEventListener('reset', () => {
                    window.requestAnimationFrame(() => {
                        if (paymentAmountDisplay) {
                            syncPaymentInputs(paymentAmountDisplay.value);
                        }
                        clearPersistedFormState();
                    });
                });
            }

            updateTotals();
        });
    </script>
@endpush
