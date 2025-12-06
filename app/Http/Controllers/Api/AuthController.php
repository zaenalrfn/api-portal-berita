<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\News;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/register",
     * tags={"Authentication"},
     * summary="Daftar akun baru",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="name", type="string", example="Budi"),
     * @OA\Property(property="email", type="string", format="email", example="budi@mail.com"),
     * @OA\Property(property="password", type="string", format="password", example="secret123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="secret123"),
     * )
     * )
     * ),
     * @OA\Response(response="201", description="Registrasi Berhasil")
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('user');

        // Buat token (personal access token)
        $tokenResult = $user->createToken('token');
        $accessToken = $tokenResult->accessToken;

        // Ambil model token yang dibuat Passport (jika tersedia)
        $tokenModel = $tokenResult->token;

        // Ambil expiry yang diset Passport; jika kosong, gunakan fallback 1 hari
        $expiresAt = $tokenModel && $tokenModel->expires_at
            ? $tokenModel->expires_at
            : now()->addDay();

        return response()->json([
            'message' => 'Register berhasil',
            'token' => $accessToken,
            'expired_at' => Carbon::parse($expiresAt)->toDateTimeString(),
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * @OA\Post(
     * path="/api/login",
     * tags={"Authentication"},
     * summary="Masuk ke dalam sistem",
     * description="Login user dan kembalikan token",
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="email", type="string", format="email", example="user@mail.com"),
     * @OA\Property(property="password", type="string", format="password", example="secret123"),
     * )
     * )
     * ),
     * @OA\Response(response="200", description="Login Berhasil"),
     * @OA\Response(response="401", description="Unauthorized")
     * )
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah'],
            ]);
        }

        // Buat token (personal access token)
        $tokenResult = $user->createToken('token');
        $accessToken = $tokenResult->accessToken;

        $tokenModel = $tokenResult->token;

        $expiresAt = $tokenModel && $tokenModel->expires_at
            ? $tokenModel->expires_at
            : now()->addDay();

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $accessToken,
            'expired_at' => Carbon::parse($expiresAt)->toDateTimeString(),
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * @OA\Get(
     * path="/api/user",
     * tags={"Authentication"},
     * summary="Ambil data user yang sedang login",
     * security={{"apiAuth":{}}},
     * @OA\Response(response="200", description="Data User")
     * )
     */
    public function getUser(Request $request)
    {
        $user = $request->user();
        $totalUserNews = News::where('user_id', $user->id)->count();

        return response()->json([
            'status' => true,
            'user' => $user,
            'total_news' => $totalUserNews ?? 0
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/logout",
     * tags={"Authentication"},
     * summary="Keluar dari sistem",
     * security={{"apiAuth":{}}},
     * @OA\Response(response="200", description="Logout Berhasil")
     * )
     */
    public function logout(Request $request)
    {
        // ambil access token model
        $accessToken = $request->user()->token();

        if ($accessToken) {
            // Revoke access token
            $accessToken->revoke();

            // Revoke refresh tokens yang terkait (jika ada)
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update(['revoked' => true]);
        }

        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }
}
