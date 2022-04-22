<?php
namespace Core;

use Core\Base\Bootstrapper as BaseBootstrapper;
use DI\Bridge\Slim\Bridge;
use Slim\App;

final class Bootstrapper extends BaseBootstrapper
{
    /** @var App */
    private static $app = null;

    public static function boot(): App
    {
        $bootstrapper = new static();

        self::$app = Bridge::create();
        $container = self::$app->getContainer();

        $bootstrapper->loadCoreHelpers();
        $bootstrapper->registerConfig($container);
        $bootstrapper->registerDependencies($container);
        $bootstrapper->registerHelpers($container);
        $bootstrapper->registerMiddlewares($container);
        $bootstrapper->registerErrorHandler($container);
        $bootstrapper->registerRoutes($container);
        $bootstrapper->configure($container);

        return self::$app;
    }

    public static function getApp(): App
    {
        return self::$app;
    }
}