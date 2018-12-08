<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\Sequence;

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

    if ($collection instanceof Sequence) {
        return $collection->last();
    } elseif ($collection instanceof \Iterator) {
        do {
            $last = $collection->current();
        } while ($collection->valid());
        // @TODO: this will catch a generator, generators are not rewindable
        //$collection->rewind();
        return $last;
    } elseif ($collection instanceof \Traversable) {
        $collection = iterator_to_array($collection);
    }

    assert(is_array($collection)); // just in case of future weirdness
    $key = array_keys($collection)[count($collection) - 1];
    // $key = array_key_last($collection); // PHP 7.3 ;_;
    return $collection[$key];
}
