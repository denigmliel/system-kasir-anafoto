<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-rounded.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/logo-rounded.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo-rounded.png') }}">

    <title>@yield('title', 'Panel Gudang')</title>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }
        :root {
            color-scheme: light;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #1d2939;
            --sidebar-width: 210px;
            --sidebar-transition: 0.25s ease;
            font-size: 14px;
        }

        body {
            margin: 0;
            background-color: #f4f6f8;
            overflow-x: hidden;
            min-height: 100vh;
        }
        .layout {
            min-height: 100vh;
            display: grid;
            grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
        }

        .layout.has-sidebar-open {
            overflow: hidden;
        }

        .sidebar {
            background: linear-gradient(180deg, #b91c1c 0%, #7f1d1d 100%);
            color: #fff;
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: space-between;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            width: var(--sidebar-width);
            z-index: 1001;
            transition: transform var(--sidebar-transition), box-shadow var(--sidebar-transition);
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.05;
            color: #fff;
        }

        .brand span {
            display: block;
        }

        .sidebar-top {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar-bottom {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .logout-form {
            width: 100%;
        }

        .sidebar-sections {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sidebar-section {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.06);
            overflow: hidden;
        }

        .sidebar-section-single {
            padding: 0;
        }

        .sidebar-single-link {
            display: block;
            padding: 10px 12px;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.2px;
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
        }

        .sidebar-single-link.active,
        .sidebar-single-link:hover {
            background-color: rgba(255, 255, 255, 0.22);
            color: #fff;
        }

        .sidebar-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            padding: 10px 12px;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.2px;
            color: rgba(255, 255, 255, 0.92);
            list-style: none;
        }

        .sidebar-summary::-webkit-details-marker {
            display: none;
        }

        .sidebar-caret {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: rotate(45deg);
            transition: transform 0.2s ease;
            margin-left: 12px;
        }

        .sidebar-section[open] .sidebar-caret {
            transform: rotate(-135deg);
        }

        .sidebar-links {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 0 12px 12px;
        }

        .sidebar-links a {
            color: rgba(255, 255, 255, 0.82);
            text-decoration: none;
            padding: 8px 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s, color 0.2s;
        }

        .sidebar-links a.active,
        .sidebar-links a:hover {
            background-color: rgba(255, 255, 255, 0.22);
            color: #fff;
        }

        .content {
            background-color: #f4f6f8;
            padding: 18px clamp(14px, 2.5vw, 26px);
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .form-control,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #d0d5dd;
            font-size: 14px;
        }

        .form-textarea {
            min-height: 110px;
        }

        .input-error {
            color: #b42318;
            font-size: 13px;
            margin-top: 4px;
        }

        .logout-button {
            border: none;
            background: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
            color: #fff;
            padding: 10px 12px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            width: 100%;
            letter-spacing: 0.2px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            box-shadow: 0 8px 18px rgba(220, 38, 38, 0.24);
        }

        .logout-button:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 14px 28px rgba(220, 38, 38, 0.32);
        }

        .logout-button:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(220, 38, 38, 0.26);
        }

        .logout-button span {
            pointer-events: none;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.38);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity var(--sidebar-transition), visibility var(--sidebar-transition);
            z-index: 1000;
        }

        .layout.has-sidebar-open .sidebar-overlay {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .sidebar-toggle {
            position: fixed;
            top: 14px;
            left: 14px;
            width: 46px;
            height: 46px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
            color: #fff;
            display: none;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
            cursor: pointer;
            z-index: 1002;
            transition: transform 0.16s ease, box-shadow 0.16s ease, filter 0.16s ease;
        }

        .sidebar-toggle:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.22);
        }

        .sidebar-toggle:active {
            transform: translateY(0);
        }

        .sidebar-toggle__icon,
        .sidebar-toggle__icon::before,
        .sidebar-toggle__icon::after {
            display: block;
            width: 22px;
            height: 2px;
            background: currentColor;
            border-radius: 2px;
            position: absolute;
            left: 0;
            transition: transform 0.2s ease, opacity 0.2s ease;
            content: '';
        }

        .sidebar-toggle__icon {
            position: relative;
        }

        .sidebar-toggle__icon::before { top: -7px; }
        .sidebar-toggle__icon::after { top: 7px; }

        .sidebar-toggle__label {
            display: none;
            font-weight: 700;
            letter-spacing: 0.2px;
            font-size: 14px;
        }

        .layout.has-sidebar-open .sidebar-toggle__icon {
            background: transparent;
        }
        .layout.has-sidebar-open .sidebar-toggle__icon::before {
            transform: translateY(7px) rotate(45deg);
        }
        .layout.has-sidebar-open .sidebar-toggle__icon::after {
            transform: translateY(-7px) rotate(-45deg);
        }

        .logout-button.logout-button--gray {
            --logout-bg: #6b7280;
            --logout-border: #4b5563;
            --logout-hover-bg: #4b5563;
        }

        .logout-button.logout-button--danger {
            --logout-bg: #dc2626;
            --logout-border: #b91c1c;
            --logout-hover-bg: #b91c1c;
        }

        .chip-button {
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            min-width: 72px;
        }

        .chip-button:hover {
            transform: translateY(-1px);
        }

        .chip-button:active {
            transform: translateY(0);
            filter: brightness(0.95);
        }

        .chip-button--yellow {
            background-color: #f59e0b;
            color: #1f2937;
            box-shadow: 0 10px 18px rgba(245, 158, 11, 0.24);
        }

        .chip-button--yellow:hover {
            box-shadow: 0 12px 22px rgba(245, 158, 11, 0.3);
        }

        .chip-button--blue {
            background-color: #2563eb;
            color: #fff;
            box-shadow: 0 10px 18px rgba(37, 99, 235, 0.24);
        }

        .chip-button--blue:hover {
            box-shadow: 0 12px 22px rgba(37, 99, 235, 0.32);
        }

        .chip-button--danger {
            background-color: #dc2626;
            color: #fff;
            box-shadow: 0 10px 18px rgba(220, 38, 38, 0.24);
        }

        .chip-button--danger:hover {
            box-shadow: 0 12px 22px rgba(220, 38, 38, 0.32);
        }

        .chip-button--gray {
            background-color: #6b7280;
            color: #fff;
            box-shadow: 0 10px 18px rgba(107, 114, 128, 0.22);
        }

        .chip-button--gray:hover {
            box-shadow: 0 12px 22px rgba(107, 114, 128, 0.28);
        }

        .main-content {
            flex: 1;
            width: 100%;
            max-width: none;
            margin: 0;
        }

        .page-title {
            margin: 0 0 18px;
            font-size: 24px;
            font-weight: 600;
            color: #182230;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
            border: 1px solid #e5e7eb;
        }

        .card + .card {
            margin-top: 20px;
        }

        .grid {
            display: grid;
            gap: 20px;
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .flash {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 18px;
        }

        .flash-success {
            background-color: #ecfdf3;
            color: #027a48;
            border: 1px solid #86efac;
        }

        .flash-error {
            background-color: #fef3f2;
            color: #b42318;
            border: 1px solid #fda29b;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th,
        table.data-table td {
            text-align: left;
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        table.data-table thead {
            background-color: #eef2ff;
        }

        .muted {
            color: #667085;
            font-size: 13px;
        }

        nav[aria-label="Pagination Navigation"] {
            margin-top: 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 14px;
        }

        nav[aria-label="Pagination Navigation"] > div {
            width: 100%;
        }

        nav[aria-label="Pagination Navigation"] > :first-child {
            display: none;
        }

        nav[aria-label="Pagination Navigation"] > :last-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        nav[aria-label="Pagination Navigation"] > :last-child > :last-child {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        nav[aria-label="Pagination Navigation"] .relative.inline-flex {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #d0d5dd;
            color: #475467;
            background-color: #fff;
            min-width: 42px;
            font-weight: 600;
            text-decoration: none;
        }

        nav[aria-label="Pagination Navigation"] span[aria-current="page"] > span {
            background-color: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        nav[aria-label="Pagination Navigation"] svg {
            width: 18px;
            height: 18px;
        }

        nav[aria-label="Pagination Navigation"] p {
            margin: 0;
            color: #475467;
        }

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
                min-width: 100%;
            }
        }

        @media (max-width: 640px) {
            nav[aria-label="Pagination Navigation"] > :first-child {
                display: flex;
                justify-content: space-between;
                gap: 12px;
            }

            nav[aria-label="Pagination Navigation"] > :last-child {
                display: none;
            }
        }

        @media (max-width: 1200px) {
            :root { --sidebar-width: 195px; }
        }

        @media (max-width: 1024px) {
            :root { --sidebar-width: 185px; }

            .sidebar {
                padding: 16px 12px;
                gap: 16px;
            }

            .sidebar-top { gap: 16px; }
            .sidebar-sections { gap: 10px; }
            .sidebar-links { padding: 0 10px 10px; }
            .content { padding: 18px 16px; }
        }

        @media (max-width: 768px) {
            :root { --sidebar-width: 170px; }

            .layout {
                display: flex;
                flex-direction: column;
                width: 100%;
                min-height: 100vh;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: min(78vw, 300px);
                max-width: 320px;
                height: 100vh;
                max-height: 100vh;
                padding: 16px 14px;
                overflow-y: auto;
                box-shadow: 0 16px 40px rgba(0, 0, 0, 0.24);
                border-radius: 0;
                transform: translateX(-100%);
            }

            .layout.has-sidebar-open .sidebar { transform: translateX(0); }
            .sidebar-toggle { display: inline-flex; }

            .content {
                margin-left: 0;
                width: 100%;
                max-width: 100%;
                padding: 14px 12px 56px;
                min-height: auto;
                height: auto;
                overflow: visible;
                margin-top: 62px;
            }

            .main-content { min-height: auto; }
            .sidebar-toggle {
                padding: 0 14px;
                width: auto;
                min-width: 46px;
                right: auto;
                left: 12px;
                top: 12px;
                z-index: 1200;
                position: sticky;
                align-self: flex-start;
            }
            .sidebar-toggle__label { display: inline; }
        }

        @media (max-width: 640px) {
            :root { --sidebar-width: 160px; }

            .sidebar { padding: 14px 12px; }
            .content { padding: 12px 10px; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="layout">
        @include('layouts.partials.sidebar')

        <div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
        <button
            type="button"
            class="sidebar-toggle"
            id="sidebar-toggle"
            aria-label="Buka menu"
            aria-expanded="false"
        >
            <span class="sidebar-toggle__icon" aria-hidden="true"></span>
            <span class="sidebar-toggle__label">Menu</span>
        </button>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const layout = document.querySelector('.layout');
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');

            if (!layout || !sidebar || !toggle || !overlay) {
                return;
            }

            const setExpanded = (state) => toggle.setAttribute('aria-expanded', state ? 'true' : 'false');

            const closeSidebar = () => {
                layout.classList.remove('has-sidebar-open');
                setExpanded(false);
            };

            const openSidebar = () => {
                layout.classList.add('has-sidebar-open');
                setExpanded(true);
            };

            const toggleSidebar = () => {
                if (layout.classList.contains('has-sidebar-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            };

            toggle.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', closeSidebar);

            sidebar.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });

            window.addEventListener('keyup', (event) => {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
