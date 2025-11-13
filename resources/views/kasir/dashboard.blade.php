@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
    <h1 class="page-title">Dashboard Kasir</h1>

    <div class="grid grid-3">
        <div class="card">
            <div class="muted">Penjualan Hari Ini</div>
            <div class="muted" style="margin-top: 4px; font-size: 13px;">
                {{ $todayDateLabel }}
            </div>
            <div style="font-size: 28px; font-weight: 700; margin-top: 8px;">
                Rp{{ number_format($todaySalesTotal, 0, ',', '.') }}
            </div>
            <div class="muted" style="margin-top: 6px;">
                Total transaksi: {{ $todayTransactionCount }}
            </div>
        </div>

        <div class="card">
            <div class="muted">Produk Terjual Hari Ini</div>
            <div style="font-size: 28px; font-weight: 700; margin-top: 8px;">
                {{ $todayItemsSold }}
            </div>
            <div class="muted" style="margin-top: 6px;">Item</div>
        </div>

        <div class="card">
            <div class="muted">Rata-rata Nilai Transaksi</div>
            <div style="font-size: 28px; font-weight: 700; margin-top: 8px;">
                @if ($todayTransactionCount > 0)
                    Rp{{ number_format($todaySalesTotal / $todayTransactionCount, 0, ',', '.') }}
                @else
                    Rp0
                @endif
            </div>
            <div class="muted" style="margin-top: 6px;">Per transaksi</div>
        </div>
    </div>

    <div class="grid" style="margin-top: 24px; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <div class="card">
            <h2 style="margin: 0 0 12px; font-size: 20px;">Top Produk Hari Ini</h2>
            @if ($topProducts->isEmpty())
                <p class="muted">Belum ada transaksi hari ini.</p>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topProducts as $product)
                            <tr>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->total_quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="card">
            <h2 style="margin: 0 0 12px; font-size: 20px;">Ringkasan Penjualan Bulan Ini</h2>
            @if ($monthlySales->isEmpty())
                <p class="muted">Belum ada transaksi pada bulan ini.</p>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($monthlySales as $summary)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($summary->date)->format('d M Y') }}</td>
                                <td>Rp{{ number_format($summary->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="card" style="margin-top: 24px;">
        <h2 style="margin: 0 0 12px; font-size: 20px;">Transaksi Terakhir</h2>

        @if ($recentTransactions->isEmpty())
            <p class="muted">Belum ada transaksi yang tercatat.</p>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Total Item</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->code }}</td>
                            <td>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                            <td>{{ $transaction->details_count }}</td>
                            <td>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($transaction->payment_method) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
