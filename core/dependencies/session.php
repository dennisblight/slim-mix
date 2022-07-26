<?php

use DI\Container;
use Slim\Middleware\Session;
use SlimSession\Helper;

$container->set(Session::class, function(Container $container) {
    return new Session(
        $container->get('config.session')->all()
    );
});

$container->set('session', function() {
    return new \Core\Libraries\Session();
});

$container->set(\SlimSession\Helper::class, function() {
    return new \Core\Libraries\Session();
});