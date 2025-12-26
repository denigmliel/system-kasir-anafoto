@extends('layouts.gudang')

@section('title', 'Dashboard Gudang')

@push('styles')
    <style>
        .dashboard-shell {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0 8px;
            font-size: 0.86rem;
            overflow-x: hidden;
            max-width: 100vw;
        }

        @media (max-width: 1100px) {
            .dashboard-shell {
                padding: 0;
                font-size: 1rem;
            }
        }

        .dashboard-hero {
            background: linear-gradient(135deg, #eef2ff 0%, #eff6ff 35%, #f8fafc 100%);
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            box-shadow: 0 12px 28px rgba(59, 130, 246, 0.1);
            width: 100%;
        }

        .dashboard-hero .eyebrow {
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #475569;
            margin: 0 0 6px;
            font-weight: 700;
        }

        .dashboard-hero h1 {
            margin: 0;
        }

        .dashboard-hero p {
            margin: 6px 0 0;
            color: #475569;
        }

        .hero-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0f172a;
            font-weight: 600;
            font-size: 12px;
        }

        .chip--warn {
            background: #fff4e5;
            color: #b45309;
        }

        .utility-bar {
            background: #fff;
            border-radius: 12px;
            padding: 8px 10px;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 8px;
            width: 100%;
        }

        .utility-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .utility-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            width: 100%;
            min-width: 0;
        }

        .health-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 8px;
            margin-bottom: 10px;
        }

        .health-card {
            background: #fff;
            border-radius: 12px;
            padding: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
            display: flex;
            flex-direction: column;
            gap: 6px;
            width: 100%;
        }

        .health-title {
            margin: 0;
            font-size: 12px;
            color: #1f2937;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        .health-title small {
            color: #64748b;
            font-weight: 600;
        }

        .health-value {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .health-subtext {
            color: #64748b;
            font-size: 11px;
            margin: 0;
        }

        .health-bar {
            height: 5px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
        }

        .health-bar__fill {
            height: 100%;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            width: 0;
            transition: width 0.3s ease;
        }

        .layout-panels {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 8px;
            margin-bottom: 10px;
            align-items: start;
        }

        .section-card {
            background: #fff;
            border-radius: 10px;
            padding: 7px 9px;
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            width: 100%;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 6px;
            margin-bottom: 5px;
        }

        .section-header h2 {
            margin: 0;
            font-size: 12px;
        }

        .section-subtitle {
            margin: 0 0 0;
            color: #64748b;
            font-size: 9px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 7px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pill--blue {
            background: #eef2ff;
            color: #312e81;
        }

        .pill--green {
            background: #ecfdf3;
            color: #166534;
        }

        .pill--orange {
            background: #fff7ed;
            color: #9a3412;
        }

        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }

        .table-modern th,
        .table-modern td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 11px;
        }

        .table-modern th {
            background: #f5f7fb;
            font-size: 10px;
            letter-spacing: 0.02em;
            color: #1f2937;
        }

        .table-modern tr:last-child td {
            border-bottom: none;
        }

        .timeline {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .timeline-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 7px;
            padding: 6px 8px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .timeline-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 10px;
            color: #fff;
        }

        .timeline-icon.is-in {
            background: linear-gradient(135deg, #16a34a, #22c55e);
        }

        .timeline-icon.is-out {
            background: linear-gradient(135deg, #f97316, #f43f5e);
        }

        .timeline-title {
            font-weight: 700;
            margin: 0 0 2px;
            color: #0f172a;
            font-size: 11px;
        }

        .timeline-meta {
            font-size: 9.5px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .timeline-note {
            font-size: 12px;
            color: #334155;
            margin: 0;
        }

        .list-modern {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 8px 9px;
            border-radius: 9px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .list-title {
            font-weight: 700;
            margin: 0 0 4px;
            color: #0f172a;
        }

        .list-meta {
            margin: 0;
            font-size: 11px;
            color: #64748b;
        }

        @media (max-width: 640px) {
            .dashboard-shell {
                font-size: 0.95rem;
                padding: 0 4px;
            }

            .dashboard-hero {
                flex-direction: column;
                align-items: flex-start;
                padding: 12px;
            }

            .utility-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 10px;
            }

            .utility-actions {
                display: grid;
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .utility-actions .chip-button {
                width: 100%;
                max-width: 100%;
                min-width: 0;
                text-align: center;
                white-space: normal;
                line-height: 1.3;
                word-break: break-word;
            }

            .health-grid {
                grid-template-columns: 1fr;
            }

            .layout-panels {
                grid-template-columns: 1fr !important;
            }

            .section-card {
                padding: 10px;
            }

            .list-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 560px) {
            .utility-actions .chip-button {
                padding: 10px 12px;
            }

            .utility-bar {
                padding: 10px;
            }
        }

        @media (max-width: 420px) {
            .dashboard-hero h1 {
                font-size: 20px;
            }

            .health-value {
                font-size: 17px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-shell">
        <div class="dashboard-hero">
            <div>
                <h1 class="page-title" style="margin: 0;">Dashboard Gudang</h1>
            </div>
        </div>

        @php
            $totalProducts = max($stats['productCount'], 1);
            $lowStockPercent = $stats['productCount'] > 0 ? round(($stats['lowStockCount'] / $stats['productCount']) * 100) : 0;
            $activePercent = $stats['productCount'] > 0 ? round(($stats['activeProductCount'] / $stats['productCount']) * 100) : 0;
            $movementTotal = $stats['stockInToday'] + $stats['stockOutToday'];
            $movementInShare = $movementTotal > 0 ? round(($stats['stockInToday'] / $movementTotal) * 100) : 0;
            $netToday = $stats['stockInToday'] - $stats['stockOutToday'];
        @endphp

        <div class="utility-bar">
            <div class="utility-meta">
                <div class="eyebrow" style="margin: 0;">Kontrol cepat</div>
            </div>
            <div class="utility-actions">
                <a href="{{ route('gudang.products.create') }}" class="chip-button chip-button--blue">Tambah Produk</a>
                <a href="{{ route('gudang.stock.movements') }}" class="chip-button chip-button--gray">Riwayat Stok</a>
                <a href="{{ route('gudang.reports.stock') }}" class="chip-button chip-button--blue">Laporan Stok</a>
            </div>
        </div>

        <div class="health-grid">
            <div class="health-card">
                <div class="health-title">
                    <span>Produk Low Stock</span>
                    <small>{{ $lowStockPercent }}%</small>
                </div>
                <div class="health-value">
                    {{ number_format($stats['lowStockCount']) }}
                    <small>/ {{ number_format($stats['productCount']) }}</small>
                </div>
                <div class="health-bar">
                    <div class="health-bar__fill" style="width: {{ min($lowStockPercent, 100) }}%;"></div>
                </div>
                <p class="health-subtext">Perlu restock sebelum stok habis.</p>
            </div>
            <div class="health-card">
                <div class="health-title">
                    <span>Aktivitas Hari Ini</span>
                    <small>Net {{ number_format($netToday) }}</small>
                </div>
                <div class="health-value">
                    {{ number_format($stats['stockInToday']) }} masuk
                </div>
                <div class="health-bar">
                    <div class="health-bar__fill" style="width: {{ min($movementInShare, 100) }}%;"></div>
                </div>
                <p class="health-subtext">{{ number_format($stats['stockOutToday']) }} keluar - {{ $movementInShare }}% transaksi masuk</p>
            </div>
            <div class="health-card">
                <div class="health-title">
                    <span>Produk Aktif</span>
                    <small>{{ $activePercent }}%</small>
                </div>
                <div class="health-value">
                    {{ number_format($stats['activeProductCount']) }}
                    <small>dari {{ number_format($stats['productCount']) }}</small>
                </div>
                <div class="health-bar">
                    <div class="health-bar__fill" style="width: {{ min($activePercent, 100) }}%;"></div>
                </div>
                <p class="health-subtext">{{ number_format($stats['categoryCount']) }} kategori terdaftar.</p>
            </div>
        </div>

        <div class="layout-panels" style="grid-template-columns: 1.1fr 0.9fr;">
            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2>Produk Hampir Habis</h2>
                        <div class="section-subtitle">Stok &le; {{ $lowStockThreshold }} pcs</div>
                    </div>
                    <a href="{{ route('gudang.products.low_stock') }}" class="chip-button chip-button--blue">
                        Lihat Semua
                    </a>
                </div>

                @if ($topLowStocks->isEmpty())
                    <p class="muted">Tidak ada produk yang berada di bawah ambang stok {{ $lowStockThreshold }}.</p>
                @else
                    <div class="table-scroll">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th style="width: 60%;">Produk</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topLowStocks as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->stock . ' ' . $product->unit }}</td>
                                        <td>
                                            <span class="pill pill--orange">Menipis</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2>Pergerakan Stok Terakhir</h2>
                        <div class="section-subtitle">8 aktivitas terbaru</div>
                    </div>
                </div>

                @if ($recentMovements->isEmpty())
                    <p class="muted">Belum ada pergerakan stok yang tercatat.</p>
                @else
                    <div class="timeline">
                        @foreach ($recentMovements as $movement)
                            <div class="timeline-item">
                                <div class="timeline-icon {{ $movement->type === 'in' ? 'is-in' : 'is-out' }}">
                                    {{ strtoupper(substr($movement->type, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="timeline-title">{{ optional($movement->product)->name ?? 'Produk tidak ditemukan' }}</div>
                                    <div class="timeline-meta">
                                        {{ \Illuminate\Support\Carbon::parse($movement->created_at)->format('d/m/Y H:i') }}
                                        - {{ ucfirst($movement->type) }} {{ number_format($movement->quantity) }}
                                        - {{ optional($movement->user)->name ?? 'Sistem' }}
                                    </div>
                                    <p class="timeline-note">{{ $movement->notes ?: 'Tidak ada catatan' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2>Produk Terakhir Diperbarui</h2>
                    <div class="section-subtitle">Pantau pembaruan stok & detail produk terbaru</div>
                </div>
            </div>

            @if ($recentlyUpdatedProducts->isEmpty())
                <p class="muted">Belum ada produk yang diperbarui akhir-akhir ini.</p>
            @else
                <div class="list-modern">
                    @foreach ($recentlyUpdatedProducts as $product)
                        <div class="list-item">
                            <div>
                                <div class="list-title">{{ $product->name }}</div>
                                <p class="list-meta">
                                    {{ $product->stock . ' ' . $product->unit }}
                                    - Diperbarui {{ \Illuminate\Support\Carbon::parse($product->updated_at)->diffForHumans() }}
                                </p>
                            </div>
                            <a href="{{ route('gudang.products.show', $product) }}" class="chip-button chip-button--blue">Detail</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
