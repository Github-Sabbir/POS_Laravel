<?php
namespace App\Http\Controllers;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
class SupplierController extends Controller {
    public function index(){return view('suppliers.index',['suppliers'=>Supplier::withCount('purchases')->latest()->paginate(20)]);}
    public function create(){return view('suppliers.form',['supplier'=>new Supplier]);}
    public function store(Request $r){Supplier::create($this->validated($r));return redirect()->route('suppliers.index')->with('success','Supplier saved.');}
    public function edit(Supplier $supplier){$supplier->load(['purchases'=>fn($q)=>$q->latest(),'payments'=>fn($q)=>$q->latest()]);return view('suppliers.form',compact('supplier'));}
    public function update(Request $r,Supplier $supplier){$supplier->update($this->validated($r));return redirect()->route('suppliers.index')->with('success','Supplier updated.');}
    public function destroy(Supplier $supplier){if($supplier->purchases()->exists())return back()->withErrors(['supplier'=>'This supplier has purchase history and cannot be deleted. Set it inactive instead.']);$supplier->delete();return back()->with('success','Supplier removed.');}
    public function payment(Request $r,Supplier $supplier){$d=$r->validate(['amount'=>'required|numeric|min:0.01','payment_method'=>'required|in:cash,card,mobile,other','note'=>'nullable|string']);if($d['amount']>$supplier->current_due+0.009)return back()->withErrors(['amount'=>'Payment cannot exceed current supplier due.']);SupplierPayment::create(['supplier_id'=>$supplier->id,'user_id'=>$r->user()->id,'amount'=>$d['amount'],'payment_method'=>$d['payment_method'],'note'=>$d['note']??null]);return back()->with('success','Supplier payment recorded.');}
    private function validated(Request $r):array{return $r->validate(['name'=>'required|max:255','company'=>'nullable|max:255','phone'=>'nullable|max:50','email'=>'nullable|email','address'=>'nullable|string','opening_due'=>'nullable|numeric|min:0','status'=>'required|in:active,inactive']);}
}
