<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SaleReturn extends Model
{
    protected $fillable = ['sale_id', 'sale_item_id', 'product_id', 'user_id', 'quantity', 'unit_price', 'cost_price', 'refund_amount', 'refund_method', 'reason'];
    protected $casts = ['quantity' => 'decimal:3', 'unit_price' => 'decimal:2', 'cost_price' => 'decimal:2', 'refund_amount' => 'decimal:2'];
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function item()
    {
        return $this->belongsTo(SaleItem::class, 'sale_item_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
