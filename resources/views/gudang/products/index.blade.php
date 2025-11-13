@extends('layouts.gudang')

@section('title', 'Manajemen Produk')

@push('styles')
    <style>
        .product-console {
            border-radius: 22px;
            padding: 24px 26px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
            background-color: #ffffff;
        }

        .product-console__form {
            margin-bottom: 18px;
        }

        .product-console__row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: flex-end;
        }

        .product-console__field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .product-console__field label {
            font-size: 13px;
            font-weight: 600;
            color: #475467;
        }

        .product-console__field input,
        .product-console__field select {
            padding: 11px 14px;
            border-radius: 12px;
            border: 1px solid #d0d5dd;
            min-width: 190px;
            font-size: 14px;
            color: #111827;
            background-color: #f8fafc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .product-console__field input:focus,
        .product-console__field select:focus {
            outline: none;
            border-color: #2563eb;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .category-select-wrapper {
            position: relative;
        }

        .category-select-wrapper .category-search-input {
            display: none;
            margin-bottom: 0;
        }

        .category-select-wrapper--enhanced .category-search-input {
            display: block;
        }

        .category-select-wrapper--enhanced .category-select {
            display: none;
        }

        .category-suggestions {
            position: absolute;
            top: calc(100% + 12px);
            left: 0;
            right: 0;
            display: none;
            max-height: 240px;
            overflow-y: auto;
            padding: 6px;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            background-color: #fff;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
            z-index: 30;
        }

        .category-suggestions.is-visible {
            display: block;
        }

        .category-suggestions--above {
            transform-origin: bottom center;
        }

        .category-suggestion {
            width: 100%;
            padding: 8px 10px;
            background: transparent;
            border: none;
            border-radius: 6px;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.1s ease;
        }

        .category-suggestion:hover,
        .category-suggestion.is-active {
            background-color: #f1f5ff;
        }

        .category-suggestion-label {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
        }

        .category-suggestion-empty {
            font-size: 13px;
            color: #6b7280;
            padding: 8px 10px;
        }

        .product-console__input-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-console__actions {
            margin-left: auto;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.2px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .button--primary {
            background-color: #2563eb;
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.24);
        }

        .button--primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.26);
        }

        .button--primary:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
        }

        .button--success {
            background-color: #16a34a;
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(22, 163, 74, 0.24);
        }

        .button--success:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(22, 163, 74, 0.28);
        }

        .button--success:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(22, 163, 74, 0.2);
        }

        nav[aria-label="Pagination Navigation"] {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 24px;
            align-items: flex-end;
        }

        nav[aria-label="Pagination Navigation"] > div:first-child {
            display: none;
        }

        nav[aria-label="Pagination Navigation"] > div:last-child {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
        }

        nav[aria-label="Pagination Navigation"] p {
            margin: 0;
            font-size: 13px;
            color: #475467;
        }

        nav[aria-label="Pagination Navigation"] span > span,
        nav[aria-label="Pagination Navigation"] span > a,
        nav[aria-label="Pagination Navigation"] a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            background-color: #ffffff;
            border: 1px solid #d0d5dd;
            border-radius: 10px;
            text-decoration: none;
            min-width: 40px;
        }

        nav[aria-label="Pagination Navigation"] span[aria-current="page"] > span {
            background-color: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        nav[aria-label="Pagination Navigation"] a:hover {
            filter: brightness(0.97);
        }

        nav[aria-label="Pagination Navigation"] svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 640px) {
            .product-console__field {
                width: 100%;
            }

            .product-console__input-group {
                flex-direction: column;
                align-items: stretch;
            }

            .product-console__field input,
            .product-console__field select {
                min-width: unset;
            }

            .product-console__actions {
                width: 100%;
                justify-content: flex-start;
                margin-left: 0;
            }

            .product-console__actions .button {
                width: 100%;
            }

            .product-console__input-group .button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <h1 class="page-title">Manajemen Produk</h1>

    <div class="card product-console">
        <form method="GET" action="{{ route('gudang.products.index') }}" class="product-console__form">
            <div class="product-console__row">
                <div class="product-console__field">
                    <label for="search">Pencarian</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Nama, kode atau ID produk"
                    >
                </div>

                @php
                    $currentCategoryId = request('category_id');
                    $currentCategoryName = '';
                    if ($currentCategoryId !== null && $currentCategoryId !== '') {
                        $matchedCategory = $categories->firstWhere('id', (int) $currentCategoryId);
                        $currentCategoryName = $matchedCategory ? $matchedCategory->name : '';
                    }
                @endphp
                <div class="product-console__field">
                    <label for="category_search">Kategori</label>
                    <div class="category-select-wrapper" id="filter-category-wrapper">
                        <input
                            type="search"
                            class="form-control category-search-input"
                            id="category_search"
                            placeholder="Cari kategori..."
                            autocomplete="off"
                            value="{{ $currentCategoryName }}"
                            @if ($currentCategoryName !== '')
                                title="{{ $currentCategoryName }}"
                            @endif
                        >
                        <select
                            name="category_id"
                            id="category_id"
                            class="form-select category-select"
                        >
                            <option value="">Semua Kategori</option>
                            <option value="uncategorized" @selected($currentCategoryId === 'uncategorized')>Tanpa Kategori</option>
                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    data-category-name="{{ $category->name }}"
                                    @selected((string) $currentCategoryId === (string) $category->id)
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="category-suggestions"></div>
                    </div>
                </div>

                <div class="product-console__field">
                    <label for="status">Status</label>
                    <div class="product-console__input-group">
                        <select
                            name="status"
                            id="status"
                        >
                            <option value="">Semua</option>
                            <option value="active" @selected(request('status') === 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Tidak Aktif</option>
                        </select>
                        <button type="submit" class="button button--primary">Filter</button>
                    </div>
                </div>

                <div class="product-console__actions">
                    <a
                        href="{{ route('gudang.products.low_stock') }}"
                        class="button button--primary"
                    >
                        Lihat Stok Menipis
                    </a>
                    <a
                        href="{{ route('gudang.products.create') }}"
                        class="button button--success"
                    >
                        Tambah Produk
                    </a>
                </div>
            </div>
        </form>

        @if ($products->isEmpty())
            <p class="muted">Belum ada produk yang tercatat.</p>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th style="width: 120px; text-align: center;">Stok</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>#{{ $product->id }}</td>
                            <td>{{ $product->code ?? '-' }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ optional($product->category)->name ?? 'Tanpa Kategori' }}</td>
                            <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                            <td style="text-align: center; white-space: nowrap;">
                                {{ $product->is_stock_unlimited ? 'Tidak terbatas' : $product->stock }}
                            </td>
                            <td>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                            <td style="text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                                <a href="{{ route('gudang.products.show', $product) }}" class="chip-button chip-button--yellow">Detail</a>
                                <a href="{{ route('gudang.products.edit', $product) }}" class="chip-button chip-button--blue">Edit</a>
                                <form
                                    method="POST"
                                    action="{{ route('gudang.products.destroy', $product) }}"
                                    onsubmit="return confirm('Hapus produk {{ $product->name }} secara permanen?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="chip-button chip-button--danger">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 16px;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('filter-category-wrapper');
            if (!wrapper) {
                return;
            }

            const select = wrapper.querySelector('.category-select');
            const searchInput = wrapper.querySelector('.category-search-input');
            const suggestions = wrapper.querySelector('.category-suggestions');

            if (!select || !searchInput || !suggestions || wrapper.dataset.searchInitialized === '1') {
                return;
            }

            wrapper.classList.add('category-select-wrapper--enhanced');
            wrapper.dataset.searchInitialized = '1';

            let suggestionButtons = [];
            let highlightedIndex = -1;
            let repositionActive = false;

            const ensureSuggestionsPortal = () => {
                if (!suggestions || suggestions.dataset.portaled === '1') {
                    return;
                }
                suggestions.dataset.portaled = '1';
                document.body.appendChild(suggestions);
                suggestions.style.position = 'absolute';
            };

            const MAX_SUGGESTION_HEIGHT = 320;
            const MIN_SUGGESTION_HEIGHT = 120;
            const SAFETY_GAP = 12;
            const HORIZONTAL_MARGIN = 8;

            const positionSuggestions = () => {
                if (!suggestions || suggestions.dataset.isVisible !== '1') {
                    return;
                }

                const rect = searchInput.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const viewportWidth = window.innerWidth;
                const availableBelow = viewportHeight - rect.bottom;
                const availableAbove = rect.top;
                const shouldOpenAbove = availableBelow < 200 && availableAbove > availableBelow;
                const availableSpace = shouldOpenAbove ? availableAbove : availableBelow;

                const desiredHeight = Math.max(availableSpace - SAFETY_GAP, MIN_SUGGESTION_HEIGHT);
                const limitedHeight = Math.min(MAX_SUGGESTION_HEIGHT, desiredHeight);
                const finalHeight = Math.min(limitedHeight, Math.max(availableSpace - 4, 4));

                suggestions.style.maxHeight = `${Math.max(finalHeight, 0)}px`;
                suggestions.style.width = `${rect.width}px`;

                // Force layout so offsetHeight is up to date with the latest maxHeight.
                const currentHeight = Math.min(suggestions.offsetHeight, finalHeight);

                const desiredTop = shouldOpenAbove
                    ? rect.top + window.scrollY - currentHeight - 4
                    : rect.bottom + window.scrollY + 4;

                const viewportTop = window.scrollY + SAFETY_GAP;
                const viewportBottom = window.scrollY + viewportHeight - SAFETY_GAP;
                const clampedTop = shouldOpenAbove
                    ? Math.max(viewportTop, desiredTop)
                    : Math.min(desiredTop, viewportBottom - currentHeight);

                const desiredLeft = rect.left + window.scrollX;
                const viewportLeft = window.scrollX + HORIZONTAL_MARGIN;
                const viewportRight = window.scrollX + viewportWidth - HORIZONTAL_MARGIN;
                let clampedLeft = desiredLeft;

                if (clampedLeft + rect.width > viewportRight) {
                    clampedLeft = viewportRight - rect.width;
                }
                clampedLeft = Math.max(clampedLeft, viewportLeft);

                suggestions.style.left = `${clampedLeft}px`;
                suggestions.style.top = `${clampedTop}px`;
                suggestions.classList.toggle('category-suggestions--above', shouldOpenAbove);
            };

            const handleViewportChange = () => {
                positionSuggestions();
            };

            const enableReposition = () => {
                if (repositionActive) {
                    return;
                }
                repositionActive = true;
                window.addEventListener('resize', handleViewportChange);
                window.addEventListener('scroll', handleViewportChange, true);
            };

            const disableReposition = () => {
                if (!repositionActive) {
                    return;
                }
                repositionActive = false;
                window.removeEventListener('resize', handleViewportChange);
                window.removeEventListener('scroll', handleViewportChange, true);
            };

            const formatOptionLabel = (option) => (option ? (option.textContent || '').trim() : '');

            const closeSuggestions = () => {
                suggestions.innerHTML = '';
                suggestions.classList.remove('is-visible');
                suggestions.classList.remove('category-suggestions--above');
                suggestions.dataset.isVisible = '0';
                suggestions.style.display = 'none';
                suggestions.style.maxHeight = '';
                highlightedIndex = -1;
                suggestionButtons = [];
                disableReposition();
            };

            const highlightSuggestion = (index) => {
                if (!suggestionButtons.length) {
                    highlightedIndex = -1;
                    return;
                }

                const maxIndex = suggestionButtons.length - 1;
                let nextIndex = index;

                if (index < 0) {
                    nextIndex = maxIndex;
                } else if (index > maxIndex) {
                    nextIndex = 0;
                }

                suggestionButtons.forEach((btn) => btn.classList.remove('is-active'));

                const button = suggestionButtons[nextIndex];
                if (button) {
                    button.classList.add('is-active');
                    button.scrollIntoView({ block: 'nearest' });
                    highlightedIndex = nextIndex;
                }
            };

            const selectOption = (option) => {
                if (!option) {
                    return;
                }

                if (select.value !== option.value) {
                    select.value = option.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }

                const label = option.value ? formatOptionLabel(option) : '';
                searchInput.value = label;
                if (label) {
                    searchInput.title = label;
                } else {
                    searchInput.removeAttribute('title');
                }

                closeSuggestions();
            };

            const renderSuggestions = (query) => {
                ensureSuggestionsPortal();
                const normalized = query.trim().toLowerCase();
                const options = Array.from(select.options).filter((option) => !option.disabled);

                const matches = options.filter((option) => {
                    if (!normalized) {
                        return true;
                    }
                    return formatOptionLabel(option).toLowerCase().includes(normalized);
                });

                suggestions.innerHTML = '';

                if (!matches.length) {
                    const empty = document.createElement('div');
                    empty.className = 'category-suggestion-empty';
                    empty.textContent = 'Kategori tidak ditemukan';
                    suggestions.appendChild(empty);
                    suggestionButtons = [];
                    highlightedIndex = -1;
                } else {
                    matches.forEach((option) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'category-suggestion';
                        button.dataset.optionIndex = option.index;
                        button.innerHTML = `
                            <div class="category-suggestion-label">${formatOptionLabel(option)}</div>
                        `;

                        button.addEventListener('mousedown', (event) => {
                            event.preventDefault();
                            selectOption(option);
                        });

                        suggestions.appendChild(button);
                    });

                    suggestionButtons = Array.from(suggestions.querySelectorAll('.category-suggestion'));
                    highlightedIndex = -1;
                }

                suggestions.classList.add('is-visible');
                suggestions.dataset.isVisible = '1';
                suggestions.style.display = 'block';
                positionSuggestions();
                enableReposition();
            };

            const syncInputWithSelection = () => {
                const selectedOption = select.options[select.selectedIndex];
                const label = selectedOption && selectedOption.value ? formatOptionLabel(selectedOption) : '';
                if (searchInput.value !== label) {
                    searchInput.value = label;
                }
                if (label) {
                    searchInput.title = label;
                } else {
                    searchInput.removeAttribute('title');
                }
            };

            searchInput.addEventListener('focus', () => {
                renderSuggestions(searchInput.value);
            });

            searchInput.addEventListener('input', () => {
                renderSuggestions(searchInput.value);
            });

            searchInput.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    if (!suggestions.classList.contains('is-visible')) {
                        renderSuggestions(searchInput.value);
                    }
                    if (suggestionButtons.length) {
                        const nextIndex = highlightedIndex === -1 ? 0 : highlightedIndex + 1;
                        highlightSuggestion(nextIndex);
                    }
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (!suggestions.classList.contains('is-visible')) {
                        renderSuggestions(searchInput.value);
                    }
                    if (suggestionButtons.length) {
                        const nextIndex = highlightedIndex === -1 ? suggestionButtons.length - 1 : highlightedIndex - 1;
                        highlightSuggestion(nextIndex);
                    }
                } else if (event.key === 'Enter') {
                    if (highlightedIndex >= 0 && suggestionButtons[highlightedIndex]) {
                        event.preventDefault();
                        const optionIndex = Number(suggestionButtons[highlightedIndex].dataset.optionIndex);
                        const option = select.options[optionIndex];
                        selectOption(option);
                        return;
                    }

                    const directMatch = Array.from(select.options).find((option) => {
                        if (!option.value) {
                            return false;
                        }
                        return formatOptionLabel(option).toLowerCase() === searchInput.value.trim().toLowerCase();
                    });

                    if (directMatch) {
                        event.preventDefault();
                        selectOption(directMatch);
                    }
                } else if (event.key === 'Escape') {
                    if (suggestions.classList.contains('is-visible')) {
                        event.preventDefault();
                        closeSuggestions();
                    }
                }
            });

            searchInput.addEventListener('blur', () => {
                window.setTimeout(() => {
                    closeSuggestions();
                    const trimmed = searchInput.value.trim();
                    if (!trimmed) {
                        searchInput.value = '';
                        select.value = '';
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    } else {
                        syncInputWithSelection();
                    }
                }, 120);
            });

            select.addEventListener('change', () => {
                syncInputWithSelection();
            });

            syncInputWithSelection();
        });
    </script>
@endpush
