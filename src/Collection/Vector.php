<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Collection;

use IrRegular\Hopper\Collection;
use IrRegular\Hopper\Foldable;
use IrRegular\Hopper\Indexed;
use IrRegular\Hopper\Lazy;
use IrRegular\Hopper\Sequence;
use IrRegular\Hopper\Mappable;

class Vector implements Collection, Sequence, Indexed, Mappable, Foldable
{
    /**
     * @var array
     */
    protected $array;

    public function __construct(array $collection)
    {
        $this->array = $collection;
    }

    public function foldl(callable $closure, $initialValue)
    {
        return array_reduce($this->array, $closure, $initialValue);
    }

    public function foldr(callable $closure, $initialValue)
    {
        return array_reduce(array_reverse($this->array), $closure, $initialValue);
    }

    public function map(callable $closure): Lazy
    {
        $generator = (function () use ($closure) {
            foreach ($this->array as $value) {
                yield $closure($value);
            }
        })();

        return new LazyVector($generator);
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

    public function rest(): Sequence
    {
        $rest = new Vector(array_slice($this->array, 1));
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

    public function getKeys(): iterable
    {
        return array_keys($this->array);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }
}
