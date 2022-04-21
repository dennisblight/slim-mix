<?php
namespace Core\Middleware;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

abstract class TransformInput
{
    protected $fields = null;
    protected $postParams = [];
    protected $queryParams = [];

    public function __invoke(ServerRequest $request, Response $response, callable $next)
    {
        if($request->getMethod() == 'POST')
        {
            $this->postParams = $request->getParsedBody();
        }

        $this->params = $request->getQueryParams();
        $this->transformParameter();
        return $next(
            $request
                ->withQueryParams($this->queryParams)
                ->withParsedBody($this->postParams),
            $response
        );
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