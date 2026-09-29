<?php

namespace App\Http\Controllers;

use App\Models\{Expense, Product, Purchase, Sale, SaleReturn};
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $sales = (float) Sale::whereDate('created_at', $today)->sum('total');
        $returns = (float) SaleReturn::whereDate('created_at', $today)->sum('refund_amount');
        $transactions = (int) Sale::whereDate('created_at', $today)->count();
        $lowStock = Product::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $canSeeFinance = auth()->user()->hasPermission('reports.view');
        $purchases = $expenses = $cogs = $grossProfit = $netProfit = 0;
        if ($canSeeFinance) {
            $purchases = (float) Purchase::whereDate('created_at', $today)->sum('total');
            $expenses = (float) Expense::whereDate('expense_date', $today)->sum('amount');
            $cogs = (float) DB::table('sale_items')->join('sales', 'sales.id', '=', 'sale_items.sale_id')->whereDate('sales.created_at', $today)->sum(DB::raw('sale_items.quantity * sale_items.cost_price'));
            $returnedCogs = (float) SaleReturn::whereDate('created_at', $today)->sum(DB::raw('quantity * cost_price'));
            $cogs = max(0, $cogs - $returnedCogs);
            $grossProfit = max(0, $sales - $returns - $cogs);
            $netProfit = $grossProfit - $expenses;
        }
        return view('dashboard.index', compact('sales', 'returns', 'transactions', 'lowStock', 'canSeeFinance', 'purchases', 'expenses', 'cogs', 'grossProfit', 'netProfit'));
    }
}
