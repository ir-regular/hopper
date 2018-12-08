<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Collection\HashMap;
use IrRegular\Hopper\Collection\LazyVector;
use IrRegular\Hopper\Collection\Set;
use IrRegular\Hopper\Collection\Vector;

function vector(iterable $collection)
{
    if ($collection instanceof Vector) {
        return $collection;
    } elseif ($collection instanceof HashMap) {
        return $collection->toVector();
    } elseif ($collection instanceof Set) {
        return $collection->toVector();
    }

    if ($collection instanceof \Generator) {
        return new LazyVector($collection);
    }

    // ensure contiguous numeric keys by stripping the existing keys

    if ($collection instanceof \Traversable) {
        $collection = iterator_to_array($collection, false);
    } else {
        assert(is_array($collection));
        $collection = array_values($collection);
    }

    return new Vector($collection);
}
