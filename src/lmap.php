<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\Lazy;
use IrRegular\Hopper\Ds\Mappable;
use IrRegular\Hopper\Ds\HashMap\Lazy as LazyHashMap;
use IrRegular\Hopper\Ds\Vector\Lazy as LazyVector;

/**
 * Map function, lazy.
 *
 * @param callable $closure
 * @param iterable $collection
 * @return Lazy
 */
function lmap(callable $closure, iterable $collection): Lazy
{
    if ($collection instanceof Mappable) {
        return $collection->lMap($closure);
    }

    $generator = (function () use ($closure, $collection) {
        foreach ($collection as $key => $element) {
            yield $key => $closure($element);
        }
    })();

    // choose a collection type consistent with how `array_map` behaves
    // (that is, non-contiguous numerical keys are ignored and re-indexed)

    $key = ($collection instanceof \Iterator)
        ? $collection->key()
        : key($collection); // peek at the first key

    // null $key means failure; empty collection

    if (is_null($key) || is_int($key) || ctype_xdigit($key)) {
        return new LazyVector($generator);
    } else {
        return new LazyHashMap($generator);
    }
}
