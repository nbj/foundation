<?php

namespace Tests\Feature;

use Closure;
use stdClass;
use Exception;
use Nbj\Http\Router;
use Nbj\Http\Request;
use RuntimeException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /** @test */
    public function it_can_be_created_statically()
    {
        $router = Router::create();

        $this->assertInstanceOf(Router::class, $router);
    }

    /** @test */
    public function it_can_be_fetched_statically()
    {
        $router = Router::getInstance();

        $this->assertInstanceOf(Router::class, $router);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_can_be_considered_a_singleton()
    {
        $routerA = Router::create();
        $routerB = Router::create();

        $this->assertEquals($routerA, $routerB);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_creates_the_router_when_the_get_instance_method_is_called_before_the_create_method()
    {
        $routerA = Router::getInstance();
        $routerB = Router::getInstance();

        $this->assertEquals($routerA, $routerB);
    }

    /** @test */
    public function it_can_register_get_routes()
    {
        $router = Router::create();

        $router->get('/some-uri', function () {
            return 'some-output';
        });

        $routes = $router->getRoutes();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertCount(1, $routes['GET']);
        $this->assertArrayHasKey('/some-uri', $routes['GET']);
        $this->assertInstanceOf(Closure::class, $routes['GET']['/some-uri']);
    }

    /** @test */
    public function it_can_register_post_routes()
    {
        $router = Router::create();

        $router->post('/some-uri', function () {
            return 'some-output';
        });

        $routes = $router->getRoutes();

        $this->assertArrayHasKey('POST', $routes);
        $this->assertCount(1, $routes['POST']);
        $this->assertArrayHasKey('/some-uri', $routes['POST']);
        $this->assertInstanceOf(Closure::class, $routes['POST']['/some-uri']);
    }

    /** @test */
    public function route_actions_can_be_registered_as_a_string()
    {
        $router = Router::create();

        $router->get('/some-uri', 'SomeController@someAction');

        $routes = $router->getRoutes();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertCount(1, $routes['GET']);
        $this->assertArrayHasKey('/some-uri', $routes['GET']);
        $this->assertArrayHasKey('controller', $routes['GET']['/some-uri']);
        $this->assertArrayHasKey('action', $routes['GET']['/some-uri']);
        $this->assertEquals('SomeController', $routes['GET']['/some-uri']['controller']);
        $this->assertEquals('someAction', $routes['GET']['/some-uri']['action']);
    }

    /** @test */
    public function it_takes_exception_to_route_action_being_an_integer()
    {
        $router = Router::create();

        $safetyCheck = null;

        try {
            $router->get('/some-uri', 1);

            $safetyCheck = 'this should never happen, safetyCheck must stay null';
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('$action must be either a string or a closure. integer given', $exception->getMessage());
        }

        $this->assertNull($safetyCheck);
    }

    /** @test */
    public function it_takes_exception_to_route_action_being_an_object()
    {
        $router = Router::create();

        $safetyCheck = null;

        try {
            $router->get('/some-uri', new stdClass);

            $safetyCheck = 'this should never happen, safetyCheck must stay null';
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('$action must be either a string or a closure. object given', $exception->getMessage());
        }

        $this->assertNull($safetyCheck);
    }

    /** @test */
    public function it_takes_exception_to_route_action_being_a_boolean()
    {
        $router = Router::create();

        $safetyCheck = null;

        try {
            $router->get('/some-uri', false);

            $safetyCheck = 'this should never happen, safetyCheck must stay null';
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('$action must be either a string or a closure. boolean given', $exception->getMessage());
        }

        $this->assertNull($safetyCheck);
    }

    /** @test */
    public function it_takes_exception_to_route_action_being_a_double()
    {
        $router = Router::create();

        $safetyCheck = null;

        try {
            $router->get('/some-uri', 1.1);

            $safetyCheck = 'this should never happen, safetyCheck must stay null';
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('$action must be either a string or a closure. double given', $exception->getMessage());
        }

        $this->assertNull($safetyCheck);
    }

    /** @test */
    public function it_takes_exception_to_route_action_string_not_being_formatted_correctly_as_controller_and_action()
    {
        $router = Router::create();

        $safetyCheck = null;

        try {
            $router->get('/some-uri', 'SomeControllerWithoutAction');

            $safetyCheck = 'this should never happen, safetyCheck must stay null';
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('$action must contain both controller and action separated by @', $exception->getMessage());
        }

        $this->assertNull($safetyCheck);
    }

    /** @test */
    public function it_takes_exception_to_route_action_string_not_being_formatted_correctly_with_too_many_arguments()
    {
        $router = Router::create();

        $safetyCheck = null;

        try {
            $router->get('/some-uri', 'SomeController@someAction@someOtherActionThatShouldNotBeHere');

            $safetyCheck = 'this should never happen, safetyCheck must stay null';
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('$action must only contain a single @', $exception->getMessage());
        }

        $this->assertNull($safetyCheck);
    }

    /** @test */
    public function it_handles_requests_to_routes_define_with_closures()
    {
        $request = $this->createSimpleRequest('/some-uri');

        $router = Router::create();

        // Define a GET route with a closure
        $router->get('/some-uri', function () {
            return 'response-from-some-uri';
        });

        $response = $router->handle($request);

        $this->assertEquals('response-from-some-uri', $response);
    }

    /** @test */
    public function it_handles_requests_to_routes_define_with_controller_action_combo()
    {
        $request = $this->createSimpleRequest('/some-uri');

        $router = Router::create();

        // Define a GET route with a closure
        $router->get('/some-uri', 'SomeController@someAction');

        $response = $router->handle($request);

        $this->assertNull($response);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_takes_exception_to_route_not_being_defined()
    {
        $request = $this->createSimpleRequest('/some-uri');

        $router = Router::create();

        $response = null;

        try {
            $response = $router->handle($request);
        } catch (Exception $exception) {
            $this->assertInstanceOf(RuntimeException::class, $exception);
            $this->assertEquals('No route for method: GET with uri: /some-uri exists.', $exception->getMessage());
        }

        $this->assertNull($response);
    }

    /**
     * Creates a simple request object
     *
     * @param string $uri
     * @param string $method
     *
     * @return Request
     */
    protected function createSimpleRequest($uri, $method = 'GET')
    {
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_SERVER['REQUEST_URI'] = $uri;

        return Request::capture();
    }
}
