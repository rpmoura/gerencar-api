<?php

namespace App\Providers;

use App\Repositories\Contracts\{UserRepositoryInterface, VehicleRepositoryInterface};
use App\Repositories\Eloquent\Users\UserRepository;
use App\Repositories\Eloquent\Vehicles\VehicleRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
