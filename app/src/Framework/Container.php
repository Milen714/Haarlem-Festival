<?php
namespace App\Framework;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

class Container {
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, string|callable|null $concrete = null): void {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'shared' => false,
        ];
    }

    public function singleton(string $abstract, string|callable|null $concrete = null): void {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'shared' => true,
        ];
    }

    public function get(string $abstract): mixed {
        return $this->resolve($abstract);
    }

    public function make(string $abstract): mixed {
        return $this->resolve($abstract);
    }

    private function resolve(string $abstract): mixed {
        if (array_key_exists($abstract, $this->instances)) {
            return $this->instances[$abstract];
        }

        $binding = $this->bindings[$abstract] ?? null;
        $concrete = $binding['concrete'] ?? $abstract;

        if (is_callable($concrete)) {
            $object = $concrete($this);
        } else {
            $object = $this->build($concrete);
        }

        if (($binding['shared'] ?? false) === true) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function build(string $className): mixed {
        if (!class_exists($className)) {
            throw new RuntimeException("Cannot resolve {$className}: class does not exist and no binding was found.");
        }

        $reflection = new ReflectionClass($className);
        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Cannot instantiate {$className}.");
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $className();
        }

        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $dependencyName = $type->getName();
                if (array_key_exists($dependencyName, $this->bindings) || class_exists($dependencyName)) {
                    $args[] = $this->resolve($dependencyName);
                    continue;
                }
            }

            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
                continue;
            }

            throw new RuntimeException(
                "Cannot resolve constructor parameter $" . $parameter->getName() . " for {$className}."
            );
        }

        return $reflection->newInstanceArgs($args);
    }
}