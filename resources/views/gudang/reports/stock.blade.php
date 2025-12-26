@extends('layouts.gudang')

@section('title', 'Laporan Stok')

@push('styles')
    <style>
        .report-shell {
            max-width: none;
            margin: 0;
            width: 100%;
        }

        .report-hero {
            background: linear-gradient(135deg, #eef2ff 0%, #e0f2fe 50%, #f8fafc 100%);
            border-radius: 16px;
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: 0 14px 32px rgba(59, 130, 246, 0.16);
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .report-hero .meta {
            margin: 4px 0 0;
            color: #475569;
        }

        .report-hero .page-title {
            font-size: 20px;
        }

        .hero-chips {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.03em;
        }

        .chip--blue {
            background: #e0f2fe;
            color: #0f172a;
        }

        .chip--green {
            background: #ecfdf3;
            color: #166534;
        }

        .filters-card {
            background: #fff;
            border-radius: 14px;
            padding: 12px;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
            margin-bottom: 12px;
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
            background: #eef2ff;
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

        .report-button--blue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.22);
        }

        .report-button--blue:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.26);
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
            margin-bottom: 12px;
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

        @media (max-width: 900px) {
            .filters-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }
    </style>
@endpush

@section('content')
    <div class="report-shell">
        <div class="report-hero">
            <div>
                <h1 class="page-title" style="margin:0;">Laporan Stok</h1>
            </div>
        </div>

        <div class="filters-card">
            <form method="GET" action="{{ route('gudang.reports.stock') }}">
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
                        <button type="submit" class="report-button report-button--blue">Terapkan</button>
                        <a href="{{ route('gudang.reports.stock') }}" class="report-button report-button--ghost">Reset</a>
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
                <div style="padding: 16px 18px;">
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

            // Inisialisasi pilihan awal jika ada
            if (hidden.value) {
                const existing = optionsData.find((opt) => String(opt.id) === String(hidden.value));
                if (existing) {
                    input.value = existing.name;
                }
            }
        });
    </script>
@endpush
