<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function change(Product $product, float $delta, string $type, ?string $refType = null, ?int $refId = null, ?int $userId = null, string $desc = ''): Product
    {
        $p = Product::withTrashed()->whereKey($product->id)->lockForUpdate()->firstOrFail();
        $prev = (float) $p->current_stock;
        $next = $prev + $delta;
        if ($next < 0) {
            throw new RuntimeException("Insufficient stock for {$p->name}.");
        }

        // Buying stock for an archived product makes it available again automatically.
        if ($delta > 0 && $p->trashed()) {
            $p->restore();
            $p->status = 'active';
        }

        $p->current_stock = $next;
        $p->save();
        StockMovement::create([
            'product_id' => $p->id,
            'quantity' => $delta,
            'previous_stock' => $prev,
            'new_stock' => $next,
            'movement_type' => $type,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'user_id' => $userId,
            'description' => $desc,
        ]);
        return $p;
    }
}
