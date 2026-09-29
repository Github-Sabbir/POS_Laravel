<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CustomerPayment extends Model
{
    protected $fillable = ['customer_id', 'sale_id', 'user_id', 'amount', 'payment_method', 'note', 'paid_at'];
    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'datetime'];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
