<?php

namespace App\Services\Associate;

use App\Services\Contracts\{AssociateServiceInterface, UserServiceInterface, VehicleServiceInterface};
use Illuminate\Database\Eloquent\Builder;

class AssociateService implements AssociateServiceInterface
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly VehicleServiceInterface $vehicleService
    ) {
    }

    public function create(string $user, string $vehicle): array
    {
        $user    = $this->userService->findOneBy('uuid', $user);
        $vehicle = $this->vehicleService->findOneBy('uuid', $vehicle);

        return $this->userService->associateCar($user->id, $vehicle->id);
    }

    /**
     * @param string $user
     * @param string $vehicle
     * @return int
     */
    public function delete(string $user, string $vehicle): int
    {
        $user    = $this->userService->findOneBy('uuid', $user);
        $vehicle = $this->vehicleService->findOneBy('uuid', $vehicle);

        return $this->userService->disassociateCar($user->id, $vehicle->id);
    }

    /**
     * @param string $user
     * @return Builder
     */
    public function listUserVehicles(string $user): Builder
    {
        $user = $this->userService->findOneBy('uuid', $user);

        $filter = [
            'user' => $user->id,
        ];

        return $this->vehicleService->findVehicles($filter);
    }
}
