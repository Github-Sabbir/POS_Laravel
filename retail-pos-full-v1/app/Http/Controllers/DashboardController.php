<?php namespace App\Http\Controllers;
use App\Models\{Sale,Purchase,Expense,Product,Customer,Supplier};
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller {
 public function index(){
  $today=now()->toDateString();
  $sales=Sale::whereDate('created_at',$today)->sum('total');
  $purchases=Purchase::whereDate('created_at',$today)->sum('total');
  $expenses=Expense::whereDate('expense_date',$today)->sum('amount');
  $cogs=DB::table('sale_items')->join('sales','sales.id','=','sale_items.sale_id')->whereDate('sales.created_at',$today)->sum(DB::raw('sale_items.quantity*sale_items.cost_price'));
  return view('dashboard.index',compact('sales','purchases','expenses','cogs'));
 }
}
