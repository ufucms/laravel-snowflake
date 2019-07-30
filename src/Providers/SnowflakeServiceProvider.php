<?php

namespace Ufucms\LaravelSnowflake\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class SnowflakeServiceProvider extends ServiceProvider
{
    protected $defer = true; // 延迟加载服务
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('snowflake', function ($app) {
            $config = Config::get('snowflake', array());
            return new Snowflake($config);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('snowflake.php'),
        ], 'config');
    }


    public function provides()
    {
        return ['snowflake'];
    }
}
