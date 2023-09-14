<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="API-интерфейсы для приложения",
 *    version="1.0.0",
 * ),
 *
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
