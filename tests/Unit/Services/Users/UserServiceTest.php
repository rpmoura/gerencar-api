<?php

namespace Tests\Unit\Services\Users;

use App\Exceptions\RepositoryException;
use App\Models\{User, Vehicle};
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\Users\UserRepository;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Users\UserService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private UserServiceInterface $service;

    private UserRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new UserService($this->repository);
    }

    /**
     * @test
     */
    public function shouldCreateUser()
    {
        $attributes = User::factory()->make()->makeVisible('password')->toArray();

        $this->repository->expects($this->once())
            ->method('create')
            ->with($attributes)
            ->willReturn(new User($attributes));

        $user = $this->service->create($attributes);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function shouldUpdateUser()
    {
        $attributes   = User::factory()->make(['id' => 1])->makeVisible('password')->toArray();
        $existingUser = (new User())->newInstance()->forceFill($attributes);

        $updatedAttributes = ['email' => 'new.email@test.com'];

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $existingUser->uuid)
            ->willReturn($existingUser);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($attributes, 1)
            ->willReturn((new User())->forceFill(array_merge($existingUser->toArray(), $updatedAttributes)));

        $user = $this->service->update($existingUser->uuid, $attributes);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($existingUser->uuid, $user->uuid);
    }

    /**
     * @test
     */
    public function shouldNotFoundUserByField()
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
    public function shouldFindUserByField()
    {
        $attributes = User::factory()->make(['id' => 1])->toArray();
        $user       = (new User())->newInstance()->forceFill($attributes);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $user->uuid)
            ->willReturn($user);

        $result = $this->service->findOneBy('uuid', $user->uuid);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->uuid, $result->uuid);
        $this->assertEquals($user->email, $result->email);
    }

    /**
     * @test
     */
    public function shouldDeleteUser()
    {
        $attributes = User::factory()->make(['id' => 1])->toArray();
        $user       = (new User())->newInstance()->forceFill($attributes);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $user->uuid)
            ->willReturn($user);

        $this->repository->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $result = $this->service->delete($user->uuid);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldDeleteUserUnsuccessfully()
    {
        $attributes = User::factory()->make(['id' => 1])->toArray();
        $user       = (new User())->newInstance()->forceFill($attributes);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with('uuid', $user->uuid)
            ->willReturn($user);

        $this->repository->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage(__('exception.user.delete_unsuccessfully'));

        $this->service->delete($user->uuid);
    }

    /**
     * @test
     */
    public function shouldFindUsers()
    {
        $user = User::factory()->create();

        $this->repository->expects($this->once())
            ->method('get')
            ->willReturn(collect([$user]));

        $result = $this->service->findUsers();

        $this->assertInstanceOf(UserRepositoryInterface::class, $result);
        $this->assertCount(1, $result->get());
    }

    /**
     * @test
     */
    public function shouldAssociateVehicle()
    {
        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->repository->expects($this->once())
            ->method('sync')
            ->with($user->id, 'vehicles', [$vehicle->id], false)
            ->willReturn(['attached' => [1]]);

        $result = $this->service->associateCar($user->id, $vehicle->id);

        $this->assertIsArray($result);
    }

    /**
     * @test
     */
    public function shouldDisassociateVehicle()
    {
        $user    = User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->repository->expects($this->once())
            ->method('detach')
            ->with($user->id, 'vehicles', $vehicle->id);

        $this->service->disassociateCar($user->id, $vehicle->id);
    }
}
