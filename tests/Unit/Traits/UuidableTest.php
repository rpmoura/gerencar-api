<?php

namespace Tests\Unit\Traits;

use App\Traits\Uuid\Uuidable;
use Illuminate\Support\Str;
use Tests\TestCase;

class UuidableTest extends TestCase
{
    private $trait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trait = new class () {
            use Uuidable;

            protected string $uuidColumnName = 'uuid';

            protected string $uuid = '';
        };
    }

    /**
     * @test
     */
    public function shouldGetUuidColumnName()
    {
        $this->assertEquals('uuid', $this->trait->getUuidColumnName());
    }

    /**
     * @test
     */
    public function shouldGenerateUuid()
    {
        $uuid = $this->trait->generateUuid();

        $this->assertIsString($uuid);

        return $uuid;
    }

    /**
     * @test
     * @depends shouldGenerateUuid
     */
    public function shouldGetUuid(string $uuid)
    {
        $this->trait->setUuid($uuid);
        $this->assertTrue(Str::isUuid($this->trait->getUuid()));
    }
}
