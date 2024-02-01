<?php

namespace Tests\Unit\Services\Vehicles;

use App\Events\VehicleDeleted;
use App\Exceptions\RepositoryException;
use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Eloquent\Vehicles\VehicleRepository;
use App\Services\Contracts\VehicleServiceInterface;
use App\Services\Vehicles\VehicleService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class VehicleServiceTest extends TestCase
{
    private VehicleServiceInterface $service;

    private VehicleRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getMockBuilder(VehicleRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new VehicleService($this->repository);
    }

    /**
     * @test
     */
    public function shouldCreateVehicle()
    {
        $attributes = Vehicle::factory()->make()->toArray();

        $this->repository->expects($this->once())
            ->method('create')
            ->with($attributes)
            ->willReturn(new Vehicle($attributes));

        $vehicle = $this->service->create($attributes);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
    }

    /**
     * @test
     */
    public function shouldUpdateVehicle()
    {
        $attributes      = Vehicle::factory()->make(['id' => 1])->toArray();
        $existingVehicle = (new Vehicle())->newInstance()->forceFill($attributes);

        $updatedAttributes = ['brand' => 'New Brand'];

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $existingVehicle->uuid)
            ->willReturn($existingVehicle);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($attributes, 1)
            ->willReturn((new Vehicle())->forceFill(array_merge($existingVehicle->toArray(), $updatedAttributes)));

        $vehicle = $this->service->update($existingVehicle->uuid, $attributes);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals($existingVehicle->uuid, $vehicle->uuid);
    }

    /**
     * @test
     */
    public function shouldNotFoundVehicleByField()
    {
        $uuid = fake()->uuid();

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $uuid)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);

        $this->service->findOneBy('uuid', $uuid);
    }

    /**
     * @test
     */
    public function shouldFindVehicleByField()
    {
        $attributes = Vehicle::factory()->make(['id' => 1])->toArray();
        $vehicle    = (new Vehicle())->newInstance()->forceFill($attributes);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $vehicle->uuid)
            ->willReturn($vehicle);

        $result = $this->service->findOneBy('uuid', $vehicle->uuid);

        $this->assertInstanceOf(Vehicle::class, $result);
        $this->assertEquals($vehicle->uuid, $result->uuid);
        $this->assertEquals($vehicle->brand, $result->brand);
        $this->assertEquals($vehicle->model, $result->model);
    }

    /**
     * @test
     */
    public function shouldDeleteVehicle()
    {
        $attributes = Vehicle::factory()->make(['id' => 1])->toArray();
        $vehicle    = (new Vehicle())->newInstance()->forceFill($attributes);

        Event::fake([VehicleDeleted::class]);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $vehicle->uuid)
            ->willReturn($vehicle);

        $this->repository->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $result = $this->service->delete($vehicle->uuid);
        $this->assertNull($result);

        Event::assertDispatched(VehicleDeleted::class);
    }

    /**
     * @test
     */
    public function shouldDeleteVehicleUnsuccessfully()
    {
        $attributes = Vehicle::factory()->make(['id' => 1])->toArray();
        $vehicle    = (new Vehicle())->newInstance()->forceFill($attributes);

        Event::fake([VehicleDeleted::class]);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $vehicle->uuid)
            ->willReturn($vehicle);

        $this->repository->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage(__('exception.vehicle.delete_unsuccessfully'));

        $this->service->delete($vehicle->uuid);

        Event::assertNotDispatched(VehicleDeleted::class);
    }

    /**
     * @test
     */
    public function shouldFindVehicles()
    {
        Vehicle::factory()->create();

        $result = $this->service->findVehicles();

        $this->assertInstanceOf(Builder::class, $result);
    }

    /**
     * @test
     */
    public function shouldDisassociateUsers()
    {
        $vehicle = Vehicle::factory()->create();

        $this->repository->expects($this->once())
            ->method('detach')
            ->with($vehicle->id, 'users', null);

        $this->service->disassociateUser($vehicle->id, null);
    }
}
