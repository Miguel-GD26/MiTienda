<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'empresa_id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    protected static function boot()
    {
        parent::boot();

        if (Auth::check() && Auth::user()->empresa_id) {
            static::addGlobalScope('empresa', function ($builder) {
                $builder->where('empresa_id', Auth::user()->empresa_id);
            });
        }
    }

}