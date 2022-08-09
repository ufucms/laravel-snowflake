<?php

namespace Ufucms\Snowflake\Providers;

use Illuminate\Support\ServiceProvider;
use Ufucms\Snowflake\Snowflake;

abstract class AbstractServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    abstract public function boot();

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Snowflake::class, function () {
            $epoch        = config('snowflake.epoch', null);
            $workerId     = config('snowflake.worker_id', 1);
            $datacenterId = config('snowflake.datacenter_id', 1);
            $timestamp = $epoch === null ? null :  $timestamp = strtotime($epoch);
            return new Snowflake($timestamp, $workerId, $datacenterId);
        });
    }
}