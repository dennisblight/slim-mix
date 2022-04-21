<?php

use Core\Collection;
use DI\Container;

define('BASEPATH', realpath(__DIR__));
define('ENVIRONMENT', $_SERVER['ENV'] ?? 'production');

require_once BASEPATH . '/vendor/autoload.php';
Core\Bootstrapper::boot()->run();