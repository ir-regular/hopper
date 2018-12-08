<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use function IrRegular\Hopper\Collection\HashMap\convert_to_key;
use function IrRegular\Hopper\Collection\HashMap\is_valid_key;
use IrRegular\Hopper\Ds\Set;
use IrRegular\Hopper\Ds\Set\HashMapBased;

function set(iterable $collection): Set
{
    $elements    = [];
    $uniqueIndex = [];

    foreach ($collection as $element) {
        $key = is_valid_key($element)
            ? $element
            : convert_to_key($element);

        $elementAdded = !array_key_exists($key, $uniqueIndex);

        if ($elementAdded) {
            $uniqueIndex[$key] = true;
            $elements[] = $element;
        }
    }

    return new HashMapBased($elements, $uniqueIndex);
}
