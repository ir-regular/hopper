<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

interface ListAccessible extends Collection
{
    public function first();

    public function last();

    public function rest(): ListAccessible;
}

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

/**
 * Returns the second element of the collection
 *
 * Useful when dealing with [key, value] pairs returned by HashMap.
 *
 * @param iterable $collection
 * @return mixed
 */
function second(iterable $collection)
{
    if (is_array($collection)) {
        assert(count($collection) > 1);
        return $collection[1];
    }

    return first(rest($collection));
}

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

/**
 * @param iterable $collection
 * @return iterable
 */
function rest(iterable $collection): iterable
{
    assert(!is_empty($collection));

    if ($collection instanceof ListAccessible) {
        return $collection->rest();
    } elseif ($collection instanceof \Iterator) {
        $collection->next();
        return $collection;
    } else {
        assert(is_array($collection)); // just in case of future weirdness
        return array_slice($collection, 1); // it'll re-index the keys, if numeric
    }
}
