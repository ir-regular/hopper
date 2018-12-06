<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Map function, eager.
 *
 * @param callable $closure
 * @param iterable $collection
 * @return iterable
 */
function map(callable $closure, iterable $collection): iterable
{
    if ($collection instanceof Mappable) {
        return $collection->map($closure);
    } elseif (is_array($collection)) {
        return array_map($closure, $collection);
    } else {
        assert($collection instanceof \Traversable);
        // walking manually might be faster for really long collections, but for now, for simplicity...
        $collection = iterator_to_array($collection);
        return array_map($closure, $collection);
    }
}
