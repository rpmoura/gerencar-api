<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user           = new User();
        $user->uuid     = Str::uuid();
        $user->email    = 'admin@email.com';
        $user->password = 'admin';
        $user->save();

        $user           = new User();
        $user->uuid     = Str::uuid();
        $user->email    = 'user@email.com';
        $user->password = 'password';
        $user->save();
    }
}
