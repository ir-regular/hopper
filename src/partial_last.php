<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Returns a single-argument function, assuming the provided argument should be injected last to $function.
 *
 * For inspiration, see ->> threading macro in Clojure.
 * Functions that require this pattern deal with all elements of a collection at once (example: map.)
 *
 * @param callable $function
 * @param mixed ...$operands
 * @return callable
 */
function partial_last(callable $function, ...$operands): callable
{
    return function ($x) use ($function, $operands) {
        $operands[] = $x;
        return $function(...$operands);
    };
}
