<?php

namespace App\Http\Controllers;

use App\Models\{Expense, Product, Purchase, Sale, SaleReturn};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();
        $from = $today->copy();
        $to = $today->copy()->endOfDay();

        if ($user->role === 'admin') {
            $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : $today->copy();
            $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : $today->copy()->endOfDay();
            if ($from->gt($to)) {
                [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
            }
        } elseif ($user->role === 'manager') {
            if ($request->filled('from') || $request->filled('to')) {
                $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : $today->copy()->subDays(6);
                $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : $today->copy()->endOfDay();
                if ($from->gt($to)) {
                    [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
                }
                if ($from->diffInDays($to) > 6) {
                    $from = $to->copy()->startOfDay()->subDays(6);
                }
            }
        }

        $sales = (float) Sale::whereBetween('created_at', [$from, $to])->sum('total');
        $returns = (float) SaleReturn::whereBetween('created_at', [$from, $to])->sum('refund_amount');
        $transactions = (int) Sale::whereBetween('created_at', [$from, $to])->count();
        $lowStock = Product::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $canSeeFinance = $user->hasPermission('reports.view');
        $purchases = $expenses = $cogs = $grossProfit = $netProfit = 0;

        if ($canSeeFinance) {
            $purchases = (float) Purchase::whereBetween('created_at', [$from, $to])->sum('total');
            $expenses = (float) Expense::whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])->sum('amount');
            $cogs = (float) DB::table('sale_items')->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->whereBetween('sales.created_at', [$from, $to])
                ->sum(DB::raw('sale_items.quantity * sale_items.cost_price'));
            $returnedCogs = (float) SaleReturn::whereBetween('created_at', [$from, $to])->sum(DB::raw('quantity * cost_price'));
            $cogs = max(0, $cogs - $returnedCogs);
            $grossProfit = max(0, $sales - $returns - $cogs);
            $netProfit = $grossProfit - $expenses;
        }

        $activity = [];
        $days = max(1, $from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay()) + 1);
        if ($days === 1) {
            for ($hour = 0; $hour < 24; $hour++) {
                $start = $from->copy()->startOfDay()->addHours($hour);
                $end = $start->copy()->addHour();
                $activity[] = [
                    'label' => $start->format('g A'),
                    'sales' => (float) Sale::whereBetween('created_at', [$start, $end])->sum('total'),
                ];
            }
        } else {
            $cursor = $from->copy()->startOfDay();
            while ($cursor->lte($to)) {
                $end = $cursor->copy()->endOfDay();
                $activity[] = [
                    'label' => $cursor->format('d M'),
                    'sales' => (float) Sale::whereBetween('created_at', [$cursor, $end])->sum('total'),
                ];
                $cursor->addDay();
            }
        }

        return view('dashboard.index', compact(
            'sales', 'returns', 'transactions', 'lowStock', 'canSeeFinance',
            'purchases', 'expenses', 'cogs', 'grossProfit', 'netProfit',
            'from', 'to', 'activity', 'days'
        ));
    }
}
