<?php

namespace Tests\Unit\Repositories\Eloquent\Vehicles;

use App\Models\{User, Vehicle};
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Eloquent\Vehicles\VehicleRepository;
use Illuminate\Container\Container as Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

class VehicleRepositoryTest extends TestCase
{
    use InteractsWithContainer;

    protected $app;

    private readonly VehicleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = $this->createPartialMock(Application::class, ['make']);
        $this->app->expects($this->atLeastOnce())
            ->method('make')
            ->with(Vehicle::class, [])
            ->willReturn(new Vehicle());

        $this->repository = new VehicleRepository($this->app);
    }

    /**
     * @test
     */
    public function shouldFindVehiclesByField()
    {
        $vehicle = Vehicle::factory()->create();

        $result = $this->repository->findBy('uuid', $vehicle->uuid);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
    }

    /**
     * @test
     */
    public function shouldFindVehicleByField()
    {
        $vehicle = Vehicle::factory()->create();

        $result = $this->repository->findOneBy('uuid', $vehicle->uuid);

        $this->assertInstanceOf(Vehicle::class, $result);
        $this->assertEquals($vehicle->uuid, $result->uuid);
        $this->assertEquals($vehicle->brand, $result->brand);
    }

    /**
     * @test
     */
    public function shouldNotFoundVehicle()
    {
        Vehicle::factory(3)->create();

        $result = $this->repository->findOneBy('uuid', fake()->uuid);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldNotFoundVehicles()
    {
        Vehicle::factory(3)->create();

        $result = $this->repository->findBy('uuid', fake()->uuid);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function shouldCreateVehicle()
    {
        $attributes = Vehicle::factory()->make()->toArray();

        $vehicle = $this->repository->create($attributes);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals($attributes['brand'], $vehicle->brand);
    }

    /**
     * @test
     */
    public function shouldUpdateVehicle()
    {
        $existingVehicle = Vehicle::factory()->create();

        $updatedAttributes = [
            'brand' => fake()->company(),
            'model' => fake()->colorName(),
        ];

        $vehicle = $this->repository->update($updatedAttributes, $existingVehicle->id);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals($updatedAttributes['brand'], $vehicle->brand);
        $this->assertEquals($updatedAttributes['model'], $vehicle->model);
    }

    /**
     * @test
     */
    public function shouldReturnAllVehicles()
    {
        Vehicle::factory()->create();

        $result = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $result);

        foreach ($result as $vehicle) {
            $this->assertInstanceOf(Vehicle::class, $vehicle);
        }
    }

    /**
     * @test
     */
    public function shouldReturnGetVehicles()
    {
        Vehicle::factory()->create();

        $result = $this->repository->get();

        $this->assertInstanceOf(Collection::class, $result);

        foreach ($result as $vehicle) {
            $this->assertInstanceOf(Vehicle::class, $vehicle);
        }
    }

    /**
     * @test
     */
    public function shouldReturnPaginatedResults()
    {
        $mockContainer = \Mockery::mock(new Application());
        $this->mock(Application::class, fn () => $mockContainer);
        $mockContainer->expects('make')->times(3)->withArgs([Vehicle::class])->andReturn(new Vehicle());
        $mockContainer->expects('make')->once()->withArgs(['request'])->andReturn(new Request());

        $repository = new VehicleRepository($mockContainer);

        Vehicle::factory()->create();

        $result = $repository->paginate();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    /**
     * @test
     */
    public function shouldDeleteVehicle()
    {
        $vehicle = Vehicle::factory()->create();

        $result = $this->repository->delete($vehicle->id);

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldFindVehiclesByUser()
    {
        User::factory()
            ->has(Vehicle::factory()->count(3))
            ->create();

        $user = User::factory()
            ->has(Vehicle::factory()->count(2))
            ->create();

        $filter = [
            'user' => $user->id,
        ];

        $result = $this->repository->findVehicles($filter)->get();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }
}
