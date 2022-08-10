<?php
namespace Core;

use Slim\App;
use DI\Bridge\Slim\Bridge;
use Core\Base\Bootstrapper as BaseBootstrapper;
use Symfony\Component\Console\Application as ConsoleApp;

final class Bootstrapper extends BaseBootstrapper
{
    /** @var App */
    private static $app = null;

    /** @var ConsoleApp */
    private static $consoleApp = null;

    public static function boot()
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

        if(is_cli())
        {
            self::$consoleApp = new ConsoleApp("slim-mix");
            $bootstrapper->registerCommands(self::$consoleApp, $container);
            return self::$consoleApp;
        }

        return self::$app;
    }

    public static function getApp(): App
    {
        return self::$app;
    }

    public static function getConsoleApp(): ConsoleApp
    {
        return self::$consoleApp;
    }
}