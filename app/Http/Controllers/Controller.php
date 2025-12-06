<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Dokumentasi API Portal Berita",
 * description="Dokumentasi lengkap endpoint API untuk Auth, Berita, dan Komentar.",
 * @OA\Contact(
 * email="admin@example.com"
 * ),
 * )
 *
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="API Server Utama"
 * )
 *
 * @OA\SecurityScheme(
 * type="http",
 * description="Gunakan token yang didapat dari endpoint /login",
 * name="Authorization",
 * in="header",
 * scheme="bearer",
 * bearerFormat="JWT",
 * securityScheme="apiAuth",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
