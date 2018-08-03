<?php

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\Collection;
use IrRegular\Hopper\Foldable;
use IrRegular\Hopper\ListAccessible;
use IrRegular\Hopper\Mappable;

class Set implements Collection, Foldable, ListAccessible, Mappable
{
    /**
     * @var array
     */
    public $array;

    public function __construct(array $a)
    {
        // ensure values of $a are a unique set
        $this->array = array_fill_keys($a, true);
    }

    public function foldl(callable $closure, $initialValue)
    {
        return array_reduce(array_keys($this->array), $closure, $initialValue);
    }

    public function foldr(callable $closure, $initialValue)
    {
        return array_reduce(array_reverse(array_keys($this->array)), $closure, $initialValue);
    }

    public function map(callable $closure): \Generator
    {
        foreach (array_keys($this->array) as $value) {
            yield $closure($value);
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->array);
    }

    public function first()
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot retrieve first');
    }

    public function last()
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot retrieve last');
    }

    public function rest(): ListAccessible
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot retrieve rest');
    }

    public function getIterator()
    {
        return new \ArrayIterator(array_keys($this->array));
    }
}
