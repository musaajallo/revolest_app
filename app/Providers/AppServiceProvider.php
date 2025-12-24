<?php

namespace App\Providers;

use App\Listeners\AuthActivityListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
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
    }
}
