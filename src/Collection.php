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
    } else {
        assert(is_array($collection)); // just in case of future weirdness
        return empty($collection);
    }
}

/**
 * @param iterable $collection
 * @return int
 */
function size(iterable $collection): int
{
    if ($collection instanceof Collection) {
        return $collection->getCount();
    } elseif ($collection instanceof \Iterator) {
        return iterator_count($collection);
    } else {
        assert(is_array($collection));
        return count($collection);
    }
}
