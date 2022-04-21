<?php

use DI\Container;

return function (Container $container) {
    
    $container->set('cache', function(Container $container) {
        $cachePath = $container->get('settings')->get('cachePath', BASEPATH . '/storage/cache');
        $config = new Phpfastcache\Config\Config(['path' => $cachePath]);
        return Phpfastcache\CacheManager::getInstance('files', $config);
    });
};