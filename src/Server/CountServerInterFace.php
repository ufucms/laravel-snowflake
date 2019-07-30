<?php

namespace Ufucms\LaravelSnowflake\Server;


interface CountServerInterFace
{
    public function getSequenceId($key);
}