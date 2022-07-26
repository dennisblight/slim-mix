<?php
namespace Core\Middleware\Logger\Writer;

use Laminas\Diactoros\Request;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

abstract class BaseWriter
{
    protected $config = [];

    function __construct($config)
    {
        $this->config = $config;
    }

    abstract public function logRequest(ServerRequest $request);

    abstract public function logResponse(string $requestResult, Response $response);

    public function getMaxLength()
    {
        return array_item($this->config, 'max_length', INF);
    }
}