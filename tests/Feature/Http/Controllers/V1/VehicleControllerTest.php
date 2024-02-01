<?php

namespace Tests\Feature\Http\Controllers\V1;

use App\Events\VehicleDeleted;
use App\Exceptions\RepositoryException;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VehicleControllerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateVehicle()
    {
        $attributes = [
            'brand' => fake()->company(),
            'model' => fake()->colorName(),
        ];

        $response = $this->post('/v1/vehicles', ['vehicle' => $attributes]);

        $response->assertStatus(201);
        $response->assertJsonStructure(
            [
                'data' => [
                    'vehicle' => [
                        'uuid',
                        'brand',
                        'model',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function shouldEditVehicle()
    {
        $vehicle = Vehicle::factory()->create();

        $attributes = [
            'brand' => 'new brand',
            'model' => 'new model',
        ];

        $response = $this->put("/v1/vehicles/{$vehicle->uuid}", ['vehicle' => $attributes]);

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [
                    'vehicle' => [
                        'uuid',
                        'brand',
                        'model',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]
        );
        $this->assertEquals('new brand', $response->json('data.vehicle.brand'));
        $this->assertEquals('new model', $response->json('data.vehicle.model'));
    }

    /**
     * @test
     */
    public function shouldNotFoundVehicleForEdit()
    {
        Vehicle::factory()->create();

        $attributes = [
            'brand' => fake()->company(),
        ];

        $response = $this->put('/v1/vehicles/' . fake()->uuid(), ['vehicle' => $attributes]);

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function shouldFindVehicle()
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->get("/v1/vehicles/{$vehicle->uuid}");

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [
                    'vehicle' => [
                        'uuid',
                        'brand',
                        'model',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function shouldNotFoundVehicle()
    {
        Vehicle::factory()->create();

        $response = $this->get('/v1/vehicles/' . fake()->uuid());

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function shouldListVehiclesWithPagination()
    {
        Vehicle::factory(2)->create();

        $response = $this->get("/v1/vehicles?page=1");

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [
                    'vehicles' => [
                        'data' => [
                            [
                                'uuid',
                                'brand',
                                'model',
                                'created_at',
                                'updated_at',
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function shouldDeleteVehicle()
    {
        $vehicle = Vehicle::factory()->create();

        Event::fake([VehicleDeleted::class]);

        $response = $this->delete("/v1/vehicles/{$vehicle->uuid}");

        $response->assertStatus(200);

        Event::assertDispatched(VehicleDeleted::class);
    }

    /**
     * @test
     */
    public function shouldNotFoundVehicleForDelete()
    {
        Vehicle::factory()->create();

        Event::fake([VehicleDeleted::class]);

        $response = $this->delete('/v1/vehicles/' . fake()->uuid());

        $response->assertStatus(404);

        Event::assertNotDispatched(VehicleDeleted::class);
    }

    /**
     * @test
     */
    public function shouldUnsuccessfullyDeleteVehicle()
    {
        $vehicle = Vehicle::factory()->create();

        Event::fake([VehicleDeleted::class]);

        $service = \Mockery::mock('App\Services\Vehicles\VehicleService');
        $service
            ->shouldReceive('delete')
            ->once()
            ->withArgs([$vehicle->uuid])
            ->andThrow(RepositoryException::class);
        $this->app->instance('App\Services\Vehicles\VehicleService', $service);

        $response = $this->delete("/v1/vehicles/{$vehicle->uuid}");

        $response->assertStatus(500);

        Event::assertNotDispatched(VehicleDeleted::class);
    }
}
