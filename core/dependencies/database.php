<?php

use DI\Container;
use Core\Collection;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;

return function (Container $container) {
    
    /** @var Collection */
    $config = $container->get('config.database');

    foreach($config->connections as $name => $params)
    {
        $params->merge(['className'=>Connection::class]);
        $container->set('db.' . $name, function() use ($params) {
            return new Connection($params->all());
        });
    }

    $container->set('db', DI\get('db.' . $config->default));
    $container->set(Connection::class, DI\get('db.' . $config->default));
    
    $defaultConfig = $config->connections[$config->default]->all();
    ConnectionManager::setConfig('default', $defaultConfig);
};