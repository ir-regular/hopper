<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

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
