<?php

namespace App\Providers;

use App\Models\UserType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Model::unguard();

        Gate::define('admin', fn() => auth()->hasUser() && auth()->user()->type == UserType::Admin);

        Blade::if('admin', fn() => Gate::allows('admin'));
    }
}
