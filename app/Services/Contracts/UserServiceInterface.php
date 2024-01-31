<?php

namespace App\Services\Contracts;

use App\Models\User;

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

}
