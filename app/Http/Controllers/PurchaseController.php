<?php

namespace App\Http\Controllers;

use App\Models\{Purchase, PurchaseItem, Product, Supplier};
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchases.index', ['purchases' => Purchase::with('supplier')->latest()->paginate(20)]);
    }

    public function create()
    {
        return view('purchases.form', [
            'purchase' => new Purchase(),
            'products' => Product::where('status', 'active')->get(),
            'suppliers' => Supplier::where('status', 'active')->get(),
        ]);
    }

    public function store(Request $r, InventoryService $inv)
    {
        $d = $this->validatePurchase($r);
        $sub = $this->subtotal($d['items']);
        $paid = round((float) $d['paid'], 2);
        if ($paid > $sub) return back()->withInput()->withErrors(['paid' => 'Paid amount cannot be greater than the purchase total.']);

        return DB::transaction(function () use ($d, $r, $inv, $sub, $paid) {
            $p = Purchase::create([
                'reference_no' => $this->nextReference(),
                'supplier_id' => $d['supplier_id'] ?? null,
                'user_id' => $r->user()->id,
                'subtotal' => $sub,
                'total' => $sub,
                'paid' => $paid,
                'due' => max(0, $sub - $paid),
                'status' => 'received',
            ]);
            $this->applyItems($p, $d['items'], $inv, $r->user()->id, 1);
            return redirect()->route('purchases.index')->with('success', "Purchase {$p->reference_no} completed.");
        });
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load('items');
        return view('purchases.form', [
            'purchase' => $purchase,
            'products' => Product::where('status', 'active')->orWhereIn('id', $purchase->items->pluck('product_id'))->get()->unique('id')->values(),
            'suppliers' => Supplier::where('status', 'active')->orWhere('id', $purchase->supplier_id)->get(),
        ]);
    }

    public function update(Request $r, Purchase $purchase, InventoryService $inv)
    {
        $d = $this->validatePurchase($r);
        $sub = $this->subtotal($d['items']);
        $paid = round((float) $d['paid'], 2);
        if ($paid > $sub) return back()->withInput()->withErrors(['paid' => 'Paid amount cannot be greater than the purchase total.']);

        return DB::transaction(function () use ($d, $r, $purchase, $inv, $sub, $paid) {
            // Reverse the original stock receipt first, then apply the edited receipt.
            foreach ($purchase->items as $old) {
                $inv->change($old->product, -(float) $old->quantity, 'PURCHASE_EDIT', Purchase::class, $purchase->id, $r->user()->id, 'Reverse old purchase quantity for edit');
            }
            $purchase->items()->delete();
            $purchase->update([
                'supplier_id' => $d['supplier_id'] ?? null,
                'subtotal' => $sub,
                'total' => $sub,
                'paid' => $paid,
                'due' => max(0, $sub - $paid),
                'status' => 'received',
            ]);
            $this->applyItems($purchase, $d['items'], $inv, $r->user()->id, 1);
            return redirect()->route('purchases.index')->with('success', "Purchase {$purchase->reference_no} updated.");
        });
    }

    public function destroy(Purchase $purchase, InventoryService $inv, Request $r)
    {
        return DB::transaction(function () use ($purchase, $inv, $r) {
            foreach ($purchase->items as $item) {
                try {
                    $inv->change($item->product, -(float) $item->quantity, 'PURCHASE_DELETE', Purchase::class, $purchase->id, $r->user()->id, 'Reverse deleted purchase');
                } catch (RuntimeException $e) {
                    throw new RuntimeException("Cannot delete {$purchase->reference_no}: {$e->getMessage()} The stock from this purchase has already been used.");
                }
            }
            $ref = $purchase->reference_no;
            $purchase->delete();
            return redirect()->route('purchases.index')->with('success', "Purchase {$ref} deleted and stock reversed.");
        });
    }

    private function validatePurchase(Request $r): array
    {
        return $r->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
        ]);
    }

    private function subtotal(array $items): float
    {
        return round(collect($items)->sum(fn ($x) => (float) $x['quantity'] * (float) $x['unit_cost']), 2);
    }

    private function applyItems(Purchase $purchase, array $items, InventoryService $inv, int $userId, int $direction): void
    {
        foreach ($items as $it) {
            $prod = Product::whereKey($it['product_id'])->lockForUpdate()->firstOrFail();
            $line = round((float) $it['quantity'] * (float) $it['unit_cost'], 2);
            PurchaseItem::create(['purchase_id' => $purchase->id, 'product_id' => $prod->id, 'quantity' => $it['quantity'], 'unit_cost' => $it['unit_cost'], 'line_total' => $line]);
            $inv->change($prod, (float) $it['quantity'] * $direction, 'PURCHASE', Purchase::class, $purchase->id, $userId, $direction > 0 ? 'Purchase received' : 'Purchase stock reversal');
        }
    }

    private function nextReference(): string
    {
        $base = 'PUR-' . now()->format('Ymd-His');
        $candidate = $base;
        $n = 1;
        while (Purchase::where('reference_no', $candidate)->exists()) {
            $candidate = $base . '-' . str_pad((string) $n++, 2, '0', STR_PAD_LEFT);
        }
        return $candidate;
    }
}
