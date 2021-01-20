<?php

namespace App\Facades;

use App\Container\Container;
use App\Support\Pdf as PdfService;
use Mockery;

class Pdf
{
    protected static $mock;

    protected static function getFacadeAccessor()
    {
        return PdfService::class;
    }

    public static function __callStatic($method, $args)
    {
        if (static::$mock) {
            return static::$mock->{$method}(...$args);
        }

        $container = Container::getInstance();

        $service = $container->make(self::getFacadeAccessor());

        return $service->{$method}(...$args);
    }

    public static function shouldReceive()
    {
        if (!static::$mock) {
            $object = Container::getInstance()->make(static::getFacadeAccessor());
            static::$mock = Mockery::mock($object);
        }

        return static::$mock->shouldReceive(...func_get_args());
    }
}
