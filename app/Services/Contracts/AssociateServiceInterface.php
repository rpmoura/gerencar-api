<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface AssociateServiceInterface
{
    /**
     * @param string $user
     * @param string $vehicle
     * @return array
     */
    public function create(string $user, string $vehicle): array;

    /**
     * @param string $user
     * @param string $vehicle
     * @return int
     */
    public function delete(string $user, string $vehicle): int;

    /**
     * @param string $user
     * @return Builder
     */
    public function listUserVehicles(string $user): Builder;
}
