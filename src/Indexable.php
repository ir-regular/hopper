<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

interface Indexable
{
    public function isKey($key): bool;

    public function get($key, $default = null);

    public function getKeys(): iterable;
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
 * Return (eagerly) keys of an indexable collection.
 *
 * For unordered types (Set, HashMap) the order of keys is unspecified.
 * You must not rely on the current order, as it is an implementation detail and may change.
 *
 * @param iterable $collection
 * @return iterable
 */
function keys(iterable $collection): iterable
{
    if ($collection instanceof Indexable) {
        return $collection->getKeys();
    } elseif ($collection instanceof \Traversable) {
        return array_keys(iterator_to_array($collection, true));
    } else {
        assert(is_array($collection));
        return array_keys($collection);
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
