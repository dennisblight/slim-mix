<?php
namespace Core;

use Core\Base\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $recursiveCollection = false;

    public function __construct(array $data = [], $recursiveCollection = false)
    {
        $this->recursiveCollection = $recursiveCollection;
        $this->replace($data);
    }
    
    public function set($key, $value): void
    {
        $this->data[$key] = is_array($value) && $this->recursiveCollection
            ? new Collection($value, true)
            : $value;
    }
}