<?php

namespace App\Services\Contracts;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;

interface VehicleServiceInterface
{
    /**
     * @param string $key
     * @param mixed $value
     * @return Vehicle
     */
    public function findOneBy(string $key, mixed $value): Vehicle;

    /**
     * @param array<string, mixed> $attributes
     * @return Vehicle
     */
    public function create(array $attributes): Vehicle;

    /**
     * @param string $uuid
     * @param array<string, mixed> $attributes
     * @return Vehicle
     */
    public function update(string $uuid, array $attributes): Vehicle;

    /**
     * @param string $uuid
     * @return void
     */
    public function delete(string $uuid): void;

    /**
     * @param array<string, mixed> $filter
     * @return Builder
     */
    public function findVehicles(array $filter = []): Builder;
}
