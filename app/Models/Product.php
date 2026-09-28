<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class Product extends Model {
    use SoftDeletes;
    protected $fillable=['sku','category_id','brand_id','name','unit','purchase_price','selling_price','wholesale_price','current_stock','minimum_stock','image','description','status'];
    protected $casts=['purchase_price'=>'decimal:2','selling_price'=>'decimal:2','wholesale_price'=>'decimal:2','current_stock'=>'decimal:3','minimum_stock'=>'decimal:3'];
    protected static function booted(): void {
        static::creating(function(Product $product){
            if(blank($product->sku)){
                do{$sku='PRD-'.strtoupper(Str::random(10));}while(static::withTrashed()->where('sku',$sku)->exists());
                $product->sku=$sku;
            }
        });
    }
    public function category(){return $this->belongsTo(Category::class);}
    public function brand(){return $this->belongsTo(Brand::class);}
    public function barcodes(){return $this->hasMany(ProductBarcode::class);}
    public function stockMovements(){return $this->hasMany(StockMovement::class);}
}
