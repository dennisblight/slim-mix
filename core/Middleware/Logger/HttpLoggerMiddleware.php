<?php
namespace Core\Middleware\Logger;

use Core\Base\ComponentTrait;
use Core\Middleware\Logger\Writer\BaseWriter;
use Closure;
use Core\Collection;
use DI\Container;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

class HttpLoggerMiddleware
{
    /** @var Container */
    protected $container;

    /** @var Collection */
    protected $config;

    public function __construct(Container $container)
    {
        $this->config = $container->get('config.http_logger');
        $this->container = $container;
    }

    public function __invoke(ServerRequest $request, RequestHandlerInterface $handler)
    {
        /** @var Route|null */
        $route = $request->getAttribute('route');
        if(!is_null($route) && $route->getArgument('ignoreHttpLog', false))
        {
            return $handler->handle($request);
        }

        if(isset($this->config['writer']) && is_subclass_of($writerClass = $this->config['writer'], BaseWriter::class))
        {
            $writer = $this->container->get($writerClass);
            $result = $writer->logRequest($request);

            $finalResponse = $handler->handle($request);

            $writer->logResponse($result, $finalResponse);
            return $finalResponse;
        }
        
        return $handler->handle($request);
    }
}
