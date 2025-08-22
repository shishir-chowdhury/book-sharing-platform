<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {

            $data = $request->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|email|unique:users',
                'password'  => 'required|confirmed|min:6',
                'latitude'  => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $user = new User();
            $user->name      = $data['name'];
            $user->email     = $data['email'];
            $user->password = $data['password'];
            $user->latitude  = $data['latitude'];
            $user->longitude = $data['longitude'];
            $user->save();

            DB::statement(
                'UPDATE users SET location = POINT(?, ?) WHERE id = ?',
                [$user->longitude, $user->latitude, $user->id]
            );

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'User registered successfully',
                'user'    => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'email'     => $user->email,
                    'latitude'  => $user->latitude,
                    'longitude' => $user->longitude,
                ],
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error registering user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Registration failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user and return JWT token
     */
    public function login(Request $request)
    {
        try {

            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required'
            ]);

            if (! $token = auth('api')->attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            return response()->json([
                'message' => 'Login successful',
                'token'   => $token
            ]);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            Log::error('JWT error during login', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Could not create token',
                'error'   => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error during login', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return authenticated user info
     */
    public function me()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Error fetching authenticated user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Could not fetch user',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
