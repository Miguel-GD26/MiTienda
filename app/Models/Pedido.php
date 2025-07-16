<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['empresa_id', 'cliente_id', 'total', 'notas', 'estado'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
}
