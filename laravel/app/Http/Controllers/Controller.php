<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
* @OA\Info(
*    title="BeeLegacy",
*    description="API para plataforma BeeLegacy",
*    version="1.0.0",
*    @OA\Contact(
*       email="ricardo@empdigital.cl"
*    )
* )
* @OA\Tag(
*     name="Users",
*     description="Todos los métodos de Autenticación"
* )
* @OA\Tag(
*     name="Customer",
*     description="Todos los métodos de Clientes"
* )
* @OA\Server(url="http://localhost:83/api/")
* @OA\SecurityScheme(
*   securityScheme="token",
*   type="apiKey",
*   in="header",
*   name="Authorization"
* )
*/

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
