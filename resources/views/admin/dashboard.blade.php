@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
    <style>
        .module-switch-button {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 999px;
            background: linear-gradient(135deg, #f87171 0%, #7f1d1d 100%);
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.2px;
            text-decoration: none;
            box-shadow: 0 12px 26px rgba(127, 29, 29, 0.28);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .module-switch-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(127, 29, 29, 0.35);
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <h1 class="page-title">Dashboard Admin</h1>

    <div class="grid grid-3">
        <div class="card">
            <div class="muted">Status Sistem</div>
            <div style="font-size: 28px; font-weight: 700; margin-top: 6px;">Aktif</div>
            <p class="muted" style="margin-top: 10px;">Semua layanan berjalan normal.</p>
        </div>

        <div class="card">
            <div class="muted">Pengguna Saat Ini</div>
            <div style="font-size: 28px; font-weight: 700; margin-top: 6px;">
                {{ auth()->user()->name ?? 'Admin' }}
            </div>
            <p class="muted" style="margin-top: 10px;">Selamat datang kembali di panel administrasi.</p>
        </div>

        <div class="card">
            <div class="muted">Langkah Berikutnya</div>
            <div style="font-size: 28px; font-weight: 700; margin-top: 6px;">Mulai</div>
            <p class="muted" style="margin-top: 10px;">Gunakan menu di sebelah kiri untuk navigasi.</p>
        </div>
    </div>

    <div class="grid" style="margin-top: 24px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
        <div class="card">
            <h2 style="margin: 0 0 12px; font-size: 20px;">Modul Kasir</h2>
            <p class="muted" style="margin-bottom: 16px;">Akses fitur Point of Sale dan riwayat transaksi.</p>
            <a href="{{ route('kasir.dashboard') }}" class="module-switch-button">
                Kasir
            </a>
        </div>

        <div class="card">
            <h2 style="margin: 0 0 12px; font-size: 20px;">Modul Gudang</h2>
            <p class="muted" style="margin-bottom: 16px;">Kelola stok, pembelian, dan supplier.</p>
            <a href="{{ route('gudang.dashboard') }}" class="module-switch-button">
                Gudang
            </a>
        </div>
    </div>

    <div class="card" style="margin-top: 24px;">
        <h2 style="margin: 0 0 12px; font-size: 20px;">Catatan</h2>
        <p style="margin: 0; color: #1f2937;">
            Halaman ini masih dapat dikembangkan untuk menampilkan statistik penjualan, aktivitas pengguna,
            ataupun laporan lainnya sesuai kebutuhan administrasi.
        </p>
    </div>
@endsection
