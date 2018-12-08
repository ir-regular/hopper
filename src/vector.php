<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Collection\HashMap;
use IrRegular\Hopper\Collection\Set;
use IrRegular\Hopper\Ds\Vector\Eager;
use IrRegular\Hopper\Ds\Vector\Lazy;

function vector(iterable $collection)
{
    if ($collection instanceof Eager) {
        return $collection;
    } elseif ($collection instanceof HashMap) {
        return $collection->toVector();
    } elseif ($collection instanceof Set) {
        return $collection->toVector();
    }

    if ($collection instanceof \Generator) {
        return new Lazy($collection);
    }

    // ensure contiguous numeric keys by stripping the existing keys

    if ($collection instanceof \Traversable) {
        $collection = iterator_to_array($collection, false);
    } else {
        assert(is_array($collection));
        $collection = array_values($collection);
    }

    return new Eager($collection);
}
