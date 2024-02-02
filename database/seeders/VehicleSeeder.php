<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicle        = new Vehicle();
        $vehicle->brand = 'VW';
        $vehicle->model = 'Fusca';
        $vehicle->save();

        $vehicle        = new Vehicle();
        $vehicle->brand = 'VW';
        $vehicle->model = 'Gol';
        $vehicle->save();

        $vehicle        = new Vehicle();
        $vehicle->brand = 'Fiat';
        $vehicle->model = '147';
        $vehicle->save();

        $vehicle        = new Vehicle();
        $vehicle->brand = 'Fiat';
        $vehicle->model = 'Uno';
        $vehicle->save();
    }
}
