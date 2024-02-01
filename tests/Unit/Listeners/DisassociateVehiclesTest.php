<?php

namespace Tests\Unit\Listeners;

use App\Events\UserDeleted;
use App\Listeners\DisassociateVehicles;
use App\Models\User;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Users\UserService;
use Tests\TestCase;

class DisassociateVehiclesTest extends TestCase
{
    private readonly UserServiceInterface $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = \Mockery::mock(UserService::class);
        $this->app->instance(UserServiceInterface::class, $this->userService);
    }

    /**
     * @test
     */
    public function shouldDisassociateCars()
    {
        $user     = User::factory()->create();
        $event    = new UserDeleted($user);
        $listener = new DisassociateVehicles($this->userService);

        $this->userService->shouldReceive('disassociateCar')->once()->withArgs([$user->id, null, true])->andReturn(1);

        $listener->handle($event);
    }
}
