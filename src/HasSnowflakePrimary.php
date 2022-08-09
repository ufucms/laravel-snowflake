<?php
namespace Ufucms\Snowflake;

use Ufucms\Snowflake\Snowflake;

trait HasSnowflakePrimary
{
    public static function bootHasSnowflakePrimary()
    {
        static::saving(function ($model) {
            if (is_null($model->getKey())) {
                $model->setIncrementing(false);
                $keyName = $model->getKeyName();
                $id      = app(Snowflake::class)->nextId();
                $model->setAttribute($keyName, $id);
            }
        });
    }
}
