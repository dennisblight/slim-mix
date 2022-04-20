<?php
namespace Core\Annotation\Route;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"METHOD"})
 */
class Delete extends Route
{
    public function __construct(string $path, string $name = null, array $arguments = [])
    {
        parent::__construct(['DELETE'], $path, $name, $arguments);
    }
}