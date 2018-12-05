<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Predicate: does $collection contain a particular $key?
 *
 * @param iterable $collection
 * @param mixed $key
 * @return bool
 */
function is_key(iterable $collection, $key): bool
{
    if ($collection instanceof Indexable) {
        return $collection->isKey($key);

    } elseif (is_array($collection)) {
        assert(is_int($key) || is_string($key));
        return array_key_exists($key, $collection);

    } else {
        foreach ($collection as $thisKey => $thisValue) {
            if ($key == $thisKey) {
                return true;
            }
        }

        return false;
    }
}
