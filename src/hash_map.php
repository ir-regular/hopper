<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\HashMap;
use IrRegular\Hopper\Ds\HashMap\ArrayWrap;
use IrRegular\Hopper\Ds\HashMap\Eager as EagerHashMap;
use IrRegular\Hopper\Ds\HashMap\Lazy as LazyHashMap;
use function IrRegular\Hopper\Language\convert_to_key;
use function IrRegular\Hopper\Language\is_valid_key;

/**
 * If you use numerical strings as indices, PHP will convert them to ints
 * before handing it over to this function. So, if you really want to have keys like '1', '2', ...
 * you need to supply the keys in the optional $keys argument. Also if you want to index
 * by values which are not valid PHP keys, such as objects or arrays.
 *
 * @param iterable $collection
 * @param iterable|null $keys Keys of the correct, un-cast-by-PHP type.
 * @return HashMap
 */
function hash_map(iterable $collection, iterable $keys = null): HashMap
{
    if ($collection instanceof \Generator) {
        return new LazyHashMap($collection);
    }

    if ($collection instanceof \Traversable) {
        $collection = iterator_to_array($collection, true);
    }

    if (empty($keys)) {
        $keys = array_keys($collection);
    } else {
        assert(size($keys) == size($collection));
    }

    // ensure you can perform operations on string keys
    // (but also preserve the original keys with appropriate types)

    $values = [];
    $stringIndex = [];
    $containsUnsafeKeys = false;

    foreach ($collection as $value) {
        $originalKey = current($keys);

        if (is_valid_key($originalKey)) {
            $safeKey = $originalKey;
        } else {
            $safeKey = convert_to_key($originalKey);
            $containsUnsafeKeys = true;
        }

        $stringIndex[$safeKey] = $originalKey;
        $values[$safeKey] = $value;

        next($keys);
    }

    return $containsUnsafeKeys
        ? new EagerHashMap($values, $stringIndex)
        : new ArrayWrap($values);
}
