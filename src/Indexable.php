<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

interface Indexable
{
    public function isKey($key): bool;

    public function get($key, $default = null);
}

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

/**
 * @param iterable $collection
 * @param iterable $path
 * @param mixed|null $default
 * @return mixed|null
 */
function get_in(iterable $collection, iterable $path, $default = null)
{
    foreach ($path as $segment) {
        $collection = get($collection, $segment);

        if ($collection === null) {
            return $default;
        }
    }

    return $collection;
}
