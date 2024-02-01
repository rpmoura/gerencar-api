<?php

namespace Tests\Feature\Listeners;

use App\Events\UserDeleted;
use App\Listeners\DisassociateVehicles;
use App\Models\{User, Vehicle};
use App\Services\Contracts\UserServiceInterface;
use Tests\TestCase;

class DisassociateVehiclesTest extends TestCase
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
        $otherUser = User::factory()
            ->has(Vehicle::factory()->count(3))
            ->create();

        $user = User::factory()
            ->has(Vehicle::factory()->count(3))
            ->create();

        $event    = new UserDeleted($user);
        $listener = new DisassociateVehicles($this->app->make(UserServiceInterface::class));

        $listener->handle($event);

        $this->assertDatabaseMissing('users_x_vehicles', ['user_id' => $user->id]);
        $this->assertDatabaseHas('users_x_vehicles', ['user_id' => $otherUser->id]);
        $this->assertDatabaseCount('users_x_vehicles', 3);
    }
}
