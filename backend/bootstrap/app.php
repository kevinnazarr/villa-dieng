<?php

use App\Exceptions\BookingConflictException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['admin' => \App\Http\Middleware\EnsureAdmin::class]);

        // Named limiters on Redis explicitly — CACHE_STORE stays file.
        // Limiter definitions live in AppServiceProvider::boot (facades
        // are not available inside this closure).
        $middleware->throttleWithRedis();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Consistent API error envelope. Validation stays distinguishable
        // via `errors`. Domain double-booking conflicts map to 409 here.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            if ($e instanceof BookingConflictException) {
                return response()->json(['message' => 'BOOKING_CONFLICT'], 409);
            }

            if (BookingConflictException::isExclusionViolation($e)) {
                return response()->json(['message' => 'BOOKING_CONFLICT'], 409);
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], $e->status);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json(['message' => 'Not Found.'], 404);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            if ($e instanceof AuthorizationException) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            if ($e instanceof InvalidArgumentException) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            // Sanitized fallback: preserve HTTP status, never leak internals.
            // Exact HttpException (e.g. abort(403, 'Forbidden.')) carries a
            // safe explicit message; anything else stays generic.
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                return response()->json(['message' => $e->getMessage() ?: 'Server Error.'], $e->getStatusCode());
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                return response()->json(['message' => 'Server Error.'], $e->getStatusCode());
            }

            if ($e instanceof \Throwable) {
                return response()->json(['message' => 'Server Error.'], 500);
            }

            return null;
        });
    })->create();
