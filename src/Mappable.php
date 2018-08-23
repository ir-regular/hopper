<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Collection\HashMap;

interface Mappable
{
    public function map(callable $closure): \Generator;
}

/**
 * Map function, lazy.
 *
 * @param callable $closure
 * @param iterable $collection
 * @return iterable
 */
function map(callable $closure, iterable $collection): iterable
{
    if ($collection instanceof Mappable) {
        if ($collection instanceof HashMap) {
            $collectionConstructor = '\IrRegular\Hopper\Collection\hash_map';
        } else {
            $collectionConstructor = '\IrRegular\Hopper\Collection\vector';
        }

        $generator = (function () use ($closure, $collection) {
            yield from $collection->map($closure);
        })();

        // Set is currently not mappable (this may change)

    } else {
        // consistent with how `array_map` behaves

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
    }

    // Calling `vector()` or `hash_map()` on the generator creates a LazyHashMap or LazyVector

    return $collectionConstructor($generator);
}
