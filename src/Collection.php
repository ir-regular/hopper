<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Represents a collection that holds elements but does not specify the access method.
 */
interface Collection
{
    public function isEmpty(): bool;

    public function getCount(): int;

    public function getValues(): iterable;
}

/**
 * Predicate check: is the internal collection iterator empty?
 *
 * @param iterable $collection
 *
 * @return bool
 */
function is_empty(iterable $collection): bool
{
    if ($collection instanceof Collection) {
        return $collection->isEmpty();
    } elseif ($collection instanceof \Iterator) { // PSA: \Generator is also an iterator
        return ($collection->valid() === false);
    } elseif ($collection instanceof \Traversable) {
        return (iterator_count($collection) == 0);
    } else {
        assert(is_array($collection)); // just in case of future weirdness
        return empty($collection);
    }
}

class is_empty
{
    use FunctionConstantPolyfillTrait;
}

/**
 * @param iterable $collection
 * @return int
 */
function size(iterable $collection): int
{
    if ($collection instanceof Collection) {
        return $collection->getCount();
    } elseif ($collection instanceof \Traversable) {
        return iterator_count($collection);
    } else {
        assert(is_array($collection));
        return count($collection);
    }
}

class size
{
    use FunctionConstantPolyfillTrait;
}

/**
 * Return (eagerly) values of a collection, resetting the keys to a contiguous numeric range.
 *
 * For unordered types (Set, HashMap) the order of values is unspecified.
 * You must not rely on the current order, as it is an implementation detail and may change.
 *
 * @param iterable $collection
 * @return iterable
 */
function values(iterable $collection): iterable
{
    if ($collection instanceof Collection) {
        return $collection->getValues();
    } elseif ($collection instanceof \Traversable) {
        return iterator_to_array($collection, false);
    } else {
        assert(is_array($collection));
        return array_values($collection);
    }
}

class values
{
    use FunctionConstantPolyfillTrait;
}
