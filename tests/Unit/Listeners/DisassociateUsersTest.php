<?php

namespace Tests\Unit\Listeners;

use App\Events\VehicleDeleted;
use App\Listeners\DisassociateUsers;
use App\Models\Vehicle;
use App\Services\Contracts\VehicleServiceInterface;
use App\Services\Vehicles\VehicleService;
use Tests\TestCase;

class DisassociateUsersTest extends TestCase
{
    private readonly VehicleServiceInterface $vehicleService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vehicleService = \Mockery::mock(VehicleService::class);
        $this->app->instance(VehicleServiceInterface::class, $this->vehicleService);
    }

    /**
     * @test
     */
    public function shouldDisassociateCars()
    {
        $vehicle  = Vehicle::factory()->create();
        $event    = new VehicleDeleted($vehicle);
        $listener = new DisassociateUsers($this->vehicleService);

        $this->vehicleService
            ->shouldReceive('disassociateUser')
            ->once()
            ->withArgs([$vehicle->id, null, true])
            ->andReturn(1);

        $listener->handle($event);
    }
}
