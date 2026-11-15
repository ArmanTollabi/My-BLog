<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

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
        Paginator::defaultView('vendor.pagination.glass');
        
         if (app()->environment('production')) {
        URL::forceScheme('https');
    }

        RateLimiter::for('login',function (Request $request){
            return Limit::perMinute(6)->by($request->ip());
        });

        RateLimiter::for('api',function (Request $request){
            return $request->user() ? Limit::perMinute(30) ->by('user: '.$request->user()->id) :
                Limit::perMinute(10)->by('ip:'.$request->ip());
        });
    }
}
