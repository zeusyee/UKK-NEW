<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    /**
     * Login dengan email dan password
     *
     * @bodyParam email string required Email user
     * @bodyParam password string required Password user
     * @response 200 {"token": "...", "user": {...}}
     * @response 401 {"message": "Invalid credentials"}
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            
            // Only allow members to login via API
            if ($user->role !== 'member') {
                Auth::logout();
                return response()->json(['message' => 'Hanya member yang dapat login via API'], 403);
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => $this->formatUser($user)
            ], 200);
        }

        return response()->json(['message' => 'Email atau password salah'], 401);
    }

    /**
     * Registrasi user baru
     *
     * @bodyParam name string required Nama user
     * @bodyParam email string required Email user
     * @bodyParam password string required Password (min 6 karakter)
     * @bodyParam password_confirmation string required Konfirmasi password
     * @response 201 {"message": "User berhasil didaftarkan", "user": {...}}
     * @response 422 {"errors": {...}}
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate username from email (part before @)
        $username = explode('@', $request->email)[0];
        
        // Ensure username is unique by adding numbers if needed
        $base_username = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base_username . $counter;
            $counter++;
        }

        $user = User::create([
            'username' => $username,
            'full_name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member', // Default role untuk API adalah member
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User berhasil didaftarkan',
            'token' => $token,
            'user' => $this->formatUser($user)
        ], 201);
    }

    /**
     * Login dengan Google OAuth
     *
     * @bodyParam google_token string required Google ID Token
     * @response 200 {"token": "...", "user": {...}}
     * @response 422 {"message": "Invalid token"}
     */
    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // TODO: Implementasi Google OAuth verification
            // Verify token dengan Google OAuth Library
            
            return response()->json(['message' => 'Google login belum diimplementasi'], 501);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token tidak valid'], 401);
        }
    }

    /**
     * Logout (hapus token)
     *
     * @response 200 {"message": "Logout berhasil"}
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }

    /**
     * Ambil profil user yang sedang login
     *
     * @response 200 {"user": {...}}
     */
    public function getProfile(Request $request)
    {
        return response()->json([
            'user' => $this->formatUser($request->user())
        ], 200);
    }

    /**
     * Update profil user
     *
     * @bodyParam name string Nama user
     * @bodyParam email string Email user
     * @response 200 {"message": "Profil berhasil diupdate", "user": {...}}
     * @response 422 {"errors": {...}}
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->user_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $user->full_name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diupdate',
            'user' => $this->formatUser($user)
        ], 200);
    }

    /**
     * Format user data untuk response
     */
    private function formatUser($user)
    {
        return [
            'id' => $user->user_id,
            'name' => $user->full_name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
        ];
    }
}
