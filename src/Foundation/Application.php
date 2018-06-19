<?php

namespace Nbj\Foundation;

use Closure;
use InvalidArgumentException;

class Application
{
    /**
     * Holds the application instance
     *
     * @var Application $instance
     */
    protected static $instance;

    /**
     * Holds all registered services in the application
     *
     * @var array $services
     */
    protected $services;

    /**
     * Static construct
     *
     * @return static
     */
    public static function create()
    {
        if (self::$instance) {
            return self::$instance;
        }

        self::$instance = new static;

        return self::$instance;
    }

    /**
     * Gets the instance of the application
     *
     * @return Application
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            return self::create();
        }

        return self::$instance;
    }

    /**
     * Application constructor.
     *
     * This is private to prevent developers from
     * creating new instances of this class
     */
    private function __construct()
    {
        $this->services = [];
    }

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
