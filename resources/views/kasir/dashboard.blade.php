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
                <div style="position: relative; height: 260px;">
                    <canvas id="monthly-sales-chart"></canvas>
                </div>
                <table class="data-table" style="margin-top: 16px;">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Jumlah Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($monthlySales as $summary)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($summary->date)->format('d M Y') }}</td>
                                <td>Rp{{ number_format($summary->total, 0, ',', '.') }}</td>
                                <td>{{ $summary->transaction_count }}</td>
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

@push('scripts')
    @if (!$monthlySales->isEmpty())
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const canvas = document.getElementById('monthly-sales-chart');

                if (!canvas) {
                    return;
                }

                const ctx = canvas.getContext('2d');
                const height = canvas.clientHeight || 260;
                const gradient = ctx.createLinearGradient(0, 0, 0, height);
                gradient.addColorStop(0, 'rgba(220, 38, 38, 0.35)');
                gradient.addColorStop(1, 'rgba(220, 38, 38, 0)');

                const salesData = @json($monthlySalesTotals);
                const transactionCounts = @json($monthlySalesTransactionCounts);
                const maxValue = salesData.length ? Math.max(...salesData) : 0;
                const suggestedMax = Math.max(100000, Math.ceil(maxValue / 100000) * 100000);

                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: @json($monthlySalesLabels),
                        datasets: [
                            {
                                type: 'bar',
                                label: 'Total Penjualan',
                                data: salesData,
                                backgroundColor: 'rgba(239, 68, 68, 0.35)',
                                borderColor: 'rgba(220, 38, 38, 0.65)',
                                borderWidth: 1.5,
                                borderRadius: 8,
                                barPercentage: 0.7,
                                categoryPercentage: 0.6,
                                maxBarThickness: 28,
                                order: 2,
                            },
                            {
                                type: 'line',
                                label: 'Tren Penjualan',
                                data: salesData,
                                fill: true,
                                backgroundColor: gradient,
                                borderColor: '#b91c1c',
                                borderWidth: 3,
                                pointBackgroundColor: '#b91c1c',
                                pointHoverBackgroundColor: '#7f1d1d',
                                pointBorderColor: '#fff',
                                pointHoverBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                tension: 0.35,
                                order: 1,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax,
                                ticks: {
                                    stepSize: 100000,
                                    callback: (value) => new Intl.NumberFormat('id-ID', {
                                        maximumFractionDigits: 0,
                                    }).format(value),
                                    padding: 8,
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
                        layout: {
                            padding: {
                                top: 8,
                                right: 12,
                                bottom: 4,
                                left: 0,
                            },
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                filter: (tooltipItem) => tooltipItem.datasetIndex === 1,
                                displayColors: false,
                                callbacks: {
                                    label: (context) => {
                                        const value = context.parsed.y ?? 0;
                                        const txCount = transactionCounts[context.dataIndex] ?? 0;

                                        const totalLabel = `Total: ${new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            maximumFractionDigits: 0,
                                        }).format(value)}`;

                                        const countLabel = `Transaksi: ${new Intl.NumberFormat('id-ID').format(txCount)}`;

                                        return [totalLabel, countLabel];
                                    },
                                },
                            },
                        },
                    },
                });
            });
        </script>
    @endif
@endpush
