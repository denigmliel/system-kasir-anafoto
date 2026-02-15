@extends('layouts.admin')

@section('title', 'Produk Stok Menipis')

@push('styles')
    <style>
        .low-stock-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .low-stock-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #0f172a;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .back-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.1);
        }

        .low-stock-filter {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            padding: 12px;
            background-color: #ffffff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
            margin-bottom: 12px;
        }

        .low-stock-filter label {
            font-size: 11px;
            font-weight: 600;
            color: #475467;
            margin-bottom: 4px;
            display: inline-block;
        }

        .low-stock-filter input,
        .low-stock-filter select {
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            font-size: 12.5px;
            color: #111827;
            background-color: #f8fafc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .low-stock-filter input:focus,
        .low-stock-filter select:focus {
            outline: none;
            border-color: #b91c1c;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.12);
        }

        .field-hint {
            margin-top: 4px;
            font-size: 10.5px;
            color: #6b7280;
        }

        .low-stock-filter__actions {
            display: flex;
            gap: 8px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .low-stock-filter__actions button,
        .low-stock-filter__actions a {
            padding: 8px 12px;
            border-radius: 9px;
            border: none;
            font-weight: 600;
            font-size: 12.5px;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .low-stock-filter__actions button {
            background-color: #b91c1c;
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(185, 28, 28, 0.22);
        }

        .low-stock-filter__actions a {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        .low-stock-info {
            font-size: 12px;
            color: #475467;
            margin-bottom: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .data-table th {
            text-align: left;
            padding: 10px 12px;
            background: #f8fafc;
            color: #0f172a;
            font-weight: 700;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            background-color: #fee2e2;
            color: #991b1b;
        }

        .stock-badge--warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            background: #ecfdf3;
            color: #047857;
            border: 1px solid #86efac;
        }

        .status-pill--inactive {
            background: #fef3f2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        nav[aria-label="Pagination Navigation"] {
            display: flex;
            flex-direction: column;
            gap: 6px;
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
            gap: 6px;
        }

        nav[aria-label="Pagination Navigation"] p {
            margin: 0;
            font-size: 11px;
            color: #475467;
        }

        nav[aria-label="Pagination Navigation"] span > span,
        nav[aria-label="Pagination Navigation"] span > a,
        nav[aria-label="Pagination Navigation"] a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 9px;
            font-size: 11.5px;
            font-weight: 600;
            color: #1f2937;
            background-color: #ffffff;
            border: 1px solid #d0d5dd;
            border-radius: 8px;
            text-decoration: none;
            min-width: 32px;
        }

        nav[aria-label="Pagination Navigation"] span[aria-current="page"] > span {
            background-color: #b91c1c;
            color: #ffffff;
            border-color: #b91c1c;
        }

        nav[aria-label="Pagination Navigation"] a:hover {
            filter: brightness(0.97);
        }

        nav[aria-label="Pagination Navigation"] svg {
            width: 18px;
            height: 18px;
        }
    </style>
@endpush

@section('content')
    <div class="low-stock-header">
        <div>
            <h1 class="page-title">Produk Stok Menipis</h1>
            <p class="muted" style="margin: 0;">Pantau produk dengan stok rendah agar pembelian ulang bisa segera dilakukan.</p>
        </div>
        <div class="low-stock-actions">
            <a href="{{ route('admin.dashboard') }}" class="back-link">Kembali ke Dashboard</a>
        </div>
    </div>

    <form method="GET" class="low-stock-filter">
        <div>
            <label for="search">Pencarian</label>
            <input
                type="text"
                name="search"
                id="search"
                placeholder="Nama produk, kode, atau #ID"
                value="{{ request('search') }}"
            >
        </div>

        <div>
            <label for="category_id">Kategori</label>
            <select name="category_id" id="category_id">
                <option value="">Semua</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="threshold">Ambang Stok</label>
            <input
                type="number"
                min="0"
                name="threshold"
                id="threshold"
                value="{{ $threshold }}"
            >
            <div class="field-hint">Default: {{ $defaultThreshold }}</div>
        </div>

        <div class="low-stock-filter__actions">
            <button type="submit">Terapkan Filter</button>
            <a href="{{ route('admin.low_stock') }}">Reset</a>
        </div>
    </form>

    @if ($products->isEmpty())
        <div class="card">
            <p class="muted">
                Tidak ada produk yang berada di bawah ambang stok {{ $threshold }}.
            </p>
        </div>
    @else
        <div class="card">
            <div class="low-stock-info">
                Menampilkan {{ number_format($products->firstItem()) }}-{{ number_format($products->lastItem()) }}
                dari {{ number_format($products->total()) }} produk dengan stok &le; {{ $threshold }}.
            </div>

            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Kode</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th style="width: 120px; text-align: center;">Stok</th>
                            <th style="width: 120px; text-align: center;">Target/Min</th>
                            <th style="width: 130px; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            @php
                                $isOut = $product->stock <= 0;
                                $isCritical = $product->stock > 0 && $product->stock <= $defaultThreshold;
                            @endphp
                            <tr>
                                <td>#{{ $product->id }}</td>
                                <td>{{ $product->code ?? '-' }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ optional($product->category)->name ?? 'Tanpa Kategori' }}</td>
                                <td style="text-align: center;">
                                    @if ($isOut)
                                        <span class="stock-badge">Stok habis</span>
                                    @elseif ($isCritical)
                                        <span class="stock-badge stock-badge--warning">{{ $product->stock }} pcs</span>
                                    @else
                                        {{ $product->stock }} pcs
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    {{ $product->min_stock !== null ? $product->min_stock . ' pcs' : '-' }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-pill {{ $product->is_active ? '' : 'status-pill--inactive' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
                {{ $products->links() }}
            </div>
        </div>
    @endif
@endsection
