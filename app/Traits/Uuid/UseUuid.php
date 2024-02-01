<?php

namespace App\Traits\Uuid;

use App\Exceptions\NotFoundUuidColumnException;

trait UseUuid
{
    use Uuidable;
    public static function bootUseUuid(): void
    {
        static::creating(function ($model) {
            /** @phpstan-ignore-next-line */
            (new static())->hasColumnUuid($model);

            $model->setUuid($model->generateUuid());
        });

        static::saving(function ($model) {
            /** @phpstan-ignore-next-line */
            (new static())->hasColumnUuid($model);

            $originalUuid = $model->getOriginal($model->getUuidColumnName());

            if ($originalUuid !== $model->getUuid() && $originalUuid) {
                $model->setUuid($originalUuid);
            }
        });
    }

    /**
     * @param $model
     * @return void
     * @throws NotFoundUuidColumnException
     */
    private function hasColumnUuid($model): void
    {
        if (!$model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $model->getUuidColumnName())) {
            throw new NotFoundUuidColumnException(
                "Don't have a '{$model->getUuidColumnName()}' column on '{$model->getTable()}' table."
            );
        }
    }
}
