<?php
namespace App\Controllers;

use App\Data\Tables\Users;
use App\Forms\LoginForm;
use Cake\Database\Connection;
use Cake\ORM\Locator\TableLocator;
use Core\Annotation\Route;
use eftec\bladeone\BladeOne;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as Session;

/**
 * @Route\Group("/")
 */
class ExampleController
{
    /**
     * @Route\Post("/")
     */
    public function index(LoginForm $form, ServerRequest $request)
    {
        return new Response\JsonResponse([]);
    }

    /**
     * @Route\Get("/")
     */
    public function view(BladeOne $view)
    {
        $html = $view->run('index');
        return new Response\HtmlResponse($html);
    }
}