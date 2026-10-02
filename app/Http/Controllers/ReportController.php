<?php

namespace App\Http\Controllers;

use App\Models\{Expense, Product, Purchase, Sale, SaleReturn};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->date('from')?->startOfDay() ?? now()->startOfMonth();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();
        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $salesQuery = Sale::whereBetween('created_at', [$from, $to]);
        $purchaseQuery = Purchase::whereBetween('created_at', [$from, $to]);
        $expenseQuery = Expense::whereBetween('expense_date', [$from->toDateString(), $to->toDateString()]);
        $returnQuery = SaleReturn::whereBetween('created_at', [$from, $to]);
        $grossSales = (float) $salesQuery->sum('total');
        $returns = (float) $returnQuery->sum('refund_amount');
        $revenue = max(0, $grossSales - $returns);
        $purchases = (float) $purchaseQuery->sum('total');
        $expenses = (float) $expenseQuery->sum('amount');
        $cogs = (float) DB::table('sale_items')->join('sales', 'sales.id', '=', 'sale_items.sale_id')->whereBetween('sales.created_at', [$from, $to])->sum(DB::raw('sale_items.quantity * sale_items.cost_price'));
        $returnedCogs = (float) $returnQuery->sum(DB::raw('quantity * cost_price'));
        $cogs = max(0, $cogs - $returnedCogs);
        $gross = $revenue - $cogs;
        $net = $gross - $expenses;
        $paymentBreakdown = Sale::whereBetween('created_at', [$from, $to])->select('payment_method', DB::raw('COUNT(*) as transactions'), DB::raw('SUM(total) as amount'))->groupBy('payment_method')->orderByDesc('amount')->get();
        $topProducts = DB::table('sale_items')->join('sales', 'sales.id', '=', 'sale_items.sale_id')->join('products', 'products.id', '=', 'sale_items.product_id')->whereBetween('sales.created_at', [$from, $to])->select('products.name', DB::raw('SUM(sale_items.quantity) as quantity'), DB::raw('SUM(sale_items.line_total) as amount'))->groupBy('products.id', 'products.name')->orderByDesc('amount')->limit(10)->get();
        $low = Product::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $transactions = (int) $salesQuery->count();

        $dailyActivity = [];
        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to)) {
            $dayEnd = $cursor->copy()->endOfDay();
            $dailyActivity[] = [
                'date' => $cursor->copy(),
                'sales' => (float) Sale::whereBetween('created_at', [$cursor, $dayEnd])->sum('total'),
                'returns' => (float) SaleReturn::whereBetween('created_at', [$cursor, $dayEnd])->sum('refund_amount'),
                'expenses' => (float) Expense::whereDate('expense_date', $cursor->toDateString())->sum('amount'),
                'purchases' => (float) Purchase::whereBetween('created_at', [$cursor, $dayEnd])->sum('total'),
            ];
            $cursor->addDay();
            if (count($dailyActivity) >= 120) {
                break;
            }
        }

        return view('reports.index', compact('from', 'to', 'grossSales', 'returns', 'revenue', 'purchases', 'expenses', 'cogs', 'gross', 'net', 'paymentBreakdown', 'topProducts', 'low', 'transactions', 'dailyActivity'));
    }
}
