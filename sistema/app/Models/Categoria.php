<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //protected $primeryKey='id';
    //protected $table='categorias';
    protected $fillable=['nombre','descripcion'];    
}
