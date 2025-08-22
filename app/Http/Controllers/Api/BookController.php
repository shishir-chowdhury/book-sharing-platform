<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class BookController extends Controller
{
    /**
     * Store a new book
     */
    public function store(Request $request)
    {
        try {

            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException $e) {
                return response()->json(['message' => 'Token expired'], 401);
            } catch (TokenInvalidException $e) {
                return response()->json(['message' => 'Token invalid'], 401);
            } catch (JWTException $e) {
                return response()->json(['message' => 'Token not provided'], 401);
            }

            $data = $request->validate([
                'title'       => 'required|string|max:255',
                'author'      => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $book = $user->books()->create($data);

            return response()->json([
                'message' => 'Book shared successfully',
                'book'    => $book
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error sharing book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to share book',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nearby books
     */
    public function nearby()
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException $e) {
                return response()->json(['message' => 'Token expired'], 401);
            } catch (TokenInvalidException $e) {
                return response()->json(['message' => 'Token invalid'], 401);
            } catch (JWTException $e) {
                return response()->json(['message' => 'Token not provided'], 401);
            }

            if (! $user->location || $user->latitude === null || $user->longitude === null) {
                return response()->json(['message' => 'Your location is not set'], 422);
            }

            $radius = (int) env('SEARCH_RADIUS', 10000);
            $lat = $user->latitude;
            $lng = $user->longitude;

            $books = Book::selectRaw(
                'books.*, ST_Distance_Sphere(users.location, POINT(?, ?)) AS distance',
                [$lng, $lat]
            )
                ->join('users', 'users.id', '=', 'books.user_id')
                ->where('users.id', '!=', $user->id)
                ->having('distance', '<=', $radius)
                ->orderBy('distance')
                ->with(['user' => function ($q) {
                    $q->select('id', 'name');
                }])
                ->get();

            $result = $books->map(function ($b) {
                return [
                    'id'          => $b->id,
                    'title'       => $b->title,
                    'author'      => $b->author,
                    'description' => $b->description,
                    'distance_m'  => round($b->distance ?? 0, 0),
                    'user'        => [
                        'id'   => $b->user->id,
                        'name' => $b->user->name,
                    ],
                ];
            });

            return response()->json(['books' => $result]);

        } catch (\Exception $e) {
            Log::error('Error fetching nearby books', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to fetch nearby books',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
