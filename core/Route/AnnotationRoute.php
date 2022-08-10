<?php
namespace Core\Route;

use Slim\App;
use DI\Container;
use ReflectionClass;
use ReflectionMethod;
use Core\Annotation\Route;
use Doctrine\Common\Annotations\AnnotationReader;

class AnnotationRoute
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register()
    {
        $config = $this->container->get('settings');
        $cacheManager = $this->container->get('cache');
        if($config->get('routeCaching', false))
        {
            $cache = $cacheManager->getItem('routeAnnotation');
            if(!is_null($cache->get()))
            {
                $this->registerResolvedRoutes($cache->get());
                return;
            }
        }

        $resolvedRoutes = $this->getRoutes();

        if($config->get('routeCaching', false))
        {
            $cache = $this->container->get('cache')->getItem('routeAnnotation');
            $cache->set($resolvedRoutes)->expiresAfter(5 * 60);
            $cacheManager->save($cache);
        }
        
        $this->registerResolvedRoutes($resolvedRoutes);
    }

    public function getRoutes()
    {
        $resolvedRoutes = [];
        $classes = $this->getControllerClasses();
        foreach($classes as $class)
        {
            $routes = $this->getControllerRoute($class);
            $resolvedRoutes = array_merge($resolvedRoutes, $routes);
        }

        return $resolvedRoutes;
    }

    public function getControllerRoute($controller)
    {
        $ref = new ReflectionClass($controller);
        $reader = $this->container->get(AnnotationReader::class);

        /** @var Route\Group|null */
        $routeGroup = $reader->getClassAnnotation($ref, Route\Group::class);

        $controllerMiddlewares = $this->getControllerMiddlewares($ref);

        $basePath = isset($routeGroup) ? $routeGroup->path : '';
        $baseMiddlewares = [];
        foreach($controllerMiddlewares as $item)
        {
            $baseMiddlewares = array_merge($baseMiddlewares, $item->middlewares);
        }
        
        $resolvedRoutes = [];

        $actions = $ref->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach($actions as $action)
        {
            $annotations = $reader->getMethodAnnotations($action);
            $middlewares = [];
            $routes = [];
            foreach($annotations as $annotation)
            {
                if($annotation instanceof Route\Route)
                {
                    array_push($routes, [
                        $annotation->methods,
                        $this->cleanUrl($basePath . $annotation->path),
                        [$controller, $action->name],
                        $annotation->name,
                        $annotation->arguments,
                    ]);
                }
                elseif($annotation instanceof Route\Middleware)
                {
                    $middlewares = array_merge($middlewares, $annotation->middlewares);
                }
            }

            $middlewares = array_merge($baseMiddlewares, $middlewares);

            $routes = array_map(function($item) use ($middlewares) {
                array_push($item, $middlewares);
                return $item;
            }, $routes);

            $resolvedRoutes = array_merge($resolvedRoutes, $routes);
        }
        
        return $resolvedRoutes;
    }

    public function getControllerMiddlewares(ReflectionClass $class)
    {
        $reader = $this->container->get(AnnotationReader::class);

        /** @var Annotation\Middleware[] */
        $classAnnotations = $reader->getClassAnnotations($class);
        return array_filter($classAnnotations, function($item) {
            return $item instanceof Route\Middleware;
        });
    }

    private function getControllerClasses()
    {
        $files = rglob(BASEPATH . '/app/Controllers/*.php');
        $controllers = [];
        foreach($files as $file)
        {
            $controller = str_replace(
                [BASEPATH . '/app/', '.php', '/'],
                ['App\\', '', '\\'],
                $file
            );

            if(class_exists($controller)) array_push($controllers, $controller);
        }

        return $controllers;
    }

    private function registerResolvedRoutes($resolvedRoutes)
    {
        foreach($resolvedRoutes as $item)
        {
            [$methods, $path, $handle, $name, $arguments, $middlewares] = $item;

            $route = $this->map($methods, $path, $handle);

            foreach($middlewares as $mw)
            {
                $route->add($mw);
            }

            if(isset($name))
            {
                $route->setName($name);
            }

            if(!empty($arguments))
            {
                $route->setArguments($arguments);
            }
        }
    }

    private function map(array $methods, $pattern, $callable)
    {
        $route = $this->container->get(App::class)->map($methods, $pattern, $callable);
        if (is_callable([$route, 'setContainer'])) {
            $route->setContainer($this->container);
        }

        if (is_callable([$route, 'setOutputBuffering'])) {
            $route->setOutputBuffering($this->container->get('settings')['outputBuffering']);
        }

        return $route;
    }

    private function cleanUrl($url)
    {
        $url = rtrim($url, '/');
        while(strpos($url, '//') !== false)
        {
            $url = str_replace('//', '/', $url);
        }

        return empty($url) ? '[/]' : $url;
    }
}