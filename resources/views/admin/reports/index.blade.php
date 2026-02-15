@extends('layouts.admin')

@section('title', 'Laporan Sistem')

@push('styles')
    <style>
        .report-intro {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .report-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .report-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 10px;
            padding: 8px 14px;
            font-weight: 700;
            font-size: 12.5px;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .report-button--primary {
            background: linear-gradient(135deg, #b91c1c, #7f1d1d);
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(185, 28, 28, 0.22);
        }

        .report-button--primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(185, 28, 28, 0.28);
        }

        .report-button--ghost {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        .report-card {
            display: grid;
            gap: 10px;
        }

        .report-points {
            margin: 0;
            padding-left: 20px;
            color: #334155;
            line-height: 1.5;
        }

        .report-points li + li {
            margin-top: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="report-intro">
        <div>
            <h1 class="page-title" style="margin-bottom: 6px;">Laporan Sistem</h1>
            <p class="muted" style="margin: 0;">
                Pusat ringkasan penjualan dan persediaan untuk mendukung keputusan operasional.
            </p>
        </div>
        <div class="report-actions">
            <a href="{{ route('admin.reports.sales') }}" class="report-button report-button--primary">Laporan Penjualan</a>
            <a href="{{ route('admin.reports.stock') }}" class="report-button report-button--ghost">Laporan Persediaan</a>
        </div>
    </div>

    <div class="card report-card">
        <h2 style="margin: 0; font-size: 16px;">Cakupan Laporan</h2>
        <ol class="report-points">
            <li>
                Laporan pada sistem ini mencakup ringkasan penjualan dan informasi persediaan, sehingga pengguna dapat melihat
                rekap pada periode tertentu serta menelusuri data rinci sesuai kebutuhan.
            </li>
            <li>
                Laporan penjualan menyajikan rekap transaksi berdasarkan periode yang dipilih, seperti total nilai penjualan dan
                jumlah transaksi pada rentang waktu tertentu. Sistem juga menyediakan daftar transaksi sebagai rincian yang dapat
                ditelusuri untuk keperluan pemeriksaan.
            </li>
            <li>
                Selain laporan penjualan, sistem menyediakan laporan persediaan untuk menampilkan kondisi stok terkini setiap
                produk. Laporan ini digunakan untuk memantau ketersediaan item, status produk, serta mendukung pengendalian restock
                berdasarkan kondisi persediaan.
            </li>
        </ol>
    </div>
@endsection
