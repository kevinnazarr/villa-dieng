<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Named API limiters (Redis store via throttleWithRedis in
        // bootstrap/app.php). Keys carry the limiter name so the three
        // windows stay independent. CACHE_STORE stays file.
        RateLimiter::for('api', fn (Request $r) => Limit::perMinute(60)->by('api:'.$r->ip()));
        RateLimiter::for('auth-strict', fn (Request $r) => Limit::perMinute(10)->by('auth:'.$r->ip()));
        RateLimiter::for('booking', fn (Request $r) => Limit::perMinute(30)->by('booking:'.$r->ip()));
    }
}
