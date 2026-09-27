<?php namespace App\Http\Controllers;
use App\Models\{Product,ProductBarcode}; use Illuminate\Http\Request;
class BarcodeController extends Controller {
 public function index(Request $r){$products=Product::where('status','active')->orderBy('name')->get();$barcodes=ProductBarcode::with('product')->when($r->q,fn($q,$s)=>$q->where('barcode','like',"%$s%")->orWhereHas('product',fn($p)=>$p->where('name','like',"%$s%")))->latest()->paginate(25)->withQueryString();return view('products.barcodes',compact('products','barcodes'));}
 public function store(Request $r){$d=$r->validate(['product_id'=>'required|exists:products,id','barcode'=>'required|string|max:100|unique:product_barcodes,barcode','is_primary'=>'nullable|boolean']);if($r->boolean('is_primary'))ProductBarcode::where('product_id',$d['product_id'])->update(['is_primary'=>false]);ProductBarcode::create($d);return back()->with('success','Barcode saved.');}
 public function destroy(ProductBarcode $barcode){$barcode->delete();return back()->with('success','Barcode deleted.');}
}
