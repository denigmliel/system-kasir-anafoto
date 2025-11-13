<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KasirController extends Controller
{
    private const UNIT_STOCK_MULTIPLIERS = [
        'PACK' => 10,
        'BOX' => 6,
        'LUSIN' => 12,
    ];

    public function dashboard()
    {
        $user = Auth::user();

        $todayTransactions = Transaction::with(['details'])
            ->where('user_id', $user->id)
            ->whereDate('transaction_date', today())
            ->orderByDesc('transaction_date')
            ->get();

        $recentTransactions = Transaction::withCount('details')
            ->where('user_id', $user->id)
            ->latest('transaction_date')
            ->limit(7)
            ->get();

        $topProducts = TransactionDetail::select('product_name', DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('transaction', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereDate('transaction_date', today());
            })
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $monthlySales = Transaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date')
            ->get();

        $todaySalesTotal = $todayTransactions->sum('total_amount');
        $todayItemsSold = $todayTransactions
            ->flatMap(fn ($transaction) => $transaction->details)
            ->sum('quantity');

        return view('kasir.dashboard', [
            'todayDateLabel' => Carbon::now()->translatedFormat('d M Y'),
            'todaySalesTotal' => $todaySalesTotal,
            'todayTransactions' => $todayTransactions,
            'todayTransactionCount' => $todayTransactions->count(),
            'todayItemsSold' => $todayItemsSold,
            'recentTransactions' => $recentTransactions,
            'topProducts' => $topProducts,
            'monthlySales' => $monthlySales,
        ]);
    }

    public function pos()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with(['units', 'category'])
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        $paymentMethods = [
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
        ];

        return view('kasir.pos', compact('products', 'paymentMethods', 'categories'));
    }

    public function createTransaction(Request $request)
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(['cash', 'transfer', 'qris'])],
            'payment_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_unit_id' => ['required', 'exists:product_units,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Tambahkan minimal satu produk ke dalam transaksi.',
            'items.*.product_unit_id.exists' => 'Satuan produk yang dipilih tidak ditemukan.',
        ]);

        $user = Auth::user();
        $items = collect($data['items'])->map(function ($item) {
            return [
                'product_unit_id' => (int) $item['product_unit_id'],
                'quantity' => (int) $item['quantity'],
            ];
        });

        $transaction = DB::transaction(function () use ($items, $user, $data) {
            $unitIds = $items->pluck('product_unit_id')->all();

            /** @var \Illuminate\Support\Collection<int,\App\Models\ProductUnit> $productUnits */
            $productUnits = ProductUnit::whereIn('id', $unitIds)
                ->with('product')
                ->get()
                ->keyBy('id');

            $productIds = $productUnits->pluck('product_id')->unique()->values()->all();

            /** @var \Illuminate\Support\Collection<int,\App\Models\Product> $products */
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $grossSubtotal = 0.0;
            $detailsPayload = [];

            $reservedStock = [];

            foreach ($items as $index => $item) {
                /** @var \App\Models\ProductUnit|null $unit */
                $unit = $productUnits->get($item['product_unit_id']);

                if (! $unit) {
                    throw ValidationException::withMessages([
                        "items.{$index}.product_unit_id" => 'Satuan produk tidak ditemukan.',
                    ]);
                }

                /** @var \App\Models\Product|null $product */
                $product = $products->get($unit->product_id);

                if (! $product) {
                    throw ValidationException::withMessages([
                        "items.{$index}.product_unit_id" => 'Produk tidak ditemukan.',
                    ]);
                }

                if (! $product->is_active) {
                    throw ValidationException::withMessages([
                        "items.{$index}.product_unit_id" => 'Produk tidak aktif.',
                    ]);
                }

                $stockDeduction = $this->calculateStockDeduction($product, $unit, $item['quantity']);

                if (! $product->is_stock_unlimited) {
                    $reservedTotal = ($reservedStock[$product->id] ?? 0) + $stockDeduction;

                    if ($reservedTotal > $product->stock) {
                        throw ValidationException::withMessages([
                            "items.{$index}.quantity" => "Stok {$product->name} tidak mencukupi.",
                        ]);
                    }

                    $reservedStock[$product->id] = $reservedTotal;
                }

                $price = (float) $unit->price;
                $lineSubtotal = max($price * $item['quantity'], 0);

                $grossSubtotal += $lineSubtotal;

                $detailsPayload[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit' => $unit->name,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $lineSubtotal,
                    'stock_deduction' => $stockDeduction,
                ];
            }

            $totalAmount = max($grossSubtotal, 0);

            if ($data['payment_amount'] < $totalAmount) {
                throw ValidationException::withMessages([
                    'payment_amount' => 'Nominal pembayaran kurang dari total belanja.',
                ]);
            }

            $transaction = null;
            $maxAttempts = 5;

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                try {
                    $transaction = Transaction::create([
                        'code' => Transaction::generateCode(),
                        'user_id' => $user->id,
                        'transaction_date' => now(),
                        'subtotal' => $grossSubtotal,
                        'total_amount' => $totalAmount,
                        'payment_method' => $data['payment_method'],
                        'payment_amount' => $data['payment_amount'],
                        'change_amount' => $data['payment_amount'] - $totalAmount,
                    ]);

                    break;
                } catch (QueryException $exception) {
                    $isDuplicateCode = (int) $exception->getCode() === 23000;

                    if (! $isDuplicateCode || $attempt === $maxAttempts) {
                        throw $exception;
                    }
                }
            }

            foreach ($detailsPayload as $detail) {
                $stockDeduction = $detail['stock_deduction'] ?? 0;
                $detailData = $detail;
                unset($detailData['stock_deduction']);

                $transaction->details()->create($detailData);

                $product = $products->get($detailData['product_id']);

                if (! $product->is_stock_unlimited && $stockDeduction > 0) {
                    $product->decrement('stock', $stockDeduction);
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => 'out',
                    'quantity' => $stockDeduction > 0 ? $stockDeduction : $detailData['quantity'],
                    'reference_type' => 'transaction',
                    'reference_id' => $transaction->id,
                    'notes' => 'Penjualan #' . $transaction->code,
                ]);
            }

            return $transaction;
        });

        return redirect()
            ->route('kasir.pos')
            ->with('success', 'Transaksi berhasil disimpan.')
            ->with('print_transaction_id', $transaction->id);
    }

    protected function calculateStockDeduction(Product $product, ProductUnit $unit, int $quantity): int
    {
        if ($product->is_stock_unlimited) {
            return 0;
        }

        $multiplier = self::UNIT_STOCK_MULTIPLIERS[strtoupper((string) $unit->name)] ?? 1;
        $multiplier = max(1, (int) $multiplier);

        return max(0, $quantity) * $multiplier;
    }

    public function transactionHistory(Request $request)
    {
        $user = Auth::user();

        $transactions = Transaction::withCount('details')
            ->where('user_id', $user->id)
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('transaction_date', $request->input('date'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $q = $request->input('search');
                $query->where('code', 'like', '%' . $q . '%');
            })
            ->orderByDesc('transaction_date')
            ->paginate(15)
            ->withQueryString();

        return view('kasir.transactions.history', [
            'transactions' => $transactions,
        ]);
    }

    public function printReceipt($transactionId)
    {
        $transaction = Transaction::with(['details', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($transactionId);

        $storeName = Setting::get('store_name', config('app.name', 'ANA FOTOCOPY'));
        $storeAddress = Setting::get('store_address', 'Jl. Benda Raya, Maruga Rt.005/04 Kel. Serua, Kec. Ciputat, Tangerang Selatan');
        if (trim((string) $storeAddress) === '' || $storeAddress === 'Jl. Contoh No. 123, Depok') {
            $storeAddress = 'Jl. Benda Raya, Maruga Rt.005/04 Kel. Serua, Kec. Ciputat, Tangerang Selatan';
        }
        $storePhone = Setting::get('store_phone', '0823-1094-6322');
        if (trim((string) $storePhone) === '' || $storePhone === '021-12345678') {
            $storePhone = '0823-1094-6322';
        }
        $receiptFooter = Setting::get('receipt_footer', 'Terima kasih sudah berbelanja!');

        return view('receipts.thermal', compact(
            'transaction',
            'storeName',
            'storeAddress',
            'storePhone',
            'receiptFooter'
        ));
    }

}
