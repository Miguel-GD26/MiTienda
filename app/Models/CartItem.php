<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model {
    use HasFactory;
    protected $fillable = ['cart_id', 'producto_id', 'cantidad'];
    public function cart() { return $this->belongsTo(Cart::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}