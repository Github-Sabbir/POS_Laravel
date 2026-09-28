<?php namespace App\Http\Controllers;
use App\Models\{Supplier,SupplierPayment}; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
class SupplierController extends Controller {
 public function index(){return view('suppliers.index',['suppliers'=>Supplier::withCount('purchases')->latest()->paginate(20)]);}
 public function create(){return view('suppliers.form',['supplier'=>new Supplier]);}
 public function store(Request $r){$d=$r->validate(['name'=>'required|max:255','company'=>'nullable|max:255','phone'=>'nullable|max:50','email'=>'nullable|email','address'=>'nullable','opening_due'=>'nullable|numeric|min:0','status'=>'required|in:active,inactive']);Supplier::create($d);return redirect()->route('suppliers.index')->with('success','Supplier saved.');}
 public function edit(Supplier $supplier){$supplier->load('purchases');return view('suppliers.form',compact('supplier'));}
 public function update(Request $r,Supplier $supplier){$d=$r->validate(['name'=>'required|max:255','company'=>'nullable|max:255','phone'=>'nullable|max:50','email'=>'nullable|email','address'=>'nullable','opening_due'=>'nullable|numeric|min:0','status'=>'required|in:active,inactive']);$supplier->update($d);return redirect()->route('suppliers.index')->with('success','Supplier updated.');}
 public function destroy(Supplier $supplier){if($supplier->purchases()->exists())return back()->withErrors(['supplier'=>'This supplier has purchase history and cannot be deleted. Set it inactive instead.']);$supplier->delete();return back()->with('success','Supplier deleted.');}
 public function payment(Request $r,Supplier $supplier){$d=$r->validate(['amount'=>'required|numeric|min:.01','payment_method'=>'required|in:cash,card,mobile,other','note'=>'nullable']);if($d['amount']>$supplier->current_due) return back()->withErrors(['amount'=>'Payment cannot exceed current supplier due.']);SupplierPayment::create(['supplier_id'=>$supplier->id,'user_id'=>$r->user()->id,'amount'=>$d['amount'],'payment_method'=>$d['payment_method'],'note'=>$d['note']??null,'paid_at'=>now()]);return back()->with('success','Supplier payment recorded.');}
}
