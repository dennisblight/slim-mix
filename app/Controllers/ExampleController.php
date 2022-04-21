<?php
namespace App\Controllers;

use App\Data\Tables\Users;
use App\Forms\LoginForm;
use Cake\Database\Connection;
use Cake\ORM\Locator\TableLocator;
use Core\Annotation\Route;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

class ExampleController
{
    /**
     * @Route\Post("/")
     */
    public function index(LoginForm $form, Users $userTable)
    {
        $users = $userTable->find();
        // var_dump($users);exit;
        return new Response\JsonResponse(($users));
    }
}