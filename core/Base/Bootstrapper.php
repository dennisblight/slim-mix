<?php
namespace Core\Base;

use Slim\App;
use DI\Container;
use Core\Collection;
use Core\Route\AnnotationRoute;
use Laminas\Diactoros\Response;
use Core\Middleware\CorsMiddleware;
use Laminas\Diactoros\ServerRequest;
use Slim\Middleware\ErrorMiddleware;
use Symfony\Component\Console\Application as ConsoleApp;

abstract class Bootstrapper
{
    public function loadCoreHelpers()
    {
        foreach(glob(BASEPATH . '/core/helpers/*.php') as $script)
        {
            require $script;
        }
    }

    public function configure(Container $container)
    {
        /** @var Collection */
        $settings = $container->get('settings');

        $timezone = $settings->get('timezone', 'UTC');
        date_default_timezone_set($timezone);

        if(!is_cli())
        {
            $path = rtrim($_SERVER['REQUEST_URI'], '/');
            
            while(strpos($path, '//') !== false)
            {
                $path = str_replace('//', '/', $path);
            }

            $_SERVER['REQUEST_URI'] = empty($path) ? '/' : $path;
            // var_dump($_SERVER);
        }

        /** @var App */
        $app = $container->get(App::class);
        $basePath = get_base_path();
        $app->getRouteCollector()->setBasePath($basePath);
    }

    public function registerConfig(Container $container)
    {
        $configPath = BASEPATH . '/app/config/';
        $configDefinition = [];

        $configPattern = $configPath . '*.php';
        $offset = strlen($configPath);
        $trailing = 4;
        foreach(glob($configPattern) as $script)
        {
            $configKey = substr($script, $offset, -$trailing);
            $configDefinition[$configKey] = 0x01;
        }

        if(defined('ENVIRONMENT'))
        {
            $configPattern = $configPath . '*.' . ENVIRONMENT . '.php';
            $offset = strlen($configPath);
            $trailing = strlen(ENVIRONMENT) + 5;
            foreach(glob($configPattern) as $script)
            {
                $configKey = substr($script, $offset, -$trailing);
                $configDefinition[$configKey] = 0x02 | array_item($configDefinition, $configKey, 0);
            }

            $configPattern = $configPath . ENVIRONMENT . '/*.php';
            $offset = strlen($configPattern) - 4;
            $trailing = 4;
            foreach(glob($configPattern) as $script)
            {
                $configKey = substr($script, $offset, -$trailing);
                $configDefinition[$configKey] = 0x04 | array_item($configDefinition, $configKey, 0);
            }
        }

        foreach($configDefinition as $key => $flag)
        {
            $this->injectConfig($container, $key, $flag);
        }
        
        $container->set('settings', \DI\get('config.config'));
    }

    private function injectConfig(Container $container, string $configKey, int $flag)
    {
        $container->set('config.' . $configKey, function() use ($configKey, $flag) {
            $configPath = BASEPATH . '/app/config/';
            $config = new Collection([], true);

            if($flag & 0x01)
            {
                $path = $configPath . $configKey . '.php';
                $config->merge(require $path);
            }

            if($flag & 0x02)
            {
                $path = $configPath . $configKey . '.' . ENVIRONMENT . '.php';
                $config->merge(require $path);
            }

            if($flag & 0x04)
            {
                $path = $configPath . ENVIRONMENT . '/' . $configKey . '.php';
                $config->merge(require $path);
            }

            return $config;
        });
    }

    public function registerDependencies(Container $container)
    {
        $dependenciesPaths = [
            BASEPATH . '/app/dependencies',
            BASEPATH . '/core/dependencies',
        ];

        foreach($dependenciesPaths as $dependenciesPath)
        {
            foreach(glob($dependenciesPath . '/*.php') as $script)
            {
                $load = function($container) use ($script) {
                    $call = require $script;
                    if(is_callable($call)) $call($container);
                };

                $load($container);
            }
        }
    }

    public function registerHelpers(Container $container)
    {
        /** @var Collection */
        $settings = $container->get('settings');
        $appHelpers = $settings->get('autoloadHelpers', false);
        if($appHelpers === true)
        {
            foreach(rglob(BASEPATH . '/app/helpers/*.php') as $helperFile)
            {
                $load = function() use ($helperFile) {
                    require $helperFile;
                };
                
                $load();
            }
        }
        elseif(is_array($appHelpers))
        {
            $helperDirectory = BASEPATH . '/app/helpers/';
            foreach($appHelpers as $helperName)
            {
                if(file_exists($helperFile = $helperDirectory . $helperName . '.php'))
                {
                    $load = function() use ($helperFile) {
                        require $helperFile;
                    };
                    
                    $load();
                }
            }
        }
    }

    public function registerMiddlewares(Container $container)
    {
        /** @var Collection */
        $settings = $container->get('settings');

        /** @var App */
        $app = $container->get(App::class);
        
        $app->add(function(ServerRequest $request, $handler) use ($container) {
            $container->set(ServerRequest::class, $request);
            return $handler->handle($request);
        });

        $middlewares = $settings->get('middleware', []);
        foreach($middlewares as $middleware)
        {
            $app->add($middleware);
        }

        if($settings->get('enableBodyParsing', false))
        {
            $app->addBodyParsingMiddleware();
        }
        
        $cors = $settings->get('cors');
        if($cors && $cors->get('enableCors', false))
        {
            $app->options('/{routes:.+}', function (Response $response) {
                return $response;
            });

            $app->add(CorsMiddleware::class);
        }

        $app->addRoutingMiddleware();
    }

    public function registerErrorHandler(Container $container)
    {
        /** @var App */
        $app = $container->get(App::class);

        /** @var Collection */
        $config = $container->get('settings')->get('errors', new Collection());

        if(!$config->get('enableErrorHandler', true))
            return;

        $middleware = $app->addErrorMiddleware(
            $config->get('displayErrorDetails', false),
            $config->get('logErrors', false),
            $config->get('logErrorsDetails', false)
        );

        foreach($config->get('handlers', []) as $handler => $types)
        {
            $middleware->setErrorHandler($types, $handler, true);
        }

        $container->set(ErrorMiddleware::class, $middleware);
    }

    public function registerRoutes(Container $container)
    {
        $settings = $container->get('settings');
        $routeCaching = $settings->get('routeCaching', false);

        if($routeCaching && array_key_exists('cachePath', $settings))
        {
            $routerCacheFile = join_path($settings['cachePath'], 'routeDispatcher.php');
            $settings['routerCacheFile'] = $routerCacheFile;
        }

        /** @var App */
        $app = $container->get(App::class);

        $routes = (array) $settings->get('routes', []);
        foreach($routes as $route)
        {
            if(file_exists($routeFile = BASEPATH . '/app/routes/' . $route . '.php'))
            {
                $load = function($app) use ($routeFile) {
                    $call = include $routeFile;
                    if(is_callable($call)) $call($app);
                };

                $load($app);
            }
        }

        if($settings->get('annotationRouting', false))
        {
            $routingService = new AnnotationRoute($container);
            $routingService->register();
        }
    }

    public function registerCommands(ConsoleApp $consoleApp, Container $container)
    {
        if($container->has('config.commands'))
        {
            $commands = $container->get('config.commands');
            foreach($commands as $command)
            {
                $consoleApp->add($container->get($command));
            }
        }
    }
}