@extends('layouts.admin')

@section('title', 'Korelasi Stok & Pembelian')

@push('styles')
    <style>
        .report-shell {
            max-width: none;
            margin: 0;
            width: 100%;
            display: grid;
            gap: 14px;
        }

        .report-hero {
            background: linear-gradient(135deg, #fff1f2 0%, #fff7ed 55%, #ffffff 100%);
            border-radius: 16px;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: 0 16px 36px rgba(185, 28, 28, 0.14);
            border: 1px solid #fecdd3;
            flex-wrap: wrap;
        }

        .hero-content {
            display: grid;
            gap: 6px;
        }

        .title-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .title-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 0.2px;
            background: #b91c1c;
            color: #fff;
        }

        .report-hero .meta {
            margin: 0;
            color: #475569;
            font-size: 12.5px;
        }

        .report-hero .page-title {
            font-size: 22px;
        }

        .hero-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .report-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #0f172a;
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .report-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.1);
        }

        .hero-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 11px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
        }

        .filters-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            display: grid;
            gap: 10px;
        }

        .quick-filters {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .quick-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
        }

        .quick-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #0f172a;
            font-weight: 700;
            font-size: 11px;
            text-decoration: none;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }

        .quick-pill.active {
            background: #b91c1c;
            border-color: #b91c1c;
            color: #fff;
            box-shadow: 0 10px 18px rgba(185, 28, 28, 0.2);
        }

        .quick-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            align-items: end;
        }

        .filters-grid label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 5px;
            display: block;
        }

        .filters-grid input,
        .filters-grid select {
            padding: 8px 10px;
            border-radius: 9px;
            border: 1px solid #d0d5dd;
            width: 100%;
            background: #fff;
            font-size: 12.5px;
        }

        .category-combobox {
            position: relative;
        }

        .combo-list {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            max-height: 240px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
            padding: 6px 0;
            display: none;
            z-index: 10;
        }

        .combo-item {
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.1s ease;
        }

        .combo-item:hover,
        .combo-item.is-active {
            background: #fee2e2;
        }

        .filters-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .report-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 10px;
            padding: 9px 14px;
            font-weight: 700;
            font-size: 12.5px;
            letter-spacing: 0.2px;
            cursor: pointer;
            color: #ffffff;
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
            text-decoration: none;
        }

        .report-button--primary {
            background: linear-gradient(135deg, #b91c1c, #7f1d1d);
            box-shadow: 0 10px 22px rgba(185, 28, 28, 0.22);
        }

        .report-button--primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(185, 28, 28, 0.26);
        }

        .report-button--ghost {
            background: #e2e8f0;
            color: #0f172a;
            box-shadow: none;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .stat-title {
            margin: 0;
            font-size: 12px;
            color: #475569;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 800;
            margin-top: 6px;
        }

        .stat-muted {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .info-banner {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            display: grid;
            gap: 10px;
        }

        .info-title {
            font-weight: 800;
            font-size: 13px;
            color: #0f172a;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 8px;
        }

        .info-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 10px;
            display: grid;
            gap: 4px;
        }

        .info-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
        }

        .info-value {
            font-size: 12px;
            color: #0f172a;
            font-weight: 700;
            word-break: break-word;
        }

        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }

        .table-modern th,
        .table-modern td {
            padding: 9px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 12.5px;
            vertical-align: top;
        }

        .table-modern th {
            background: #f8fafc;
            color: #1f2937;
            letter-spacing: 0.01em;
            font-weight: 700;
        }

        .table-modern tr:last-child td {
            border-bottom: none;
        }

        .sort-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: inherit;
            font-weight: 700;
        }

        .sort-link span {
            font-size: 11px;
            color: #b91c1c;
        }

        .text-right {
            text-align: right;
        }

        .product-name {
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
        }

        .product-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }

        .product-sub.is-inactive {
            color: #b91c1c;
        }

        .ratio-cell {
            display: grid;
            gap: 4px;
        }

        .ratio-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
        }

        .ratio-bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, #b91c1c, #f97316);
        }

        .ratio-value {
            font-size: 11px;
            color: #475569;
            font-weight: 700;
        }

        .gap {
            font-weight: 700;
            font-size: 12px;
        }

        .gap--warn {
            color: #c2410c;
        }

        .gap--ok {
            color: #166534;
        }

        .gap--neutral {
            color: #64748b;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 0.02em;
        }

        .pill--ok {
            background: #ecfdf3;
            color: #166534;
        }

        .pill--warn {
            background: #fff7ed;
            color: #9a3412;
        }

        .pill--danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .pagination-wrap {
            padding: 14px 18px;
            border-top: 1px solid #e2e8f0;
            background: #fff;
        }

        .pagination-wrap nav[aria-label="Pagination Navigation"] {
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 12px;
        }

        .pagination-wrap nav[aria-label="Pagination Navigation"] > :first-child {
            display: none;
        }

        .pagination-wrap nav[aria-label="Pagination Navigation"] > :last-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .pagination-wrap nav[aria-label="Pagination Navigation"] > :last-child > :last-child {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination-wrap nav[aria-label="Pagination Navigation"] .relative.inline-flex {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            background: #fff;
            color: #475467;
            text-decoration: none;
            min-width: 32px;
            font-weight: 600;
        }

        .pagination-wrap nav[aria-label="Pagination Navigation"] span[aria-current="page"] > span {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #fff;
        }

        .pagination-wrap nav[aria-label="Pagination Navigation"] svg {
            width: 16px;
            height: 16px;
        }

        .pagination-wrap nav[aria-label="Pagination Navigation"] p {
            margin: 0;
            color: #64748b;
        }

        @media (max-width: 640px) {
            .pagination-wrap nav[aria-label="Pagination Navigation"] > :first-child {
                display: flex;
                justify-content: space-between;
                gap: 12px;
            }

            .pagination-wrap nav[aria-label="Pagination Navigation"] > :last-child {
                display: none;
            }
        }

        @media (max-width: 900px) {
            .filters-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .report-hero {
                padding: 12px;
            }

            .hero-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .hero-actions .report-link {
                flex: 1 1 auto;
                justify-content: center;
            }

            .hero-chips {
                width: 100%;
            }

            .filters-card {
                padding: 10px;
            }

            .quick-filters {
                gap: 6px;
            }

            .filters-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .filters-actions {
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .table-modern th,
            .table-modern td {
                white-space: nowrap;
            }
        }

        @media (max-width: 640px) {
            .report-hero .page-title {
                font-size: 18px;
            }

            .title-badge {
                font-size: 10px;
            }

            .hero-chip {
                font-size: 10px;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .hero-actions .report-link {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
        @php
            $today = \Illuminate\Support\Carbon::now();
            $todayDate = $today->toDateString();
            $range7 = $today->copy()->subDays(6)->toDateString();
            $range30 = $today->copy()->subDays(29)->toDateString();
            $range90 = $today->copy()->subDays(89)->toDateString();

        $overallDenom = $summary['totalPurchased'] + $summary['totalStock'];
        $overallRatio = $overallDenom > 0
            ? round(($summary['totalPurchased'] / $overallDenom) * 100, 1)
            : 0;

        $isQuick7 = $filters['start_date'] === $range7 && $filters['end_date'] === $todayDate;
        $isQuick30 = $filters['start_date'] === $range30 && $filters['end_date'] === $todayDate;
        $isQuick90 = $filters['start_date'] === $range90 && $filters['end_date'] === $todayDate;
        $currentSort = $filters['sort'] ?? 'priority';
        $ratioSortTarget = $currentSort === 'ratio_desc' ? 'ratio_asc' : 'ratio_desc';
        $ratioSortLabel = $currentSort === 'ratio_desc' ? '▼' : ($currentSort === 'ratio_asc' ? '▲' : '⇅');
    @endphp

    <div class="report-shell">
        <div class="report-hero">
            <div class="hero-content">
                <div class="title-row">
                    <h1 class="page-title" style="margin:0;">Korelasi Stok & Pembelian</h1>
                    <span class="title-badge">Admin Insight</span>
                </div>
                <p class="meta">Periode: {{ $summary['rangeLabel'] }}</p>
                <div class="hero-chips">
                    <span class="hero-chip">Produk: {{ number_format($summary['productCount']) }}</span>
                    <span class="hero-chip">Restock: {{ number_format($summary['restockCandidates']) }}</span>
                    <span class="hero-chip">Batas stok rendah: <= {{ $lowStockThreshold }}</span>
                    <span class="hero-chip">Rasio serap: {{ $overallRatio > 0 ? number_format($overallRatio, 1) . '%' : '-' }}</span>
                </div>
                <p class="meta">Pembelian dihitung dari transaksi penjualan pelanggan.</p>
            </div>
            <div class="hero-actions">
                <a href="{{ route('admin.dashboard') }}" class="report-link">Dashboard Admin</a>
                <a href="{{ route('admin.reports.stock') }}" class="report-link">Laporan Persediaan</a>
            </div>
        </div>

        <div class="filters-card">
            <div class="quick-filters">
                <span class="quick-label">Filter cepat:</span>
                <a
                    href="{{ route('admin.reports.stock_correlation', ['start_date' => $range7, 'end_date' => $todayDate, 'category_id' => $filters['category_id'], 'status' => $filters['status'], 'sort' => $filters['sort'] ?? 'priority']) }}"
                    class="quick-pill {{ $isQuick7 ? 'active' : '' }}"
                >
                    7 Hari
                </a>
                <a
                    href="{{ route('admin.reports.stock_correlation', ['start_date' => $range30, 'end_date' => $todayDate, 'category_id' => $filters['category_id'], 'status' => $filters['status'], 'sort' => $filters['sort'] ?? 'priority']) }}"
                    class="quick-pill {{ $isQuick30 ? 'active' : '' }}"
                >
                    30 Hari
                </a>
                <a
                    href="{{ route('admin.reports.stock_correlation', ['start_date' => $range90, 'end_date' => $todayDate, 'category_id' => $filters['category_id'], 'status' => $filters['status'], 'sort' => $filters['sort'] ?? 'priority']) }}"
                    class="quick-pill {{ $isQuick90 ? 'active' : '' }}"
                >
                    90 Hari
                </a>
            </div>

            <form method="GET" action="{{ route('admin.reports.stock_correlation') }}">
                <div class="filters-grid">
                    <div>
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $filters['start_date'] }}">
                    </div>
                    <div>
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $filters['end_date'] }}">
                    </div>
                    <div>
                        @php
                            $selectedCategory = $categories->firstWhere('id', $filters['category_id']);
                            $selectedCategoryName = $selectedCategory->name ?? '';
                        @endphp
                        <label for="category_input">Kategori</label>
                        <div
                            class="category-combobox"
                            data-options='@json($categories->map(fn($c) => ["id" => $c->id, "name" => $c->name]))'
                        >
                            <input
                                type="text"
                                id="category_input"
                                name="category_name"
                                value="{{ $selectedCategoryName }}"
                                placeholder="Cari kategori..."
                                autocomplete="off"
                            >
                            <input type="hidden" name="category_id" id="category_id" value="{{ $filters['category_id'] }}">
                            <div class="combo-list" id="category_list"></div>
                        </div>
                    </div>
                    <div>
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Semua</option>
                            <option value="active" @selected(($filters['status'] ?? '') === 'active')>Aktif</option>
                            <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Tidak Aktif</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort">Urutkan</label>
                        <select id="sort" name="sort">
                            <option value="priority" @selected(($filters['sort'] ?? 'priority') === 'priority')>Prioritas (Kritis - Aman)</option>
                            <option value="purchase_desc" @selected(($filters['sort'] ?? '') === 'purchase_desc')>Pembelian (Tinggi - Rendah)</option>
                            <option value="purchase_asc" @selected(($filters['sort'] ?? '') === 'purchase_asc')>Pembelian (Rendah - Tinggi)</option>
                            <option value="ratio_desc" @selected(($filters['sort'] ?? '') === 'ratio_desc')>Rasio Serap (Tinggi - Rendah)</option>
                            <option value="ratio_asc" @selected(($filters['sort'] ?? '') === 'ratio_asc')>Rasio Serap (Rendah - Tinggi)</option>
                            <option value="gap_desc" @selected(($filters['sort'] ?? '') === 'gap_desc')>Selisih (Tinggi - Rendah)</option>
                            <option value="gap_asc" @selected(($filters['sort'] ?? '') === 'gap_asc')>Selisih (Rendah - Tinggi)</option>
                            <option value="stock_asc" @selected(($filters['sort'] ?? '') === 'stock_asc')>Stok (Terendah)</option>
                            <option value="stock_desc" @selected(($filters['sort'] ?? '') === 'stock_desc')>Stok (Tertinggi)</option>
                            <option value="last_purchase_desc" @selected(($filters['sort'] ?? '') === 'last_purchase_desc')>Terakhir Beli (Terbaru)</option>
                            <option value="last_purchase_asc" @selected(($filters['sort'] ?? '') === 'last_purchase_asc')>Terakhir Beli (Terlama)</option>
                        </select>
                    </div>
                    <div class="filters-actions">
                        <button type="submit" class="report-button report-button--primary">Terapkan</button>
                        <a href="{{ route('admin.reports.stock_correlation') }}" class="report-button report-button--ghost">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total stok (filter)</div>
                <div class="stat-value">{{ number_format($summary['totalStock']) }}</div>
                <div class="stat-muted">Stok saat ini untuk produk terfilter</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total pembelian (qty)</div>
                <div class="stat-value">{{ number_format($summary['totalPurchased']) }}</div>
                <div class="stat-muted">Akumulasi transaksi di periode terpilih</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Produk terpantau</div>
                <div class="stat-value">{{ number_format($summary['productCount']) }} produk</div>
                <div class="stat-muted">Mengikuti filter yang dipilih</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Prioritas restock</div>
                <div class="stat-value">{{ number_format($summary['restockCandidates']) }} produk</div>
                <div class="stat-muted">Stok <= {{ $lowStockThreshold }} atau pembelian >= stok</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Rasio serap total</div>
                <div class="stat-value">{{ $overallRatio > 0 ? number_format($overallRatio, 1) . '%' : '-' }}</div>
                <div class="stat-muted">Pembelian / (pembelian + stok)</div>
            </div>
        </div>

        <div class="info-banner">
            <div class="info-title">Panduan Analitik</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Definisi pembelian</div>
                    <div class="info-value">Diambil dari transaksi penjualan pelanggan.</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Rasio serap</div>
                    <div class="info-value">Pembelian / (pembelian + stok).</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Prioritas</div>
                    <div class="info-value">Kritis: stok <= {{ $lowStockThreshold }}. Perlu: pembelian >= stok.</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Selisih</div>
                    <div class="info-value">Pembelian dikurangi stok untuk melihat gap persediaan.</div>
                </div>
            </div>
        </div>

        <div class="card" style="padding:0; overflow:hidden;">
            @if ($products->isEmpty())
                <p class="muted" style="padding: 16px 18px;">Tidak ada data untuk filter yang dipilih.</p>
            @else
                <div class="table-scroll">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th style="min-width: 70px;">ID</th>
                                <th style="min-width: 240px;">Produk</th>
                                <th style="min-width: 160px;">Kategori</th>
                                <th class="text-right" style="min-width: 120px;">Stok</th>
                                <th class="text-right" style="min-width: 120px;">Pembelian</th>
                                <th class="text-right" style="min-width: 110px;">Selisih</th>
                                <th style="min-width: 170px;">
                                    <a
                                        href="{{ request()->fullUrlWithQuery(['sort' => $ratioSortTarget]) }}"
                                        class="sort-link"
                                        title="Urutkan rasio serap"
                                    >
                                        Rasio Serap <span>{{ $ratioSortLabel }}</span>
                                    </a>
                                </th>
                                <th style="min-width: 140px;">Terakhir</th>
                                <th style="min-width: 120px;">Prioritas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @php
                                    $purchaseQty = (int) ($product->purchase_qty ?? 0);
                                    $ratio = (float) ($product->purchase_ratio ?? 0);
                                    $ratioPercent = $ratio > 0 ? min(100, $ratio) : 0;
                                    $gap = $purchaseQty - $product->stock;
                                    $gapLabel = ($gap > 0 ? '+' : '') . number_format($gap);
                                    $needsRestock = $product->stock <= $lowStockThreshold
                                        || ($purchaseQty > 0 && $purchaseQty >= $product->stock);

                                    $priorityLabel = 'Aman';
                                    $priorityClass = 'pill--ok';
                                    if ($product->stock <= $lowStockThreshold) {
                                        $priorityLabel = 'Kritis';
                                        $priorityClass = 'pill--danger';
                                    } elseif ($purchaseQty > 0 && $purchaseQty >= $product->stock) {
                                        $priorityLabel = 'Perlu';
                                        $priorityClass = 'pill--warn';
                                    }
                                @endphp
                                <tr>
                                    <td>#{{ $product->id }}</td>
                                    <td>
                                        <div class="product-name">{{ $product->name }}</div>
                                        <div class="product-sub {{ $product->is_active ? '' : 'is-inactive' }}">
                                            {{ $product->is_active ? 'Aktif' : 'Tidak aktif' }}
                                        </div>
                                    </td>
                                    <td>{{ optional($product->category)->name ?? 'Tanpa Kategori' }}</td>
                                    <td class="text-right">{{ number_format($product->stock) }} {{ $product->unit }}</td>
                                    <td class="text-right">{{ number_format($purchaseQty) }}</td>
                                    <td class="text-right">
                                        <span class="gap {{ $gap > 0 ? 'gap--warn' : ($gap < 0 ? 'gap--ok' : 'gap--neutral') }}">
                                            {{ $gapLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="ratio-cell">
                                            <div class="ratio-bar">
                                                <span style="width: {{ $ratioPercent }}%;"></span>
                                            </div>
                                            <div class="ratio-value">{{ $ratio > 0 ? number_format($ratio, 1) . '%' : '-' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $product->last_purchase ? \Illuminate\Support\Carbon::parse($product->last_purchase)->format('d M Y') : 'Belum ada' }}
                                    </td>
                                    <td>
                                        <span class="pill {{ $priorityClass }}">{{ $priorityLabel }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrap">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const combo = document.querySelector('.category-combobox');
            if (!combo) return;

            const input = combo.querySelector('#category_input');
            const hidden = combo.querySelector('#category_id');
            const list = combo.querySelector('#category_list');
            const optionsData = (() => {
                try {
                    return JSON.parse(combo.dataset.options || '[]');
                } catch {
                    return [];
                }
            })();

            let filtered = optionsData.slice(0);

            const render = () => {
                list.innerHTML = '';
                filtered.forEach((opt) => {
                    const item = document.createElement('div');
                    item.className = 'combo-item';
                    item.textContent = opt.name;
                    item.dataset.id = opt.id;
                    item.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        applySelection(opt);
                    });
                    list.appendChild(item);
                });
                list.style.display = filtered.length ? 'block' : 'none';
            };

            const applySelection = (opt) => {
                input.value = opt ? opt.name : '';
                hidden.value = opt ? opt.id : '';
                list.style.display = 'none';
            };

            const filter = (query) => {
                const q = query.trim().toLowerCase();
                filtered = q
                    ? optionsData.filter((opt) => opt.name.toLowerCase().includes(q))
                    : optionsData.slice(0);
                render();
            };

            input.addEventListener('focus', () => {
                filter(input.value);
            });

            input.addEventListener('input', () => {
                filter(input.value);
            });

            input.addEventListener('blur', () => {
                setTimeout(() => {
                    list.style.display = 'none';
                }, 120);
            });

            if (hidden.value) {
                const existing = optionsData.find((opt) => String(opt.id) === String(hidden.value));
                if (existing) {
                    input.value = existing.name;
                }
            }
        });
    </script>
@endpush
