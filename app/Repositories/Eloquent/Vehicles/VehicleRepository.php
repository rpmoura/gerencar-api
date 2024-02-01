<?php

namespace App\Repositories\Eloquent\Vehicles;

use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository as EloquentBaseRepository;

class VehicleRepository extends EloquentBaseRepository implements VehicleRepositoryInterface
{
    public function model(): string
    {
        return Vehicle::class;
    }
}
