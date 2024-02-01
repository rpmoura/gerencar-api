<?php

namespace Tests\Feature\Http\Controllers\V1;

use App\Models\{User, Vehicle};
use Tests\TestCase;

class AssociateControllerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateAssociation()
    {
        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $response = $this->post("/v1/users/{$user->uuid}/vehicles/{$vehicle->uuid}");

        $response->assertStatus(201);
        $this->assertEquals(__('message.user.vehicle.associate_successfully'), $response->json('message'));
    }

    /**
     * @test
     */
    public function shouldDeleteAssociation()
    {
        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $response = $this->delete("/v1/users/{$user->uuid}/vehicles/{$vehicle->uuid}");

        $response->assertStatus(200);
        $this->assertEquals(__('message.user.vehicle.disassociate_successfully'), $response->json('message'));
    }

    /**
     * @test
     */
    public function shouldListAssociations()
    {
        User::factory()
            ->has(Vehicle::factory()->count(7))
            ->create();

        $user = User::factory()
            ->has(Vehicle::factory()->count(5))
            ->create();

        $response = $this->get("/v1/users/{$user->uuid}/vehicles");

        $response->assertStatus(200);
        $this->assertEquals(__('message.user.vehicle.listed_successfully'), $response->json('message'));
        $this->assertCount(5, $response->json('data.vehicles.data'));
        $this->assertDatabaseCount('users_x_vehicles', 12);
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
    public function shouldNotFoundUserForAssociate()
    {
        User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $uuid = fake()->uuid();

        $response = $this->post("/v1/users/{$uuid}/vehicles/{$vehicle->uuid}");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function shouldNotFoundVehicleForAssociate()
    {
        $user = User::factory()->create();
        Vehicle::factory()->create();

        $uuid = fake()->uuid();

        $response = $this->post("/v1/users/{$user->uuid}/vehicles/{$uuid}");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function shouldNotFoundUserForDisassociate()
    {
        User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $uuid = fake()->uuid();

        $response = $this->delete("/v1/users/{$uuid}/vehicles/{$vehicle->uuid}");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function shouldNotFoundVehicleForDisassociate()
    {
        $user = User::factory()->create();
        Vehicle::factory()->create();

        $uuid = fake()->uuid();

        $response = $this->delete("/v1/users/{$user->uuid}/vehicles/{$uuid}");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function shouldNotFoundUserForListAssociations()
    {
        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $user->vehicles()->sync($vehicle, false);

        $uuid = fake()->uuid();

        $response = $this->get("/v1/users/{$uuid}/vehicles");

        $response->assertStatus(404);
    }
}
