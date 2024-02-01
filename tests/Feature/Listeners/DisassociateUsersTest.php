<?php

namespace Tests\Feature\Listeners;

use App\Events\VehicleDeleted;
use App\Listeners\DisassociateUsers;
use App\Models\{User, Vehicle};
use App\Services\Contracts\VehicleServiceInterface;
use Tests\TestCase;

class DisassociateUsersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function shouldDisassociateCars()
    {
        $otherVehicle = Vehicle::factory()
            ->has(User::factory()->count(4))
            ->create();

        $vehicle = Vehicle::factory()
            ->has(User::factory()->count(2))
            ->create();

        $event    = new VehicleDeleted($vehicle);
        $listener = new DisassociateUsers($this->app->make(VehicleServiceInterface::class));

        $listener->handle($event);

        $this->assertDatabaseMissing('users_x_vehicles', ['vehicle_id' => $vehicle->id]);
        $this->assertDatabaseHas('users_x_vehicles', ['vehicle_id' => $otherVehicle->id]);
        $this->assertDatabaseCount('users_x_vehicles', 4);
    }
}
