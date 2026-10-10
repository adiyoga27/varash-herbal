<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Subscriber;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $valid = fn ($q) => $q->where('status', '!=', 'cancelled');

        $start = now()->subDays(13)->startOfDay();
        $daily = Order::where('created_at', '>=', $start)->where('status', '!=', 'cancelled')
            ->get(['created_at', 'total'])
            ->groupBy(fn ($o) => $o->created_at->format('Y-m-d'));

        $chart = collect(range(0, 13))->map(function ($i) use ($start, $daily) {
            $date = $start->copy()->addDays($i);
            $orders = $daily->get($date->format('Y-m-d'), collect());

            return ['label' => $date->translatedFormat('d M'), 'count' => $orders->count(), 'total' => (int) $orders->sum('total')];
        });

        return view('admin.dashboard', [
            'stats' => [
                'revenue_month' => Order::where($valid)->where('created_at', '>=', now()->startOfMonth())->sum('total'),
                'orders_today' => Order::whereDate('created_at', Carbon::today())->count(),
                'orders_pending' => Order::where('status', 'pending')->count(),
                'products' => Product::count(),
                'subscribers' => Subscriber::active()->count(),
                'subscribers_week' => Subscriber::where('created_at', '>=', now()->subWeek())->count(),
            ],
            'chart' => $chart,
            'recentOrders' => Order::latest()->take(6)->get(),
            'topProducts' => OrderItem::selectRaw('product_name, SUM(quantity) as qty, SUM(subtotal) as revenue')
                ->whereHas('order', $valid)
                ->groupBy('product_name')->orderByDesc('qty')->take(5)->get(),
            'lowStock' => Product::where('is_active', true)->where('stock', '<=', 10)->orderBy('stock')->take(5)->get(),
            'activities' => ActivityLog::with('user')->latest('id')->take(8)->get(),
        ]);
    }
}
