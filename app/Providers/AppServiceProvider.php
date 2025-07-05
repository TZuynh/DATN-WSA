<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
        try {
            if (\Schema::hasTable('settings')) {
                Log::info('[Settings] Table exists.');
            } else {
                Log::warning('[Settings] Table does NOT exist!');
            }
        } catch (\Exception $e) {
            Log::error('[Settings] Exception: ' . $e->getMessage());
        }
        Paginator::useBootstrapFive();
    }
}
