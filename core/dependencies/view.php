<?php

use Core\Collection;
use DI\Container;
use eftec\bladeone\BladeOne as View;

$container->set(View::class, function(Container $container) {
    $config = $container->has('config.view')
        ? $container->get('config.view')
        : new Collection()
    ;

    $viewsPath = $config->get('viewsPath', BASEPATH . '/resources/views');
    $cachePath = $config->get('cachePath', BASEPATH . '/storage/cache/views');
    $bladeMode = $config->get('bladeMode', View::MODE_DEBUG);
    $baseUrl   = get_base_url();

    $blade = new View($viewsPath, $cachePath, $bladeMode);

    $aliasClasses = $container->has('config.alias')
        ? $container->get('config.alias')
        : new Collection()
    ;

    $blade->setAliasClasses($aliasClasses->all());
    $blade->setBaseUrl($baseUrl);

    return $blade;
});