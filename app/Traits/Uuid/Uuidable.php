<?php

namespace App\Traits\Uuid;

use Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuidable
{
    public function getUuidColumnName(): string
    {
        return property_exists($this, 'uuidColumnName') ? $this->uuidColumnName : 'uuid';
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        if (!empty($this->getUuidColumnName())) {
            return (string)$this->{$this->getUuidColumnName()};
        }

        return null;
    }

    /**
     * @param string $value
     * @return void
     */
    public function setUuid(string $value): void
    {
        if (!empty($this->getUuidColumnName())) {
            $this->{$this->getUuidColumnName()} = $value;
        }
    }

    public function generateUuid(): string
    {
        return RamseyUuid::uuid4()->toString();
    }
}
