<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    { 
        $totalProducts = Product::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalCustomers = Customer::count();
        $monthlyRevenue = Order::whereMonth('created_at', now()->month)->sum('total_amount');
        $recentOrders = Order::with(['customer', 'product'])->latest()->take(5)->get();
        return view('index', compact(
            'totalProducts',
            'pendingOrders',
            'totalCustomers',
            'monthlyRevenue',
            'recentOrders'
        ));
    }
    
}
