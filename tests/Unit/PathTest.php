<?php

use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use Middlewares\Utils\FactoryDiscovery;

use Middlewares\JSONAPI\Path;

Factory::setFactory(new FactoryDiscovery(FactoryDiscovery::SUNRISE));

test('Test path middleware', function () {
    /** @var PHPUnit\Framework\TestCase $this */

    $middleware = new Path('/api');

    Dispatcher::run(
        [
            $middleware,
            function ($request) {
                $this->assertTrue($request->getAttribute(Path::ATTRIBUTE_ROOT));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_RESOURCE));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_ID));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_RELATED));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_RELATIONSHIP));
            }
        ],
        Factory::createServerRequest('get', '/api')
    );

    Dispatcher::run(
        [
            $middleware,
            function ($request) {
                $this->assertFalse($request->getAttribute(Path::ATTRIBUTE_ROOT));
                $this->assertEquals('people', $request->getAttribute(Path::ATTRIBUTE_RESOURCE));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_ID));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_RELATED));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_RELATIONSHIP));
            }
        ],
        Factory::createServerRequest('get', '/api/people')
    );

    Dispatcher::run(
        [
            $middleware,
            function ($request) {
                $this->assertFalse($request->getAttribute(Path::ATTRIBUTE_ROOT));
                $this->assertEquals('people', $request->getAttribute(Path::ATTRIBUTE_RESOURCE));
                $this->assertEquals('1', $request->getAttribute(Path::ATTRIBUTE_ID));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_RELATED));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_RELATIONSHIP));
            }
        ],
        Factory::createServerRequest('get', '/api/people/1')
    );

    Dispatcher::run(
        [
            $middleware,
            function ($request) {
                $this->assertFalse($request->getAttribute(Path::ATTRIBUTE_ROOT));
                $this->assertEquals('people', $request->getAttribute(Path::ATTRIBUTE_RESOURCE));
                $this->assertEquals('1', $request->getAttribute(Path::ATTRIBUTE_ID));
                $this->assertEquals('articles', $request->getAttribute(Path::ATTRIBUTE_RELATED));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_RELATIONSHIP));
            }
        ],
        Factory::createServerRequest('get', '/api/people/1/articles')
    );

    Dispatcher::run(
        [
            $middleware,
            function ($request) {
                $this->assertFalse($request->getAttribute(Path::ATTRIBUTE_ROOT));
                $this->assertEquals('people', $request->getAttribute(Path::ATTRIBUTE_RESOURCE));
                $this->assertEquals('1', $request->getAttribute(Path::ATTRIBUTE_ID));
                $this->assertNull($request->getAttribute(Path::ATTRIBUTE_RELATED));
                $this->assertEquals('articles', $request->getAttribute(Path::ATTRIBUTE_RELATIONSHIP));
            }
        ],
        Factory::createServerRequest('get', '/api/people/1/relationships/articles')
    );
});