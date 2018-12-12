<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Left-fold/reduce function, uses the first element (or key-value pair) of $collection as initial value.
 *
 * @param callable $closure
 * @param iterable $collection
 * @return mixed
 */
function foldl1(callable $closure, iterable $collection)
{
    return foldl($closure, first($collection), rest($collection));
}
