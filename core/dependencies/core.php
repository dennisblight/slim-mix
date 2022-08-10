<?php

use Core\Collection;
use Core\Libraries\MiddlewareDispatcher;
use DI\Container;
use Psr\Http\Server\MiddlewareInterface;

$container->set('cache', function(Container $container) {
    $cachePath = $container->get('settings')->get('cachePath', BASEPATH . '/storage/cache');
    $config = new Phpfastcache\Config\Config(['path' => $cachePath]);
    return Phpfastcache\CacheManager::getInstance('files', $config);
});

$container->set('registry', function() {
    return new Collection();
});