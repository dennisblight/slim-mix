<?php
namespace Core\Base;

trait CollectionTrait
{
    protected $data = [];

    public function has($key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    public function set($key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function __set(string $name, $value)
    {
        $this->set($name, $value);
    }

    public function __get(string $name)
    {
        return $this->get($name);
    }

    public function __unset(string $name)
    {
        $this->remove($name);
    }

    public function remove($key): void
    {
        if($this->has($key))
        {
            unset($this->data[$key]);
        }
    }

    public function replace(array $items): void
    {
        $this->data = [];
        $this->merge($items);
    }

    public function merge(array $items): void
    {
        foreach ($items as $key => $value)
        {
            $this->set($key, $value);
        }
    }

    public function all(): array
    {
        return $this->data;
    }

    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    public function offsetUnset($key): void
    {
        $this->remove($key);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }

    public function jsonSerialize()
    {
        return $this->all();
    }
}