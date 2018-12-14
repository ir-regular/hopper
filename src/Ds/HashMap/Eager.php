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
use function IrRegular\Hopper\Language\convert_to_key;
use function IrRegular\Hopper\Language\is_valid_key;
use function IrRegular\Hopper\set;

class Eager implements HashMapInterface
{
    /**
     * @var array|null
     */
    protected $index;

    /**
     * @var array
     */
    protected $array;

    public function __construct(array $collection, ?array $stringIndex = null)
    {
        $this->array = $collection;
        $this->index = $stringIndex;
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
            if (is_null($this->index)) {
                return $default;
            }

            $key = convert_to_key($key);
        }

        return $this->array[$key] ?? $default;
    }

    public function isKey($key): bool
    {
        if (!is_valid_key($key)) {
            if (is_null($this->index)) {
                return false;
            }

            $key = convert_to_key($key);
            return array_key_exists($key, $this->index);
        } else {
            return array_key_exists($key, $this->array);
        }
    }

    public function getKeys(): Set
    {
        return is_null($this->index)
            ? set(array_keys($this->array))
            : set(array_values($this->index));
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
        $keys = !is_null($this->index) ? $this->index : array_keys($this->array);
        $collection = array_map($closure, $this->array, $keys);

        // for now - preserving keys as they were

        return new self($collection, $this->index);
    }

    public function lMap(callable $closure): LazyInterface
    {
        $generator = (function () use ($closure) {
            foreach ($this->getValueKeyPairList() as [$value, $key]) {
                yield [$closure($value, $key), $key];
            }
        })();

        return new Lazy($generator, Lazy::FORMAT_VK);
    }

    // HashMaph

    public function toVector(): Vector
    {
        return new EagerVector($this->getValueKeyPairList());
    }

    /**
     * Returns an (eagerly generated) array of [value, key] pairs of original array.
     *
     * @return array
     */
    protected function getValueKeyPairList(): array
    {
        $index = is_null($this->index)
            ? array_keys($this->array)
            : $this->index;

        return array_map(null, $this->array, $index);
    }
}
