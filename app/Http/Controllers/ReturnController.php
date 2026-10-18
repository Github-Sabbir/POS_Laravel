<?php

namespace App\Http\Controllers;

use App\Models\{Product, Sale, SaleItem, SaleReturn};
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
class ReturnController extends Controller
{
    public function index()
    {
        $returns = SaleReturn::with(['sale', 'product', 'user'])->latest()->paginate(20);
        return view('returns.index', compact('returns'));
    }
    public function lookup(string $invoice)
    {
        $sale = Sale::with(['items.product'])->where('invoice_no', $invoice)->first();
        if (!$sale) {
            return response()->json(['message' => 'Invoice not found.'], 404);
        }
        $returned = SaleReturn::where('sale_id', $sale->id)->select('sale_item_id', DB::raw('SUM(quantity) as quantity'))->groupBy('sale_item_id')->pluck('quantity', 'sale_item_id');
        $items = $sale->items->map(function ($item) use ($returned) {
            $alreadyReturned = (float) ($returned[$item->id] ?? 0);
            $remaining = max(0, (float) $item->quantity - $alreadyReturned);
            return ['id' => $item->id, 'product_id' => $item->product_id, 'name' => $item->product->name, 'sku' => $item->product->sku, 'quantity' => (float) $item->quantity, 'returned' => $alreadyReturned, 'remaining' => $remaining, 'unit_price' => (float) $item->unit_price];
        })->filter(fn($item) => $item['remaining'] > 0)->values();
        return response()->json(['invoice' => $sale->invoice_no, 'customer' => optional($sale->customer)->name ?? 'Walk-in customer', 'paid' => (float) $sale->paid, 'items' => $items]);
    }
    public function store(Request $request, InventoryService $inv)
    {
        $data = $request->validate(['sale_item_id' => 'required|exists:sale_items,id', 'quantity' => 'required|numeric|min:.001', 'refund_amount' => 'nullable|numeric|min:0', 'refund_method' => 'nullable|in:cash,card,mobile,other', 'reason' => 'nullable|string|max:255']);
        return DB::transaction(function () use ($data, $request, $inv) {
            $item = SaleItem::with('sale')->lockForUpdate()->findOrFail($data['sale_item_id']);
            $alreadyReturned = (float) SaleReturn::where('sale_item_id', $item->id)->sum('quantity');
            $quantity = (float) $data['quantity'];
            if ($quantity > (float) $item->quantity - $alreadyReturned) {
                throw new RuntimeException('Return quantity exceeds the remaining sold quantity.');
            }
            $refund = (float) ($data['refund_amount'] ?? 0);
            $maxRefund = $quantity * (float) $item->unit_price;
            if ($refund > $maxRefund) {
                throw new RuntimeException('Refund cannot exceed the returned item value.');
            }
            $product = Product::lockForUpdate()->findOrFail($item->product_id);
            $inv->change($product, $quantity, 'RETURN', Sale::class, $item->sale_id, $request->user()->id, 'Sales return');
            SaleReturn::create(['sale_id' => $item->sale_id, 'sale_item_id' => $item->id, 'product_id' => $item->product_id, 'user_id' => $request->user()->id, 'quantity' => $quantity, 'unit_price' => $item->unit_price, 'cost_price' => $item->cost_price, 'refund_amount' => $refund, 'refund_method' => $data['refund_method'] ?? null, 'reason' => $data['reason'] ?? null]);
            return back()->with('success', 'Sales return processed and stock restored.');
        });
    }
}
