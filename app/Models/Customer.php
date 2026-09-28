<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model {
    protected $fillable=['name','phone','email','address','opening_due','status'];
    protected $casts=['opening_due'=>'decimal:2'];
    public function sales(){return $this->hasMany(Sale::class);}
    public function payments(){return $this->hasMany(CustomerPayment::class);}
    public function getCurrentDueAttribute(): float {
        return max(0, (float)$this->opening_due + (float)$this->sales()->sum('due') - (float)$this->payments()->sum('amount'));
    }
}
