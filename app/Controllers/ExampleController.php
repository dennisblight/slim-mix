<?php
namespace App\Controllers;

use Core\Annotation\Route;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

class ExampleController
{
    /**
     * @Route\Get("/[{name}]")
     */
    public function index(ServerRequest $request, Response $response, $name = null)
    {
        $body = $response->getBody();
        return $response;
    }
}