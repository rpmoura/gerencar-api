<?php

namespace Tests\Unit\Services\Associate;

use App\Models\{User, Vehicle};
use App\Services\Associate\AssociateService;
use App\Services\Contracts\{AssociateServiceInterface, UserServiceInterface, VehicleServiceInterface};
use App\Services\Users\UserService;
use App\Services\Vehicles\VehicleService;
use Illuminate\Database\Eloquent\Builder;
use Tests\TestCase;

class AssociateServiceTest extends TestCase
{
    private readonly UserServiceInterface $userService;

    private readonly VehicleServiceInterface $vehicleService;

    private readonly AssociateServiceInterface $associateService;

    public function setUp(): void
    {
        parent::setUp();

        $this->userService = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->vehicleService = $this->getMockBuilder(VehicleService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->associateService = new AssociateService(
            userService: $this->userService,
            vehicleService: $this->vehicleService
        );
    }

    /**
     * @test
     */
    public function shouldAssociateVehicle()
    {
        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->userService->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $user->uuid)
            ->willReturn($user);

        $this->vehicleService->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $vehicle->uuid)
            ->willReturn($vehicle);

        $result = $this->associateService->create($user->uuid, $vehicle->uuid);

        $this->assertIsArray($result);
    }

    /**
     * @test
     */
    public function shouldDisassociateVehicle()
    {
        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->userService->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $user->uuid)
            ->willReturn($user);

        $this->vehicleService->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $vehicle->uuid)
            ->willReturn($vehicle);

        $result = $this->associateService->delete($user->uuid, $vehicle->uuid);

        $this->assertIsInt($result);
    }

    /**
     * @test
     */
    public function shouldListUserVehicles()
    {
        $user = User::factory()
            ->has(Vehicle::factory())
            ->create();

        $this->userService->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $user->uuid)
            ->willReturn($user);

        $mockBuilder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->vehicleService->expects($this->once())
            ->method('findVehicles')
            ->with(['user' => $user->id])
            ->willReturn($mockBuilder);

        $result = $this->associateService->listUserVehicles($user->uuid);

        $this->assertInstanceOf(Builder::class, $result);
    }
}
