<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Report;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
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
        $chartData   = $salesByDay->pluck('total');

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

    // ─── Shared query logic ───────────────────────────────────────────────────
    private function getDateRange($range)
    {
        $end   = Carbon::now();
        $start = match ($range) {
            'today'   => $end->copy()->startOfDay(),
            '7days'   => $end->copy()->subDays(7),
            '30days'  => $end->copy()->subDays(30),
            '3months' => $end->copy()->subMonths(3),
            '1year'   => $end->copy()->subYear(),
            default   => $end->copy()->subDays(30),
        };

        if ($range === 'today') {
            $end = $end->copy()->endOfDay();
        }

        return [$start, $end];
    }

    private function getResults($type, $start, $end)
    {
        if ($type === 'sales') {
            return DB::table('orders')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->groupByRaw('DATE(created_at)')
                ->orderBy('date')
                ->get();

        } elseif ($type === 'inventory') {
            $stockColumn = 'stock';
            $columns     = DB::getSchemaBuilder()->getColumnListing('products');

            if (!in_array($stockColumn, $columns)) {
                return DB::table('products')
                    ->select('name', 'price')
                    ->orderBy('name', 'asc')
                    ->get();
            }

            return DB::table('products')
                ->select(
                    'name',
                    $stockColumn,
                    'price',
                    DB::raw("$stockColumn * price as total")
                )
                ->orderBy($stockColumn, 'asc')
                ->get();

        } elseif ($type === 'customer') {
            return Customer::select('id', 'name', 'gender', 'created_at')
                ->withCount(['orders as total_qty' => function ($query) use ($start, $end) {
                    $query->whereBetween('order_date', [$start, $end])
                          ->select(DB::raw('SUM(quantity)'));
                }])
                ->withSum(['orders as total_price' => function ($query) use ($start, $end) {
                    $query->whereBetween('order_date', [$start, $end]);
                }], 'total_amount')
                ->get();

        } elseif ($type === 'financial') {
            return DB::table('orders')
                ->whereBetween('order_date', [$start, $end])
                ->selectRaw('DATE(order_date) as date, SUM(quantity) as sold_qty, SUM(total_amount) as revenue')
                ->groupByRaw('DATE(order_date)')
                ->orderBy('date')
                ->get();
        }

        return collect();
    }

    // ─── Generate (view) ──────────────────────────────────────────────────────
    public function generate(Request $request)
    {
        $type  = $request->input('type');
        $range = $request->input('range');

        [$start, $end] = $this->getDateRange($range);

        $results    = $this->getResults($type, $start, $end);
        $grandTotal = $type === 'sales' ? $results->sum('total') : 0;

        return view('reports.generated', compact(
            'type', 'range', 'results', 'start', 'end', 'grandTotal'
        ));
    }

    // ─── Export to Excel ──────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $type  = $request->input('type');
        $range = $request->input('range');

        [$start, $end] = $this->getDateRange($range);

        $results  = $this->getResults($type, $start, $end);
        $filename = ucfirst($type) . '_Report_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new ReportExport($results, $type), $filename);
    }
}