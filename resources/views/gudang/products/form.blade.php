@extends('layouts.gudang')

@section('title', $mode === 'create' ? 'Tambah Produk' : 'Edit Produk')

@push('styles')
    <style>
        .form-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .form-group--narrow {
            max-width: 420px;
        }

        .form-group--narrow .form-control {
            width: 100%;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px 28px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #182230;
        }

        .form-control,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #d0d5dd;
            font-size: 14px;
            background-color: #fff;
        }

        .scroll-select {
            position: relative;
            width: 100%;
        }

        .scroll-select.is-open {
            z-index: 30;
        }

        .scroll-select__native {
            position: absolute;
            inset: 0;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .scroll-select__trigger {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #d0d5dd;
            background-color: #fff;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .scroll-select__trigger:focus-visible {
            outline: none;
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25);
        }

        .scroll-select__label {
            flex: 1;
            text-align: left;
            color: #111322;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .scroll-select__icon {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #475467;
        }

        .scroll-select__icon svg {
            width: 100%;
            height: 100%;
        }

        .scroll-select__dropdown {
            position: absolute;
            left: 0;
            right: 0;
            margin-top: 4px;
            border-radius: 12px;
            border: 1px solid #d0d5dd;
            background-color: #fff;
            box-shadow: 0 25px 40px rgba(15, 23, 42, 0.12);
            max-height: 320px;
            padding: 8px;
            display: none;
            flex-direction: column;
            gap: 8px;
        }

        .scroll-select.is-open .scroll-select__dropdown {
            display: flex;
        }

        .scroll-select__search-input {
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .scroll-select__search-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .scroll-select__options {
            flex: 1;
            overflow-y: auto;
            padding: 4px 0;
        }

        .scroll-select__option {
            display: block;
            width: 100%;
            padding: 10px 16px;
            text-align: left;
            background: transparent;
            border: none;
            font-size: 14px;
            color: #1f2937;
            cursor: pointer;
        }

        .scroll-select__option:hover,
        .scroll-select__option.is-focused {
            background-color: #f1f5f9;
        }

        .scroll-select__option.is-selected {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }

        .scroll-select__option:disabled {
            color: #94a3b8;
            cursor: not-allowed;
            background: transparent;
        }

        .form-control--compact {
            max-width: 220px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        .unit-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .unit-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d0d5dd;
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }

        .unit-table thead {
            background-color: #f8fafc;
        }

        .unit-table th,
        .unit-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #e4e7ec;
            font-size: 14px;
        }
        .unit-table th:first-child,
        .unit-table td:first-child {
            padding-right: 20px;
        }

        .unit-table th:nth-child(2),
        .unit-table td:nth-child(2) {
            padding-left: 12px;
        }

        .unit-table tbody tr:last-child td {
            border-bottom: none;
        }

        .unit-actions {
            display: flex;
            gap: 8px;
        }

        .unit-actions button {
            border: none;
            padding: 6px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .unit-add-button {
            align-self: flex-start;
            background-color: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            box-shadow: none;
        }

        .unit-add-button:hover {
            background-color: #1d4ed8;
        }

        .unit-remove-button {
            background-color: #f97066;
            color: #fff;
        }

        .unit-default-radio {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .currency-input {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 10px;
            border: 1px solid #d0d5dd;
            border-radius: 10px;
            background-color: #fff;
        }

        .currency-input:focus-within {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        .currency-prefix {
            font-weight: 600;
            color: #1f3b68;
        }

        .currency-input input {
            border: none;
            outline: none;
            background: transparent;
            flex: 1;
            font-size: 14px;
            padding: 0;
        }

        .form-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 16px;
        }

        .form-button {
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
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
        }

        .form-button--success {
            background-color: #16a34a;
            color: #ffffff;
        }

        .form-button--success:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(22, 163, 74, 0.26);
        }

        .form-button--success:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(22, 163, 74, 0.2);
        }

        .form-button--primary {
            background-color: #2563eb;
            color: #ffffff;
        }

        .form-button--primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.24);
        }

        .form-button--primary:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.2);
        }

        .form-button--danger {
            background-color: #dc2626;
            color: #ffffff;
        }

        .form-button--danger:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(220, 38, 38, 0.24);
        }

        .form-button--danger:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(220, 38, 38, 0.2);
        }

        .input-error {
            margin-top: 4px;
            color: #b42318;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .unit-actions {
                justify-content: flex-end;
            }
        }
    </style>
@endpush

@section('content')
    <h1 class="page-title">
        {{ $mode === 'create' ? 'Tambah Produk' : 'Edit Produk' }}
    </h1>

    <div class="card">
        <form
            method="POST"
            action="{{ $mode === 'create' ? route('gudang.products.store') : route('gudang.products.update', $product) }}"
        >
            @csrf
            @if ($mode === 'edit')
                @method('PUT')
            @endif
            @php
                $returnTo = old('redirect_to', $redirectTo ?? request('redirect_to'));
            @endphp
            @if ($returnTo)
                <input type="hidden" name="redirect_to" value="{{ $returnTo }}">
            @endif

    @php
        $allowedUnits = isset($allowedUnits)
            ? array_values(array_map('strtoupper', $allowedUnits))
            : ['PCS', 'PACK', 'BOX', 'RIM', 'LEMBAR', 'LUSIN'];

        $existingUnits = $product->relationLoaded('units')
            ? $product->units->map(function ($unit) {
                return [
                    'name' => strtoupper((string) $unit->name),
                    'price' => $unit->price,
                    'is_default' => $unit->is_default,
                ];
            })->toArray()
            : [];

        if (empty($existingUnits)) {
            $existingUnits[] = [
                'name' => strtoupper((string) $product->unit),
                'price' => $product->price,
                'is_default' => true,
            ];
        }

        $unitRows = old('units', $existingUnits);
        $unitRows = array_values(array_map(function ($unit) {
            return [
                'name' => isset($unit['name']) ? strtoupper((string) $unit['name']) : '',
                'price' => $unit['price'] ?? '',
                'is_default' => !empty($unit['is_default']),
            ];
        }, is_array($unitRows) ? $unitRows : []));

        if (empty($unitRows)) {
            $unitRows[] = ['name' => '', 'price' => '', 'is_default' => true];
        }

        $defaultFromOld = old('default_unit');
        if ($defaultFromOld === null) {
            $defaultIndex = 0;
            foreach ($unitRows as $idx => $row) {
                if (!empty($row['is_default'])) {
                    $defaultIndex = $idx;
                    break;
                }
            }
        } else {
            $defaultIndex = (int) $defaultFromOld;
            if (!array_key_exists($defaultIndex, $unitRows)) {
                $defaultIndex = 0;
            }
        }
    @endphp

            <div class="form-section">
                <div class="form-group form-group--narrow">
                    <label class="muted" style="font-weight: 600;">Kode Produk</label>
                    <div style="padding: 10px 12px; border-radius: 10px; border: 1px solid #d0d5dd; background: #f8fafc;">
                        {{ $product->code ?? 'Kode akan dibuat otomatis saat produk disimpan.' }}
                    </div>
                </div>

                <div class="form-group form-group--narrow">
                    <label for="name">Nama Produk</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $product->name) }}"
                        required
                    >
                    @error('name')
                        <div class="input-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group form-group--narrow">
                        <label for="category_id">Kategori</label>
                        <select
                            id="category_id"
                            name="category_id"
                            class="form-select"
                            data-search-placeholder="Cari kategori..."
                        >
                            <option value="">Tanpa Kategori</option>
                            @foreach (($categories ?? collect()) as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(old('category_id', $product->category_id) == $category->id)
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group form-group--narrow">
                        <label for="new_category">Tambah Kategori Baru</label>
                        <input
                            type="text"
                            id="new_category"
                            name="new_category"
                            class="form-control"
                            value="{{ old('new_category') }}"
                            placeholder="Contoh: Bahan Habis Pakai"
                        >
                        <small class="muted">
                            Jika diisi, sistem akan membuat kategori baru dan menggunakannya.
                        </small>
                        @error('new_category')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="unit-section">
                    <div>
                        <strong>Satuan & Harga</strong>
                        <p class="muted" style="margin-top: 4px;">
                            Pilih satuan dari daftar yang tersedia, atur harga, dan tandai salah satunya sebagai default transaksi.
                        </p>
                    </div>

                    <table class="unit-table" id="unit-table" data-available-units='@json($allowedUnits)'>
                        <thead>
                            <tr>
                                <th style="width: 34%;">Nama Satuan</th>
                                <th style="width: 32%;">Harga Jual</th>
                                <th style="width: 20%;">Jadikan Default</th>
                                <th style="width: 20%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unitRows as $index => $unit)
                                <tr class="unit-row">
                                    <td>
                                        @php($selectedUnit = old("units.$index.name", $unit['name']))
                                        <select
                                            name="units[{{ $index }}][name]"
                                            class="form-select"
                                            required
                                            data-field="name"
                                        >
                                            <option value="" disabled {{ $selectedUnit === '' ? 'selected' : '' }}>
                                                Pilih satuan
                                            </option>
                                            @foreach ($allowedUnits as $option)
                                                <option value="{{ $option }}" {{ $selectedUnit === $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("units.$index.name")
                                            <div class="input-error">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <div class="currency-input">
                                            <span class="currency-prefix">Rp</span>
                                            <input
                                                type="number"
                                                name="units[{{ $index }}][price]"
                                                value="{{ old("units.$index.price", $unit['price']) }}"
                                                min="0"
                                                step="1"
                                                required
                                                data-field="price"
                                            >
                                        </div>
                                        @error("units.$index.price")
                                            <div class="input-error">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <label class="unit-default-radio">
                                            <input
                                                type="radio"
                                                name="default_unit"
                                                value="{{ $index }}"
                                                {{ $defaultIndex === $index ? 'checked' : '' }}
                                                data-field="default"
                                            >
                                            <span>Default</span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="unit-actions">
                                            <button type="button" class="unit-remove-button" data-action="remove-unit">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <button type="button" class="unit-add-button" id="add-unit-row">
                        + Tambah Satuan
                    </button>

                    @error('units')
                        <div class="input-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="stock">Stok Saat Ini</label>
                        <input
                            type="text"
                            id="stock"
                            name="stock"
                            class="form-control form-control--compact"
                            value="{{ old('stock', $product->display_stock) }}"
                            required
                            inputmode="numeric"
                            pattern="^(-|\d+)$"
                            title="Masukkan angka atau tanda '-' untuk stok tidak terbatas"
                        >
                        <small style="display: block; margin-top: 6px; color: #475467;">
                            Masukkan angka untuk stok pasti atau '-' jika stok tidak dapat dihitung.
                        </small>
                        @error('stock')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <div class="form-check">
                            <input
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', $product->is_active))
                            >
                            <label for="is_active" style="margin: 0;">Produk aktif</label>
                        </div>
                        @error('is_active')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <button
                    type="submit"
                    class="form-button form-button--success"
                >
                    {{ $mode === 'create' ? 'Simpan Produk' : 'Simpan Perubahan' }}
                </button>
                <a
                    href="{{ route('gudang.products.index') }}"
                    class="form-button form-button--primary"
                >
                    Kembali
                </a>
            </div>
        </form>

        @if ($mode === 'edit')
            <form
                method="POST"
                action="{{ route('gudang.products.destroy', $product) }}"
                style="margin-top: 16px;"
                onsubmit="return confirm('Nonaktifkan produk ini?')"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="form-button form-button--danger">
                    Nonaktifkan Produk
                </button>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {


            function enhanceScrollableSelect(select) {
                if (!select || select.dataset.scrollEnhanced === '1') {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'scroll-select';
                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(select);

                select.classList.add('scroll-select__native');
                select.tabIndex = -1;
                select.dataset.scrollEnhanced = '1';

                const trigger = document.createElement('button');
                trigger.type = 'button';
                trigger.className = 'scroll-select__trigger';
                trigger.innerHTML = `
                    <span class="scroll-select__label"></span>
                    <span class="scroll-select__icon" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" focusable="false">
                            <path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.086l3.71-3.855a.75.75 0 1 1 1.08 1.04l-4.24 4.4a.75.75 0 0 1-1.08 0l-4.24-4.4a.75.75 0 0 1 .02-1.06z" />
                        </svg>
                    </span>
                `;
                wrapper.appendChild(trigger);

                const dropdown = document.createElement('div');
                dropdown.className = 'scroll-select__dropdown';
                wrapper.appendChild(dropdown);

                const searchInput = document.createElement('input');
                searchInput.type = 'search';
                searchInput.className = 'scroll-select__search-input';
                searchInput.placeholder = select.dataset.searchPlaceholder || 'Cari...';
                dropdown.appendChild(searchInput);

                const optionsContainer = document.createElement('div');
                optionsContainer.className = 'scroll-select__options';
                dropdown.appendChild(optionsContainer);

                let optionButtons = [];

                const buildOptions = () => {
                    optionsContainer.innerHTML = '';
                    optionButtons = Array.from(select.options).map((option) => {
                        const optionButton = document.createElement('button');
                        optionButton.type = 'button';
                        optionButton.className = 'scroll-select__option';
                        optionButton.dataset.value = option.value;
                        optionButton.dataset.label = (option.textContent || '').toLowerCase();
                        optionButton.textContent = option.textContent || '';
                        optionButton.disabled = option.disabled;
                        optionButton.addEventListener('click', () => {
                            if (option.disabled) {
                                return;
                            }
                            select.value = option.value;
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                            updateDisplay();
                            closeDropdown();
                            trigger.focus();
                        });
                        optionsContainer.appendChild(optionButton);
                        return optionButton;
                    });
                };

                const getVisibleButtons = () => optionButtons.filter((button) => button.style.display !== 'none');

                const filterOptions = (term) => {
                    const normalized = term.trim().toLowerCase();
                    optionButtons.forEach((button) => {
                        const label = button.dataset.label || '';
                        const matches = !normalized || label.includes(normalized);
                        button.style.display = matches ? 'block' : 'none';
                    });
                };

                const resetSearch = () => {
                    searchInput.value = '';
                    filterOptions('');
                };

                const updateDisplay = () => {
                    const label = trigger.querySelector('.scroll-select__label');
                    const selectedOption = select.options[select.selectedIndex];
                    label.textContent = selectedOption ? selectedOption.textContent : 'Pilih kategori';

                    optionButtons.forEach((button) => {
                        button.classList.toggle('is-selected', button.dataset.value === select.value);
                    });
                };

                const focusSelectedOption = ({ scrollOnly = false } = {}) => {
                    const selectedButton = optionButtons.find((button) => button.dataset.value === select.value && button.style.display !== 'none' && !button.disabled);
                    const fallbackButton = getVisibleButtons().find((button) => !button.disabled);
                    const target = selectedButton || fallbackButton;
                    if (!target) {
                        return;
                    }
                    if (!scrollOnly) {
                        target.focus();
                    }
                    optionsContainer.scrollTop = target.offsetTop - optionsContainer.clientHeight / 2 + target.clientHeight / 2;
                };

                const focusFirstVisibleOption = () => {
                    const first = getVisibleButtons().find((button) => !button.disabled);
                    if (first) {
                        first.focus();
                        optionsContainer.scrollTop = first.offsetTop - optionsContainer.clientHeight / 2 + first.clientHeight / 2;
                    }
                };

                const openDropdown = () => {
                    wrapper.classList.add('is-open');
                    resetSearch();
                    window.requestAnimationFrame(() => {
                        searchInput.focus();
                        searchInput.select();
                        focusSelectedOption({ scrollOnly: true });
                    });
                };

                const closeDropdown = () => {
                    wrapper.classList.remove('is-open');
                };

                const toggleDropdown = () => {
                    if (wrapper.classList.contains('is-open')) {
                        closeDropdown();
                    } else {
                        openDropdown();
                    }
                };

                trigger.addEventListener('click', (event) => {
                    event.preventDefault();
                    toggleDropdown();
                });

                trigger.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        toggleDropdown();
                    } else if (event.key === 'Escape') {
                        event.preventDefault();
                        closeDropdown();
                    }
                });

                dropdown.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        closeDropdown();
                        trigger.focus();
                    }
                });

                dropdown.addEventListener('focusout', (event) => {
                    if (!wrapper.contains(event.relatedTarget)) {
                        closeDropdown();
                    }
                });

                document.addEventListener('click', (event) => {
                    if (!wrapper.contains(event.target)) {
                        closeDropdown();
                    }
                });

                searchInput.addEventListener('input', () => {
                    filterOptions(searchInput.value);
                });

                searchInput.addEventListener('keydown', (event) => {
                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        focusFirstVisibleOption();
                    } else if (event.key === 'Enter') {
                        event.preventDefault();
                        const first = getVisibleButtons().find((button) => !button.disabled);
                        if (first) {
                            first.click();
                        }
                    }
                });

                select.addEventListener('change', updateDisplay);

                buildOptions();
                filterOptions('');
                updateDisplay();
            }
            const categorySelect = document.getElementById('category_id');
            enhanceScrollableSelect(categorySelect);

            const table = document.getElementById('unit-table');
            const tableBody = table ? table.querySelector('tbody') : null;
            const addButton = document.getElementById('add-unit-row');
            const availableUnits = table ? JSON.parse(table.dataset.availableUnits || '[]') : [];

            const getRows = () => Array.from(tableBody?.querySelectorAll('.unit-row') ?? []);
            const getSelects = () => Array.from(tableBody?.querySelectorAll('select[data-field="name"]') ?? []);
            const getCurrentSelections = () => getSelects().map((select) => select.value || '');

            function populateSelectOptions(select, selectedValue, selections) {
                if (!select) {
                    return;
                }

                const fragment = document.createDocumentFragment();

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.disabled = true;
                placeholder.textContent = 'Pilih satuan';
                placeholder.selected = !selectedValue;
                fragment.appendChild(placeholder);

                const usedUnits = new Set(selections.filter(Boolean));
                if (selectedValue) {
                    usedUnits.delete(selectedValue);
                }

                availableUnits.forEach((unit) => {
                    const option = document.createElement('option');
                    option.value = unit;
                    option.textContent = unit;
                    if (unit === selectedValue) {
                        option.selected = true;
                    } else if (usedUnits.has(unit)) {
                        option.disabled = true;
                    }
                    fragment.appendChild(option);
                });

                select.innerHTML = '';
                select.appendChild(fragment);
            }

            function refreshSelects() {
                const selects = getSelects();
                const selections = getCurrentSelections();

                selects.forEach((select, index) => {
                    populateSelectOptions(select, selections[index], selections);
                });
            }

            function updateAddButtonState() {
                if (!addButton) {
                    return;
                }

                const rowCount = getRows().length;
                const limit = availableUnits.length;
                const shouldDisable = limit && rowCount >= limit;

                addButton.disabled = shouldDisable;
                addButton.style.opacity = shouldDisable ? '0.6' : '';
                addButton.style.cursor = shouldDisable ? 'not-allowed' : 'pointer';
            }

            function ensureDefaultSelection() {
                const rows = getRows();
                const checkedRow = rows.find((row) => {
                    const radio = row.querySelector('input[type="radio"]');
                    return radio && radio.checked;
                });

                if (!checkedRow && rows[0]) {
                    const firstRadio = rows[0].querySelector('input[type="radio"]');
                    if (firstRadio) {
                        firstRadio.checked = true;
                    }
                }
            }

            function reindexRows() {
                const rows = getRows();

                rows.forEach((row, index) => {
                    const nameField = row.querySelector('[data-field="name"]');
                    const priceInput = row.querySelector('input[data-field="price"]');
                    const radioInput = row.querySelector('input[data-field="default"]');

                    if (nameField) {
                        nameField.name = `units[${index}][name]`;
                    }

                    if (priceInput) {
                        priceInput.name = `units[${index}][price]`;
                    }

                    if (radioInput) {
                        radioInput.value = index;
                    }
                });

                ensureDefaultSelection();
                refreshSelects();
                updateAddButtonState();
            }

            function createRow() {
                const row = document.createElement('tr');
                row.classList.add('unit-row');
                row.innerHTML = `
                    <td>
                        <select class="form-select" required data-field="name"></select>
                    </td>
                    <td>
                        <div class="currency-input">
                            <span class="currency-prefix">Rp</span>
                            <input
                                type="number"
                                min="0"
                                step="1"
                                required
                                data-field="price"
                            >
                        </div>
                    </td>
                    <td>
                        <label class="unit-default-radio">
                            <input
                                type="radio"
                                name="default_unit"
                                data-field="default"
                            >
                            <span>Default</span>
                        </label>
                    </td>
                    <td>
                        <div class="unit-actions">
                            <button type="button" class="unit-remove-button" data-action="remove-unit">
                                Hapus
                            </button>
                        </div>
                    </td>
                `;
                return row;
            }

            addButton?.addEventListener('click', function () {
                if (!tableBody) {
                    return;
                }

                if (availableUnits.length && getRows().length >= availableUnits.length) {
                    return;
                }

                const newRow = createRow();
                tableBody.appendChild(newRow);

                const newSelect = newRow.querySelector('select[data-field="name"]');
                const currentSelections = getCurrentSelections();
                const firstUnused = availableUnits.find((unit) => !currentSelections.includes(unit));

                if (newSelect && firstUnused) {
                    newSelect.value = firstUnused;
                }

                reindexRows();
            });

            tableBody?.addEventListener('change', function (event) {
                if (event.target.matches('select[data-field="name"]')) {
                    reindexRows();
                }
            });

            tableBody?.addEventListener('click', function (event) {
                const button = event.target.closest('[data-action="remove-unit"]');
                if (!button) {
                    return;
                }

                const rows = getRows();
                if (rows.length <= 1) {
                    const nameField = rows[0].querySelector('[data-field="name"]');
                    const priceInput = rows[0].querySelector('input[data-field="price"]');
                    if (nameField) {
                        nameField.value = '';
                    }
                    if (priceInput) {
                        priceInput.value = '';
                    }
                    reindexRows();
                    return;
                }

                button.closest('.unit-row')?.remove();
                reindexRows();
            });

            reindexRows();
        });
    </script>
@endpush









