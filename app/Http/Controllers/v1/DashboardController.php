<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function index()
    {
        // 1. Sales Chart Data (Last 30 Days)
        $salesData = \App\Models\v1\Order::where('ordered_at', '>=', now()->subDays(30))
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(ordered_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $salesData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M'));
        $chartValues = $salesData->pluck('total');

        // 2. Top 5 Best Selling Menus
        $topMenus = \App\Models\v1\OrderDetail::select(
            'trx_order_details.mdx_menu_id',
            'trx_order_details.menu_name',
            DB::raw('SUM(trx_order_details.quantity) as total_qty'),
            DB::raw('SUM(trx_order_details.subtotal) as total_sales')
        )
            ->join('trx_orders', 'trx_orders.id', '=', 'trx_order_details.trx_order_id')
            ->where('trx_orders.status', '!=', 'cancelled')
            ->groupBy('trx_order_details.mdx_menu_id', 'trx_order_details.menu_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 3. Top 5 Stores by Sales
        $topStores = \App\Models\v1\Store::withSum([
            'orders' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }
        ], 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->limit(5)
            ->get();

        // 4. Quick Stats
        $stats = [
            'total_brands' => \App\Models\v1\Brand::count(),
            'total_stores' => \App\Models\v1\Store::count(),
            'total_menus' => \App\Models\v1\Menu::count(),
            'total_orders' => \App\Models\v1\Order::count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'chart' => [
                    'labels' => $chartLabels,
                    'values' => $chartValues,
                ],
                'top_menus' => $topMenus,
                'top_stores' => $topStores,
            ]
        ]);
    }
}
