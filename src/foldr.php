<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Right-fold function, eager.
 *
 * @param callable $closure
 * @param mixed $initialValue
 * @param iterable $collection
 * @return mixed
 */
function foldr(callable $closure, $initialValue, iterable $collection)
{
    if ($collection instanceof Foldable) {
        return $collection->foldr($closure, $initialValue);
    } elseif (is_array($collection)) {
        return array_reduce(array_reverse($collection), $closure, $initialValue);
    } else {
        assert($collection instanceof \Traversable);

        $realisedCollection = [];

        foreach ($collection as $key => $element) {
            $realisedCollection[$key] = $element;
        }

        return array_reduce(array_reverse($realisedCollection), $closure, $initialValue);
    }
}
