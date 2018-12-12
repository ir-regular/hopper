<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\HashMap\Eager as EagerHashMap;
use IrRegular\Hopper\Ds\Set;
use IrRegular\Hopper\Ds\Vector;
use IrRegular\Hopper\Ds\Vector\Eager as EagerVector;
use IrRegular\Hopper\Ds\Vector\Lazy as LazyVector;

function vector(iterable $collection): Vector
{
    if ($collection instanceof EagerVector) {
        return $collection;
    } elseif ($collection instanceof EagerHashMap) {
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

    return new EagerVector($collection);
}
