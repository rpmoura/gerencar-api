<?php

namespace App\Services\Contracts;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

interface UserServiceInterface
{
    /**
     * @param string $key
     * @param mixed $value
     * @return User
     */
    public function findOneBy(string $key, mixed $value): User;

    /**
     * @param array<string, mixed> $attributes
     * @return User
     */
    public function create(array $attributes): User;

    /**
     * @param string $uuid
     * @param array<string, mixed> $attributes
     * @return User
     */
    public function update(string $uuid, array $attributes): User;

    /**
     * @param string $uuid
     * @return void
     */
    public function delete(string $uuid): void;

    /**
     * @return UserRepositoryInterface
     */
    public function findUsers(): UserRepositoryInterface;

    /**
     * @param int $userId
     * @param int $vehicleId
     * @return array
     */
    public function associateCar(int $userId, int $vehicleId): array;

    /**
     * @param int $userId
     * @param int $vehicleId
     * @return int
     */
    public function disassociateCar(int $userId, int $vehicleId): int;
}
