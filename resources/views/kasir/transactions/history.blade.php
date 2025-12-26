@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@push('styles')
    <style>
        .history-compact {
            font-size: 12px;
            display: grid;
            gap: 8px;
            width: 100%;
            margin: 0;
            padding: 6px 0 12px;
        }

        .history-compact .card {
            padding: 10px;
            border-radius: 10px;
        }

        .history-compact .page-title {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .filters .form-control {
            width: auto;
            min-width: 150px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .history-apply-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 10px;
            padding: 8px 12px;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.2px;
            cursor: pointer;
            color: #ffffff;
            background-color: #2563eb;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.18);
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .history-apply-button:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.26);
        }

        .history-apply-button:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.2);
        }

        .history-table-wrapper {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .history-compact .data-table th,
        .history-compact .data-table td {
            padding: 10px 12px;
            font-size: 13px;
        }

    </style>
@endpush

@section('content')
    @php($displayTimezone = config('app.timezone_display', 'Asia/Jakarta'))
    <div class="history-compact">
        <h1 class="page-title">Riwayat Transaksi</h1>

        <div class="card">
            <form method="GET" action="{{ route('kasir.transaction.history') }}">
                <div class="filters">
                    <div>
                        <label class="form-label" for="date">Tanggal</label>
                        <input
                            type="date"
                            id="date"
                            name="date"
                            class="form-control"
                            value="{{ request('date') }}"
                        >
                    </div>

                    <div>
                        <label class="form-label" for="search">Pencarian</label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="form-control"
                            placeholder="Cari kode transaksi"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div style="align-self: flex-end;">
                        <button type="submit" class="history-apply-button">Terapkan</button>
                    </div>
                </div>
            </form>

            @if ($transactions->isEmpty())
                <p class="muted">Belum ada transaksi sesuai filter yang dipilih.</p>
            @else
                <div class="history-table-wrapper table-scroll">
                    <table class="data-table" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->code }}</td>
                                    <td>{{ $transaction->transaction_date->timezone($displayTimezone)->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        <span class="badge">{{ ucfirst($transaction->payment_method) }}</span>
                                    </td>
                                    <td>{{ $transaction->details_count }}</td>
                                    <td>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <a
                                                class="btn btn-secondary"
                                                href="{{ route('kasir.transaction.print', $transaction->id) }}"
                                                target="_blank"
                                            >
                                                Cetak
                                            </a>
                                            <a
                                                class="btn btn-primary"
                                                href="{{ route('kasir.pos', ['transaction' => $transaction->id]) }}"
                                            >
                                                Tambah Produk
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif
        </div>
    </div>
@endsection
