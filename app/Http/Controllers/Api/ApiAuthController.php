<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class ApiAuthController extends Controller
{
    /**
     * API Login
     * Endpoint: POST /api/login
     * Body JSON: { "email": "...", "password": "..." }
     */
    public function login(Request $request)
    {
        // 1. Validate credentials
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string'
        ]);

        // 2. Find the user by email
        $user = User::where('email', $request->input('email'))->first();

        // 3. Verify password
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials.'
            ], 401);
        }

        // 4. Generate/get the api_token
        //    If an api_token already exists and you want to reuse it, just return it.
        //    If you prefer to generate a new one on each login, do it here:
        if (!$user->api_token) {
            $user->api_token = Str::random(60);
            $user->save();
        }

        // 5. Return the token (and optionally more user data)
        return response()->json([
            'message' => 'Token access generated',
            'token'   => $user->api_token
        ], 200);
    }
}
