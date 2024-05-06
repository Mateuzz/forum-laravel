<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Lib\HtmlCleaner;

class HtmlCleanerServiceProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {
        $this->app->singleton('html_cleaner',  HtmlCleaner::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
    }
}
