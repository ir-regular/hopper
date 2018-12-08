<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\Set;

use function IrRegular\Hopper\Collection\HashMap\convert_to_key;
use function IrRegular\Hopper\Collection\HashMap\is_valid_key;
use IrRegular\Hopper\Ds\Mappable;
use IrRegular\Hopper\Ds\Sequence;
use IrRegular\Hopper\Ds\Set as SetInterface;
use IrRegular\Hopper\Ds\Vector;
use IrRegular\Hopper\Ds\Vector\Eager;

class HashMapBased implements SetInterface
{
    /**
     * @var array
     */
    protected $uniqueIndex = [];

    /**
     * @var array
     */
    protected $array = [];

    public function __construct(array $elements, array $uniqueIndex)
    {
        $this->array = $elements;
        $this->uniqueIndex = $uniqueIndex;
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

    // Set does not have a defined access order.
    // To perform in-order processing, you must convert it to a vector.
    // @TODO: conversion methods

    public function foldl(callable $closure, $initialValue)
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot fold');
    }

    public function foldr(callable $closure, $initialValue)
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot fold');
    }

    public function map(callable $closure): Mappable
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot map');
    }

    public function first()
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot retrieve first');
    }

    public function last()
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot retrieve last');
    }

    public function rest(): Sequence
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot retrieve rest');
    }

    public function get($key, $default = null)
    {
        return $this->isKey($key) ? $key : $default;
    }

    public function isKey($key): bool
    {
        if (is_object($key) || is_scalar($key)) {
            // objects or scalars are easy to convert to a string

            if (!is_valid_key($key)) {
                $key = convert_to_key($key);
            }

            assert(is_string($key) || is_int($key));

            return array_key_exists($key, $this->uniqueIndex);

        } else {
            // we don't really want to convert anything more complicated into a string if we don't have to
            // so just search for the value

            return (array_search($key, $this->array, true) !== false);
        }
    }

    public function getKeys(): iterable
    {
        return $this->array;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }

    public function toVector(): Vector
    {
        return new Eager($this->array);
    }
}
