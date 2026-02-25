<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 5;

    public function sales(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:50'],
        ]);

        $start = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->startOfMonth();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $baseQuery = Transaction::query()
            ->whereBetween('transaction_date', [$start, $end]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $baseQuery->where('code', 'like', '%' . $search . '%');
        }

        $totalAmount = (float) (clone $baseQuery)->sum('total_amount');
        $transactionCount = (int) (clone $baseQuery)->count();
        $avgOrder = $transactionCount > 0 ? $totalAmount / $transactionCount : 0;

        $transactions = $baseQuery
            ->with('user')
            ->withCount('details')
            ->orderByDesc('transaction_date')
            ->paginate(20)
            ->withQueryString();

        $paymentLabels = [
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
            'debit' => 'Debit',
        ];

        return view('admin.reports.sales', [
            'transactions' => $transactions,
            'summary' => [
                'totalAmount' => $totalAmount,
                'transactionCount' => $transactionCount,
                'avgOrder' => $avgOrder,
            ],
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'search' => $request->input('search'),
            ],
            'rangeLabel' => $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y'),
            'paymentLabels' => $paymentLabels,
        ]);
    }

    public function stock(Request $request)
    {
        $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['nullable', 'in:all,active,inactive'],
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

        $lowStockCount = $productsForTotals
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->count();
        $outOfStockCount = $productsForTotals
            ->where('stock', '<=', 0)
            ->count();
        $lowStockOnlyCount = $productsForTotals
            ->where('stock', '>', 0)
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->count();
        $okStockCount = $productsForTotals
            ->where('stock', '>', self::LOW_STOCK_THRESHOLD)
            ->count();

        $categoryStats = $productsForTotals
            ->groupBy(fn ($product) => optional($product->category)->name ?? 'Tanpa Kategori')
            ->map(function ($group, $name) {
                return [
                    'name' => $name,
                    'stock' => $group->sum('stock'),
                    'value' => $group->sum(fn ($product) => $product->stock * $product->price),
                    'items' => $group->count(),
                ];
            })
            ->sortByDesc('stock')
            ->values();

        $topCategories = $categoryStats->take(6);
        $lowStockThreshold = self::LOW_STOCK_THRESHOLD;

        return view('admin.reports.stock', compact(
            'products',
            'categories',
            'totalValue',
            'totalStock',
            'pageItems',
            'pageStock',
            'pageValue',
            'pageActive',
            'pageInactive',
            'lowStockCount',
            'outOfStockCount',
            'lowStockOnlyCount',
            'okStockCount',
            'topCategories',
            'lowStockThreshold'
        ));
    }

    public function stockCorrelation(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['nullable', 'in:all,active,inactive'],
        ]);

        $start = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->subDays(30)->startOfDay();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

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

        $productIds = $productsForTotals->pluck('id')->all();
        $purchaseRows = collect();

        if (! empty($productIds)) {
            $purchaseRows = TransactionDetail::select(
                    'transaction_details.product_id',
                    DB::raw('SUM(transaction_details.quantity) as total_qty'),
                    DB::raw('MAX(transactions.transaction_date) as last_purchase')
                )
                ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->whereBetween('transactions.transaction_date', [$start, $end])
                ->whereIn('transaction_details.product_id', $productIds)
                ->groupBy('transaction_details.product_id')
                ->get()
                ->keyBy('product_id');
        }

        $products->setCollection(
            $products->getCollection()->map(function ($product) use ($purchaseRows) {
                $row = $purchaseRows->get($product->id);
                $product->purchase_qty = (int) ($row->total_qty ?? 0);
                $product->last_purchase = $row->last_purchase ?? null;
                $denominator = $product->purchase_qty + $product->stock;
                $product->purchase_ratio = $denominator > 0
                    ? round(($product->purchase_qty / $denominator) * 100, 1)
                    : 0;

                return $product;
            })
        );

        $totalPurchased = (int) $purchaseRows->sum('total_qty');
        $totalStock = (int) $productsForTotals->sum('stock');
        $productCount = $productsForTotals->count();

        $restockCandidates = $productsForTotals->filter(function ($product) use ($purchaseRows) {
            $row = $purchaseRows->get($product->id);
            $purchaseQty = (int) ($row->total_qty ?? 0);

            return $product->stock <= self::LOW_STOCK_THRESHOLD
                || ($purchaseQty > 0 && $purchaseQty >= $product->stock);
        })->count();

        return view('admin.reports.stock_correlation', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'category_id' => $request->input('category_id'),
                'status' => $request->input('status', 'all'),
            ],
            'summary' => [
                'rangeLabel' => $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y'),
                'totalPurchased' => $totalPurchased,
                'totalStock' => $totalStock,
                'productCount' => $productCount,
                'restockCandidates' => $restockCandidates,
            ],
            'lowStockThreshold' => self::LOW_STOCK_THRESHOLD,
        ]);
    }
}
