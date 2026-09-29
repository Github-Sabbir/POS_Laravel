<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SupplierPayment extends Model
{
    protected $fillable = ['supplier_id', 'purchase_id', 'user_id', 'amount', 'payment_method', 'note', 'paid_at'];
    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'datetime'];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
