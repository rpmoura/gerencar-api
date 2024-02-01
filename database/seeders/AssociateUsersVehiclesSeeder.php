<?php

namespace Database\Seeders;

use App\Models\{User, Vehicle};
use Illuminate\Database\Seeder;

class AssociateUsersVehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users    = User::query()->get();
        $vehicles = Vehicle::query()->get();

        foreach ($users as $user) {
            $user->vehicles()->attach($vehicles->random(3));
        }
    }
}
