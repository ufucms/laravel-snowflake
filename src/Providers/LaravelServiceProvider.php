<?php

namespace Ufucms\Snowflake\Providers;

use Ufucms\Snowflake\Snowflake;

class LaravelServiceProvider extends AbstractServiceProvider
{
    /**
     * Bootstrap any application services for laravel.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/snowflake.php' => config_path('snowflake.php'),
        ]);
    }
}
