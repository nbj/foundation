<?php

namespace Nbj\Foundation;

use Closure;
use InvalidArgumentException;
use Nbj\Foundation\Traits\Singleton;

class Application
{
    use Singleton;

    /**
     * Holds all registered services in the application
     *
     * @var array $services
     */
    protected $services = [];

    /**
     * Gets all services
     *
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Registers a service in the application
     *
     * @param string $name
     * @param mixed $service
     */
    public function register($name, $service)
    {
        if ($service instanceof Closure) {
            $service = $service($name);
        }

        $this->services[$name] = $service;
    }

    /**
     * Resolves a service out of the application
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function resolve($name)
    {
        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        }

        throw new InvalidArgumentException(sprintf('Service with name: %s was not registered in the application', $name));
    }
}
