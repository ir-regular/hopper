<?php

namespace IrRegular\Hopper;

interface Mappable
{
    public function map(callable $closure): \Generator;
}

/**
 * Map function, lazy.
 *
 * @param callable $closure
 * @param iterable $collection
 * @return \Generator
 */
function map(callable $closure, iterable $collection)
{
    if ($collection instanceof Mappable) {
        yield from $collection->map($closure);

    } elseif (is_iterable($collection)) {
        // consistent with how `array_map` behaves
        foreach ($collection as $key => $element) {
            yield $key => $closure($element);
        }
    }
}
