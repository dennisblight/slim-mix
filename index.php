<?php

use Core\Collection;
use DI\Container;

define('BASEPATH', realpath(__DIR__));
define('ENVIRONMENT', $_SERVER['ENV'] ?? 'production');

require_once BASEPATH . '/vendor/autoload.php';
Core\Bootstrapper::boot();//->run();

// /** @var Container */
// $container = Core\Bootstrapper::getApp()->getContainer();
// $container->set('asdf', function() {
//     return new Collection(['ssss' => 'aaaa']);
// });

// $dir = BASEPATH . '/app/config/';
// $globPattern = BASEPATH . '/app/config/*.php';
// foreach(glob($globPattern) as $filename)
// {
//     echo substr($filename, strlen($dir), -4) . PHP_EOL;
// }