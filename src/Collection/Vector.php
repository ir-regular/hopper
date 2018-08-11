<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\Collection;
use IrRegular\Hopper\Foldable;
use IrRegular\Hopper\Indexable;
use IrRegular\Hopper\ListAccessible;
use IrRegular\Hopper\Mappable;

class Vector implements Collection, ListAccessible, Indexable, Mappable, Foldable
{
    /**
     * @var array
     */
    public $array;

    public function __construct(iterable $collection)
    {
        // ensure contiguous numeric keys by stripping the existing keys

        if ($collection instanceof \Traversable) {
            $this->array = iterator_to_array($collection, false);
        } else {
            assert(is_array($collection));
            $this->array = array_values($collection);
        }
    }

    public function foldl(callable $closure, $initialValue)
    {
        return array_reduce($this->array, $closure, $initialValue);
    }

    public function foldr(callable $closure, $initialValue)
    {
        return array_reduce(array_reverse($this->array), $closure, $initialValue);
    }

    public function map(callable $closure): \Generator
    {
        foreach ($this->array as $value) {
            yield $closure($value);
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->array);
    }

    public function getCount(): int
    {
        return count($this->array);
    }

    public function getValues(): iterable
    {
        return $this->array;
    }

    public function first()
    {
        assert(!empty($this->array));
        return $this->array[0];
    }

    public function last()
    {
        assert(!empty($this->array));
        $key = count($this->array) - 1;
        // $key = array_key_last($this->array); // PHP 7.3
        return $this->array[$key];
    }

    public function rest(): ListAccessible
    {
        $rest = new Vector([]);
        $rest->array = array_slice($this->array, 1);
        return $rest;
    }

    public function get($key, $default = null)
    {
        assert(is_int($key));
        return $this->array[$key] ?? $default;
    }

    public function isKey($key): bool
    {
        assert(is_int($key));
        return array_key_exists($key, $this->array);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }
}

function vector(iterable $collection)
{
    return new Vector($collection);
}
