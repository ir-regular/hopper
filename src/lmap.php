<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\Lazy;

/**
 * Map function, lazy.
 *
 * @param callable $closure
 * @param iterable $collection
 * @return iterable
 */
function lmap(callable $closure, iterable $collection): iterable
{
    if ($collection instanceof Lazy) {
        return $collection->lMap($closure);
    }

    // choose a collection type consistent with how `array_map` behaves
    // (that is, non-contiguous numerical keys are ignored and re-indexed)

    $key = key($collection); // peek at the first key

    if (is_null($key) || is_int($key) || ctype_xdigit($key)) {
        $collectionConstructor = '\IrRegular\Hopper\Collection\vector';
    } else {
        $collectionConstructor = '\IrRegular\Hopper\Collection\hash_map';
    }

    $generator = (function () use ($closure, $collection) {
        foreach ($collection as $key => $element) {
            yield $key => $closure($element);
        }
    })();

    // Calling `vector()` or `hash_map()` on the generator creates a LazyHashMap or LazyVector

    return $collectionConstructor($generator);
}
