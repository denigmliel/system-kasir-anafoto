@extends('layouts.admin')

@section('title', 'Laporan Persediaan')

@push('styles')
    <style>
        .report-shell {
            display: grid;
            gap: 12px;
        }

        .report-hero {
            background: linear-gradient(135deg, #fee2e2 0%, #fff7ed 50%, #f8fafc 100%);
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: 0 14px 32px rgba(185, 28, 28, 0.12);
            flex-wrap: wrap;
        }

        .report-hero .page-title {
            font-size: 20px;
            margin: 0;
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
            font-weight: 600;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .report-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.1);
        }

        .filters-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

        .category-combobox input {
            width: 100%;
            padding: 8px 10px;
            border-radius: 9px;
            border: 1px solid #d0d5dd;
            background: #fff;
            font-size: 12.5px;
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
            background: #fef2f2;
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

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 12px;
        }

        .chart-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            display: grid;
            gap: 8px;
        }

        .chart-title {
            margin: 0;
            font-size: 13px;
            color: #1f2937;
            font-weight: 700;
        }

        .chart-subtitle {
            margin: 0;
            font-size: 11.5px;
            color: #64748b;
        }

        .chart-wrapper {
            position: relative;
            height: 240px;
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

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 9px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 0.02em;
        }

        .pill--active {
            background: #ecfdf3;
            color: #166534;
        }

        .pill--inactive {
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
    </style>
@endpush

@section('content')
    <div class="report-shell">
        <div class="report-hero">
            <div>
                <h1 class="page-title">Laporan Persediaan</h1>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="report-link">Kembali ke Laporan</a>
        </div>

        <div class="muted" style="margin-top: -4px;">
            Ambang stok rendah: {{ $lowStockThreshold }} item.
        </div>

        <div class="filters-card">
            <form method="GET" action="{{ route('admin.reports.stock') }}">
                <div class="filters-grid">
                    <div>
                        @php
                            $selectedCategory = $categories->firstWhere('id', request('category_id'));
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
                            <input type="hidden" name="category_id" id="category_id" value="{{ request('category_id') }}">
                            <div class="combo-list" id="category_list"></div>
                        </div>
                    </div>

                    <div>
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="all" @selected(request('status', 'all') === 'all')>Semua</option>
                            <option value="active" @selected(request('status') === 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="filters-actions">
                        <button type="submit" class="report-button report-button--primary">Terapkan</button>
                        <a href="{{ route('admin.reports.stock') }}" class="report-button report-button--ghost">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total stok (berdasarkan filter)</div>
                <div class="stat-value">{{ number_format($totalStock) }}</div>
                <div class="stat-muted">Mengikuti filter yang dipilih</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Nilai persediaan (berdasarkan filter)</div>
                <div class="stat-value">Rp{{ number_format($totalValue, 0, ',', '.') }}</div>
                <div class="stat-muted">Perkalian stok x harga</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Ringkasan halaman ini</div>
                <div class="stat-value">{{ number_format($pageItems) }} produk</div>
                <div class="stat-muted">Stok: {{ number_format($pageStock) }} | Nilai: Rp{{ number_format($pageValue, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Status produk (halaman ini)</div>
                <div class="stat-value">{{ number_format($pageActive) }} aktif</div>
                <div class="stat-muted">Nonaktif: {{ number_format($pageInactive) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Stok menipis</div>
                <div class="stat-value">{{ number_format($lowStockCount) }} produk</div>
                <div class="stat-muted">Stok &le; {{ $lowStockThreshold }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Stok habis</div>
                <div class="stat-value">{{ number_format($outOfStockCount) }} produk</div>
                <div class="stat-muted">Perlu restock segera</div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div>
                    <p class="chart-title">Distribusi Stok per Kategori</p>
                    <p class="chart-subtitle">Top kategori dengan stok terbanyak (berdasarkan filter).</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="category-stock-chart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div>
                    <p class="chart-title">Kondisi Stok</p>
                    <p class="chart-subtitle">Ringkasan stok aman vs menipis.</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="stock-health-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="card" style="padding:0; overflow:hidden;">
            @if ($products->isEmpty())
                <p class="muted" style="padding: 16px 18px;">Tidak ada data stok untuk filter yang dipilih.</p>
            @else
                <div class="table-scroll">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th style="min-width: 70px;">ID</th>
                                <th style="min-width: 220px;">Produk</th>
                                <th style="min-width: 160px;">Kategori</th>
                                <th style="min-width: 140px;">Stok</th>
                                <th style="min-width: 120px;">Harga</th>
                                <th style="min-width: 140px;">Nilai</th>
                                <th style="min-width: 120px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>#{{ $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ optional($product->category)->name ?? 'Tanpa Kategori' }}</td>
                                    <td>{{ $product->stock }} {{ $product->unit }}</td>
                                    <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td>Rp{{ number_format($product->stock * $product->price, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($product->is_active)
                                            <span class="pill pill--active">Aktif</span>
                                        @else
                                            <span class="pill pill--inactive">Tidak aktif</span>
                                        @endif
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const categoryLabels = @json($topCategories->pluck('name'));
            const categoryStocks = @json($topCategories->pluck('stock'));

            const categoryCanvas = document.getElementById('category-stock-chart');
            if (categoryCanvas && categoryLabels.length) {
                const ctx = categoryCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: categoryLabels,
                        datasets: [
                            {
                                label: 'Total Stok',
                                data: categoryStocks,
                                backgroundColor: 'rgba(185, 28, 28, 0.6)',
                                borderColor: '#b91c1c',
                                borderWidth: 1,
                                borderRadius: 6,
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
                                grid: { display: false },
                            },
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (item) => `${item.formattedValue} unit`,
                                },
                            },
                        },
                    },
                });
            }

            const stockHealthCanvas = document.getElementById('stock-health-chart');
            if (stockHealthCanvas) {
                const ctx = stockHealthCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Stok Habis', 'Stok Menipis', 'Stok Aman'],
                        datasets: [
                            {
                                data: [
                                    {{ $outOfStockCount }},
                                    {{ $lowStockOnlyCount }},
                                    {{ $okStockCount }},
                                ],
                                backgroundColor: ['#ef4444', '#f59e0b', '#22c55e'],
                                borderWidth: 0,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    label: (item) => `${item.label}: ${item.formattedValue} produk`,
                                },
                            },
                        },
                        cutout: '62%',
                    },
                });
            }

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
