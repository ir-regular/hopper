<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Returns the last element of the collection.
 *
 * Note that it will realise (and discard most of) the entire iterator, so don't feed it generators.
 *
 * @param iterable $collection
 * @return mixed
 */
function last(iterable $collection)
{
    assert(!is_empty($collection));

    if ($collection instanceof ListAccessible) {
        return $collection->last();
    } elseif ($collection instanceof \Iterator) {
        do {
            $last = $collection->current();
        } while ($collection->valid());
        $collection->rewind();
        return $last;
    } else {
        assert(is_array($collection)); // just in case of future weirdness
        $key = array_keys($collection)[count($collection) - 1];
        // $key = array_key_last($collection); // PHP 7.3 ;_;
        return $collection[$key];
    }
}
