<?php

namespace App\Providers;

use App\Services\AlbumService;
use App\Services\AuthService;
use App\Services\Interfaces\AlbumServiceInterface;
use App\Services\Interfaces\AuthServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AlbumServiceInterface::class, AlbumService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
