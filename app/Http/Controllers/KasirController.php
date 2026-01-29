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

    public function dashboard()
    {
        $user = Auth::user();
        $now = now();
        $todayDate = $now->copy()->startOfDay();

        $todayTransactions = Transaction::with(['details'])
            ->where('user_id', $user->id)
            ->whereDate('transaction_date', $todayDate)
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
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date')
            ->get();

        $monthlySalesLabels = $monthlySales
            ->map(fn ($summary) => Carbon::parse($summary->date)->translatedFormat('d M'))
            ->values();

        $monthlySalesDates = $monthlySales
            ->map(fn ($summary) => Carbon::parse($summary->date)->toDateString())
            ->values();

        $monthlySalesTotals = $monthlySales
            ->map(fn ($summary) => (int) $summary->total)
            ->values();

        $monthlySalesTransactionCounts = $monthlySales
            ->map(fn ($summary) => (int) $summary->transaction_count)
            ->values();

        $todaySalesTotal = $todayTransactions->sum('total_amount');
        $todayItemsSold = $todayTransactions
            ->flatMap(fn ($transaction) => $transaction->details)
            ->sum('quantity');
        $todayTransactionCount = $todayTransactions->count();
        $todayAvgOrder = $todayTransactionCount > 0
            ? $todaySalesTotal / $todayTransactionCount
            : 0;

        $paymentLabels = [
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
        ];

        $paymentBreakdown = $todayTransactions
            ->groupBy('payment_method')
            ->map(function ($group, $method) use ($paymentLabels) {
                return [
                    'method' => $method,
                    'label' => $paymentLabels[$method] ?? ucfirst($method),
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount'),
                ];
            })
            ->values();

        $weekStart = $now->copy()->subDays(6)->startOfDay();
        $weekEnd = $now->copy()->endOfDay();

        $weeklyRows = Transaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [$weekStart, $weekEnd])
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $weeklySalesLabels = [];
        $weeklySalesTotals = [];
        $weeklySalesTransactionCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->format('Y-m-d');
            $label = Carbon::parse($date)->translatedFormat('d M');
            $totals = $weeklyRows->get($date);

            $weeklySalesLabels[] = $label;
            $weeklySalesTotals[] = (float) ($totals->total ?? 0);
            $weeklySalesTransactionCounts[] = (int) ($totals->transaction_count ?? 0);
        }

        $weeklyTotal = array_sum($weeklySalesTotals);

        $previousWeekStart = $now->copy()->subDays(13)->startOfDay();
        $previousWeekEnd = $now->copy()->subDays(7)->endOfDay();
        $previousWeekTotal = (float) Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_date', [$previousWeekStart, $previousWeekEnd])
            ->sum('total_amount');

        $weeklyChange = $weeklyTotal - $previousWeekTotal;
        $weeklyTrendDirection = $weeklyChange > 0 ? 'up' : ($weeklyChange < 0 ? 'down' : 'flat');
        $weeklyChangePct = $previousWeekTotal > 0
            ? ($weeklyChange / $previousWeekTotal) * 100
            : null;

        $topProductsWeek = TransactionDetail::select(
                'product_name',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as gross_total')
            )
            ->whereHas('transaction', function ($query) use ($user, $weekStart, $weekEnd) {
                $query->where('user_id', $user->id)
                    ->whereBetween('transaction_date', [$weekStart, $weekEnd]);
            })
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return view('kasir.dashboard', [
            'todayDateLabel' => $now->translatedFormat('d M Y'),
            'todaySalesTotal' => $todaySalesTotal,
            'todayTransactions' => $todayTransactions,
            'todayTransactionCount' => $todayTransactionCount,
            'todayItemsSold' => $todayItemsSold,
            'todayAvgOrder' => $todayAvgOrder,
            'paymentBreakdown' => $paymentBreakdown,
            'recentTransactions' => $recentTransactions,
            'topProducts' => $topProducts,
            'topProductsWeek' => $topProductsWeek,
            'monthlySales' => $monthlySales,
            'monthlySalesLabels' => $monthlySalesLabels,
            'monthlySalesDates' => $monthlySalesDates,
            'monthlySalesTotals' => $monthlySalesTotals,
            'monthlySalesTransactionCounts' => $monthlySalesTransactionCounts,
            'weeklySalesLabels' => $weeklySalesLabels,
            'weeklySalesTotals' => $weeklySalesTotals,
            'weeklySalesTransactionCounts' => $weeklySalesTransactionCounts,
            'weeklyTrend' => [
                'current_total' => $weeklyTotal,
                'previous_total' => $previousWeekTotal,
                'change' => $weeklyChange,
                'change_pct' => $weeklyChangePct,
                'trend' => $weeklyTrendDirection,
            ],
        ]);
    }

    public function pos(Request $request)
    {
        $user = Auth::user();

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

        $editingTransactionPayload = null;

        if ($request->filled('transaction')) {
            $editingTransaction = Transaction::with(['details.product.units'])
                ->where('user_id', $user->id)
                ->find($request->input('transaction'));

            if ($editingTransaction) {
                $editingTransactionPayload = $this->buildTransactionPrefillPayload($editingTransaction);
            }
        }

        return view('kasir.pos', [
            'products' => $products,
            'paymentMethods' => $paymentMethods,
            'categories' => $categories,
            'editingTransaction' => $editingTransactionPayload,
        ]);
    }

    protected function buildTransactionPrefillPayload(Transaction $transaction): array
    {
        $transaction->loadMissing(['details.product.units', 'details.product.category']);

        $items = $transaction->details
            ->map(function (TransactionDetail $detail) {
                $product = $detail->product;

                if (! $product) {
                    return null;
                }

                $matchedUnit = $product->units
                    ? $product->units->firstWhere('name', $detail->unit)
                    : null;

                if (! $matchedUnit && $product->units) {
                    $matchedUnit = $product->units->first();
                }

                return [
                    'product_id' => $product->id,
                    'product_unit_id' => optional($matchedUnit)->id,
                    'quantity' => (int) $detail->quantity,
                    'category_id' => $product->category_id,
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'id' => $transaction->id,
            'code' => $transaction->code,
            'items' => $items,
            'payment_method' => $transaction->payment_method,
            'payment_amount' => (int) $transaction->payment_amount,
        ];
    }

    public function createTransaction(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'payment_method' => ['required', Rule::in(['cash', 'transfer', 'qris'])],
            'payment_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_unit_id' => ['required', 'exists:product_units,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'transaction_id' => [
                'nullable',
                'integer',
                Rule::exists('transactions', 'id')->where(fn ($query) => $query->where('user_id', $user->id)),
            ],
        ], [
            'items.required' => 'Tambahkan minimal satu produk ke dalam transaksi.',
            'items.*.product_unit_id.exists' => 'Satuan produk yang dipilih tidak ditemukan.',
        ]);

        $items = collect($data['items'])->map(function ($item) {
            return [
                'product_unit_id' => (int) $item['product_unit_id'],
                'quantity' => (int) $item['quantity'],
            ];
        });

        $transactionId = isset($data['transaction_id']) ? (int) $data['transaction_id'] : null;

        $transaction = DB::transaction(function () use ($items, $user, $data, $transactionId) {
            $unitIds = $items->pluck('product_unit_id')->all();

            /** @var \Illuminate\Support\Collection<int,\App\Models\ProductUnit> $productUnits */
            $productUnits = ProductUnit::whereIn('id', $unitIds)
                ->with('product')
                ->get()
                ->keyBy('id');

            $productIds = $productUnits->pluck('product_id')->unique();
            $transactionToUpdate = null;

            if ($transactionId) {
                $transactionToUpdate = Transaction::with('details')
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->findOrFail($transactionId);

                $productIds = $productIds
                    ->merge($transactionToUpdate->details->pluck('product_id'))
                    ->unique();
            }
            $productIdList = $productIds->values()->all();

            /** @var \Illuminate\Support\Collection<int,\App\Models\Product> $products */
            $products = Product::whereIn('id', $productIdList)
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

                $reservedTotal = ($reservedStock[$product->id] ?? 0) + $stockDeduction;

                if ($reservedTotal > $product->stock) {
                    throw ValidationException::withMessages([
                        "items.{$index}.quantity" => "Stok {$product->name} tidak mencukupi.",
                    ]);
                }

                $reservedStock[$product->id] = $reservedTotal;

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

            if ($transactionToUpdate) {
                $movements = StockMovement::where('reference_type', 'transaction')
                    ->where('reference_id', $transactionToUpdate->id)
                    ->get();

                foreach ($movements as $movement) {
                    $product = $products->get($movement->product_id);

                    if ($product && $movement->type === 'out') {
                        $product->increment('stock', $movement->quantity);
                    }

                    $movement->delete();
                }

                $transactionToUpdate->details()->delete();

                $transactionToUpdate->update([
                    'subtotal' => $grossSubtotal,
                    'total_amount' => $totalAmount,
                    'payment_method' => $data['payment_method'],
                    'payment_amount' => $data['payment_amount'],
                    'change_amount' => $data['payment_amount'] - $totalAmount,
                ]);

                $transaction = $transactionToUpdate;
            } else {
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
            }

            foreach ($detailsPayload as $detail) {
                $stockDeduction = $detail['stock_deduction'] ?? 0;
                $detailData = $detail;
                unset($detailData['stock_deduction']);

                $transaction->details()->create($detailData);

                $product = $products->get($detailData['product_id']);

                if ($stockDeduction > 0) {
                    $product->decrement('stock', $stockDeduction);
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => 'out',
                    'quantity' => $stockDeduction > 0 ? $stockDeduction : $detailData['quantity'],
                    'created_at' => $transaction->transaction_date,
                    'reference_type' => 'transaction',
                    'reference_id' => $transaction->id,
                    'notes' => 'Penjualan #' . $transaction->code,
                ]);
            }

            return $transaction;
        });

        $message = $transactionId
            ? 'Transaksi berhasil diperbarui.'
            : 'Transaksi berhasil disimpan.';

        return redirect()
            ->route('kasir.pos')
            ->with('success', $message)
            ->with('print_transaction_id', $transaction->id);
    }

    protected function calculateStockDeduction(Product $product, ProductUnit $unit, int $quantity): int
    {
        return max(0, $quantity);
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
            ->paginate(20)
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
