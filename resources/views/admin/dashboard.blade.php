@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
    <style>
        .dashboard-hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 8px;
            padding: 10px;
            border-radius: 9px;
            background: linear-gradient(135deg, #fee2e2 0%, #fff7ed 45%, #ecfeff 100%);
            border: 1px solid #fca5a5;
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.08);
            margin-bottom: 8px;
        }

        .hero-title {
            margin: 0;
            font-size: 19px;
            font-weight: 700;
            color: #0f172a;
        }

        .hero-subtitle {
            margin: 2px 0 6px;
            color: #475569;
        }

        .pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .pill-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 8px;
            border-radius: 7px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #b91c1c;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.04);
        }

        .pill-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        }

        .hero-highlight {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            padding: 9px;
            display: grid;
            gap: 4px;
            align-content: center;
        }

        .hero-highlight .value {
            font-size: 16px;
            font-weight: 800;
            color: #b91c1c;
        }

        .hero-highlight .label {
            color: #475569;
            font-weight: 600;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-bottom: 8px;
        }

        .stat-card {
            border: 1px solid #e5e7eb;
            border-radius: 9px;
            padding: 9px;
            background: #fff;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
            display: grid;
            gap: 3px;
        }

        .stat-label {
            color: #475569;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-value {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        .stat-sub {
            color: #6b7280;
            font-size: 10px;
        }

        .chart-layout {
            grid-template-columns: 2fr 1fr;
            align-items: start;
        }

        .chart-wrapper {
            position: relative;
            height: 200px;
        }

        .chart-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .range-switch {
            display: inline-flex;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            background: #fff;
        }

        .range-btn {
            padding: 7px 10px;
            border: none;
            background: transparent;
            font-weight: 700;
            color: #475569;
            cursor: pointer;
            transition: background 0.12s ease, color 0.12s ease;
        }

        .range-btn.active {
            background: #b91c1c;
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
        }

        .trend-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 9px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-weight: 700;
            color: #0f172a;
        }

        .trend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .trend-up { background: #22c55e; }
        .trend-down { background: #ef4444; }
        .trend-flat { background: #94a3b8; }

        .metric-large {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .metric-sub {
            color: #64748b;
            font-size: 12px;
            margin: 0;
        }

        .table-scroll {
            max-height: 200px;
            overflow: auto;
            margin-top: 4px;
            border-radius: 9px;
            border: 1px solid #e5e7eb;
        }

        .compact-table {
            width: 100%;
            border-collapse: collapse;
        }

        .compact-table th,
        .compact-table td {
            padding: 5px 7px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }

        .compact-table thead th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            z-index: 1;
            font-weight: 700;
            color: #0f172a;
        }

        .list-group {
            display: grid;
            gap: 4px;
            margin: 6px 0 0;
        }

        .list-item {
            padding: 6px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
        }

        .item-meta {
            color: #64748b;
            font-size: 9px;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fef2f2;
            color: #b91c1c;
            border-radius: 999px;
            padding: 3px 5px;
            font-weight: 700;
            font-size: 9px;
        }

        .enterprise-grid {
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 10px;
        }

        .alert-critical {
            border: 1px solid #fecdd3;
            background: linear-gradient(120deg, #fef2f2, #fff1f2);
            color: #b91c1c;
            padding: 12px;
            border-radius: 10px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            box-shadow: 0 10px 24px rgba(185, 28, 28, 0.08);
            margin-bottom: 10px;
        }

        .alert-critical .alert-body {
            display: grid;
            gap: 4px;
        }

        .alert-critical .alert-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            text-decoration: none;
            background: #fff;
            color: #b91c1c;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .mini-table th,
        .mini-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
            font-size: 12px;
        }

        .mini-table th {
            color: #0f172a;
            font-weight: 700;
            background: #f8fafc;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
        }

        .cashier-meta {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .closing-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .closing-table th,
        .closing-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
            font-size: 12px;
            white-space: nowrap;
        }

        .closing-table th {
            background: #f8fafc;
            color: #0f172a;
            font-weight: 700;
        }

        .pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
            font-weight: 700;
            font-size: 11px;
        }

        #snapshot-clock {
            font-size: 12px;
            padding: 6px 10px;
        }

        .link-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 8px;
            border-radius: 7px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #b91c1c;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.04);
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }

        .link-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.07);
        }

        .recap-combo {
            position: relative;
            min-width: 170px;
        }

        .recap-trigger {
            width: 100%;
            padding: 7px 10px;
            border-radius: 8px;
            border: 1px solid #fca5a5;
            background: linear-gradient(180deg, #fff, #fff7f7);
            font-weight: 700;
            font-size: 13px;
            color: #0f172a;
            box-shadow: 0 8px 20px rgba(185, 28, 28, 0.08);
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .recap-trigger:hover {
            border-color: #ef4444;
            box-shadow: 0 10px 26px rgba(185, 28, 28, 0.12);
        }

        .recap-trigger:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 10px 26px rgba(185, 28, 28, 0.16);
        }

        .recap-combo.open .recap-trigger {
            border-color: #dc2626;
            box-shadow: 0 12px 28px rgba(185, 28, 28, 0.18);
        }

        .recap-trigger span {
            display: inline-block;
        }

        .recap-trigger .chevron {
            font-size: 12px;
            color: #dc2626;
            transition: transform 0.15s ease, color 0.15s ease;
        }

        .recap-combo.open .recap-trigger .chevron {
            transform: rotate(180deg);
            color: #b91c1c;
        }

        .recap-options {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            left: 0;
            background: #fff;
            border: 1px solid #fecdd3;
            border-radius: 12px;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.16);
            padding: 6px;
            display: none;
            z-index: 10;
        }

        .recap-options.open {
            display: grid;
            gap: 8px;
        }

        .recap-option {
            text-align: left;
            width: 100%;
            border: 1px solid #f1f5f9;
            background: #fff;
            color: #0f172a;
            border-radius: 10px;
            padding: 8px 10px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.12s ease;
        }

        .recap-option:hover {
            border-color: #fecdd3;
            background: #fff7f7;
            color: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(185, 28, 28, 0.08);
        }

        .recap-option.active {
            background: #b91c1c;
            color: #fff;
            border-color: #b91c1c;
            box-shadow: 0 12px 28px rgba(185, 28, 28, 0.22);
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 8px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #0f172a;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }

        .download-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.16);
        }

        @media (max-width: 960px) {
            .chart-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .dashboard-hero,
            .stats-grid,
            .chart-layout,
            .enterprise-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-hero { gap: 6px; padding: 8px; }
            .hero-title { font-size: 18px; }
            .hero-highlight .value { font-size: 15px; }

            .card { padding: 10px; }
            .chart-wrapper { height: 180px; }
            .metric-large { font-size: 20px; }
            .chart-actions { gap: 6px; }
            .range-btn { padding: 6px 8px; }
            .trend-chip { padding: 6px 8px; font-size: 12px; }

            .mini-table th,
            .mini-table td,
            .closing-table th,
            .closing-table td {
                font-size: 11px;
                padding: 6px;
            }

            .list-item { flex-direction: column; align-items: flex-start; gap: 6px; }
            .item-title { font-size: 12px; }
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-hero">
        <div>
            <p class="tag" style="margin: 0 0 10px;" id="snapshot-clock">
                <span id="wib-clock">{{ $todayLabel }}</span>
            </p>
            <h1 class="hero-title">Dashboard Admin</h1>
        </div>
        <div class="hero-highlight">
            <div class="label">Pendapatan Bulan Ini</div>
            <div class="value">Rp{{ number_format($metrics['monthSalesTotal'], 0, ',', '.') }}</div>
            <div class="stat-sub">Transaksi: {{ number_format($metrics['monthTransactionCount']) }}x</div>
        </div>
    </div>

    @if ($metrics['lowStockCount'] > 0)
        <div class="alert-critical">
            <div class="alert-body">
                <strong>Notifikasi Stok Menipis</strong>
                <div>Ada {{ number_format($metrics['lowStockCount']) }} produk dengan stok &le; {{ $lowStockThreshold }}. Segera lakukan restock.</div>
            </div>
            <div class="alert-actions">
                <a href="{{ route('gudang.products.low_stock') }}" class="btn-link">Lihat daftar</a>
            </div>
        </div>
    @endif

    <div class="grid stats-grid">
        <div class="stat-card">
            <div class="stat-label">
                Transaksi Hari Ini
                <span class="tag">Live</span>
            </div>
            <div class="stat-value">{{ number_format($metrics['todayTransactionCount']) }}x</div>
            <div class="stat-sub">
                Total: Rp{{ number_format($metrics['todaySalesTotal'], 0, ',', '.') }} · Rata-rata: Rp{{ number_format($metrics['todayAvgOrder'], 0, ',', '.') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-value">Rp{{ number_format($metrics['todaySalesTotal'], 0, ',', '.') }}</div>
            <div class="stat-sub">Data diambil dari seluruh kasir aktif.</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Produk Aktif</div>
            <div class="stat-value">{{ number_format($metrics['activeProductCount']) }}</div>
            <div class="stat-sub">Total produk terdaftar: {{ number_format($metrics['productCount']) }} item.</div>
        </div>
    </div>

    @php
        $formatIdr = fn ($value) => 'Rp' . number_format($value, 0, ',', '.');
        $defaultRevenue = $revenue7Days;
        $trendLabel = $defaultRevenue['trend'] === 'up' ? 'Naik' : ($defaultRevenue['trend'] === 'down' ? 'Turun' : 'Stabil');
        $trendDiff = $defaultRevenue['change_pct'] !== null
            ? ($defaultRevenue['change'] >= 0 ? '+' : '-') . number_format(abs($defaultRevenue['change_pct']), 1, ',', '.') . '%'
            : ($defaultRevenue['change'] >= 0 ? '+' : '-') . $formatIdr(abs($defaultRevenue['change']));
    @endphp

    <div class="grid chart-layout" style="gap: 9px; margin-bottom: 9px;">
        <div class="card">
            <div class="chart-actions">
                <div>
                    <h2 style="margin: 0; font-size: 15px;">Grafik Pendapatan</h2>
                    <p class="muted" style="margin: 3px 0 0;">Performa 7 / 30 hari terakhir (Rp)</p>
                </div>
                <div class="range-switch" id="revenue-range">
                    <button type="button" class="range-btn active" data-range="7">7 Hari</button>
                    <button type="button" class="range-btn" data-range="30">30 Hari</button>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                <div>
                    <p class="metric-sub" style="margin: 0;">Total periode aktif</p>
                    <p class="metric-large" id="revenue-total">{{ $formatIdr($defaultRevenue['total']) }}</p>
                    <p class="metric-sub" id="revenue-compare" style="margin-top: 4px;">{{ $formatIdr($defaultRevenue['previous_total']) }} periode sebelumnya</p>
                </div>
                <div class="trend-chip" id="revenue-trend" data-trend="{{ $defaultRevenue['trend'] }}">
                    <span class="trend-dot trend-{{ $defaultRevenue['trend'] }}" id="revenue-trend-dot"></span>
                    <span id="revenue-trend-label">{{ $trendLabel }}</span>
                    <span id="revenue-trend-diff">{{ $trendDiff }} vs periode sebelumnya</span>
                </div>
            </div>
            <div class="chart-wrapper">
                <canvas id="revenue-chart"></canvas>
            </div>
        </div>

        @php
            $activeRecapLabel = collect($recapOptionList)->firstWhere('active', true)['label'] ?? $recapHeading;
        @endphp
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 9px; flex-wrap: wrap;">
                <div>
                    <h2 style="margin: 0; font-size: 15px;">{{ $recapHeading }}</h2>
                    <p class="muted" style="margin: 3px 0 0;">Ringkasan total & jumlah transaksi.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('admin.dashboard.export', ['range' => $recapRange, 'length' => $recapLength]) }}" class="download-btn">
                        Download Excel
                    </a>
                    <div class="recap-combo" id="recap-combo">
                        <button type="button" class="recap-trigger" id="recap-combo-trigger">
                            <span id="recap-combo-label">{{ $activeRecapLabel }}</span>
                            <span class="chevron">&#9662;</span>
                        </button>
                        <div class="recap-options" id="recap-option-list">
                            @foreach ($recapOptionList as $option)
                                <button type="button"
                                    class="recap-option {{ $option['active'] ? 'active' : '' }}"
                                    data-label="{{ $option['label'] }}"
                                    data-url="{{ route('admin.dashboard', ['range' => $option['range'], 'length' => $option['length']]) }}">
                                    {{ $option['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <span class="tag" style="margin-top: 8px; display: inline-flex;">{{ $recapBadge }}</span>
            @if ($transactionRecap->isEmpty())
                <p class="muted" style="margin-top: 10px;">Belum ada data transaksi pada rentang ini.</p>
            @else
                <div class="table-scroll">
                    <table class="compact-table">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Total</th>
                                <th>Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactionRecap as $recap)
                                <tr>
                                    <td>{{ $recap['label'] }}</td>
                                    <td>Rp{{ number_format($recap['total'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($recap['transaction_count']) }}x</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="grid enterprise-grid">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                <h2 style="margin: 0; font-size: 15px;">Top 5 Produk Terlaris (bulan ini)</h2>
                <span class="tag">Update real-time</span>
            </div>
            @if ($topSellingProducts->isEmpty())
                <p class="muted" style="margin-top: 10px;">Belum ada transaksi bulan ini.</p>
            @else
                <div class="table-scroll">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topSellingProducts as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ number_format($item->total_quantity) }}</td>
                                    <td>{{ $formatIdr($item->gross_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap;">
                <div>
                    <h2 style="margin: 0; font-size: 15px;">Grafik Jam Sibuk</h2>
                    <p class="muted" style="margin: 3px 0 0;">{{ $peakHoursRangeLabel }} · Berdasarkan jumlah transaksi</p>
                </div>
                <span class="tag">{{ $peakHourHeadline }}</span>
            </div>
            <div class="chart-wrapper" style="height: 220px; margin-top: 8px;">
                <canvas id="peak-hours-chart"></canvas>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                <h2 style="margin: 0; font-size: 15px;">Kasir Aktif</h2>
                <span class="tag">30 menit terakhir</span>
            </div>
            @if ($activeCashiers->isEmpty())
                <p class="muted" style="margin-top: 10px;">Belum ada kasir aktif dalam 30 menit terakhir.</p>
            @else
                <div class="list-group">
                    @foreach ($activeCashiers as $cashier)
                        <div class="list-item">
                            <div class="cashier-meta">
                                <div class="item-title" style="display: flex; align-items: center; gap: 6px;">
                                    <span class="status-dot"></span>
                                    <span>{{ $cashier['name'] }}</span>
                                </div>
                                <div class="item-meta">{{ $cashier['email'] }}</div>
                                <div class="item-meta">Aktif {{ optional($cashier['last_active_at'])->diffForHumans() }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div class="item-meta">Kas laci</div>
                                <div class="item-title">{{ $formatIdr($cashier['cash_in_drawer']) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 15px;">Transaksi Terbaru</h2>
                <span class="tag">5 terakhir</span>
            </div>
            @if ($recentTransactions->isEmpty())
                <p class="muted" style="margin-top: 10px;">Belum ada transaksi yang tercatat.</p>
            @else
                <div class="list-group">
                    @foreach ($recentTransactions as $transaction)
                        <div class="list-item">
                            <div>
                                <div class="item-title">#{{ $transaction->code }}</div>
                                <div class="item-meta">
                                    {{ optional($transaction->transaction_date)->format('d M Y H:i') }} ·
                                    Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="item-meta">
                                {{ $transaction->user->name ?? 'Kasir' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clockEl = document.getElementById('wib-clock');

            const formatWib = (date) => new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            }).format(date) + ' WIB';

            if (clockEl) {
                const tick = () => {
                    clockEl.textContent = formatWib(new Date());
                };
                tick();
                setInterval(tick, 1000);
            }

            const revenueData = {
                '7': @json($revenue7Days),
                '30': @json($revenue30Days),
            };

            const revenueCanvas = document.getElementById('revenue-chart');
            const rangeWrapper = document.getElementById('revenue-range');
            const totalEl = document.getElementById('revenue-total');
            const compareEl = document.getElementById('revenue-compare');
            const trendLabelEl = document.getElementById('revenue-trend-label');
            const trendDiffEl = document.getElementById('revenue-trend-diff');
            const trendDotEl = document.getElementById('revenue-trend-dot');
            const trendWrap = document.getElementById('revenue-trend');
            let revenueChart;

            const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }).format(value || 0);

            const setActiveRange = (rangeKey) => {
                if (!rangeWrapper) return;
                rangeWrapper.querySelectorAll('.range-btn').forEach((btn) => {
                    if (btn.dataset.range === rangeKey) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            };

            const updateRevenueMeta = (dataset) => {
                if (totalEl) totalEl.textContent = formatCurrency(dataset.total ?? 0);
                if (compareEl) compareEl.textContent = formatCurrency(dataset.previous_total ?? 0) + ' periode sebelumnya';

                const trend = dataset.trend || 'flat';
                if (trendWrap) trendWrap.dataset.trend = trend;
                if (trendDotEl) trendDotEl.className = 'trend-dot trend-' + trend;

                const label = trend === 'up' ? 'Naik' : (trend === 'down' ? 'Turun' : 'Stabil');
                if (trendLabelEl) trendLabelEl.textContent = label;

                const change = dataset.change ?? 0;
                const changePct = dataset.change_pct;
                let diffText;

                if (changePct !== null && changePct !== undefined && !Number.isNaN(changePct)) {
                    diffText = (change >= 0 ? '+' : '-') + Math.abs(changePct).toFixed(1) + '%';
                } else {
                    diffText = (change >= 0 ? '+' : '-') + formatCurrency(Math.abs(change));
                }

                if (trendDiffEl) {
                    trendDiffEl.textContent = diffText + ' vs periode sebelumnya';
                }
            };

            const renderRevenueChart = (rangeKey) => {
                const dataset = revenueData[rangeKey];
                if (!revenueCanvas || !dataset) return;

                const labels = dataset.labels || [];
                const totals = dataset.totals || [];

                if (revenueChart) {
                    revenueChart.destroy();
                }

                const ctx = revenueCanvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, revenueCanvas.clientHeight || 320);
                gradient.addColorStop(0, 'rgba(239, 68, 68, 0.28)');
                gradient.addColorStop(1, 'rgba(239, 68, 68, 0.02)');

                revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Pendapatan',
                                data: totals,
                                fill: true,
                                backgroundColor: gradient,
                                borderColor: '#b91c1c',
                                borderWidth: 3,
                                tension: 0.32,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => new Intl.NumberFormat('id-ID', {
                                        maximumFractionDigits: 0,
                                    }).format(value),
                                },
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.25)',
                                },
                            },
                            x: {
                                grid: {
                                    display: false,
                                },
                            },
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                displayColors: false,
                                callbacks: {
                                    label: (item) => formatCurrency(item.parsed.y || 0),
                                },
                            },
                        },
                    },
                });

                updateRevenueMeta(dataset);
                setActiveRange(rangeKey);
            };

            renderRevenueChart('7');

            if (rangeWrapper) {
                rangeWrapper.querySelectorAll('.range-btn').forEach((button) => {
                    button.addEventListener('click', () => {
                        renderRevenueChart(button.dataset.range);
                    });
                });
            }

            const peakCanvas = document.getElementById('peak-hours-chart');
            const peakLabels = @json($peakHourLabels);
            const peakCounts = @json($peakHourCounts);

            if (peakCanvas && peakLabels.length) {
                const ctx = peakCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: peakLabels,
                        datasets: [
                            {
                                label: 'Jumlah Transaksi',
                                data: peakCounts,
                                backgroundColor: 'rgba(185, 28, 28, 0.6)',
                                borderColor: '#b91c1c',
                                borderWidth: 1,
                                borderRadius: 4,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                },
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.2)',
                                },
                            },
                            x: {
                                grid: {
                                    display: false,
                                },
                            },
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (item) => `${item.formattedValue} transaksi`,
                                },
                            },
                        },
                    },
                });
            }

            const combo = document.getElementById('recap-combo');
            const comboTrigger = document.getElementById('recap-combo-trigger');
            const optionList = document.getElementById('recap-option-list');

            if (combo && comboTrigger && optionList) {
                const options = Array.from(optionList.querySelectorAll('.recap-option'));

                const openList = () => {
                    optionList.classList.add('open');
                    combo.classList.add('open');
                };

                const closeList = () => {
                    optionList.classList.remove('open');
                    combo.classList.remove('open');
                };

                comboTrigger.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const isOpen = optionList.classList.contains('open');
                    if (isOpen) {
                        closeList();
                    } else {
                        openList();
                    }
                });

                options.forEach((opt) => {
                    opt.addEventListener('click', () => {
                        closeList();
                        window.location.href = opt.dataset.url;
                    });
                });

                document.addEventListener('click', (event) => {
                    if (!combo.contains(event.target)) {
                        closeList();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeList();
                    }
                });
            }
        });
    </script>
@endpush








