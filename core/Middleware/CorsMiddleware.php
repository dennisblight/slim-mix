<?php
namespace Core\Middleware;

use Core\Collection;
use DI\Container;
use Slim\Routing\RouteContext;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware
{
    /** @var Collection */
    public $config;

    public function __construct(Container $container)
    {
        $this->config = $container->get('settings')->get('cors', new Collection());
    }

    public function __invoke(ServerRequest $request, RequestHandlerInterface $handler)
    {
        $routeContext = RouteContext::fromRequest($request);
        $routingResults = $routeContext->getRoutingResults();
        $methods = $routingResults->getAllowedMethods();
        
        $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');
        if($this->config->has('allowHeaders'))
        {
            $allowHeaders = $this->config->allowHeaders;
            $requestHeaders = array_map(function($item) {
                return trim($item);
            }, explode(',', $requestHeaders));
            $requestHeaders = join(', ', array_intersect($allowHeaders->all(), $requestHeaders));
        }

        $origin = '*';
        if($this->config->has('allowOrigins'))
        {
            $requestOrigin = $request->getHeaderLine('origin');
            $allowOrigins = (array) $this->config->allowOrigins;

            if(in_array('*', $allowOrigins))
            {
                $origin = '*';
            }
            elseif(in_array($requestOrigin, $allowOrigins))
            {
                $origin = $requestOrigin;
            }
            else
            {
                $origin = (string) $request->getUri()->withPath('');
            }
        }

        $response = $handler->handle($request);

        $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
        $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
        $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);

        // Optional: Allow Ajax CORS requests with Authorization header
        // $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}