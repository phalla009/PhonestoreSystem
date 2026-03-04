<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\ProductionLog;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== Summary Stats =====
        $totalProducts = Product::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalCustomers = Customer::count();
        $currentMonthRevenue = Order::whereMonth('order_date', now()->month)
                                     ->sum('total_amount');

        // ===== Recent Orders =====
        $recentOrders = Order::with(['customer', 'product'])
                             ->latest()
                             ->take(5)
                             ->get();

        // ===== Monthly Sales Chart (safe for ONLY_FULL_GROUP_BY) =====
        $monthlySales = Order::selectRaw('MONTH(order_date) as month_number, MONTHNAME(order_date) as month_name, SUM(total_amount) as total')
                             ->groupBy('month_number', 'month_name')
                             ->orderBy('month_number')
                             ->get();

        $months = $monthlySales->pluck('month_name')->toArray();
        $monthlyRevenue = $monthlySales->pluck('total')->toArray();

        // ===== Run Time vs Downtime Chart =====
        $productionLogs = ProductionLog::select('phase_name', 'run_time', 'downtime')->get();
        $productionPhases = $productionLogs->pluck('phase_name')->toArray();
        $runtime = $productionLogs->pluck('run_time')->toArray();
        $downtime = $productionLogs->pluck('downtime')->toArray();

        // ===== Customer Total Quantity Ordered =====
        $customerTotals = Order::selectRaw('customer_id, SUM(quantity) as total_qty')
                               ->with('customer')
                               ->groupBy('customer_id')
                               ->get();

        return view('index', compact(
            'totalProducts',
            'pendingOrders',
            'totalCustomers',
            'currentMonthRevenue',
            'recentOrders',
            'months',
            'monthlyRevenue',
            'productionPhases',
            'runtime',
            'downtime',
            'customerTotals'
        ));
    }
}