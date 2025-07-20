<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseControllerLaravel; 

/**
 * @method
 */
class Controller extends BaseControllerLaravel 
{
    use AuthorizesRequests, ValidatesRequests; 
}