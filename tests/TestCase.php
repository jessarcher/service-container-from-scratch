<?php

namespace Tests;

use App\Container\Container;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function swap($abstract, $instance)
    {
        Container::getInstance()->instance($abstract, $instance);

        return $instance;
    }
}
