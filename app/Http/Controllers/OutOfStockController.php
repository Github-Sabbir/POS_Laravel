<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class OutOfStockController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'barcodes'])
            ->where('status', 'active')
            ->where('current_stock', '<=', 0)
            ->orderBy('name')
            ->paginate(20);

        return view('products.out-of-stock', compact('products'));
    }

    public function archive(Request $request, Product $product)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $product->delete();
        return back()->with('success', 'Out-of-stock product archived.');
    }
}
