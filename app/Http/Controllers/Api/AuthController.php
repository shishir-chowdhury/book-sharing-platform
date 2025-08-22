<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
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
        $user->password  = $data['password'];
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
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
