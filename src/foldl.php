<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\Foldable;

/**
 * Left-fold/reduce function, eager.
 *
 * @param callable $closure
 * @param mixed $initialValue
 * @param iterable $collection
 * @return mixed
 */
function foldl(callable $closure, $initialValue, iterable $collection)
{
    if ($collection instanceof Foldable) {
        return $collection->foldl($closure, $initialValue);
    } elseif (is_array($collection)) {
        return array_reduce($collection, $closure, $initialValue);
    } else {
        assert($collection instanceof \Traversable);
        $carry = $initialValue;
        foreach ($collection as $key => $element) {
            $carry = $closure($carry, $element, $key);
        }
        return $carry;
    }
}
