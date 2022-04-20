<?php
namespace Core\Middleware;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

abstract class TransformInput
{
    protected $fields = null;
    protected $params = [];

    public function __invoke(ServerRequest $request, Response $response, callable $next)
    {
        if($request->getMethod() == 'POST')
        {
            $this->params = $request->getParsedBody();
            $this->transformParameter();
            return $next($request->withParsedBody($this->params), $response);
        }
        else
        {
            $this->params = $request->getQueryParams();
            $this->transformParameter();
            return $next($request->withQueryParams($this->params), $response);
        }
    }

    protected function transformParameter()
    {
        foreach($this->params as $key => $value)
        {
            $this->params[$key] = $this->transform($value);
        }
    }

    abstract protected function transform($value);
}