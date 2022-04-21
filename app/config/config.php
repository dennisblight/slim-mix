<?php

return [
    'annotationRouting' => true,
    'timezone' => 'Asia/Jakarta',

    'appKey'     => 'eUohHdOCVk1iMKq32vrWwmWsNzCar5AjhNoJ39L31Vs=',
    'appName'    => 'Slim Mix',
    'appVersion' => '1.0',

    'middleware' => [
        Slim\Middleware\Session::class,
        Core\Middleware\TrimSlashes::class,
    ],

    'password' => [
        'algo' => PASSWORD_BCRYPT,
        'cost' => 11,
        'salt' => base64_decode('kqPFItKjei1G3w=='),
        'mask' => base64_decode('2IBHZ9NgdZQQfA=='),
    ],
];