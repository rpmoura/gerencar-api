<?php

namespace App\Services\Users;

use App\Exceptions\RepositoryException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class UserService implements UserServiceInterface
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function create(array $attributes): User
    {
        return $this->userRepository->create($attributes);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return User
     */
    public function findOneBy(string $key, mixed $value): User
    {
        $user = $this->userRepository->findOneBy($key, $value);

        if (!$user instanceof User) {
            throw new NotFoundHttpException(__('exception.user.not_found'));
        }

        return $user;
    }

    /**
     * @param string $uuid
     * @param array<string, mixed> $attributes
     * @return User
     */
    public function update(string $uuid, array $attributes): User
    {
        $user = $this->findOneBy('uuid', $uuid);

        return $this->userRepository->update($attributes, $user->id);
    }

    /**
     * @param string $uuid
     * @return void
     */
    public function delete(string $uuid): void
    {
        $user = $this->findOneBy('uuid', $uuid);

        $result = $this->userRepository->delete($user->id);

        if (!$result) {
            throw new RepositoryException(__('exception.user.delete_unsuccessfully'));
        }
    }

    public function findUsers(): UserRepositoryInterface
    {
        return $this->userRepository;
    }
}
