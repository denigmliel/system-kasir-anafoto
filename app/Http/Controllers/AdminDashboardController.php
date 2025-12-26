<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Exports\TransactionDetailExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminDashboardController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 5;

    public function index(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $todayTransactions = Transaction::whereDate('transaction_date', $today)->get();
        $monthTransactions = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth]);

        $todaySalesTotal = $todayTransactions->sum('total_amount');
        $todayTransactionCount = $todayTransactions->count();
        $todayAvgOrder = $todayTransactionCount > 0
            ? $todaySalesTotal / $todayTransactionCount
            : 0;

        $monthSalesTotal = (clone $monthTransactions)->sum('total_amount');
        $monthTransactionCount = (clone $monthTransactions)->count();

        $recapRange = $this->resolveRecapRange($request->query('range'));
        $recapLength = $this->resolveRecapLength($recapRange, $request->query('length'));
        $transactionRecap = $this->buildTransactionRecap($recapRange, $recapLength);
        [$recapHeading, $recapBadge] = $this->recapMeta($recapRange, $recapLength);
        $recapOptionList = $this->recapOptions($recapRange, $recapLength);

        $metrics = [
            'todaySalesTotal' => $todaySalesTotal,
            'todayTransactionCount' => $todayTransactionCount,
            'todayAvgOrder' => $todayAvgOrder,
            'monthSalesTotal' => $monthSalesTotal,
            'monthTransactionCount' => $monthTransactionCount,
            'productCount' => Product::count(),
            'activeProductCount' => Product::where('is_active', true)->count(),
            'lowStockCount' => Product::where('stock', '<=', self::LOW_STOCK_THRESHOLD)->count(),
        ];

        $lowStockProducts = Product::where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(5)
            ->get();

        $recentTransactions = Transaction::with('user')
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        $topSellingProducts = $this->topProductsForCurrentMonth();
        $revenue7Days = $this->buildRecentRevenue(7);
        $revenue30Days = $this->buildRecentRevenue(30);
        $activeCashiers = $this->activeCashierSummaries();
        $closingShiftSummary = $this->closingShiftSummaries();
        $peakHours = $this->peakHoursSummary();

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'transactionRecap' => $transactionRecap,
            'recapRange' => $recapRange,
            'recapBadge' => $recapBadge,
            'recapHeading' => $recapHeading,
            'recapOptionList' => $recapOptionList,
            'recapLength' => $recapLength,
            'lowStockProducts' => $lowStockProducts,
            'recentTransactions' => $recentTransactions,
            'lowStockThreshold' => self::LOW_STOCK_THRESHOLD,
            'todayLabel' => $today->translatedFormat('d M Y'),
            'topSellingProducts' => $topSellingProducts,
            'revenue7Days' => $revenue7Days,
            'revenue30Days' => $revenue30Days,
            'activeCashiers' => $activeCashiers,
            'closingShiftSummary' => $closingShiftSummary,
            'peakHourLabels' => $peakHours['labels'],
            'peakHourCounts' => $peakHours['counts'],
            'peakHourTotals' => $peakHours['totals'],
            'peakHourHeadline' => $peakHours['headline'],
            'peakHoursRangeLabel' => $peakHours['range_label'],
        ]);
    }

    public function exportRecap(Request $request)
    {
        $recapRange = $this->resolveRecapRange($request->query('range'));
        $recapLength = $this->resolveRecapLength($recapRange, $request->query('length'));
        [$start, $end] = $this->recapWindow($recapRange, $recapLength);

        $rangeLabel = match ($recapRange) {
            'weekly' => 'mingguan',
            'yearly' => 'tahunan',
            default => 'bulanan',
        };

        $timestamp = now()->format('Ymd_His');
        $filename = "transaksi-{$rangeLabel}-{$timestamp}.xlsx";

        return Excel::download(new TransactionDetailExport($start, $end, $rangeLabel), $filename);
    }

    private function resolveRecapRange(?string $range): string
    {
        $allowed = ['weekly', 'monthly', 'yearly'];

        return in_array($range, $allowed, true) ? $range : 'weekly';
    }

    private function resolveRecapLength(string $range, $length): int
    {
        $value = filter_var($length, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        $allowed = match ($range) {
            'weekly' => [1, 2, 4, 8, 12],
            'yearly' => [1, 3, 5],
            default => [1, 3, 6, 12],
        };

        $default = $range === 'weekly' ? 1 : 1;

        if ($value !== false && in_array($value, $allowed, true)) {
            return $value;
        }

        return $default;
    }

    private function recapMeta(string $range, int $length): array
    {
        return match ($range) {
            'weekly' => ['Rekap Transaksi Mingguan', $length . ' minggu'],
            'yearly' => ['Rekap Transaksi Tahunan', $length . ' tahun'],
            default => ['Rekap Transaksi Bulanan', $length . ' bulan'],
        };
    }

    private function buildTransactionRecap(string $range, int $length)
    {
        return match ($range) {
            'weekly' => $this->buildWeeklyRecap($length),
            'yearly' => $this->buildYearlyRecap($length),
            default => $this->buildMonthlyRecap($length),
        };
    }

    private function recapWindow(string $range, int $length): array
    {
        return match ($range) {
            'weekly' => [
                now()->startOfWeek()->subWeeks(max($length, 1) - 1),
                now()->endOfWeek(),
            ],
            'yearly' => [
                now()->startOfYear()->subYears(max($length, 1) - 1),
                now()->endOfYear(),
            ],
            default => [
                now()->startOfMonth()->subMonths(max($length, 1) - 1),
                now()->endOfMonth(),
            ],
        };
    }

    private function buildChartData(string $range): array
    {
        return match ($range) {
            'weekly' => $this->buildWeeklyChart(),
            'yearly' => $this->buildYearlyChart(),
            default => $this->buildMonthlyChart(),
        };
    }

    private function buildMonthlyRecap(int $months)
    {
        $monthFormat = $this->monthFormatExpression();

        return Transaction::selectRaw($monthFormat . ' as month, SUM(total_amount) as total, COUNT(*) as transaction_count')
            ->where('transaction_date', '>=', now()->subMonths(max($months, 1) - 1)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                $label = Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('M Y');

                return [
                    'month' => $row->month,
                    'label' => $label,
                    'total' => (float) $row->total,
                    'transaction_count' => (int) $row->transaction_count,
                ];
            });
    }

    private function buildMonthlyChart(): array
    {
        $monthFormat = $this->monthFormatExpression();
        $startRange = now()->subMonths(5)->startOfMonth();
        $endRange = now()->endOfMonth();

        $rows = Transaction::selectRaw($monthFormat . ' as bucket, SUM(total_amount) as total, COUNT(*) as transaction_count')
            ->whereBetween('transaction_date', [$startRange, $endRange])
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $labels = $rows->map(fn ($row) => Carbon::createFromFormat('Y-m', $row->bucket)->translatedFormat('M Y'));
        $totals = $rows->map(fn ($row) => (float) $row->total);
        $counts = $rows->map(fn ($row) => (int) $row->transaction_count);

        return [
            'labels' => $labels,
            'totals' => $totals,
            'counts' => $counts,
        ];
    }

    private function buildWeeklyRecap(int $weeks)
    {
        $startRange = now()->startOfWeek()->subWeeks(max($weeks, 1) - 1);
        $endRange = now()->endOfWeek();

        $transactions = Transaction::whereBetween('transaction_date', [$startRange, $endRange])->get();

        return $transactions
            ->groupBy(function ($transaction) {
                return Carbon::parse($transaction->transaction_date)->startOfWeek()->format('Y-m-d');
            })
            ->map(function ($group, $startOfWeek) {
                $start = Carbon::parse($startOfWeek);
                $end = (clone $start)->endOfWeek();

                return [
                    'period' => $start->format('Y-m-d'),
                    'label' => $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M'),
                    'total' => (float) $group->sum('total_amount'),
                    'transaction_count' => (int) $group->count(),
                ];
            })
            ->sortBy('period')
            ->values();
    }

    private function buildWeeklyChart(): array
    {
        $startRange = now()->startOfWeek();
        $endRange = now()->endOfWeek();

        $rows = Transaction::selectRaw('DATE(transaction_date) as bucket, SUM(total_amount) as total, COUNT(*) as transaction_count')
            ->whereBetween('transaction_date', [$startRange, $endRange])
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $labels = $rows->map(fn ($row) => Carbon::parse($row->bucket)->translatedFormat('d M'));
        $totals = $rows->map(fn ($row) => (float) $row->total);
        $counts = $rows->map(fn ($row) => (int) $row->transaction_count);

        return [
            'labels' => $labels,
            'totals' => $totals,
            'counts' => $counts,
        ];
    }

    private function buildYearlyRecap(int $years)
    {
        $yearFormat = $this->yearFormatExpression();

        return Transaction::selectRaw($yearFormat . ' as year, SUM(total_amount) as total, COUNT(*) as transaction_count')
            ->where('transaction_date', '>=', now()->subYears(max($years, 1) - 1)->startOfYear())
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(function ($row) {
                return [
                    'period' => $row->year,
                    'label' => (string) $row->year,
                    'total' => (float) $row->total,
                    'transaction_count' => (int) $row->transaction_count,
                ];
            });
    }

    private function buildYearlyChart(): array
    {
        $yearFormat = $this->yearFormatExpression();
        $startRange = now()->subYears(4)->startOfYear();
        $endRange = now()->endOfYear();

        $rows = Transaction::selectRaw($yearFormat . ' as bucket, SUM(total_amount) as total, COUNT(*) as transaction_count')
            ->whereBetween('transaction_date', [$startRange, $endRange])
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $labels = $rows->map(fn ($row) => (string) $row->bucket);
        $totals = $rows->map(fn ($row) => (float) $row->total);
        $counts = $rows->map(fn ($row) => (int) $row->transaction_count);

        return [
            'labels' => $labels,
            'totals' => $totals,
            'counts' => $counts,
        ];
    }

    private function monthFormatExpression(): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'pgsql' => "to_char(transaction_date, 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', transaction_date)",
            default => "DATE_FORMAT(transaction_date, '%Y-%m')",
        };
    }

    private function yearFormatExpression(): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'pgsql' => "to_char(transaction_date, 'YYYY')",
            'sqlite' => "strftime('%Y', transaction_date)",
            default => "DATE_FORMAT(transaction_date, '%Y')",
        };
    }

    private function hourFormatExpression(): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'pgsql' => "EXTRACT(HOUR FROM transaction_date)",
            'sqlite' => "CAST(strftime('%H', transaction_date) AS INTEGER)",
            default => "HOUR(transaction_date)",
        };
    }

    private function chartMeta(string $range): array
    {
        return match ($range) {
            'weekly' => [
                'heading' => 'Performa Penjualan Mingguan',
                'subtitle' => 'Total & jumlah transaksi per hari (minggu ini).',
                'badge' => 'Harian',
            ],
            'yearly' => [
                'heading' => 'Performa Penjualan Tahunan',
                'subtitle' => 'Total & jumlah transaksi per tahun.',
                'badge' => 'Tahunan',
            ],
            default => [
                'heading' => 'Performa Penjualan Bulanan',
                'subtitle' => 'Total & jumlah transaksi per bulan.',
                'badge' => 'Bulanan',
            ],
        };
    }

    private function recapOptions(string $activeRange, int $activeLength): array
    {
        $options = [
            [
                'range' => 'weekly',
                'length' => $activeRange === 'weekly' ? $activeLength : 1,
                'label' => 'Mingguan',
            ],
            [
                'range' => 'monthly',
                'length' => $activeRange === 'monthly' ? $activeLength : 3,
                'label' => 'Bulanan',
            ],
            [
                'range' => 'yearly',
                'length' => $activeRange === 'yearly' ? $activeLength : 1,
                'label' => 'Tahunan',
            ],
        ];

        return array_map(function ($option) use ($activeRange, $activeLength) {
            $option['active'] = $option['range'] === $activeRange && $option['length'] === $activeLength;

            return $option;
        }, $options);
    }

    private function closingShiftSummaries()
    {
        $today = today();

        $cashiersToday = Transaction::whereDate('transaction_date', $today)
            ->pluck('user_id')
            ->unique()
            ->values();

        if ($cashiersToday->isEmpty()) {
            return collect();
        }

        $cashTotals = Transaction::select(
                'user_id',
                DB::raw('SUM(total_amount) as cash_total'),
                DB::raw('SUM(payment_amount) as cash_received'),
                DB::raw('SUM(change_amount) as change_given'),
                DB::raw('COUNT(*) as cash_transactions')
            )
            ->whereDate('transaction_date', $today)
            ->where('payment_method', 'cash')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $nonCashTotals = Transaction::select(
                'user_id',
                DB::raw('SUM(total_amount) as non_cash_total'),
                DB::raw('COUNT(*) as non_cash_transactions')
            )
            ->whereDate('transaction_date', $today)
            ->whereIn('payment_method', ['debit', 'qris', 'transfer'])
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $users = User::whereIn('id', $cashiersToday)->get(['id', 'name', 'email']);

        return $users->map(function (User $user) use ($cashTotals, $nonCashTotals) {
            $cash = $cashTotals->get($user->id);
            $nonCash = $nonCashTotals->get($user->id);

            $expectedDrawer = max(
                0,
                (float) ($cash->cash_received ?? 0) - (float) ($cash->change_given ?? 0)
            );

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'cash_total' => (float) ($cash->cash_total ?? 0),
                'cash_received' => (float) ($cash->cash_received ?? 0),
                'change_given' => (float) ($cash->change_given ?? 0),
                'cash_transactions' => (int) ($cash->cash_transactions ?? 0),
                'non_cash_total' => (float) ($nonCash->non_cash_total ?? 0),
                'non_cash_transactions' => (int) ($nonCash->non_cash_transactions ?? 0),
                'expected_drawer' => $expectedDrawer,
                'counted_cash' => null,
                'difference' => null,
            ];
        })->sortByDesc('cash_total')->values();
    }

    private function peakHoursSummary(): array
    {
        $rangeDays = 14;
        $start = now()->subDays($rangeDays - 1)->startOfDay();
        $end = now()->endOfDay();

        $hourExpression = $this->hourFormatExpression();

        $rows = Transaction::selectRaw($hourExpression . ' as hour_bucket, COUNT(*) as trx_count, SUM(total_amount) as total_amount')
            ->whereBetween('transaction_date', [$start, $end])
            ->groupBy('hour_bucket')
            ->orderBy('hour_bucket')
            ->get()
            ->keyBy('hour_bucket');

        $labels = [];
        $counts = [];
        $totals = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $label = str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':00';
            $labels[] = $label;

            $bucket = $hour;
            $counts[] = (int) ($rows[$bucket]->trx_count ?? 0);
            $totals[] = (float) ($rows[$bucket]->total_amount ?? 0);
        }

        $topHours = collect($labels)
            ->map(fn ($label, $idx) => ['label' => $label, 'count' => $counts[$idx] ?? 0])
            ->sortByDesc('count')
            ->take(3)
            ->filter(fn ($item) => $item['count'] > 0)
            ->values();

        $headline = $topHours->isEmpty()
            ? 'Belum ada data transaksi pada rentang ini.'
            : 'Jam ramai: ' . $topHours->pluck('label')->implode(', ');

        return [
            'labels' => $labels,
            'counts' => $counts,
            'totals' => $totals,
            'headline' => $headline,
            'range_label' => "{$rangeDays} hari terakhir",
        ];
    }

    private function topProductsForCurrentMonth()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return TransactionDetail::select(
                'product_name',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as gross_total')
            )
            ->whereHas('transaction', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('transaction_date', [$startOfMonth, $endOfMonth]);
            })
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }

    private function buildRecentRevenue(int $days): array
    {
        $days = max(1, $days);
        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate = now()->endOfDay();

        $rows = Transaction::selectRaw('DATE(transaction_date) as bucket, SUM(total_amount) as total')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy('bucket');

        $labels = [];
        $totals = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $bucket = $date->format('Y-m-d');

            $labels[] = $date->translatedFormat('d M');
            $totals[] = (float) ($rows[$bucket]->total ?? 0);
        }

        $currentTotal = array_sum($totals);

        $previousStart = $startDate->copy()->subDays($days);
        $previousEnd = $startDate->copy()->subSecond();
        $previousTotal = (float) Transaction::whereBetween('transaction_date', [$previousStart, $previousEnd])
            ->sum('total_amount');

        $change = $currentTotal - $previousTotal;
        $trend = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat');
        $changePct = $previousTotal > 0 ? ($change / $previousTotal) * 100 : null;

        return [
            'labels' => $labels,
            'totals' => $totals,
            'total' => $currentTotal,
            'previous_total' => $previousTotal,
            'change' => $change,
            'change_pct' => $changePct,
            'trend' => $trend,
        ];
    }

    private function activeCashierSummaries()
    {
        $activityThreshold = now()->subMinutes(30)->getTimestamp();

        $sessionActivity = DB::table('sessions')
            ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $activityThreshold)
            ->groupBy('user_id')
            ->pluck('last_activity', 'user_id');

        if ($sessionActivity->isEmpty()) {
            return collect();
        }

        $cashierIds = $sessionActivity->keys();

        $cashTotals = Transaction::select('user_id', DB::raw('SUM(payment_amount - change_amount) as net_cash'))
            ->whereIn('user_id', $cashierIds)
            ->where('payment_method', 'cash')
            ->whereDate('transaction_date', today())
            ->groupBy('user_id')
            ->pluck('net_cash', 'user_id');

        $users = User::whereIn('id', $cashierIds)
            ->where('role', 'kasir')
            ->get(['id', 'name', 'email']);

        return $users->map(function (User $user) use ($sessionActivity, $cashTotals) {
            $lastActivity = $sessionActivity->get($user->id);
            $cashInDrawer = max((float) ($cashTotals[$user->id] ?? 0), 0);

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'last_active_at' => $lastActivity ? Carbon::createFromTimestamp($lastActivity) : null,
                'cash_in_drawer' => $cashInDrawer,
            ];
        })->sortByDesc('cash_in_drawer')->values();
    }
}
