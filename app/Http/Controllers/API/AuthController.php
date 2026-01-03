<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Remappers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $plainToken = $user->createToken('auth_token')->plainTextToken;

        $tokenModel = $user->tokens()->latest()->first();
        $tokenModel->expires_at = Carbon::now()->addHours(10);
        $tokenModel->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $plainToken,
            'expires_at' => $tokenModel->expires_at,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek user
        $user = User::where('is_active',true)->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau Password Salah.'],
            ]);
        }

        // $user->tokens()->delete();

        $plainToken = $user->createToken('auth_token')->plainTextToken;

        $tokenModel = $user->tokens()->latest()->first();
        $tokenModel->expires_at = Carbon::now()->addHours(10);
        $tokenModel->save();

        $remapUser = Remappers::remapUser($user);

        return response()->json([
            'message' => 'Login successful.',
            'user' => $remapUser,
            'token' => $plainToken,
            'expires_at' => $tokenModel->expires_at,
        ]);
    }
    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful.'
        ]);
    }
}
