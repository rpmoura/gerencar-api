<?php

namespace App\Services\Vehicles;

use App\Exceptions\RepositoryException;
use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Services\Contracts\VehicleServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VehicleService implements VehicleServiceInterface
{
    public function __construct(private readonly VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function create(array $attributes): Vehicle
    {
        return $this->vehicleRepository->create($attributes);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Vehicle
     */
    public function findOneBy(string $key, mixed $value): Vehicle
    {
        $vehicle = $this->vehicleRepository->findOneBy($key, $value);

        if (!$vehicle instanceof Vehicle) {
            throw new NotFoundHttpException(__('exception.vehicle.not_found'));
        }

        return $vehicle;
    }

    /**
     * @param string $uuid
     * @param array<string, mixed> $attributes
     * @return Vehicle
     */
    public function update(string $uuid, array $attributes): Vehicle
    {
        $vehicle = $this->findOneBy('uuid', $uuid);

        return $this->vehicleRepository->update($attributes, $vehicle->id);
    }

    /**
     * @param string $uuid
     * @return void
     */
    public function delete(string $uuid): void
    {
        $vehicle = $this->findOneBy('uuid', $uuid);

        $result = $this->vehicleRepository->delete($vehicle->id);

        if (!$result) {
            throw new RepositoryException(__('exception.vehicle.delete_unsuccessfully'));
        }
    }

    public function findVehicles(array $filter = []): Builder
    {
        return $this->vehicleRepository->findVehicles();
    }
}
