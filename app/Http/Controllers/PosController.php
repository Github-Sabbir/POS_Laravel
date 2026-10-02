<?php

namespace App\Http\Controllers;

use App\Models\{Customer, Product, ProductBarcode, Sale, SaleItem, Setting};
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
class PosController extends Controller
{
    public function index()
    {
        return view('pos.index', [
            'customers' => Customer::where('status', 'active')->orderBy('name')->get(),
            'products' => Product::with('barcodes')->where('status', 'active')->orderBy('name')->limit(200)->get(),
        ]);
    }
    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'user']);
        $receiptWidth = Setting::where('key', 'receipt_width')->value('value') ?: '80mm';
        $shopName = Setting::where('key', 'shop_name')->value('value') ?: 'Retail POS';
        $logoPath = Setting::where('key', 'logo_path')->value('value');
        return view('sales.receipt', compact('sale', 'receiptWidth', 'shopName', 'logoPath'));
    }
    public function lookup(string $query)
    {
        $query = trim($query);
        $exact = Product::with('barcodes')
            ->where('status', 'active')
            ->where(fn($q) => $q->where('sku', $query)->orWhereHas('barcodes', fn($b) => $b->where('barcode', $query)))
            ->first();

        $suggestions = Product::with('barcodes')
            ->where('status', 'active')
            ->where(fn($q) => $q->where('name', 'like', "%{$query}%")
                ->orWhere('sku', 'like', "%{$query}%")
                ->orWhereHas('barcodes', fn($b) => $b->where('barcode', 'like', "%{$query}%")))
            ->orderBy('name')
            ->limit(12)
            ->get();

        return response()->json([
            'product' => $exact,
            'suggestions' => $suggestions,
        ]);
    }
    public function checkout(Request $r, InventoryService $inv)
    {
        $d = $r->validate(['customer_id' => 'nullable|exists:customers,id', 'items' => 'required|array|min:1', 'items.*.product_id' => 'required|integer|exists:products,id', 'items.*.quantity' => 'required|numeric|min:.001', 'items.*.price' => 'required|numeric|min:0', 'discount' => 'nullable|numeric|min:0', 'tax' => 'nullable|numeric|min:0', 'paid' => 'required|numeric|min:0', 'payment_method' => 'required|in:cash,card,mobile,other']);
        return DB::transaction(function () use ($d, $r, $inv) {
            $subtotal = 0;
            $rows = [];
            foreach ($d['items'] as $it) {
                $p = Product::whereKey($it['product_id'])->lockForUpdate()->firstOrFail();
                $q = (float) $it['quantity'];
                if ($p->current_stock < $q) {
                    throw new RuntimeException("Insufficient stock: {$p->name}");
                }
                $price = (float) $it['price'];
                $line = $q * $price;
                $subtotal += $line;
                $rows[] = [$p, $q, $price, $line];
            }
            $discount = (float) ($d['discount'] ?? 0);
            $tax = (float) ($d['tax'] ?? 0);
            $total = max(0, $subtotal - $discount + $tax);
            $paid = (float) $d['paid'];
            if ($paid > $total) {
                $change = $paid - $total;
            } else {
                $change = 0;
            }
            $due = max(0, $total - $paid);
            $sale = Sale::create(['invoice_no' => 'INV-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)), 'customer_id' => $d['customer_id'] ?? null, 'user_id' => $r->user()->id, 'subtotal' => $subtotal, 'discount' => $discount, 'tax' => $tax, 'total' => $total, 'paid' => $paid, 'due' => $due, 'change' => $change, 'payment_method' => $d['payment_method'], 'status' => 'completed']);
            foreach ($rows as [$p, $q, $price, $line]) {
                SaleItem::create(['sale_id' => $sale->id, 'product_id' => $p->id, 'quantity' => $q, 'unit_price' => $price, 'cost_price' => $p->purchase_price, 'line_total' => $line]);
                $inv->change($p, -$q, 'SALE', Sale::class, $sale->id, $r->user()->id, 'POS sale');
            }
            return response()->json(['ok' => true, 'invoice' => $sale->invoice_no, 'sale_id' => $sale->id, 'total' => $total, 'change' => $change, 'due' => $due]);
        });
    }
}
