<?php

use App\Forms\LoginForm;
use Core\Base\AbstractForm;
use DI\Container;
use DI\Factory\RequestedEntry;
use Doctrine\Common\Annotations\AnnotationReader;
use Laminas\Diactoros\ServerRequest;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\Config as CacheConfiguration;

return function (Container $container) {
    
    $container->set('cache', function(Container $container) {
        $cachePath = $container->get('settings')->get('cachePath');
        $config = new CacheConfiguration(['path' => $cachePath]);
        return Phpfastcache\CacheManager::getInstance('files', $config);
    });

    $container->set('session', function() {
        return new SlimSession\Helper();
    });
};