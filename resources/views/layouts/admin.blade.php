<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-rounded.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/logo-rounded.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo-rounded.png') }}">

    <title>@yield('title', 'Panel Admin')</title>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        :root {
            color-scheme: light;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f6f7fb;
            color: #0f172a;
        }

        body {
            margin: 0;
            overflow-x: hidden;
            min-height: 100vh;
            background:
                radial-gradient(circle at 18% 22%, rgba(239, 68, 68, 0.14), transparent 24%),
                radial-gradient(circle at 82% 12%, rgba(14, 165, 233, 0.12), transparent 22%),
                #f6f7fb;
        }

        .layout {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .top-nav {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(12px);
            background: linear-gradient(90deg, rgba(255,255,255,0.92), rgba(255,255,255,0.85));
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
        }

        .top-nav__inner {
            max-width: none;
            margin: 0;
            padding: 10px clamp(14px, 3vw, 22px);
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: space-between;
            width: 100%;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: #b91c1c;
            letter-spacing: 0.6px;
        }

        .brand-mark .brand-logo {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 10px 20px rgba(185, 28, 28, 0.2);
            border: 1px solid #fee2e2;
            background: #fff;
        }

        .top-nav__right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .chip {
            padding: 8px 12px;
            border-radius: 999px;
            background: #fef2f2;
            color: #b91c1c;
            font-weight: 700;
            border: 1px solid #fecdd3;
            font-size: 13px;
            box-shadow: 0 8px 20px rgba(185, 28, 28, 0.12);
        }

        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            font-weight: 600;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.08);
        }

        .logout-button {
            border: none;
            background: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
            color: #fff;
            padding: 10px 14px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.3px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            box-shadow: 0 10px 22px rgba(220, 38, 38, 0.22);
        }

        .logout-button:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 14px 28px rgba(220, 38, 38, 0.28);
        }

        .logout-button:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(220, 38, 38, 0.22);
        }

        .content {
            padding: 12px clamp(16px, 3vw, 28px) 18px;
            max-width: none;
            margin: 0;
            width: 100%;
            box-sizing: border-box;
        }

        .main-content {
            flex: 1;
            width: 100%;
            max-width: none;
            margin: 0;
        }

        .flash {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .flash-success {
            background-color: #ecfdf3;
            color: #047857;
            border: 1px solid #86efac;
        }

        .flash-error {
            background-color: #fef3f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .page-title {
            margin: 0 0 16px;
            font-size: 24px;
            font-weight: 600;
            color: #111827;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 12px 12px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            border: 1px solid #e5e7eb;
        }

        .grid { display: grid; gap: 12px; }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .muted { color: #64748b; font-size: 13px; }

        .table-scroll {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 10px;
        }

        .table-scroll table {
            min-width: 560px;
        }

        @media (max-width: 640px) {
            .table-scroll table {
                min-width: 480px;
            }
        }

        @media (max-width: 768px) {
            .top-nav__inner { padding: 12px 16px; }
            .content { padding: 18px 16px 32px; }
            .user-pill { width: 100%; justify-content: center; }
            .top-nav__right { width: 100%; justify-content: flex-end; }
        }

        @media (max-width: 600px) {
            .top-nav__inner { padding: 10px 12px; }
            .brand-mark { font-size: 14px; gap: 8px; }
            .brand-mark .brand-logo { width: 30px; height: 30px; }
            .content { padding: 14px 12px 22px; }
            .card { padding: 10px; border-radius: 9px; }
            .grid { gap: 10px; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="layout">
        <header class="top-nav">
            <div class="top-nav__inner">
                <div class="brand-mark">
                    <img src="{{ asset('img/logo.png') }}" alt="ANA Fotocopy" class="brand-logo">
                    <span>Ana Fotocopy</span>
                </div>
                <div class="top-nav__right">
                    <form method="POST" action="{{ route('logout') }}" class="logout-form" style="margin:0;">
                        @csrf
                        <button type="submit" class="logout-button">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="content">
            <div class="main-content">
                @if (session('success'))
                    <div class="flash flash-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="flash flash-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="flash flash-error">
                        Terjadi kesalahan. Periksa kembali input Anda.
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
