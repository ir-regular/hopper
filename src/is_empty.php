<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

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
