<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
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

        // Buat token dengan masa berlaku 1 hari
        $tokenResult = $user->createToken('token');
        $token = $tokenResult->accessToken;

        // atur expire
        $tokenModel = $tokenResult->token;
        $tokenModel->expires_at = now()->addDay(); // 1 hari
        $tokenModel->save();

        return response()->json([
            'message' => 'Register berhasil',
            'token' => $token,
            'expired_at' => $tokenModel->expires_at->toDateTimeString(),
        ]);
    }


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

        // Buat token dengan masa berlaku 1 hari
        $tokenResult = $user->createToken('token');
        $token = $tokenResult->accessToken;

        $tokenModel = $tokenResult->token;
        $tokenModel->expires_at = now()->addDay(); // 1 hari
        $tokenModel->save();

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'expired_at' => $tokenModel->expires_at->toDateTimeString(),
        ]);
    }

    public function getUser(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }
}
