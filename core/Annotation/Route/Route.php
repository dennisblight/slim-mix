<?php
namespace Core\Annotation\Route;

use InvalidArgumentException;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"METHOD"})
 */
class Route
{
    public const ValidMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /** @var string[] */
    public $methods;

    /** @var string */
    public $path;

    /** @var string */
    public $name;

    /** @var array */
    public $arguments;

    public function __construct($methods, string $path, string $name = null, array $arguments = [])
    {
        $methods = (array) $methods;
        $methods = array_filter($methods, function($item) {
            return in_array($item, Route::ValidMethods);
        });

        if(empty($methods))
        {
            throw new InvalidArgumentException("Route has no valid method");
        }

        $this->methods = $methods;
        $this->path = $path;
        $this->name = $name;
        $this->arguments = $arguments;
    }
}