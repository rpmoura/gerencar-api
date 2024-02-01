<?php

namespace App\Listeners;

use App\Events\VehicleDeleted;
use App\Services\Contracts\VehicleServiceInterface;

class DisassociateUsers
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly VehicleServiceInterface $vehicleService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(VehicleDeleted $event): void
    {
        $this->vehicleService->disassociateUser(vehicleId: $event->vehicle->id, userId: null, withTrashed: true);
    }
}
