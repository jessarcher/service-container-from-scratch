<?php

namespace App\Facades;

use App\Container\Container;
use Mockery;
use Mockery\MockInterface;

abstract class Facade
{
    protected static array $resolvedInstance;

    abstract protected static function getFacadeAccessor();

    public static function __callStatic($method, $args)
    {
        $name = static::getFacadeAccessor();

        if (!isset(self::$resolvedInstance[$name])) {
            $container = Container::getInstance();

            self::$resolvedInstance[$name] = $container->make(static::getFacadeAccessor());
        }

        return self::$resolvedInstance[$name]->{$method}(...$args);
    }

    public static function shouldReceive()
    {
        $name = static::getFacadeAccessor();

        if (!isset(self::$resolvedInstance[$name]) || !self::$resolvedInstance[$name] instanceof MockInterface) {
            $object = Container::getInstance()->make(static::getFacadeAccessor());
            self::$resolvedInstance[$name] = Mockery::mock($object);
        }

        return static::$resolvedInstance[$name]->shouldReceive(...func_get_args());
    }
}
