@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@push('styles')
    <style>
        .kasir-dashboard {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 12px;
            width: 100%;
            margin: 0;
            padding: 6px 0 12px;
        }

        .kasir-dashboard .card {
            padding: 9px 10px;
            border-radius: 10px;
        }

        .kasir-dashboard .page-title {
            font-size: 18px;
            line-height: 1.25;
        }

        .kasir-dashboard h2 {
            font-size: 14px;
        }

        .dashboard-hero {
            background: linear-gradient(135deg, #f3f6ff 0%, #eef4ff 60%, #f8fbff 100%);
            border-radius: 10px;
            padding: 8px 10px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            box-shadow: 0 6px 14px rgba(59, 130, 246, 0.1);
            border: 1px solid #e2e8f0;
            margin-bottom: 6px;
        }

        .dashboard-hero .eyebrow {
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #475569;
            margin: 0 0 2px;
            font-weight: 700;
        }

        .dashboard-hero h1 {
            margin: 0;
        }

        .dashboard-hero p {
            margin: 2px 0 0;
            font-size: 12px;
            color: #475569;
        }

        .hero-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.02em;
            background: #e0f2fe;
            color: #0f172a;
        }

        .chip--red {
            background: #fef2f2;
            color: #b91c1c;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 8px;
            margin-bottom: 8px;
        }

        .stat-card {
            background: #fff;
            border-radius: 8px;
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.06);
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .stat-title {
            margin: 0;
            font-size: 11px;
            color: #475569;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-muted {
            font-size: 10px;
            color: #64748b;
        }

        .scrollable-table {
            max-height: 170px;
            overflow-y: auto;
            margin-top: 14px;
        }

        .scrollable-table thead th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            z-index: 1;
        }

        .scrollable-table table {
            margin: 0;
        }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 10px;
        }

        .chart-card {
            display: grid;
            gap: 6px;
        }

        .chart-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .trend-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-weight: 700;
            font-size: 11px;
            color: #0f172a;
        }

        .trend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .trend-up { background: #22c55e; }
        .trend-down { background: #ef4444; }
        .trend-flat { background: #94a3b8; }

        .payment-list {
            display: grid;
            gap: 8px;
        }

        .payment-chip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
        }

        .payment-chip .label {
            font-weight: 700;
            color: #0f172a;
        }

        .payment-chip .value {
            font-weight: 800;
            color: #0f172a;
        }

        .list-compact {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 6px;
        }

        .list-compact li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
        }

        .mini-label {
            font-size: 10px;
            color: #64748b;
        }

        .mini-value {
            font-weight: 800;
            font-size: 12px;
        }

        .kasir-dashboard .data-table th,
        .kasir-dashboard .data-table td {
            padding: 10px 12px;
            font-size: 13px;
        }

        .kasir-dashboard .data-table th {
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    @php
        $formatIdr = fn ($value) => 'Rp' . number_format($value ?? 0, 0, ',', '.');
        $avgItemsPerTransaction = $todayTransactionCount > 0 ? $todayItemsSold / $todayTransactionCount : 0;
        $weeklyData = $weeklyTrend ?? ['current_total' => 0, 'previous_total' => 0, 'change' => 0, 'change_pct' => null, 'trend' => 'flat'];
        $weeklyTrendLabel = $weeklyData['trend'] === 'up' ? 'Naik' : ($weeklyData['trend'] === 'down' ? 'Turun' : 'Stabil');
        $weeklyTrendDiff = $weeklyData['change_pct'] !== null
            ? (($weeklyData['change'] >= 0 ? '+' : '-') . number_format(abs($weeklyData['change_pct']), 1, ',', '.') . '%')
            : (($weeklyData['change'] >= 0 ? '+' : '-') . $formatIdr(abs($weeklyData['change'])));
    @endphp
    <div class="kasir-dashboard">
        <div class="dashboard-hero">
            <div>
                <div class="eyebrow">Kasir</div>
                <h1 class="page-title" style="margin: 0;">Dashboard Kasir</h1>
                <p>Ringkasan penjualan hari ini, tren bulanan, dan transaksi terbaru.</p>
            </div>
            <div class="hero-chips">
                <span class="chip chip--red" id="kasir-clock"></span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Penjualan Hari Ini</div>
                <div class="stat-muted">{{ $todayDateLabel }}</div>
                <div class="stat-value">{{ $formatIdr($todaySalesTotal) }}</div>
                <div class="stat-muted">Total transaksi: {{ $todayTransactionCount }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Transaksi Hari Ini</div>
                <div class="stat-muted">Kecepatan kasir</div>
                <div class="stat-value">{{ number_format($todayTransactionCount) }}x</div>
                <div class="stat-muted">Rata-rata item/transaksi: {{ number_format($avgItemsPerTransaction, 1, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Produk Terjual</div>
                <div class="stat-muted">Hari ini</div>
                <div class="stat-value">{{ number_format($todayItemsSold) }}</div>
                <div class="stat-muted">Item terjual</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Rata-rata Nilai Transaksi</div>
                <div class="stat-muted">Hari ini</div>
                <div class="stat-value">{{ $formatIdr($todayAvgOrder) }}</div>
                <div class="stat-muted">Per transaksi</div>
            </div>
        </div>

        <div class="section-grid">
            <div class="card chart-card">
                <div class="chart-meta">
                    <div>
                        <h2 style="margin: 0; font-size: 16px;">Performa 7 Hari Terakhir</h2>
                        <p class="muted" style="margin: 3px 0 0;">Total 7 hari: {{ $formatIdr($weeklyData['current_total']) }}</p>
                    </div>
                    <div class="trend-chip" data-trend="{{ $weeklyData['trend'] }}">
                        <span class="trend-dot trend-{{ $weeklyData['trend'] }}"></span>
                        <span>{{ $weeklyTrendLabel }}</span>
                        <span>{{ $weeklyTrendDiff }} vs 7 hari sebelumnya</span>
                    </div>
                </div>
                <div style="position: relative; height: 160px;">
                    <canvas id="weekly-sales-chart"></canvas>
                </div>
            </div>

            <div class="card">
                <h2 style="margin: 0 0 8px; font-size: 16px;">Metode Pembayaran Hari Ini</h2>
                @if ($paymentBreakdown->isEmpty())
                    <p class="muted">Belum ada transaksi hari ini.</p>
                @else
                    <div class="payment-list">
                        @foreach ($paymentBreakdown as $row)
                            <div class="payment-chip">
                                <div>
                                    <div class="label">{{ $row['label'] }}</div>
                                    <div class="mini-label">{{ number_format($row['count']) }} transaksi</div>
                                </div>
                                <div class="value">{{ $formatIdr($row['total']) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="section-grid">
            <div class="card">
                <h2 style="margin: 0 0 8px; font-size: 16px;">Top Produk Hari Ini</h2>
                @if ($topProducts->isEmpty())
                    <p class="muted">Belum ada transaksi hari ini.</p>
                @else
                    <div class="table-scroll">
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
                    </div>
                @endif
            </div>

            <div class="card">
                <h2 style="margin: 0 0 8px; font-size: 16px;">Top Produk 7 Hari</h2>
                @if ($topProductsWeek->isEmpty())
                    <p class="muted">Belum ada transaksi 7 hari terakhir.</p>
                @else
                    <ul class="list-compact">
                        @foreach ($topProductsWeek as $product)
                            <li>
                                <div>
                                    <div class="mini-value">{{ $product->product_name }}</div>
                                    <div class="mini-label">{{ number_format($product->total_quantity) }} item</div>
                                </div>
                                <div class="mini-value">{{ $formatIdr($product->gross_total) }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="card">
                <h2 style="margin: 0 0 8px; font-size: 16px;">Ringkasan Penjualan Bulan Ini</h2>
                @if ($monthlySales->isEmpty())
                    <p class="muted">Belum ada transaksi pada bulan ini.</p>
                @else
                    <div style="position: relative; height: 160px;">
                        <canvas id="monthly-sales-chart"></canvas>
                    </div>
                    <div class="table-scroll">
                        <table class="data-table">
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
                    </div>
                @endif
            </div>
        </div>

        <div class="card" style="margin-top: 12px;">
            <h2 style="margin: 0 0 8px; font-size: 16px;">Transaksi Terakhir</h2>

            @if ($recentTransactions->isEmpty())
                <p class="muted">Belum ada transaksi yang tercatat.</p>
            @else
                <div class="table-scroll">
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
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kasirClock = document.getElementById('kasir-clock');
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

            if (kasirClock) {
                const tickClock = () => {
                    kasirClock.textContent = formatWib(new Date());
                };
                tickClock();
                setInterval(tickClock, 1000);
            }

            const weeklyCanvas = document.getElementById('weekly-sales-chart');
            const weeklyLabels = @json($weeklySalesLabels);
            const weeklyTotals = @json($weeklySalesTotals);
            const weeklyCounts = @json($weeklySalesTransactionCounts);

            if (weeklyCanvas && weeklyLabels.length) {
                const ctx = weeklyCanvas.getContext('2d');
                const height = weeklyCanvas.clientHeight || 200;
                const gradient = ctx.createLinearGradient(0, 0, 0, height);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

                const maxValue = weeklyTotals.length ? Math.max(...weeklyTotals) : 0;
                const suggestedMax = Math.max(100000, Math.ceil(maxValue / 100000) * 100000);

                new Chart(weeklyCanvas, {
                    type: 'line',
                    data: {
                        labels: weeklyLabels,
                        datasets: [
                            {
                                label: 'Pendapatan',
                                data: weeklyTotals,
                                fill: true,
                                backgroundColor: gradient,
                                borderColor: '#1d4ed8',
                                borderWidth: 3,
                                tension: 0.35,
                                pointRadius: 3,
                                pointHoverRadius: 5,
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
                                    callback: (value) => new Intl.NumberFormat('id-ID', {
                                        maximumFractionDigits: 0,
                                    }).format(value),
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
                                displayColors: false,
                                callbacks: {
                                    label: (context) => {
                                        const value = context.parsed.y ?? 0;
                                        const txCount = weeklyCounts[context.dataIndex] ?? 0;
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
            }

            const monthlyCanvas = document.getElementById('monthly-sales-chart');
            const monthlyLabels = @json($monthlySalesLabels);
            const monthlyTotals = @json($monthlySalesTotals);
            const monthlyCounts = @json($monthlySalesTransactionCounts);

            if (monthlyCanvas && monthlyLabels.length) {
                const ctx = monthlyCanvas.getContext('2d');
                const height = monthlyCanvas.clientHeight || 200;
                const gradient = ctx.createLinearGradient(0, 0, 0, height);
                gradient.addColorStop(0, 'rgba(220, 38, 38, 0.35)');
                gradient.addColorStop(1, 'rgba(220, 38, 38, 0)');

                const maxValue = monthlyTotals.length ? Math.max(...monthlyTotals) : 0;
                const suggestedMax = Math.max(100000, Math.ceil(maxValue / 100000) * 100000);

                new Chart(monthlyCanvas, {
                    type: 'bar',
                    data: {
                        labels: monthlyLabels,
                        datasets: [
                            {
                                type: 'bar',
                                label: 'Total Penjualan',
                                data: monthlyTotals,
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
                                data: monthlyTotals,
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
                                        const txCount = monthlyCounts[context.dataIndex] ?? 0;

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
            }
        });
    </script>
@endpush
