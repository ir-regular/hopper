<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Right-fold function, uses the last element (or key-value pair) of $collection as initial value.
 *
 * @param callable $closure
 * @param iterable $collection
 * @return mixed
 */
function foldr1(callable $closure, iterable $collection)
{
    if (!$collection instanceof Foldable && !is_array($collection)) {
        $realisedCollection = [];

        foreach ($collection as $key => $element) {
            $realisedCollection[$key] = $element;
        }
    }

    return foldr($closure, last($collection), rest($collection));
}
