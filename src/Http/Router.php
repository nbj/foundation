<?php

namespace Nbj\Http;

use Closure;
use InvalidArgumentException;
use Nbj\Foundation\Traits\Singleton;

class Router
{
    use Singleton;

    /**
     * Holds all registered routes
     *
     * @var array $routes
     */
    protected $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    /**
     * Registers a GET route
     *
     * @param string $uri
     * @param mixed $action
     *
     * @return Router
     */
    public function get($uri, $action)
    {
        return $this->registerRoute('GET', $uri, $action);
    }

    /**
     * Registers a POST route
     *
     * @param string $uri
     * @param mixed $action
     *
     * @return Router
     */
    public function post($uri, $action)
    {
        return $this->registerRoute('POST', $uri, $action);
    }

    /**
     * Registers a route
     *
     * @param string $method
     * @param string $uri
     * @param mixed $action
     *
     * @return Router
     */
    public function registerRoute($method, $uri, $action)
    {
        // If $action is a closure assume it is
        // okay, assign it to the routes list
        // and break execution early
        if ($action instanceof Closure) {
            $this->routes[$method][$uri] = $action;

            return $this;
        }

        if (!is_string($action)) {
            throw new InvalidArgumentException(sprintf('$action must be either a string or a closure. %s given', gettype($action)));
        }

        if (!strpos($action, '@')) {
            throw new InvalidArgumentException('$action must contain both controller and action separated by @');
        }

        $controllerActionArray = explode('@', $action);

        if (count($controllerActionArray) != 2) {
            throw new InvalidArgumentException('$action must only contain a single @');
        }

        list($controller, $action) = $controllerActionArray;

        $this->routes[$method][$uri] = [
            'controller' => $controller,
            'action'     => $action,
        ];

        return $this;
    }

    /**
     * Gets all registered routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
