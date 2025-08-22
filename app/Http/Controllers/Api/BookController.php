<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $book = auth()->user()->books()->create($data);

        return response()->json([
            'message' => 'Book shared successfully',
            'book'    => $book
        ], 201);
    }

    public function nearby()
    {
        $user = auth()->user();

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
    }
}
