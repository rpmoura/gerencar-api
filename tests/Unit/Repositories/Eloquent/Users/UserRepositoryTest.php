<?php

namespace Tests\Unit\Repositories\Eloquent\Users;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\Users\UserRepository;
use Illuminate\Container\Container as Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use InteractsWithContainer;

    protected $app;

    private readonly UserRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = $this->createPartialMock(Application::class, ['make']);
        $this->app->expects($this->atLeastOnce())
            ->method('make')
            ->with(User::class, [])
            ->willReturn(new User());

        $this->repository = new UserRepository($this->app);
    }

    /**
     * @test
     */
    public function shouldFindUsersByField()
    {
        $user = User::factory()->create();

        $result = $this->repository->findBy('uuid', $user->uuid);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
    }

    /**
     * @test
     */
    public function shouldFindUserByField()
    {
        $user = User::factory()->create();

        $result = $this->repository->findOneBy('uuid', $user->uuid);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->uuid, $result->uuid);
        $this->assertEquals($user->email, $result->email);
    }

    /**
     * @test
     */
    public function shouldNotFoundUser()
    {
        User::factory(3)->create();

        $result = $this->repository->findOneBy('uuid', fake()->uuid);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldNotFoundUsers()
    {
        User::factory(3)->create();

        $result = $this->repository->findBy('uuid', fake()->uuid);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function shouldCreateUser()
    {
        $attributes = User::factory()->make()->makeVisible('password')->toArray();

        $user = $this->repository->create($attributes);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($attributes['email'], $user->email);
    }

    /**
     * @test
     */
    public function shouldUpdateUser()
    {
        $existingUser = User::factory()->create();

        $newEmail          = fake()->email();
        $updatedAttributes = [
            'email' => $newEmail,
        ];

        $user = $this->repository->update($updatedAttributes, $existingUser->id);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($newEmail, $user->email);
    }

    /**
     * @test
     */
    public function shouldReturnAllUsers()
    {
        User::factory()->create();

        $result = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $result);

        foreach ($result as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    /**
     * @test
     */
    public function shouldReturnGetUsers()
    {
        User::factory()->create();

        $result = $this->repository->get();

        $this->assertInstanceOf(Collection::class, $result);

        foreach ($result as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    /**
     * @test
     */
    public function shouldReturnPaginatedResults()
    {
        $mockContainer = \Mockery::mock(new Application());
        $this->mock(Application::class, fn () => $mockContainer);
        $mockContainer->expects('make')->times(3)->withArgs([User::class])->andReturn(new User());
        $mockContainer->expects('make')->once()->withArgs(['request'])->andReturn(new Request());

        $repository = new UserRepository($mockContainer);

        User::factory()->create();

        $result = $repository->paginate();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    /**
     * @test
     */
    public function shouldDeleteUser()
    {
        $user = User::factory()->create();

        $result = $this->repository->delete($user->id);

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }
}
