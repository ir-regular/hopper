<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Returns the first element of the collection
 *
 * @param iterable $collection
 * @return mixed
 */
function first(iterable $collection)
{
    assert(!is_empty($collection));

    if ($collection instanceof ListAccessible) {
        return $collection->first();
    } elseif ($collection instanceof \Iterator) {
        return $collection->current();
    } else {
        assert(is_array($collection)); // just in case of future weirdness
        $key = array_keys($collection)[0];
        // $key = array_key_first($collection); // PHP7.3 ;_;
        return $collection[$key];
    }
}
