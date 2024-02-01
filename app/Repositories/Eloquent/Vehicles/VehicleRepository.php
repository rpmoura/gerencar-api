<?php

namespace App\Repositories\Eloquent\Vehicles;

use App\Exceptions\RepositoryException;
use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository as EloquentBaseRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class VehicleRepository extends EloquentBaseRepository implements VehicleRepositoryInterface
{
    public function model(): string
    {
        return Vehicle::class;
    }

    /**
     * @param array<string, mixed> $filter
     * @return Builder
     * @throws RepositoryException
     * @throws BindingResolutionException
     */
    public function findVehicles(array $filter = []): Builder
    {
        $filter = new Fluent($filter);

        return $this
            ->makeModel()
            ->query()
            ->when($filter->offsetExists('user'), function ($query) use ($filter) {
                $query->whereHas('users', function ($query) use ($filter) {
                    $query->where('id', $filter->get('user'));
                });
            });
    }
}
