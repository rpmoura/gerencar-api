<?php

namespace Tests\Unit\Repositories\Eloquent;

use App\Exceptions\RepositoryException;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Container\Container as Application;
use Tests\TestCase;

class BaseRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowRepositoryExceptionIfModelIsNotInstanceEloquentModel()
    {
        $this->expectException(RepositoryException::class);

        $appMock = $this->createMock(Application::class);

        $repositoryMock = $this->getMockBuilder(BaseRepository::class)
            ->setConstructorArgs([$appMock])
            ->getMockForAbstractClass();

        $repositoryMock->method('model')->willReturn('InvalidModelClass');

        new $repositoryMock($appMock);
    }
}
