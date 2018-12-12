<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Returns a single-argument function, assuming the provided argument should be injected first to $function.
 *
 * For inspiration, see -> threading macro in Clojure.
 * Functions that require this pattern deal with a colletion as a single element (example: get_in.)
 *
 * @param callable $function
 * @param mixed ...$operands
 * @return callable
 */
function partial_first(callable $function, ...$operands): callable
{
    return function ($x) use ($function, $operands) {
        return $function($x, ...$operands);
    };
}
