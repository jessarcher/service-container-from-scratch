<?php

use App\Container\Container;
use App\Http\Controllers\HomeController;

require __DIR__ . '/../vendor/autoload.php';

$container = Container::getInstance(); 

$route = match ($_SERVER['REQUEST_URI']) {
    '/' => [HomeController::class, 'index'],
    default => null,
};

if (! $route) {
    die('Not found');
}

[$class, $method] = $route;

$controller = $container->make($class);

echo $controller->{$method}();
