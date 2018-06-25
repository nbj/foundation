<?php

namespace Tests\Unit;

use Nbj\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /** @test */
    public function a_request_can_be_captured_statically()
    {
        $request = Request::capture();

        $this->assertInstanceOf(Request::class, $request);
    }

    /** @test */
    public function a_request_knows_the_requested_uri()
    {
        $_SERVER['REQUEST_URI'] = '/some-uri?some-key=some-value';

        $request = Request::capture();

        $this->assertEquals('/some-uri', $request->getUri());
    }

    /** @test */
    public function a_request_knows_its_method()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = Request::capture();

        $this->assertEquals('GET', $request->getMethod());
    }

    /** @test */
    public function a_request_knows_if_it_is_a_post_request()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = Request::capture();
        $this->assertTrue($request->isPost());

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = Request::capture();
        $this->assertFalse($request->isPost());
    }

    /** @test */
    public function it_can_check_if_ot_has_a_specific_parameter()
    {
        $_GET['some-key'] = 'some-value';
        $_POST['some-other-key'] = 'some-other-value';

        $request = Request::capture();

        $this->assertTrue($request->has('some-key'));
        $this->assertTrue($request->has('some-other-key'));
        $this->assertFalse($request->has('this-key-does-not-exist'));
    }

    /** @test */
    public function it_can_get_a_specific_parameter()
    {
        $_GET['some-key'] = 'some-value';
        $_POST['some-other-key'] = 'some-other-value';

        $request = Request::capture();

        $this->assertEquals('some-value', $request->get('some-key'));
        $this->assertEquals('some-other-value', $request->get('some-other-key'));
    }

    /** @test */
    public function it_returns_null_if_the_request_does_not_have_a_specific_parameter()
    {
        $request = Request::capture();

        $this->assertNull($request->get('this-key-does-not-exist'));
    }
}
