<?php
namespace Core\Base;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

abstract class AbstractCollection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    use CollectionTrait;
}