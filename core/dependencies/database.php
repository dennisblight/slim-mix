<?php

use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Core\Collection;
use DI\Container;

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