<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{

    const UMBRAL_STOCK_BAJO = 5;
    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'precio_oferta',
        'stock',
        'imagen_url',
        'empresa_id',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    public function getIsOnSaleAttribute(): bool
    {
        return !is_null($this->precio_oferta) && $this->precio_oferta > 0;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->stock <= 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock > 0 && $this->stock <= self::UMBRAL_STOCK_BAJO;
    }

}
