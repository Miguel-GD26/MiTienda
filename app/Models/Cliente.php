<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'telefono', 'user_id',];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'cliente_empresa')->withTimestamps();

    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
