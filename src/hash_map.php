<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Collection\HashMap;
use IrRegular\Hopper\Collection\HashMap\Lazy;
use function IrRegular\Hopper\Collection\HashMap\convert_to_key;
use function IrRegular\Hopper\Collection\HashMap\is_valid_key;

/**
 * If you use numerical strings as indices, PHP will convert them to ints
 * before handing it over to this function. So, if you really want to have keys like '1', '2', ...
 * you need to supply the keys in the optional $keys argument
 *
 * @param iterable $collection
 * @param iterable|null $keys Keys of the correct, un-cast-by-PHP type.
 * @return HashMap
 */
function hash_map(iterable $collection, iterable $keys = null)
{
    if ($collection instanceof \Generator) {
        return new Lazy($collection);
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

    foreach ($collection as $value) {
        $originalKey = current($keys);
        $safeKey = is_valid_key($originalKey)
            ? $originalKey
            : convert_to_key($originalKey);

        $stringIndex[$safeKey] = $originalKey;
        // why not just supply $collection instead of rebuilding it as $values?
        // because you need to be able to reindex using $keys
        $values[$originalKey] = $value;
        next($keys);
    }

    return new HashMap($values, $stringIndex);
}
