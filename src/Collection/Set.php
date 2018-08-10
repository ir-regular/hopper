<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\Collection;
use IrRegular\Hopper\Foldable;
use IrRegular\Hopper\Indexable;
use IrRegular\Hopper\ListAccessible;
use IrRegular\Hopper\Mappable;

class Set implements Collection, ListAccessible, Indexable, Foldable, Mappable
{
    /**
     * @var array
     */
    public $uniqueIndex = [];

    /**
     * @var array
     */
    public $array = [];

    public function __construct(array $a)
    {
        array_map([$this, 'addElement'], $a);
    }

    public function isEmpty(): bool
    {
        return empty($this->array);
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

    public function map(callable $closure): \Generator
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

    public function rest(): ListAccessible
    {
        throw new \BadMethodCallException('Set does not have a defined access order: cannot retrieve rest');
    }

    public function get($key, $default = null)
    {
        return $this->isKey($key) ? $key : $default;
    }

    public function isKey($key): bool
    {
        if (!self::isValidArrayKey($key)) {
            $key = $this->convertToArrayKey($key);
        }

        return array_key_exists($key, $this->uniqueIndex);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }

    /**
     * @param mixed $element
     * @return bool
     */
    protected static function isValidArrayKey($element): bool
    {
        return is_int($element) || is_string($element);
    }

    /**
     * @param mixed $v
     * @return string
     */
    protected function convertToArrayKey($v): string
    {
        return is_object($v) ? spl_object_hash($v) : strval($v);
    }

    /**
     * @param mixed $element
     * @return bool Whether the element was added to the set or not (because it already existed in it.)
     */
    protected function addElement($element): bool
    {
        $key = self::isValidArrayKey($element) ? $element : $this->convertToArrayKey($element);

        $elementAdded = !array_key_exists($key, $this->uniqueIndex);

        if ($elementAdded) {
            $this->uniqueIndex[$key] = true;
            $this->array[] = $element;
        }

        return $elementAdded;
    }
}
