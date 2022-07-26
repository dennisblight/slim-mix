<?php
namespace Core\Base;

abstract class AbstractEntity extends AbstractCollection
{
    public static $properties = null;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->initialize($data);
    }

    protected function initialize(array $data = [])
    {
        if(is_array(static::$properties))
        {
            foreach(static::$properties as $field)
            {
                $this->set($field, $data[$field] ?? null);
            }
        }
        else
        {
            foreach($data as $name => $value)
            {
                $this->set($name, $value);
            }
        }
    }

    public function set($name, $value): void
    {
        if(method_exists($this, $method = 'set' . to_pascal_case($name)))
        {
            $this->$method($value);
        }
        else
        {
            $this->data[$name] = $value;
        }
    }

    public function get($name, $default = null)
    {
        if(method_exists($this, $method = 'get' . to_pascal_case($name)))
        {
            return $this->$method();
        }

        return parent::get($name, $default);
    }
}