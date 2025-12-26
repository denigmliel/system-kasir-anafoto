@php
    $userRole = auth()->user()?->role;

    $sections = collect([
        [
            'key' => 'admin',
            'label' => 'Dashboard Admin',
            'links' => [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'pattern' => 'admin.dashboard'],
            ],
        ],
        [
            'key' => 'kasir',
            'label' => 'Menu Kasir',
            'links' => [
                ['route' => 'kasir.dashboard', 'label' => 'Dashboard', 'pattern' => 'kasir.dashboard'],
                ['route' => 'kasir.pos', 'label' => 'Point of Sale', 'pattern' => 'kasir.pos'],
                ['route' => 'kasir.transaction.history', 'label' => 'Riwayat Transaksi', 'pattern' => 'kasir.transaction.*'],
            ],
        ],
        [
            'key' => 'gudang',
            'label' => 'Menu Gudang',
            'links' => [
                ['route' => 'gudang.dashboard', 'label' => 'Dashboard', 'pattern' => 'gudang.dashboard'],
                ['route' => 'gudang.products.index', 'label' => 'Produk', 'pattern' => 'gudang.products.*'],
                ['route' => 'gudang.stock.movements', 'label' => 'Pergerakan Stok', 'pattern' => 'gudang.stock.*'],
                ['route' => 'gudang.reports.stock', 'label' => 'Laporan', 'pattern' => 'gudang.reports.*'],
            ],
        ],
    ])->filter(function (array $section) use ($userRole) {
        return $userRole ? $section['key'] === $userRole : true;
    })->map(function (array $section) {
        $isActive = collect($section['links'])
            ->contains(fn ($link) => request()->routeIs($link['pattern'] ?? $link['route']));

        return $section + [
            'is_active' => $isActive,
            'is_single' => count($section['links']) === 1,
        ];
    });
@endphp

<aside class="sidebar">
    <div class="sidebar-top">
        <div class="brand">
            <span>ANA</span>
            <span>FOTOCOPY</span>
        </div>

        <div class="sidebar-sections">
            @foreach ($sections as $section)
                @if ($section['is_single'])
                    @php $link = $section['links'][0]; @endphp
                    <div class="sidebar-section sidebar-section-single">
                        <a
                            href="{{ route($link['route']) }}"
                            class="sidebar-single-link {{ request()->routeIs($link['pattern'] ?? $link['route']) ? 'active' : '' }}"
                        >
                            {{ $section['label'] }}
                        </a>
                    </div>
                @else
                    <details class="sidebar-section" open>
                        <summary class="sidebar-summary">
                            <span>{{ $section['label'] }}</span>
                            <span class="sidebar-caret" aria-hidden="true"></span>
                        </summary>

                        <nav class="sidebar-links">
                            @foreach ($section['links'] as $link)
                                <a
                                    href="{{ route($link['route']) }}"
                                    class="{{ request()->routeIs($link['pattern'] ?? $link['route']) ? 'active' : '' }}"
                                >
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </nav>
                    </details>
                @endif
            @endforeach
        </div>
    </div>

    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-button">
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
