<?php

namespace IrRegular\Hopper;

/**
 * Aggregate interface that all collections implement.
 *
 * Extends \IteratorAggregate because I want the collections to fit the `iterable` slot,
 * but that's currently underdeveloped because all the hopper functions prefer interacting
 * with appropriate interface methods from `IrRegular\Hopper` interfaces.
 */
interface Collection extends \IteratorAggregate
{
    public function isEmpty(): bool;
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
