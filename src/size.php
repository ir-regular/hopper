<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * @param iterable $collection
 * @return int
 */
function size(iterable $collection): int
{
    if ($collection instanceof Collection) {
        return $collection->getCount();
    } elseif ($collection instanceof \Traversable) {
        return iterator_count($collection);
    } else {
        assert(is_array($collection));
        return count($collection);
    }
}
