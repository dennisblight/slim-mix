<?php

use DI\Container;
use Doctrine\Common\Annotations\AnnotationReader;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\Config as CacheConfiguration;

return function (Container $container) {

    $container->set(AnnotationReader::class, function() {
        return new AnnotationReader();
    });
    
    $container->set('cache', function(Container $container) {
        $cachePath = $container->get('settings')->get('cachePath');
        $config = new CacheConfiguration(['path' => $cachePath]);
        return Phpfastcache\CacheManager::getInstance('files', $config);
    });

    $container->set('session', function() {
        return new SlimSession\Helper();
    });
};