<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Supplier extends Model
{
    protected $fillable = ['name', 'company', 'phone', 'email', 'address', 'opening_due', 'status'];
    protected $casts = ['opening_due' => 'decimal:2'];
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
    public function getCurrentDueAttribute()
    {
        return max(0, (float) $this->opening_due + (float) $this->purchases()->sum('total') - (float) $this->purchases()->sum('paid') - (float) $this->payments()->sum('amount'));
    }
}
