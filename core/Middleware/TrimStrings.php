<?php
namespace Core\Middleware;

class TrimStrings extends TransformInput
{
    protected function transform($value)
    {
        return is_array($value) || is_null($value) ? $value : trim($value);
    }
}