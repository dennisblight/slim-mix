<?php
namespace Core\Base;

use Slim\App;
use DI\Container;
use Core\Collection;
use Core\Route\AnnotationRoute;

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

        /** @var App */
        $app = $container->get(App::class);
        $app->getRouteCollector()->setBasePath(get_base_path());
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
            $configKey = 'config.' . substr($script, $offset, -$trailing);
            $configDefinition[$configKey] = 0x01;
        }

        if(defined('ENVIRONMENT'))
        {
            $configPattern = $configPath . '*.' . ENVIRONMENT . '.php';
            $offset = strlen($configPath);
            $trailing = strlen(ENVIRONMENT) + 5;
            foreach(glob($configPattern) as $script)
            {
                $configKey = 'config.' . substr($script, $offset, -$trailing);
                $configDefinition[$configKey] = 0x02 | array_item($configDefinition, $configKey, 0);
            }

            $configPattern = $configPath . ENVIRONMENT . '/*.php';
            $offset = strlen($configPattern) - 4;
            $trailing = 4;
            foreach(glob($configPattern) as $script)
            {
                $configKey = 'config.' . substr($script, $offset, -$trailing);
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
        $container->set($configKey, function() use ($configKey, $flag) {
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
                $load = function($serviceProviders) use ($script) {
                    $call = require $script;
                    if(is_callable($call)) $call($serviceProviders);
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
                else
                {
                    $container->get('logger')->warning("Couldn't load helper $helperName at '$helperFile'");
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

        $middlewares = $settings->get('middleware', []);
        foreach($middlewares as $index => $middleware)
        {
            if(is_string($middleware) && class_exists($middleware))
            {
                if(!$container->has($middleware))
                {
                    $container[$middleware] = function($container) use ($middleware) {
                        return new $middleware($container);
                    };
                }
                
                $app->add($middleware);
            }
            elseif(is_callable($middleware))
            {
                $app->add($middleware);
            }
            else
            {
                $container->get('logger')->warning("Couldn't resolve middleware index {$index} -> {$middleware}");
            }
        }
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
            else
            {
                $container->get('logger')->warning("Couldn't locate route '$route' file");
            }
        }

        if($settings->get('annotationRouting', false))
        {
            $routingService = new AnnotationRoute($container);
            $routingService->register();
        }
    }
}