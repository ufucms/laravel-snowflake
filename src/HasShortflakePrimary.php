<?php declare(strict_types=1);

namespace Ufucms\Snowflake;

use Ufucms\Snowflake\Snowflake;

trait HasShortflakePrimary
{
    public static function bootHasShortflakePrimary()
    {
        static::saving(function ($model) {
            if (is_null($model->getKey())) {
                $model->setIncrementing(false);
                $keyName = $model->getKeyName();
                $id      = app(Snowflake::class)->short();
                $model->setAttribute($keyName, $id);
            }
        });
    }
}
