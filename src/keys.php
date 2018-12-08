<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Return (eagerly) keys of an indexable collection.
 *
 * For unordered types (Set, HashMap) the order of keys is unspecified.
 * You must not rely on the current order, as it is an implementation detail and may change.
 *
 * @param iterable $collection
 * @return iterable
 */
function keys(iterable $collection): iterable
{
    if ($collection instanceof Indexed) {
        return $collection->getKeys();
    } elseif ($collection instanceof \Traversable) {
        return array_keys(iterator_to_array($collection, true));
    } else {
        assert(is_array($collection));
        return array_keys($collection);
    }
}
