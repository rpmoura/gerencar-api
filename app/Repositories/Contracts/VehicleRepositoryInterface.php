<?php

namespace App\Repositories\Contracts;

use App\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

interface VehicleRepositoryInterface extends RepositoryInterface
{
    public function findVehicles(array $filter = []): Builder;
}
