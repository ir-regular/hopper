<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\Lazy;
use IrRegular\Hopper\Ds\Mappable;

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

    // choose a collection type consistent with how `array_map` behaves
    // (that is, non-contiguous numerical keys are ignored and re-indexed)

    $key = ($collection instanceof \Iterator)
        ? $collection->key()
        : key($collection); // peek at the first key

    if (is_int($key) || ctype_xdigit($key)) {
        $collectionConstructor = '\IrRegular\Hopper\vector';
    } else {
        $collectionConstructor = '\IrRegular\Hopper\hash_map';
    }

    $generator = (function () use ($closure, $collection) {
        foreach ($collection as $key => $element) {
            yield $key => $closure($element);
        }
    })();

    // Calling `vector()` or `hash_map()` on a generator creates a HashMap\Lazy or Vector\Lazy

    return $collectionConstructor($generator);
}
