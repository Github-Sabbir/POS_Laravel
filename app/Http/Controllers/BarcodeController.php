<?php

namespace App\Http\Controllers;

use App\Models\{Product, ProductBarcode};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $products = Product::where('status', 'active')->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"))->orderBy('name')->limit(200)->get();
        $barcodes = ProductBarcode::with('product')->when($search, fn($q) => $q->where('barcode', 'like', "%{$search}%")->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))->latest()->paginate(30)->withQueryString();
        return view('products.barcodes', compact('products', 'barcodes', 'search'));
    }
    public function store(Request $request)
    {
        $data = $request->validate(['product_id' => 'required|exists:products,id', 'barcode' => 'required|string|max:100|unique:product_barcodes,barcode', 'is_primary' => 'nullable|boolean']);
        if ($request->boolean('is_primary')) {
            ProductBarcode::where('product_id', $data['product_id'])->update(['is_primary' => false]);
        }
        ProductBarcode::create($data);
        return back()->with('success', 'Barcode saved.');
    }
    public function setPrimary(ProductBarcode $barcode)
    {
        ProductBarcode::where('product_id', $barcode->product_id)->update(['is_primary' => false]);
        $barcode->update(['is_primary' => true]);
        return back()->with('success', 'Primary barcode updated.');
    }
    public function bulkGenerate(Request $request)
    {
        $data = $request->validate(['product_id' => 'required|exists:products,id', 'count' => 'required|integer|min:1|max:100']);
        $created = [];
        for ($i = 0; $i < $data['count']; $i++) {
            do {
                $code = '20' . str_pad((string) random_int(1, 99999999999), 11, '0', STR_PAD_LEFT);
            } while (ProductBarcode::where('barcode', $code)->exists());
            $created[] = ProductBarcode::create(['product_id' => $data['product_id'], 'barcode' => $code, 'is_primary' => false]);
        }
        return back()->with('success', count($created) . ' internal barcodes generated.');
    }
    public function destroy(ProductBarcode $barcode)
    {
        if ($barcode->is_primary && ProductBarcode::where('product_id', $barcode->product_id)->count() > 1) {
            return back()->withErrors(['barcode' => 'Set another barcode as primary before deleting this one.']);
        }
        $barcode->delete();
        return back()->with('success', 'Barcode deleted.');
    }
}
