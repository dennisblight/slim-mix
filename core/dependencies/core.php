<?php

use Core\Collection;
use DI\Container;

$container->set('cache', function(Container $container) {
    $cachePath = $container->get('settings')->get('cachePath', BASEPATH . '/storage/cache');
    $config = new Phpfastcache\Config\Config(['path' => $cachePath]);
    return Phpfastcache\CacheManager::getInstance('files', $config);
});

$container->set('registry', function() {
    return new Collection();
});