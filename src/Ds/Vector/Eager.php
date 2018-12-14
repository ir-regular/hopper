<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\Vector;

use IrRegular\Hopper\Ds\Lazy as LazyInterface;
use IrRegular\Hopper\Ds\Mappable;
use IrRegular\Hopper\Ds\Sequence;
use IrRegular\Hopper\Ds\Vector;

class Eager implements Vector
{
    /**
     * @var array
     */
    protected $array;

    public function __construct(array $collection)
    {
        $this->array = $collection;
    }

    // Collection

    public function isEmpty(): bool
    {
        return empty($this->array);
    }

    public function contains($value): bool
    {
        return in_array($value, $this->array);
    }

    public function count(): int
    {
        return count($this->array);
    }

    public function getValues(): Sequence
    {
        return $this;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }

    // Sequence

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
        $rest = new self(array_slice($this->array, 1));
        return $rest;
    }

    // Indexed

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

    public function offsetExists($offset)
    {
        return $this->isKey($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Hopper collections are not mutable');
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Hopper collections are not mutable');
    }

    // Foldable

    public function foldl(callable $closure, $initialValue)
    {
        return array_reduce($this->array, $closure, $initialValue);
    }

    public function foldr(callable $closure, $initialValue)
    {
        return array_reduce(array_reverse($this->array), $closure, $initialValue);
    }

    // Mappable

    public function map(callable $closure): Mappable
    {
        $newValues = array_map($closure, $this->array);
        return new self($newValues);
    }

    public function lMap(callable $closure): LazyInterface
    {
        $generator = (function () use ($closure) {
            foreach ($this->array as $value) {
                yield $closure($value);
            }
        })();

        return new Lazy($generator);
    }
}
