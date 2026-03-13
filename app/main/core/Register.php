<?php

namespace App\Main\Core;

use Psr\Container\ContainerInterface;
use App\Main\Core\Exceptions\NotFoundException;
use App\Main\Core\Exceptions\ContainerException;

class Register implements ContainerInterface
{
    private array $services = [];

    public function set(string $id, callable|object|string $concrete): void
    {
        $this->services[$id] = $concrete;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    public function get(string $id): object
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Service '$id' not found");
        }

        $concrete = $this->services[$id];

        // Сначала проверяем: это фабрика?
        if (is_callable($concrete)) {
            $object = $concrete($this);
            if (!is_object($object)) {
                throw new ContainerException("Factory for '$id' must return an object");
            }
            return $object;
        }

        // Потом: это уже готовый объект?
        if (is_object($concrete)) {
            return $concrete;
        }

        // Потом: это имя класса?
        if (is_string($concrete)) {
            if (!class_exists($concrete)) {
                throw new ContainerException("Class '$concrete' not found");
            }
            return new $concrete();
        }

        throw new ContainerException("Invalid service definition for '$id'");
    }
}