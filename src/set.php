<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use function IrRegular\Hopper\Collection\HashMap\convert_to_key;
use function IrRegular\Hopper\Collection\HashMap\is_valid_key;
use IrRegular\Hopper\Collection\Set;

function set(iterable $collection)
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

    return new Set($elements, $uniqueIndex);
}
