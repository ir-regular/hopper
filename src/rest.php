<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * @param iterable $collection
 * @return iterable
 */
function rest(iterable $collection): iterable
{
    assert(!is_empty($collection));

    if ($collection instanceof Sequence) {
        return $collection->rest();
    } elseif ($collection instanceof \Iterator) {
        $collection->next();
        return $collection;
    } else {
        assert(is_array($collection)); // just in case of future weirdness
        return array_slice($collection, 1); // it'll re-index the keys, if numeric
    }
}
