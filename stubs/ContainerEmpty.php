<?php

namespace App\Container;

class Container
{
    /** @var static */
    protected static $instance;

    private function __construct()
    {
    }

    public static function getInstance(): static
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
