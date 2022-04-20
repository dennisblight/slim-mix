<?php
namespace Core\Annotation\Route;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 */
class Middleware
{
    public $middlewares = [];

    public function __construct(...$values)
    {
        $middlewares = array_values($values);

        $middlewares = array_map(function($item) {
            $value = $item['value'];
            $baseName = $value;
            $valid = class_exists($value)
                || class_exists($value = 'Core\\Middleware\\' . $baseName)
                || class_exists($value = $value . 'Middleware')
                || class_exists($value = 'App\\Middleware\\' . $baseName)
                || class_exists($value = $value . 'Middleware')
            ;
            return $valid ? $value : null;
        }, $values);

        $middlewares = array_filter(
            $middlewares,
            function($item) {
                return !empty($item);
            }
        );

        $this->middlewares = $middlewares;
    }
}