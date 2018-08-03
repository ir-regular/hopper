<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\Collection;
use IrRegular\Hopper\Foldable;
use IrRegular\Hopper\ListAccessible;
use IrRegular\Hopper\Mappable;

class Vector implements Collection, Foldable, ListAccessible, Mappable
{
    /**
     * @var array
     */
    public $array;

    public function __construct(array $a)
    {
        // ensure contiguous numeric keys
        $this->array = array_values($a);
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

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }
}
