<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Report;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $salesThisMonth = Order::whereMonth('created_at', date('m'))
                                ->sum('total_amount');
        $ordersThisMonth = Order::whereMonth('created_at', date('m'))->count();
        $newCustomers = Customer::whereMonth('created_at', date('m'))->count();
        $customerSatisfaction = 92;
        $salesByDay = Order::selectRaw('DAY(created_at) as day, SUM(total_amount) as total')
                            ->whereMonth('created_at', date('m'))
                            ->groupBy('day')
                            ->orderBy('day')
                            ->get();
        $chartLabels = $salesByDay->pluck('day');
        $chartData = $salesByDay->pluck('total');
        return view('reports.index', compact(
            'salesThisMonth',
            'ordersThisMonth',
            'newCustomers',
            'customerSatisfaction',
            'chartLabels',
            'chartData'
        ));
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);
        return view('reports.show', compact('report'));
    }

    public function generate(Request $request)
    {
        $type = $request->input('type');
        $range = $request->input('range');
        $end = Carbon::now();
        $start = match ($range) {
            'today' => $end->copy()->startOfDay(),
            '7days' => $end->copy()->subDays(7),
            '30days' => $end->copy()->subDays(30),
            '3months' => $end->copy()->subMonths(3),
            '1year' => $end->copy()->subYear(),
            default => $end->copy()->subDays(30),
        };
        if ($range === 'today') {
            $end = $end->copy()->endOfDay();
        }
        $results = [];
        if ($type === 'sales') {
            $results = DB::table('orders')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->groupByRaw('DATE(created_at)')
                ->orderBy('date')
                ->get();
        } elseif ($type === 'inventory') {
            $stockColumn = 'stock';

            $columns = DB::getSchemaBuilder()->getColumnListing('products');
            if (!in_array($stockColumn, $columns)) {
                $results = DB::table('products')
                    ->select('name', 'price')
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                $results = DB::table('products')
                    ->select('name', $stockColumn, 'price')
                    ->orderBy($stockColumn, 'asc')
                    ->get();
            }
        } elseif ($type === 'customer') {
            $results = DB::table('customers')
                ->whereBetween('created_at', [$start, $end])
                ->select('name','gender', 'created_at')
                ->get();
        } 
        elseif ($type === 'financial') {
        $results = DB::table('orders')
            ->whereBetween('order_date', [$start, $end])
            ->selectRaw('DATE(order_date) as date, SUM(quantity) as sold_qty, SUM(total_amount) as revenue')
            ->groupByRaw('DATE(order_date)')
            ->orderBy('date')
            ->get();
         }


        return view('reports.generated', compact('type', 'range', 'results', 'start', 'end'));
    }
}
