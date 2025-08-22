<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::select('id','name','email')->get();
        return response()->json(['users' => $users]);
    }

    public function books()
    {
        $books = Book::with('user:id,name')->get()->map(function ($b) {
            return [
                'id'     => $b->id,
                'title'  => $b->title,
                'author' => $b->author,
                'user'   => ['id' => $b->user->id, 'name' => $b->user->name],
            ];
        });

        return response()->json(['books' => $books]);
    }

    public function deleteBook($id)
    {
        try {
            $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        if (! $user->is_admin) {
            return response()->json(['message' => 'Unauthorized. Only admins can delete books.'], 403);
        }

        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(['message' => 'Book deleted successfully']);
    }

}
