@extends('layouts.gudang')

@section('title', 'Pergerakan Stok')

@push('styles')
    <style>
        .stock-hero {
            background: linear-gradient(135deg, #f4f7ff 0%, #eef4ff 60%, #f8fbff 100%);
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 14px rgba(59, 130, 246, 0.08);
            border: 1px solid #e2e8f0;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .stock-hero .meta {
            color: #475569;
            margin: 4px 0 0;
        }

        .hero-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-left: auto;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 8px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.02em;
        }

        .chip--blue {
            background: #e0f2fe;
            color: #0f172a;
        }

        .chip--orange {
            background: #fff7ed;
            color: #9a3412;
        }

        .filters-card {
            background: #fff;
            border-radius: 10px;
            padding: 8px;
            box-shadow: 0 6px 12px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            margin-bottom: 8px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 8px;
            align-items: end;
        }

        .filters-grid label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 3px;
            display: block;
        }

        .filters-grid select {
            padding: 8px 10px;
            border-radius: 9px;
            border: 1px solid #d0d5dd;
            width: 100%;
            font-size: 12px;
        }

        .filters-actions {
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .stock-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 9px;
            padding: 8px 12px;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.15px;
            cursor: pointer;
            color: #ffffff;
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .stock-button--blue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.22);
        }

        .stock-button--blue:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.26);
        }

        .stock-button--blue:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.2);
        }

        .stock-button--ghost {
            background: #e2e8f0;
            color: #0f172a;
            box-shadow: none;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 8px;
            margin-bottom: 8px;
        }

        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.06);
        }

        .stat-title {
            margin: 0;
            font-size: 11px;
            color: #475569;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 800;
            margin-top: 4px;
        }

        .stat-muted {
            font-size: 10px;
            color: #64748b;
            margin-top: 3px;
        }

        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }

        .table-modern th,
        .table-modern td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 12px;
        }

        .table-modern th {
            background: #f4f6fb;
            color: #1f2937;
            letter-spacing: 0.01em;
            font-weight: 700;
        }

        .table-modern tr:last-child td {
            border-bottom: none;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.02em;
        }

        .pill--in {
            background: #ecfdf3;
            color: #166534;
        }

        .pill--out {
            background: #fff1f2;
            color: #b91c1c;
        }

        .pill--adjustment {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-user {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 6px;
            border-radius: 6px;
            background: #e0e7ff;
            color: #312e81;
            font-size: 10px;
            font-weight: 700;
        }

        nav[aria-label="Pagination Navigation"] {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 12px;
            align-items: flex-end;
        }

        nav[aria-label="Pagination Navigation"] > div:first-child {
            display: none;
        }

        nav[aria-label="Pagination Navigation"] > div:last-child {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        nav[aria-label="Pagination Navigation"] p {
            margin: 0;
            font-size: 12px;
            color: #475467;
        }

        nav[aria-label="Pagination Navigation"] span > span,
        nav[aria-label="Pagination Navigation"] span > a,
        nav[aria-label="Pagination Navigation"] a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 11px;
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            background-color: #fff;
            border: 1px solid #d0d5dd;
            border-radius: 9px;
            text-decoration: none;
            min-width: 36px;
            transition: border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }

        nav[aria-label="Pagination Navigation"] span[aria-current="page"] > span {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
        }

        nav[aria-label="Pagination Navigation"] a:hover,
        nav[aria-label="Pagination Navigation"] span > a:hover {
            border-color: #94a3b8;
            color: #0f172a;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
        }

        nav[aria-label="Pagination Navigation"] svg {
            width: 16px;
            height: 16px;
        }

        @media (max-width: 900px) {
            .filters-grid {
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            }
        }
    </style>
@endpush

@section('content')
    @php
        $pageTotal = $movements->count();
        $pageIn = $movements->where('type', 'in')->sum('quantity');
        $pageOut = $movements->where('type', 'out')->sum('quantity');
        $latestAt = optional($movements->first())->created_at;
    @endphp

    <div class="stock-hero">
        <div>
            <h1 class="page-title" style="margin:0;">Pergerakan Stok</h1>
            <p class="meta">Pantau pergerakan stok masuk/keluar dan catatan aktivitas terbaru.</p>
        </div>
        <div class="hero-chips">
            <span class="chip chip--blue">Log tercatat: {{ number_format($movements->total()) }}</span>
            <span class="chip chip--orange">Halaman ini: {{ number_format($pageTotal) }} baris</span>
        </div>
    </div>

    <div class="filters-card">
        <form method="GET" action="{{ route('gudang.stock.movements') }}">
            <div class="filters-grid">
                <div>
                    <label for="type">Jenis</label>
                    <select name="type" id="type">
                        <option value="">Semua</option>
                        <option value="in" @selected(request('type') === 'in')>Masuk</option>
                        <option value="out" @selected(request('type') === 'out')>Keluar</option>
                        <option value="adjustment" @selected(request('type') === 'adjustment')>Penyesuaian</option>
                    </select>
                </div>
                <div>
                    <label for="product_id">Produk</label>
                    <select name="product_id" id="product_id">
                        <option value="">Semua</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filters-actions">
                    <button type="submit" class="stock-button stock-button--blue">Filter</button>
                    <a href="{{ route('gudang.stock.movements') }}" class="stock-button stock-button--ghost" style="text-decoration:none;">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total log (filter saat ini)</div>
            <div class="stat-value">{{ number_format($movements->total()) }}</div>
            <div class="stat-muted">Ditampilkan {{ number_format($pageTotal) }} pada halaman ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Masuk (halaman ini)</div>
            <div class="stat-value">+{{ number_format($pageIn) }}</div>
            <div class="stat-muted">Jumlah agregat untuk baris yang terlihat</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Keluar (halaman ini)</div>
            <div class="stat-value">-{{ number_format($pageOut) }}</div>
            <div class="stat-muted">Jumlah agregat untuk baris yang terlihat</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Aktivitas terbaru</div>
            <div class="stat-value">{{ $latestAt ? \Illuminate\Support\Carbon::parse($latestAt)->diffForHumans() : '-' }}</div>
            <div class="stat-muted">Waktu log paling baru</div>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        @if ($movements->isEmpty())
            <p class="muted" style="padding: 16px 18px;">Belum ada catatan pergerakan stok.</p>
        @else
            <div class="table-scroll">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="min-width: 140px;">Tanggal</th>
                            <th style="min-width: 220px;">Produk</th>
                            <th style="min-width: 120px;">Jenis</th>
                            <th style="min-width: 80px;">Jumlah</th>
                            <th style="min-width: 120px;">User</th>
                            <th style="min-width: 220px;">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movements as $movement)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($movement->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ optional($movement->product)->name ?? 'Produk tidak ditemukan' }}</td>
                                <td>
                                    @php
                                        $type = $movement->type;
                                        $pillClass = $type === 'in' ? 'pill--in' : ($type === 'out' ? 'pill--out' : 'pill--adjustment');
                                    @endphp
                                    <span class="pill {{ $pillClass }}">{{ ucfirst($type) }}</span>
                                </td>
                                <td>{{ $movement->quantity }}</td>
                                <td>
                                    <span class="badge-user">{{ optional($movement->user)->name ?? 'Sistem' }}</span>
                                </td>
                                <td>{{ $movement->notes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 18px;">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
@endsection
