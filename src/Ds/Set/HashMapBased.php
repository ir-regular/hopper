<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds\Set;

use IrRegular\Hopper\Ds\Lazy;
use function IrRegular\Hopper\Language\convert_to_key;
use function IrRegular\Hopper\Language\is_valid_key;
use IrRegular\Hopper\Ds\Mappable;
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

    // Collection

    public function isEmpty(): bool
    {
        return empty($this->array);
    }

    public function count(): int
    {
        return count($this->array);
    }

    public function contains($value): bool
    {
        if (is_object($value) || is_scalar($value)) {
            // objects or scalars are easy to convert to a string

            if (!is_valid_key($value)) {
                $value = convert_to_key($value);
            }

            assert(is_string($value) || is_int($value));

            return array_key_exists($value, $this->uniqueIndex);

        } else {
            // we don't really want to convert anything more complicated into a string if we don't have to
            // so just search for the value

            return (array_search($value, $this->array, true) !== false);
        }
    }

    public function getValues(): iterable
    {
        return $this->array;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
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
        $newIndex = array_map('\IrRegular\Hopper\Language\convert_to_key', $newValues);

        return new self($newValues, $newIndex);
    }

    public function lMap(callable $closure): Lazy
    {
        $generator = (function () use ($closure) {
            foreach ($this->getIterator() as $element) {
                yield $closure($element);
            }
        })();

        return new Vector\Lazy($generator);
    }

    // Set

    public function toVector(): Vector
    {
        return new Eager($this->array);
    }
}
