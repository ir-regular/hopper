<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\Sequence;

/**
 * Returns the first element of the collection
 *
 * @param iterable $collection
 * @return mixed
 */
function first(iterable $collection)
{
    assert(!is_empty($collection));

    if ($collection instanceof Sequence) {
        return $collection->first();
    } elseif ($collection instanceof \Iterator) {
        return $collection->current();
    } elseif ($collection instanceof \Traversable) {
        $collection = iterator_to_array($collection);
    }

    assert(is_array($collection)); // just in case of future weirdness
    $key = array_keys($collection)[0];
    // $key = array_key_first($collection); // PHP7.3 ;_;
    return $collection[$key];
}
