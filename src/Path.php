<?php namespace Middlewares\JSONAPI;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Path implements MiddlewareInterface
{
    const ATTRIBUTE_ROOT            = 'jsonapi::path::root';
    const ATTRIBUTE_RESOURCE        = 'jsonapi::path::resource';
    const ATTRIBUTE_ID              = 'jsonapi::path::id';
    const ATTRIBUTE_RELATED         = 'jsonapi::path::related';
    const ATTRIBUTE_RELATIONSHIP    = 'jsonapi::path::relationship';

    /**
     * @var string
     *
     * Base API path. Examples:
     *   /api
     *   /api/v1
     */
    protected string $basePath = '/';

    /**
     * Path constructor.
     * @param string $basePath
     */
    public function __construct($basePath = '/')
    {
        $this->basePath = '/' . ltrim($basePath, '/'); // Make sure we always have "/" in the beginning.
    }

    /**
     * Split path into expected chunks.
     * {resource}[0] / {id}[1] / ({related}|relationships)[2] / {relationship}[3]
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        if (substr($path, 0, strlen($this->basePath)) === $this->basePath) {
            $path = substr($path, strlen($this->basePath));
        }

        $parts = explode('/', ltrim($path, '/'), 4);

        $request = $request->withAttribute(static::ATTRIBUTE_ROOT, count($parts) === 1 && empty($parts[0]));

        if (count($parts) >= 1 && !empty($parts[0])) {
            $request = $request->withAttribute(static::ATTRIBUTE_RESOURCE, $parts[0]);
        }

        if (count($parts) >= 2) {
            $request = $request->withAttribute(static::ATTRIBUTE_ID, $parts[1]);
        }

        if (count($parts) === 3) {
            $request = $request->withAttribute(static::ATTRIBUTE_RELATED, $parts[2]);
        }

        if (count($parts) === 4 && $parts[2] === 'relationships') {
            $request = $request->withAttribute(static::ATTRIBUTE_RELATIONSHIP, $parts[3]);
        }

        return $handler->handle($request);
    }
}