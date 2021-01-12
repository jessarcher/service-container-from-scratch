<?php

namespace App\Container;

use Closure;

class Container
{
    /** @var static */
    protected static $instance;

    protected array $bindings;

    protected array $instances;

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

    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared,
        ];
    }

    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function make(string $abstract): mixed
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract]['concrete'] ?? $abstract;

        if ($concrete instanceof Closure || $concrete === $abstract) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }

        if (isset($this->bindings[$abstract]) && $this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    public function build(Closure|string $concrete): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete();
        }

        try {
            return new $concrete;
        } catch (\Error $e) {
            throw new BindingResolutionException("Target [$concrete] is not instantiable.", 0, $e);
        }
    }

    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
    }
}
