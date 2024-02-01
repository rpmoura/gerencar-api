<?php

namespace Tests\Feature\Http\Controllers\V1;

use App\Exceptions\RepositoryException;
use App\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateUser()
    {
        $attributes = [
            'email'                 => 'user.um@gmail.com',
            'password'              => 'pass',
            'password_confirmation' => 'pass',
        ];

        $response = $this->post('/v1/users', ['user' => $attributes]);

        $response->assertStatus(201);
        $response->assertJsonStructure(
            [
                'data' => [
                    'user' => [
                        'uuid',
                        'email',
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
    public function shouldEditUser()
    {
        $user = User::factory()->create();

        $attributes = [
            'email' => 'new.email@email.com',
        ];

        $response = $this->put("/v1/users/{$user->uuid}", ['user' => $attributes]);

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [
                    'user' => [
                        'uuid',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]
        );
        $this->assertEquals('new.email@email.com', $response->json('data.user.email'));
    }

    /**
     * @test
     */
    public function shouldNotCreateUserBecauseExistsAlreadyExists()
    {
        $oldUser = User::factory()->create();

        $attributes = [
            'email' => $oldUser->email,
        ];

        $response = $this->post("/v1/users/", ['user' => $attributes]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function shouldNotEditUserBecauseExistsAlreadyExists()
    {
        $oldUser = User::factory()->create();
        $user    = User::factory()->create();

        $attributes = [
            'email' => $oldUser->email,
        ];

        $response = $this->put("/v1/users/{$user->uuid}", ['user' => $attributes]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function shouldNotFoundUserForEdit()
    {
        User::factory()->create();

        $attributes = [
            'email' => 'new.email@email.com',
        ];

        $response = $this->put('/v1/users/' . fake()->uuid(), ['user' => $attributes]);

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function shouldFindUser()
    {
        $user = User::factory()->create();

        $response = $this->get("/v1/users/{$user->uuid}");

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [
                    'user' => [
                        'uuid',
                        'email',
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
    public function shouldNotFoundUser()
    {
        User::factory()->create();

        $response = $this->get('/v1/users/' . fake()->uuid());

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function shouldListUsersWithPagination()
    {
        User::factory(2)->create();

        $response = $this->get("/v1/users?page=1");

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [
                    'users' => [
                        'data' => [
                            [
                                'uuid',
                                'email',
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
    public function shouldDeleteUser()
    {
        $user = User::factory()->create();

        $response = $this->delete("/v1/users/{$user->uuid}");

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function shouldNotFoundUserForDelete()
    {
        User::factory()->create();

        $response = $this->delete('/v1/users/' . fake()->uuid());

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function shouldUnsuccessfullyDeleteUser()
    {
        $user = User::factory()->create();

        $service = \Mockery::mock('App\Services\Users\UserService');
        $service
            ->shouldReceive('delete')
            ->once()
            ->withArgs([$user->uuid])
            ->andThrow(RepositoryException::class);
        $this->app->instance('App\Services\Users\UserService', $service);

        $response = $this->delete("/v1/users/{$user->uuid}");

        $response->assertStatus(500);
    }
}
