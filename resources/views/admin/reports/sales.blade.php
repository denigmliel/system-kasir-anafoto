@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@push('styles')
    <style>
        .report-shell {
            display: grid;
            gap: 12px;
        }

        .report-hero {
            background: linear-gradient(135deg, #fee2e2 0%, #fff7ed 55%, #f8fafc 100%);
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: 0 14px 32px rgba(185, 28, 28, 0.14);
            flex-wrap: wrap;
        }

        .report-hero .meta {
            margin: 4px 0 0;
            color: #475569;
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
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            align-items: end;
        }

        .filters-grid label {
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
            display: block;
        }

        .filters-grid input {
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            width: 100%;
            background: #fff;
            font-size: 12.5px;
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
            padding: 8px 12px;
            font-weight: 700;
            font-size: 12.5px;
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
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 0.02em;
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
                <h1 class="page-title">Laporan Penjualan</h1>
                <p class="meta">Periode: {{ $rangeLabel }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="report-link">Dashboard Admin</a>
        </div>

        <div class="filters-card">
            <form method="GET" action="{{ route('admin.reports.sales') }}">
                <div class="filters-grid">
                    <div>
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $filters['start_date'] }}">
                    </div>
                    <div>
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $filters['end_date'] }}">
                    </div>
                    <div>
                        <label for="search">Cari Kode</label>
                        <input type="text" id="search" name="search" placeholder="TRX..." value="{{ $filters['search'] }}">
                    </div>
                    <div class="filters-actions">
                        <button type="submit" class="report-button report-button--primary">Terapkan</button>
                        <a href="{{ route('admin.reports.sales') }}" class="report-button report-button--ghost">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total penjualan</div>
                <div class="stat-value">Rp{{ number_format($summary['totalAmount'], 0, ',', '.') }}</div>
                <div class="stat-muted">Periode terpilih</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Jumlah transaksi</div>
                <div class="stat-value">{{ number_format($summary['transactionCount']) }}x</div>
                <div class="stat-muted">Terhitung pada periode ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Rata-rata transaksi</div>
                <div class="stat-value">Rp{{ number_format($summary['avgOrder'], 0, ',', '.') }}</div>
                <div class="stat-muted">Total penjualan / transaksi</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Periode laporan</div>
                <div class="stat-value" style="font-size: 16px;">{{ $rangeLabel }}</div>
                <div class="stat-muted">Dapat disesuaikan via filter</div>
            </div>
        </div>

        <div class="card" style="padding:0; overflow:hidden;">
            @if ($transactions->isEmpty())
                <p class="muted" style="padding: 16px 18px;">Tidak ada transaksi untuk periode yang dipilih.</p>
            @else
                <div class="table-scroll">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th style="min-width: 120px;">Kode</th>
                                <th style="min-width: 170px;">Tanggal</th>
                                <th style="min-width: 150px;">Kasir</th>
                                <th style="min-width: 120px;">Metode</th>
                                <th style="min-width: 90px; text-align: center;">Item</th>
                                <th style="min-width: 140px; text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->code }}</td>
                                    <td>{{ optional($transaction->transaction_date)->format('d M Y H:i') }}</td>
                                    <td>{{ $transaction->user->name ?? 'Kasir' }}</td>
                                    <td>
                                        <span class="pill">
                                            {{ $paymentLabels[$transaction->payment_method] ?? strtoupper($transaction->payment_method ?? '-') }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">{{ number_format($transaction->details_count ?? 0) }}</td>
                                    <td style="text-align: right;">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrap">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
