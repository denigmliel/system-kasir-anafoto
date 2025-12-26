<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GudangController extends Controller
{
    private const ALLOWED_UNIT_NAMES = ['PCS', 'PACK', 'BOX', 'RIM', 'LEMBAR', 'LUSIN'];
    private const LOW_STOCK_THRESHOLD = 3;

    public function dashboard()
    {
        $startOfDay = now()->startOfDay();

        $stats = [
            'productCount' => Product::count(),
            'activeProductCount' => Product::where('is_active', true)->count(),
            'inactiveProductCount' => Product::where('is_active', false)->count(),
            'lowStockCount' => Product::where('stock', '<=', self::LOW_STOCK_THRESHOLD)->count(),
            'categoryCount' => Category::count(),
            'movementToday' => StockMovement::where('created_at', '>=', $startOfDay)->count(),
            'stockInToday' => StockMovement::where('type', 'in')
                ->where('created_at', '>=', $startOfDay)
                ->sum('quantity'),
            'stockOutToday' => StockMovement::where('type', 'out')
                ->where('created_at', '>=', $startOfDay)
                ->sum('quantity'),
        ];

        $recentMovements = StockMovement::with(['product', 'user'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        $recentlyUpdatedProducts = Product::orderByDesc('updated_at')
            ->limit(5)
            ->get(['id', 'name', 'stock', 'unit', 'updated_at']);

        $topLowStocks = Product::where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        return view('gudang.dashboard', [
            'stats' => $stats,
            'recentMovements' => $recentMovements,
            'recentlyUpdatedProducts' => $recentlyUpdatedProducts,
            'topLowStocks' => $topLowStocks,
            'lowStockThreshold' => self::LOW_STOCK_THRESHOLD,
        ]);
    }

    public function products(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        $query = Product::with('category')->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $normalized = ltrim($search, '#');

            $query->where(function ($inner) use ($search, $normalized) {
                $inner->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');

                if ($normalized !== '' && ctype_digit($normalized)) {
                    $inner->orWhere('id', (int) $normalized);
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('gudang.products.index', compact('products', 'categories'));
    }

    public function productsLowStock(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'threshold' => ['nullable', 'integer', 'min:0'],
        ]);

        $threshold = $request->filled('threshold')
            ? max(0, (int) $request->input('threshold'))
            : self::LOW_STOCK_THRESHOLD;

        $query = Product::with('category')
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $normalized = ltrim($search, '#');

            $query->where(function ($inner) use ($search, $normalized) {
                $inner->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');

                if ($normalized !== '' && ctype_digit($normalized)) {
                    $inner->orWhere('id', (int) $normalized);
                }
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('gudang.products.low_stock', [
            'products' => $products,
            'categories' => $categories,
            'threshold' => $threshold,
            'defaultThreshold' => self::LOW_STOCK_THRESHOLD,
        ]);
    }

    public function productsCreate()
    {
        $categories = Category::orderBy('name')->get();
        $product = new Product([
            'stock' => 0,
            'is_active' => true,
            'is_stock_unlimited' => false,
        ]);
        $product->setRelation('units', collect());

        return view('gudang.products.form', [
            'product' => $product,
            'categories' => $categories,
            'allowedUnits' => self::ALLOWED_UNIT_NAMES,
            'mode' => 'create',
        ]);
    }

    public function productsStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'new_category' => ['nullable', 'string', 'max:100'],
            'stock' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (! $this->isValidStockInput($value)) {
                        $fail('Stok harus berupa angka.');
                    }
                },
            ],
            'is_active' => ['nullable', 'boolean'],
            'units' => ['required', 'array', 'min:1'],
            'units.*.name' => ['required', 'string', 'max:50', Rule::in(self::ALLOWED_UNIT_NAMES)],
            'units.*.price' => ['required', 'numeric', 'min:0'],
            'default_unit' => ['nullable', 'integer', 'min:0'],
        ]);

        [$stockValue] = $this->normalizeStockInput($data['stock']);
        $categoryId = $this->resolveCategoryId($data['category_id'] ?? null, $data['new_category'] ?? null);

        $units = collect($data['units'])
            ->map(function (array $unit) {
                return [
                    'name' => strtoupper(trim($unit['name'])),
                    'price' => (float) $unit['price'],
                ];
            })
            ->filter(fn ($unit) => $unit['name'] !== '')
            ->values();

        if ($units->isEmpty()) {
            throw ValidationException::withMessages([
                'units' => 'Minimal satu satuan beserta harga harus diisi.',
            ]);
        }

        if ($units->duplicates('name')->isNotEmpty()) {
            throw ValidationException::withMessages([
                'units' => 'Nama satuan tidak boleh duplikat.',
            ]);
        }

        $defaultIndex = (int) ($data['default_unit'] ?? 0);
        if (! isset($units[$defaultIndex])) {
            $defaultIndex = 0;
        }
        $defaultUnit = $units[$defaultIndex];

        $product = DB::transaction(function () use ($request, $data, $units, $defaultUnit, $defaultIndex, $stockValue, $categoryId) {
            $productAttributes = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'category_id' => $categoryId,
                'unit' => $defaultUnit['name'],
                'price' => $defaultUnit['price'],
                'stock' => $stockValue,
                'is_stock_unlimited' => false,
                'is_active' => $request->boolean('is_active', true),
            ];

            if (Schema::hasColumn('products', 'code')) {
                $productAttributes['code'] = $this->generateProductCode();
            }

            $product = Product::create($productAttributes);

            $product->units()->createMany(
                $units->map(function (array $unit, int $index) use ($defaultIndex) {
                    return [
                        'name' => $unit['name'],
                        'price' => $unit['price'],
                        'is_default' => $index === $defaultIndex,
                    ];
                })->all()
            );

            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'adjustment',
                    'quantity' => $product->stock,
                    'created_at' => now(),
                    'reference_type' => 'initial',
                    'reference_id' => $product->id,
                    'notes' => 'Stok awal produk',
                ]);
            }

            return $product;
        });

        return redirect()
            ->route('gudang.products.show', $product)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function productsShow(Product $product)
    {
        $product->load(['category', 'units']);
        $recentMovements = $product->stockMovements()
            ->with('user')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('gudang.products.show', compact('product', 'recentMovements'));
    }

    public function productsQr(Product $product)
    {
        $product->load('category');

        $payload = $this->buildProductQrPayload($product);
        $qrSvg = QrCode::format('svg')
            ->size(360)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($payload);

        $labelCode = $product->code
            ? $product->code
            : 'ID-' . str_pad((string) $product->id, 4, '0', STR_PAD_LEFT);

        return view('gudang.products.qr', [
            'product' => $product,
            'labelCode' => $labelCode,
            'qrSvg' => $qrSvg,
            'qrPayload' => $payload,
        ]);
    }

    public function productsEdit(Request $request, Product $product)
    {
        $product->load('units');
        $categories = Category::orderBy('name')->get();
        $redirectTo = $this->normalizeRedirectUrl($request->input('redirect_to'));

        return view('gudang.products.form', [
            'product' => $product,
            'categories' => $categories,
            'allowedUnits' => self::ALLOWED_UNIT_NAMES,
            'mode' => 'edit',
            'redirectTo' => $redirectTo,
        ]);
    }

    public function productsUpdate(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'new_category' => ['nullable', 'string', 'max:100'],
            'stock' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (! $this->isValidStockInput($value)) {
                        $fail('Stok harus berupa angka.');
                    }
                },
            ],
            'is_active' => ['nullable', 'boolean'],
            'units' => ['required', 'array', 'min:1'],
            'units.*.name' => ['required', 'string', 'max:50', Rule::in(self::ALLOWED_UNIT_NAMES)],
            'units.*.price' => ['required', 'numeric', 'min:0'],
            'default_unit' => ['nullable', 'integer', 'min:0'],
        ]);

        [$stockValue] = $this->normalizeStockInput($data['stock']);
        $redirectTo = $this->normalizeRedirectUrl($request->input('redirect_to'));

        $units = collect($data['units'])
            ->map(function (array $unit) {
                return [
                    'name' => strtoupper(trim($unit['name'])),
                    'price' => (float) $unit['price'],
                ];
            })
            ->filter(fn ($unit) => $unit['name'] !== '')
            ->values();

        if ($units->isEmpty()) {
            throw ValidationException::withMessages([
                'units' => 'Minimal satu satuan beserta harga harus diisi.',
            ]);
        }

        if ($units->duplicates('name')->isNotEmpty()) {
            throw ValidationException::withMessages([
                'units' => 'Nama satuan tidak boleh duplikat.',
            ]);
        }

        $defaultIndex = (int) ($data['default_unit'] ?? 0);
        if (! isset($units[$defaultIndex])) {
            $defaultIndex = 0;
        }
        $defaultUnit = $units[$defaultIndex];

        $originalStock = $product->stock;
        $wasUnlimited = $product->is_stock_unlimited;
        $categoryId = $this->resolveCategoryId($data['category_id'] ?? null, $data['new_category'] ?? null);

        DB::transaction(function () use ($product, $request, $data, $units, $defaultUnit, $defaultIndex, $stockValue, $categoryId) {
            $updatePayload = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'category_id' => $categoryId ?? null,
                'unit' => $defaultUnit['name'],
                'price' => $defaultUnit['price'],
                'stock' => $stockValue,
                'is_stock_unlimited' => false,
                'is_active' => $request->boolean('is_active', true),
            ];

            $product->update($updatePayload);

            $product->units()->delete();
            $product->units()->createMany(
                $units->map(function (array $unit, int $index) use ($defaultIndex) {
                    return [
                        'name' => $unit['name'],
                        'price' => $unit['price'],
                        'is_default' => $index === $defaultIndex,
                    ];
                })->all()
            );
        });

        if (! $wasUnlimited) {
            $difference = $product->stock - $originalStock;

            if ($difference !== 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'adjustment',
                    'quantity' => abs($difference),
                    'created_at' => now(),
                    'reference_type' => 'edit_product',
                    'reference_id' => $product->id,
                    'notes' => $difference > 0
                        ? 'Penyesuaian stok (kenaikan) saat edit produk'
                        : 'Penyesuaian stok (penurunan) saat edit produk',
                ]);
            }
        } elseif ($wasUnlimited && $product->stock > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity' => $product->stock,
                'created_at' => now(),
                'reference_type' => 'edit_product',
                'reference_id' => $product->id,
                'notes' => 'Mengaktifkan stok terhitung saat edit produk',
            ]);
        }

        $target = $redirectTo ?: route('gudang.products.show', $product);

        return redirect()
            ->to($target)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function productsDestroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $product->delete();
        });

        return redirect()
            ->route('gudang.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function resolveCategoryId(?string $categoryId, ?string $newCategory): ?int
    {
        $name = trim((string) $newCategory);

        if ($name !== '') {
            $existing = Category::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();

            if ($existing) {
                return $existing->id;
            }

            return Category::create(['name' => $name])->id;
        }

        return $categoryId !== null && $categoryId !== ''
            ? (int) $categoryId
            : null;
    }

    private function normalizeRedirectUrl(?string $redirect): ?string
    {
        if (! $redirect) {
            return null;
        }

        if (Str::startsWith($redirect, ['/', url('/')])) {
            return $redirect;
        }

        return null;
    }

    private function isValidStockInput($value): bool
    {
        return is_string($value) && $value !== '' && ctype_digit(trim($value));
    }

    /**
     * @return array{0:int}
     */
    private function normalizeStockInput(string $value): array
    {
        return [(int) trim($value)];
    }

    public function stockMovements(Request $request)
    {
        $request->validate([
            'type' => ['nullable', Rule::in(['in', 'out', 'adjustment'])],
            'product_id' => ['nullable', 'exists:products,id'],
        ]);

        $query = StockMovement::with(['product', 'user'])
            ->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        $movements = $query->paginate(25)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('gudang.stock.movements', compact('movements', 'products'));
    }

    public function stockAdjustment(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'direction' => ['required', Rule::in(['increase', 'decrease'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::where('id', $data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($product->is_stock_unlimited) {
                $product->update(['is_stock_unlimited' => false]);
            }

            if ($data['direction'] === 'decrease' && $product->stock < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok produk tidak mencukupi untuk dikurangi.',
                ]);
            }

            if ($data['direction'] === 'increase') {
                $product->increment('stock', $data['quantity']);
            } else {
                $product->decrement('stock', $data['quantity']);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity' => $data['quantity'],
                'created_at' => now(),
                'reference_type' => 'manual_adjustment',
                'reference_id' => $product->id,
                'notes' => $data['notes']
                    ? 'Penyesuaian stok: ' . $data['direction'] . ' (' . $data['notes'] . ')'
                    : 'Penyesuaian stok: ' . $data['direction'],
            ]);
        });

        return redirect()
            ->route('gudang.stock.movements')
            ->with('success', 'Penyesuaian stok berhasil dilakukan.');
    }

    public function stockReport(Request $request)
    {
        $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['nullable', Rule::in(['all', 'active', 'inactive'])],
        ]);

        $query = Product::with('category')->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $productsForTotals = (clone $query)->get();
        $products = $query->paginate(25)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $totalValue = $productsForTotals->sum(fn ($product) => $product->stock * $product->price);
        $totalStock = $productsForTotals->sum('stock');

        $pageProducts = $products->getCollection();
        $pageItems = $pageProducts->count();
        $pageStock = $pageProducts->sum('stock');
        $pageValue = $pageProducts->sum(fn ($product) => $product->stock * $product->price);
        $pageActive = $pageProducts->where('is_active', true)->count();
        $pageInactive = $pageProducts->where('is_active', false)->count();

        return view('gudang.reports.stock', compact(
            'products',
            'categories',
            'totalValue',
            'totalStock',
            'pageItems',
            'pageStock',
            'pageValue',
            'pageActive',
            'pageInactive'
        ));
    }

    private function buildProductQrPayload(Product $product): string
    {
        $payload = [
            'type' => 'product',
            'id' => $product->id,
            'code' => $product->code ?? null,
            'name' => $product->name,
            'unit' => $product->unit,
            'price' => (float) $product->price,
        ];

        if ($product->category) {
            $payload['category'] = $product->category->name;
        }

        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function generateProductCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        $randomSegment = function (int $length) use ($alphabet): string {
            $characters = [];
            $maxIndex = strlen($alphabet) - 1;

            for ($i = 0; $i < $length; $i++) {
                $characters[] = $alphabet[random_int(0, $maxIndex)];
            }

            return implode('', $characters);
        };

        do {
            $code = $randomSegment(3) . '-' . $randomSegment(4);
        } while (Product::where('code', $code)->exists());

        return $code;
    }
}
