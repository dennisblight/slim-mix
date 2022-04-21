<?php

return [
    'default' => 'main',
    'connections' => [
        'main' => [
            'driver'     => Cake\Database\Driver\Mysql::class,
            'locahost'   => 'localhost',
            'database'   => 'itmwl',
            'username'   => 'root',
            'password'   => '',
        ],
        'remote' => [
            'driver'     => Cake\Database\Driver\Mysql::class,
            'host'       => '159.223.36.202:3366',
            'database'   => 'imagetoleaflet',
            'username'   => 'leaflet',
            'password'   => 'FcN@rR*lea&let9j*',
        ],
    ],
];