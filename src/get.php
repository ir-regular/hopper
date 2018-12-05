<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Returns an element of $collection indexed by $key, or $default if not found.
 *
 * @param iterable $collection
 * @param mixed $key
 * @param mixed|null $default
 * @return mixed|null
 */
function get(iterable $collection, $key, $default = null)
{
    if ($collection instanceof Indexable) {
        return $collection->get($key, $default);

    } elseif (is_array($collection)) {
        assert(is_int($key) || is_string($key));
        return $collection[$key] ?? $default;

    } else {
        foreach ($collection as $thisKey => $thisValue) {
            if ($key == $thisKey) {
                return $thisValue;
            }
        }

        return $default;
    }
}
