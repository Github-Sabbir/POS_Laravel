<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBarcode extends Model
{
    protected $fillable = ['product_id', 'barcode', 'is_primary'];
    protected $casts = ['is_primary' => 'boolean'];

    public function product()
    {
        // Barcodes must remain viewable when a product is archived (SoftDeleted).
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
