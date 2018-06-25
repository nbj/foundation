<?php

namespace Tests\Unit;

use stdClass;
use Exception;
use InvalidArgumentException;
use Nbj\Foundation\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /** @test */
    public function it_can_be_created_statically()
    {
        $app = Application::create();

        $this->assertInstanceOf(Application::class, $app);
    }

    /** @test */
    public function it_can_be_fetched_statically()
    {
        $app = Application::getInstance();

        $this->assertInstanceOf(Application::class, $app);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_can_be_considered_a_singleton()
    {
        $appA = Application::create();
        $appB = Application::create();

        $this->assertEquals($appA, $appB);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_creates_the_application_when_the_get_instance_method_is_called_before_the_create_method()
    {
        $appA = Application::getInstance();
        $appB = Application::getInstance();

        $this->assertEquals($appA, $appB);
    }

    /** @test */
    public function it_can_have_services_registered_to_it()
    {
        $app = Application::create();
        $service = new stdClass();

        $app->register('service-name', $service);

        $this->assertCount(1, $app->getServices());
        $this->assertContains($service, $app->getServices());
    }

    /** @test */
    public function it_can_have_services_resolved_out_of_it()
    {
        $app = Application::create();
        $app->register('service-name', new stdClass());

        $service = $app->resolve('service-name');

        $this->assertInstanceOf(stdClass::class, $service);
    }

    /** @test */
    public function it_takes_exception_to_resolving_a_service_that_is_not_registered_in_the_application()
    {
        $app = Application::create();

        $service = null;

        try {
            $service = $app->resolve('service-name-that-is-not-registered');
        } catch (Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertEquals('Service with name: service-name-that-is-not-registered was not registered in the application', $exception->getMessage());
        }

        $this->assertNull($service);
    }

    /** @test */
    public function it_can_take_a_closure_as_a_service_argument()
    {
        $app = Application::create();

        $app->register('service-name', function () {
            return new stdClass;
        });

        $this->assertInstanceOf(stdClass::class, $app->resolve('service-name'));
    }
}
