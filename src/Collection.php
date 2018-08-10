<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Represents a collection that holds elements but does not specify the access method.
 */
interface Collection
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
