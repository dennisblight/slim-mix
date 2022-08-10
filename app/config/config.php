<?php

use Core\ErrorHandler\CoreExceptionHandler;
use Core\ErrorHandler\FormValidationHandler;
use Core\ErrorHandler\SlimHttpHandler;
use Core\Exceptions\ResponseException;
use Respect\Validation\Exceptions\ValidationException;
use Slim\Exception\HttpException;

return [
    'annotationRouting' => true,
    'timezone' => 'Asia/Jakarta',

    'appKey'     => 'eUohHdOCVk1iMKq32vrWwmWsNzCar5AjhNoJ39L31Vs=',
    'appName'    => 'Slim Mix',
    'appVersion' => '1.0',

    'password' => [
        'algo' => PASSWORD_BCRYPT,
        'cost' => 11,
        'salt' => base64_decode('kqPFItKjei1G3w=='),
        'mask' => base64_decode('2IBHZ9NgdZQQfA=='),
    ],

    'middleware' => [
        Core\Middleware\TrimSlashes::class,
        Slim\Middleware\Session::class,
    ],

    'errors' => [
        'enableErrorHandler'  => true,
        'displayErrorDetails' => ENVIRONMENT !== 'production',
        'handlers' => [
            SlimHttpHandler::class => HttpException::class,
            CoreExceptionHandler::class => ResponseException::class,
            FormValidationHandler::class => ValidationException::class,
        ],
    ],
];