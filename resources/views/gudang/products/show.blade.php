@extends('layouts.gudang')

@section('title', 'Detail Produk')

@push('styles')
    <style>
        .product-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 16px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.2px;
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(22, 163, 74, 0.18);
        }

        .product-status-pill--active {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }

        .product-status-pill--inactive {
            background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.18);
        }

        .product-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .product-actions > * {
            flex: 1 1 180px;
        }

        .product-action-button {
            width: 100%;
            display: inline-flex;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
    <h1 class="page-title">Detail Produk</h1>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <div class="card">
            <div class="product-header">
                <h2 style="margin: 0; font-size: 20px;">{{ $product->name }}</h2>
                <span class="product-status-pill {{ $product->is_active ? 'product-status-pill--active' : 'product-status-pill--inactive' }}">
                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <p class="muted" style="margin-top: 6px;">ID Produk: #{{ $product->id }}</p>
            <p class="muted" style="margin-top: 2px;">Kode Produk: {{ $product->code ?? '-' }}</p>

            <div style="margin-top: 16px;">
                <p><strong>Kategori:</strong> {{ optional($product->category)->name ?? 'Tanpa Kategori' }}</p>
                @if ($product->units->isNotEmpty())
                    <div style="margin: 12px 0;">
                        <strong>Satuan & Harga:</strong>
                        <div class="table-scroll">
                            <table class="data-table" style="margin-top: 10px;">
                                <thead>
                                    <tr>
                                        <th>Satuan</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->units as $unit)
                                        <tr>
                                            <td>{{ $unit->name }}</td>
                                            <td>Rp{{ number_format($unit->price, 0, ',', '.') }}</td>
                                            <td>{{ $unit->is_default ? 'Default' : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <p><strong>Satuan:</strong> {{ $product->unit }}</p>
                    <p><strong>Harga Jual:</strong> Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                @endif
                <p>
                    <strong>Stok Saat Ini:</strong>
                    {{ $product->display_stock }}
                </p>
                @if ($product->description)
                    <p style="margin-top: 12px;"><strong>Deskripsi:</strong><br>{{ $product->description }}</p>
                @endif
            </div>

            <div class="product-actions">
                <a
                    href="{{ route('gudang.products.qr', $product) }}"
                    class="chip-button chip-button--blue product-action-button"
                >
                    Lihat QR Code
                </a>
                <a
                    href="{{ route('gudang.products.edit', $product) }}"
                    class="chip-button chip-button--gray product-action-button"
                >
                    Edit Produk
                </a>
                <a
                    href="{{ route('gudang.products.index') }}"
                    class="chip-button chip-button--blue product-action-button"
                >
                    Kembali
                </a>
                <form
                    method="POST"
                    action="{{ route('gudang.products.destroy', $product) }}"
                    onsubmit="return confirm('Hapus produk {{ $product->name }} secara permanen?')"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="chip-button chip-button--danger product-action-button">
                        Hapus Produk
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <h2 style="margin: 0 0 12px; font-size: 20px;">Pergerakan Stok Terbaru</h2>
            @if ($recentMovements->isEmpty())
                <p class="muted">Belum ada catatan pergerakan stok.</p>
            @else
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentMovements as $movement)
                                <tr>
                                    <td>{{ \Illuminate\Support\Carbon::parse($movement->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>{{ ucfirst($movement->type) }}</td>
                                    <td>{{ $movement->quantity }}</td>
                                    <td>{{ $movement->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
