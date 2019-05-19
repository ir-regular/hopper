<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\HashMap;

use IrRegular\Hopper\Ds\HashMap as HashMapInterface;
use IrRegular\Hopper\Ds\Lazy as LazyInterface;
use IrRegular\Hopper\Ds\Mappable;
use IrRegular\Hopper\Ds\Sequence;
use IrRegular\Hopper\Ds\Set;
use IrRegular\Hopper\Ds\Vector;
use IrRegular\Hopper\Ds\Vector\Eager as EagerVector;
use function IrRegular\Hopper\Language\is_valid_key;
use function IrRegular\Hopper\set;

/**
 * Straightforward HashMap implementation for use when all keys are non-numeric strings.
 *
 * Pretty much a wrapper around a PHP array, thus the name.
 */
class ArrayWrap implements HashMapInterface
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

    public function count(): int
    {
        return count($this->array);
    }

    public function getValues(): Sequence
    {
        return new EagerVector(array_values($this->array));
    }

    public function contains($value): bool
    {
        return in_array($value, $this->array);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getValueKeyPairList());
    }

    // Indexed

    public function get($key, $default = null)
    {
        if (!is_valid_key($key)) {
            return false;
        }

        return $this->array[$key] ?? $default;
    }

    public function isKey($key): bool
    {
        if (!is_valid_key($key)) {
            return false;
        }

        return array_key_exists($key, $this->array);
    }

    public function getKeys(): Set
    {
        return set(array_keys($this->array));
    }

    // ArrayAccess via Indexed

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
        return array_reduce(
            $this->getValueKeyPairList(),
            function ($carry, $pair) use ($closure) {
                return $closure($carry, ...$pair);
            },
            $initialValue
        );
    }

    public function foldr(callable $closure, $initialValue)
    {
        return array_reduce(
            array_reverse($this->getValueKeyPairList()),
            function ($carry, $pair) use ($closure) {
                return $closure($carry, ...$pair);
            },
            $initialValue
        );
    }

    // Mappable

    public function map(callable $closure): Mappable
    {
        // for now, there is no option ot modify indexes
        $collection = $this->getValueKeyPairList($closure);
        return new self($collection);
    }

    public function lMap(callable $closure): LazyInterface
    {
        $generator = (function () use ($closure) {
            foreach ($this->getValueKeyPairList() as [$value, $key]) {
                yield $key => $closure($value, $key);
            }
        })();

        return new Lazy($this->createMutable(), $generator, Lazy::FORMAT_PHP);
    }

    // HashMaph

    public function toVector(): Vector
    {
        return new EagerVector($this->getValueKeyPairList());
    }

    /**
     * Returns an (eagerly generated) array of [value, key] pairs of original array.
     *
     * @param callable|null $callback Apply optional callback to the pairs.
     * @return array
     */
    protected function getValueKeyPairList(?callable $callback = null): array
    {
        $index = array_keys($this->array);
        return array_map($callback, $this->array, $index);
    }

    public function requestMutableAccess(LazyInterface $wrapper)
    {
        $wrapper->
    }

    protected function put($key, $value)
    {
        if (!is_valid_key($key)) {
            throw new \InvalidArgumentException('Invalid key provided');
        }

        $this->array[$key] = $value;
    }
}
