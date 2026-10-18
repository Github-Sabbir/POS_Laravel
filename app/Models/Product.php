<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use SoftDeletes;
    protected $fillable = ['category_id', 'brand_id', 'sku', 'name', 'unit', 'purchase_price', 'selling_price', 'wholesale_price', 'tax_rate', 'current_stock', 'minimum_stock', 'image', 'description', 'status'];
    protected $casts = ['purchase_price' => 'decimal:2', 'selling_price' => 'decimal:2', 'wholesale_price' => 'decimal:2', 'tax_rate' => 'decimal:2', 'current_stock' => 'decimal:3', 'minimum_stock' => 'decimal:3'];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function barcodes()
    {
        return $this->hasMany(ProductBarcode::class);
    }
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
