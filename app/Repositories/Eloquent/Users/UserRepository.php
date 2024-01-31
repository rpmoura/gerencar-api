<?php

namespace App\Repositories\Eloquent\Users;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository as EloquentBaseRepository;

class UserRepository extends EloquentBaseRepository implements UserRepositoryInterface
{
    public function model(): string
    {
        return User::class;
    }
}
