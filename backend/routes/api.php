<?php

use App\Http\Controllers\Api\V1\Admin\AvailabilityBlockController as AdminAvailabilityBlockController;
use App\Http\Controllers\Api\V1\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AvailabilityController;
use App\Http\Controllers\Api\V1\CabinController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

// Stock route — kept untouched.
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('throttle:api')->group(function () {
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

    Route::get('/me', [MeController::class, 'show'])->middleware('auth:sanctum');

    Route::post('/probe/validation', function (Request $request) {
        $validated = $request->validate(['email' => 'required|email']);

        return response()->json(['data' => $validated]);
    });

    // Public catalog (property-scoped cabins: slug is unique per property).
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::get('/properties/{property:slug}', [PropertyController::class, 'show']);
    Route::get('/properties/{property:slug}/cabins', [CabinController::class, 'index']);
    Route::get('/properties/{property:slug}/cabins/{cabin:slug}', [CabinController::class, 'show']);

    Route::get('/availability', [AvailabilityController::class, 'show']);

    // Guest checkout (booking_code + guest_email; mismatch → 404).
    Route::post('/reservations', [ReservationController::class, 'store'])->middleware('throttle:booking');
    Route::get('/reservations/{reservation:booking_code}', [ReservationController::class, 'show']);
    Route::post('/reservations/{reservation:booking_code}/cancel', [ReservationController::class, 'cancel']);
    Route::post('/reservations/{reservation:booking_code}/payments', [PaymentController::class, 'initiate'])->middleware('throttle:booking');
    Route::post('/payments/{payment}/process', [PaymentController::class, 'process'])->middleware('throttle:booking');

    // Auth (role never accepted from input; token expiration null).
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:auth-strict');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:auth-strict');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Authenticated customer.
    Route::get('/my/reservations', [MeController::class, 'reservations'])->middleware('auth:sanctum');

    // Admin (auth + EnsureAdmin → 403).
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/reservations', [AdminReservationController::class, 'index']);
        Route::get('/reservations/{reservation}', [AdminReservationController::class, 'show']);
        Route::post('/reservations/{reservation}/confirm', [AdminReservationController::class, 'confirm']);
        Route::post('/reservations/{reservation}/cancel', [AdminReservationController::class, 'cancel']);
        Route::get('/availability-blocks', [AdminAvailabilityBlockController::class, 'index']);
        Route::post('/availability-blocks', [AdminAvailabilityBlockController::class, 'store']);
        Route::delete('/availability-blocks/{block}', [AdminAvailabilityBlockController::class, 'destroy']);
    });
});
