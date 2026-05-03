<?php

namespace App\Providers;

use App\Listeners\AuthActivityListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register auth activity listeners
        Event::listen(Login::class, [AuthActivityListener::class, 'handleLogin']);
        Event::listen(Logout::class, [AuthActivityListener::class, 'handleLogout']);

        // Public-form rate limiter — scoped per IP + path so each form has
        // its own bucket. See docs/PUBLIC_FORM_SECURITY.md.
        RateLimiter::for('public-form', fn (Request $request) => Limit::perMinute(5)
            ->by($request->ip().':'.$request->path()));
    }
}
