<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Model::shouldBeStrict(! $this->app->isProduction());

        $this->configurePasswordDefaults();
        $this->configureRateLimiting();
    }

    /**
     * Stricter password requirements outside of local development.
     */
    private function configurePasswordDefaults(): void
    {
        Password::defaults(fn () => $this->app->isProduction()
            ? Password::min(8)->letters()->mixedCase()->numbers()->uncompromised()
            : Password::min(8)->letters()->numbers());
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(5)->by(
            $request->string('email')->lower()->append('|', $request->ip())->toString(),
        ));
    }
}
