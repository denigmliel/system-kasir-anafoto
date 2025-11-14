@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@push('styles')
    <style>
        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .filters .form-control {
            width: auto;
            min-width: 180px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .history-apply-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.2px;
            cursor: pointer;
            color: #ffffff;
            background-color: #2563eb;
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.22);
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
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .history-table-wrapper--scrollable {
            max-height: 460px; /* approx. 10 rows */
            overflow-y: auto;
            border: none;
        }

        .history-table-wrapper--scrollable::-webkit-scrollbar {
            width: 8px;
        }

        .history-table-wrapper--scrollable::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.6);
            border-radius: 999px;
        }
    </style>
@endpush

@section('content')
    @php($displayTimezone = config('app.timezone_display', 'Asia/Jakarta'))
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
            @php($scrollable = $transactions->count() > 10)
            <div class="history-table-wrapper {{ $scrollable ? 'history-table-wrapper--scrollable' : '' }}">
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

            <div style="margin-top: 16px;">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection
