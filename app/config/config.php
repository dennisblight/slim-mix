<?php

return [
    'cachePath' => BASEPATH . '/storage/cache',
    'annotationRouting' => true,
    'timezone' => 'Asia/Jakarta',

    'middleware' => [
        Slim\Middleware\Session::class,
        Core\Middleware\TrimSlashes::class,
    ],
];