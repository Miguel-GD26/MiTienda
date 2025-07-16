<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
   protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
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

}
