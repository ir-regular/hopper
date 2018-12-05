<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

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
