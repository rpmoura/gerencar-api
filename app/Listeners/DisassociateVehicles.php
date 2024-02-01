<?php

namespace App\Listeners;

use App\Events\UserDeleted;
use App\Services\Contracts\UserServiceInterface;

class DisassociateVehicles
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly UserServiceInterface $userService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(UserDeleted $event): void
    {
        $this->userService->disassociateCar(userId: $event->user->id, vehicleId: null, withTrashed: true);
    }
}
