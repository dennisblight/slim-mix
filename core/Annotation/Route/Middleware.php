<?php
namespace Core\Annotation\Route;

use Doctrine\Common\Annotations\Annotation;
use Exception;

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

            if(!$valid) throw new Exception("Could not resolve \"$baseName\" middleware");

            return $value;
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