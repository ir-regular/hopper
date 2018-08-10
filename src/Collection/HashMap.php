<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\Collection;
use IrRegular\Hopper\Foldable;
use IrRegular\Hopper\ListAccessible;
use IrRegular\Hopper\Mappable;

class HashMap implements Collection, Foldable, ListAccessible, Mappable
{
    const KEY_PREFIX = 'k_';

    /**
     * @var array
     */
    public $index;

    /**
     * @var array
     */
    public $array;

    public function __construct(array $a)
    {
        $this->array = $a;

        // ensure you can perform operations on string keys
        // (but also preserve the original keys with appropriate types)

        foreach (array_keys($a) as $originalKey) {
            $safeKey = $this->sanitiseKey($originalKey);
            $this->index[$safeKey] = $originalKey;
        }
    }

    public function foldl(callable $closure, $initialValue)
    {
        return array_reduce(
            $this->getKeyValuePairList(),
            $closure,
            $initialValue
        );
    }

    public function foldr(callable $closure, $initialValue)
    {
        return array_reduce(
            array_reverse($this->getKeyValuePairList()),
            $closure,
            $initialValue
        );
    }

    public function map(callable $closure): \Generator
    {
        foreach ($this->array as $key => $value) {
            yield $key => $closure([$key, $value]);
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->array);
    }

    public function first()
    {
        assert(!empty($this->array));
        $key = array_keys($this->array)[0];
        // $key = array_key_first($this->array); // PHP 7.3
        return [$key, $this->array[$key]];
    }

    public function last()
    {
        assert(!empty($this->array));
        $key = array_keys($this->array)[count($this->array) - 1];
        // $key = array_key_last($this->array); // PHP 7.3
        return [$key, $this->array[$key]];
    }

    public function rest(): ListAccessible
    {
        $rest = new HashMap([]);
        // Amazingly, array_slice _does_ work on arrays with string keys. IKR?!
        $rest->index = array_slice($this->index, 1);
        $rest->array = array_slice($this->array, 1);
        return $rest;
    }

    protected function sanitiseKey($originalKey): string
    {
        // 1. prefix ensures numeric strings don't get cast to numbers
        // 2. `strval` is safe since it distinguishes between 0 and ''
        return self::KEY_PREFIX . strval($originalKey);
    }

    /**
     * Returns an (eagerly generated) array of [key, value] pairs of original array.
     *
     * @return array
     */
    protected function getKeyValuePairList(): array
    {
        return array_map(null, $this->index, $this->array);
    }

    public function getIterator()
    {
        // key-value pairs
        return new \ArrayIterator($this->getKeyValuePairList());
    }
}
