<?php

namespace Ufucms\LaravelSnowflake\Facades;

use Illuminate\Support\Facades\Facade;

class Snowflake extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'snowflake';
    }
}
