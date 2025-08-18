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
    
    /**
     * Devuelve true si el producto tiene un precio de oferta activo.
     * (Este es tu código original, que usaremos en la vista).
     * @return bool
     */
    public function getIsOnSaleAttribute(): bool
    {
        return !is_null($this->precio_oferta) && $this->precio_oferta > 0;
    }
    
    /**
     * ¡NUEVO Y CRUCIAL!
     * Este accesor devuelve el precio de oferta si existe, de lo contrario, el precio normal.
     * Esta es ahora nuestra ÚNICA FUENTE DE VERDAD para el precio.
     * @return float
     */
    public function getPrecioFinalAttribute(): float
    {
        // Reutilizamos tu propia lógica 'is_on_sale' para mantener la consistencia.
        if ($this->is_on_sale) {
            return (float)$this->precio_oferta;
        }
        return (float)$this->precio;
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
