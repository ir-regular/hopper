<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

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
