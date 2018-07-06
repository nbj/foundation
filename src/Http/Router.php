<?php

namespace Nbj\Http;

use Closure;
use RuntimeException;
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
     * Gets all registered routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

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

        $this->guardAgainstInvalidFormattedAction($action);

        // If everything checks out at this point
        // Chop up the action string into the
        // controller and action strings
        list($controller, $action) = explode('@', $action);

        $this->routes[$method][$uri] = [
            'controller' => $controller,
            'action'     => $action,
        ];

        return $this;
    }

    /**
     * Loads routes from php files in a specific path
     *
     * @param string $path
     */
    public function loadRoutesFrom($path)
    {
        if (!file_exists($path)) {
            $message = sprintf('Path to routes files does not exist: %s', $path);

            throw new InvalidArgumentException($message);
        }

        // Scan path for all files
        $files = scandir($path);

        // Filter files down to only php files
        $files = array_filter($files, function ($file) {
            return strpos($file, '.php') !== false;
        });

        // Resolve each routes file into the router
        foreach ($files as $file) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $router = $this;

            require $path . DIRECTORY_SEPARATOR . $file;
        }
    }

    /**
     * Handles a request
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        $route = $this->resolveRoute($request);

        if ($route instanceof Closure) {
            return $route($request);
        }

        return $this->handleControllerActionBasedRoute($route, $request);
    }

    /**
     * @param $route
     * @param Request $request
     *
     * @return mixed
     */
    protected function handleControllerActionBasedRoute($route, Request $request)
    {
        list($controllerName, $actionName) = array_values($route);

        $controllerClass = sprintf('%s\\%s', CONTROLLER_NAMESPACE, $controllerName);

        if (!class_exists($controllerClass)) {
            throw new RuntimeException(sprintf('Controller class %s does not exist', $controllerClass));
        }

        $controller = new $controllerClass;

        if (!method_exists($controller, $actionName)) {
            throw new RuntimeException(sprintf('Controller %s does not have action: %s()', $controllerClass, $actionName));
        }

        return $controller->$actionName($request);
    }

    /**
     * Guards against the action being invalidly formatted
     *
     * @param mixed $action
     *
     * @throws InvalidArgumentException
     */
    protected function guardAgainstInvalidFormattedAction($action)
    {
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
    }

    /**
     * Resolves the route
     *
     * @param Request $request
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    protected function resolveRoute(Request $request)
    {
        $this->guardAgainstRouteNotBeingDefined($request);

        return $this->routes[$request->getMethod()][$request->getUri()];
    }

    /**
     * Guards against route not being defined
     *
     * @param Request $request
     *
     * @throws RuntimeException
     */
    protected function guardAgainstRouteNotBeingDefined(Request $request)
    {
        if (!isset($this->routes[$request->getMethod()][$request->getUri()])) {
            $message = sprintf('No route for method: %s with uri: %s exists.', $request->getMethod(), $request->getUri());

            throw new RuntimeException($message, 500);
        }
    }
}
