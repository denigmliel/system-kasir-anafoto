<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
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
            'sort' => ['nullable', 'in:priority,purchase_desc,purchase_asc,ratio_desc,ratio_asc,gap_desc,gap_asc,stock_desc,stock_asc,last_purchase_desc,last_purchase_asc'],
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

        $query = Product::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $categories = Category::orderBy('name')->get();

        $purchaseAgg = TransactionDetail::select(
                'transaction_details.product_id',
                DB::raw('SUM(transaction_details.quantity) as purchase_qty'),
                DB::raw('MAX(transactions.transaction_date) as last_purchase')
            )
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereBetween('transactions.transaction_date', [$start, $end])
            ->groupBy('transaction_details.product_id');

        $applyPurchaseJoin = function ($builder) use ($purchaseAgg) {
            return $builder->leftJoinSub($purchaseAgg, 'purchase_agg', function ($join) {
                $join->on('products.id', '=', 'purchase_agg.product_id');
            });
        };

        $totalsQuery = $applyPurchaseJoin((clone $query))
            ->select(
                'products.id',
                'products.stock',
                DB::raw('COALESCE(purchase_agg.purchase_qty, 0) as purchase_qty')
            );

        $productsForTotals = $totalsQuery->get();

        $sort = $request->input('sort', 'priority');
        $sort = in_array($sort, [
            'priority',
            'purchase_desc',
            'purchase_asc',
            'ratio_desc',
            'ratio_asc',
            'gap_desc',
            'gap_asc',
            'stock_desc',
            'stock_asc',
            'last_purchase_desc',
            'last_purchase_asc',
        ], true) ? $sort : 'priority';

        $productsQuery = $applyPurchaseJoin((clone $query))
            ->select(
                'products.*',
                DB::raw('COALESCE(purchase_agg.purchase_qty, 0) as purchase_qty'),
                DB::raw('purchase_agg.last_purchase as last_purchase')
            );

        $this->applyStockCorrelationSorting($productsQuery, $sort);

        $products = $productsQuery->paginate(25)->withQueryString();

        $products->setCollection(
            $products->getCollection()->map(function ($product) {
                $product->purchase_qty = (int) ($product->purchase_qty ?? 0);
                $denominator = $product->purchase_qty + (int) $product->stock;
                $product->purchase_ratio = $denominator > 0
                    ? round(($product->purchase_qty / $denominator) * 100, 1)
                    : 0;

                return $product;
            })
        );

        $totalPurchased = (int) $productsForTotals->sum('purchase_qty');
        $totalStock = (int) $productsForTotals->sum('stock');
        $productCount = $productsForTotals->count();

        $restockCandidates = $productsForTotals->filter(function ($product) {
            $purchaseQty = (int) ($product->purchase_qty ?? 0);

            return $product->stock <= self::LOW_STOCK_THRESHOLD
                || ($purchaseQty > 0 && $purchaseQty >= $product->stock);
        })->count();

        $chartPayload = $this->buildStockCorrelationChart(
            $start,
            $end,
            $totalStock,
            $productsForTotals->pluck('id')->all()
        );

        return view('admin.reports.stock_correlation', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'category_id' => $request->input('category_id'),
                'status' => $request->input('status', 'all'),
                'sort' => $sort,
            ],
            'summary' => [
                'rangeLabel' => $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y'),
                'totalPurchased' => $totalPurchased,
                'totalStock' => $totalStock,
                'productCount' => $productCount,
                'restockCandidates' => $restockCandidates,
            ],
            'lowStockThreshold' => self::LOW_STOCK_THRESHOLD,
            'chart' => $chartPayload,
        ]);
    }

    private function applyStockCorrelationSorting($productsQuery, string $sort): void
    {
        switch ($sort) {
            case 'purchase_desc':
                $productsQuery->orderByDesc('purchase_qty')->orderBy('products.stock');
                break;
            case 'purchase_asc':
                $productsQuery->orderBy('purchase_qty')->orderBy('products.stock');
                break;
            case 'ratio_desc':
                $productsQuery->orderByDesc(DB::raw(
                    'CASE WHEN (COALESCE(purchase_agg.purchase_qty, 0) + products.stock) = 0
                        THEN 0
                        ELSE COALESCE(purchase_agg.purchase_qty, 0) / (COALESCE(purchase_agg.purchase_qty, 0) + products.stock)
                    END'
                ));
                break;
            case 'ratio_asc':
                $productsQuery->orderBy(DB::raw(
                    'CASE WHEN (COALESCE(purchase_agg.purchase_qty, 0) + products.stock) = 0
                        THEN 0
                        ELSE COALESCE(purchase_agg.purchase_qty, 0) / (COALESCE(purchase_agg.purchase_qty, 0) + products.stock)
                    END'
                ));
                break;
            case 'gap_desc':
                $productsQuery->orderByDesc(DB::raw('COALESCE(purchase_agg.purchase_qty, 0) - products.stock'));
                break;
            case 'gap_asc':
                $productsQuery->orderBy(DB::raw('COALESCE(purchase_agg.purchase_qty, 0) - products.stock'));
                break;
            case 'stock_desc':
                $productsQuery->orderByDesc('products.stock');
                break;
            case 'stock_asc':
                $productsQuery->orderBy('products.stock');
                break;
            case 'last_purchase_desc':
                $productsQuery->orderByDesc('last_purchase');
                break;
            case 'last_purchase_asc':
                $productsQuery->orderBy('last_purchase');
                break;
            case 'priority':
            default:
                $productsQuery->orderByRaw(
                    'CASE
                        WHEN products.stock <= ? THEN 1
                        WHEN COALESCE(purchase_agg.purchase_qty, 0) >= products.stock AND COALESCE(purchase_agg.purchase_qty, 0) > 0 THEN 2
                        ELSE 3
                    END',
                    [self::LOW_STOCK_THRESHOLD]
                )->orderBy('products.stock');
                break;
        }
    }

    private function buildStockCorrelationChart(Carbon $start, Carbon $end, int $totalStock, array $productIds): array
    {
        $dailyPurchases = TransactionDetail::select(
                DB::raw('DATE(transactions.transaction_date) as purchase_date'),
                DB::raw('SUM(transaction_details.quantity) as total_qty')
            )
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereBetween('transactions.transaction_date', [$start, $end])
            ->when(! empty($productIds), function ($query) use ($productIds) {
                $query->whereIn('transaction_details.product_id', $productIds);
            })
            ->groupBy(DB::raw('DATE(transactions.transaction_date)'))
            ->orderBy('purchase_date')
            ->get()
            ->keyBy('purchase_date');

        $labels = [];
        $ratios = [];
        $purchases = [];

        $period = CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay());
        foreach ($period as $date) {
            $dateKey = $date->toDateString();
            $purchaseQty = (int) ($dailyPurchases->get($dateKey)->total_qty ?? 0);
            $denominator = $purchaseQty + $totalStock;
            $ratio = $denominator > 0 ? round(($purchaseQty / $denominator) * 100, 1) : 0;

            $labels[] = $date->format('d M');
            $ratios[] = $ratio;
            $purchases[] = $purchaseQty;
        }

        return [
            'labels' => $labels,
            'ratios' => $ratios,
            'purchases' => $purchases,
            'totalStock' => $totalStock,
        ];
    }
}
