<?php

namespace App\Providers;

use App\Models\Pengembalian;
use App\Observers\PengembalianObserver;
use App\Http\Responses\FilamentLogoutToLoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as FilamentLogoutResponseContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FilamentLogoutResponseContract::class, FilamentLogoutToLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Pengembalian::observe(PengembalianObserver::class);
    }
}
