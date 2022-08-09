<?php

namespace Ufucms\Snowflake\Providers;

class LumenServiceProvider extends AbstractServiceProvider
{
    /**
     * Bootstrap any application services for lumen.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../../config/snowflake.php');

        $this->mergeConfigFrom($path, 'snowflake');
    }
}
