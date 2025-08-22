<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HealthController extends Controller
{
    public function index(): JsonResponse
    {
        $status = 'ok';
        $dbMessage = 'Database connected successfully';

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $status = 'error';
            $dbMessage = 'Database connection failed: ' . $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'app' => config('app.name'),
            'env' => config('app.env'),
            'version' => config('app.version'),
            'datetime' => Carbon::now()->toDateTimeString(),
            'database' => $dbMessage
        ], $status === 'ok' ? 200 : 500);
    }
}
