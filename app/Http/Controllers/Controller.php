<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseControllerLaravel; // Renombrado para claridad

/**
 * @method \Illuminate\Routing\ControllerMiddlewareOptions middleware(string|array $middleware, array $options = [])
 */
class Controller extends BaseControllerLaravel // <--- AHÍ ESTÁ LA CLAVE
{
    use AuthorizesRequests, ValidatesRequests; // Los traits se usan aquí, en la clase base
}