<?php

use DI\Container;
use Slim\Middleware\Session;

return function (Container $container) {

    $container->set(Session::class, function(Container $container) {
        return new Session(
            $container->get('config.session')->all()
        );
    });

    $container->set('session', function() {
        return new SlimSession\Helper();
    });
};