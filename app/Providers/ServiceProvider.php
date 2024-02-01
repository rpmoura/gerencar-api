<?php

namespace App\Providers;

use App\Services\Associate\AssociateService;
use App\Services\Contracts\{AssociateServiceInterface, UserServiceInterface, VehicleServiceInterface};
use App\Services\Users\UserService;
use App\Services\Vehicles\VehicleService;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(VehicleServiceInterface::class, VehicleService::class);
        $this->app->bind(AssociateServiceInterface::class, AssociateService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
