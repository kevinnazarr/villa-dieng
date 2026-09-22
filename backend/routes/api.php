<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        $checks = ['database' => 'down', 'redis' => 'down'];

        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (Throwable) {
            // stays 'down'
        }

        try {
            Redis::connection()->ping();
            $checks['redis'] = 'ok';
        } catch (Throwable) {
            // stays 'down'
        }

        $ready = ! in_array('down', $checks, true);

        return response()->json(
            ['status' => $ready ? 'ok' : 'degraded', 'checks' => $checks],
            $ready ? 200 : 503
        );
    });

    Route::get('/me', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/probe/validation', function (Request $request) {
        $validated = $request->validate(['email' => 'required|email']);

        return response()->json(['data' => $validated]);
    });
});