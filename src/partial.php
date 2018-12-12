<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Returns a function with a number of arguments pre-filled.
 *
 * If $function accepts N arguments, and you provide M $operands,
 * the returned function will require (N-M) arguments.
 *
 * @param callable $function
 * @param mixed ...$operands
 * @return callable
 */
function partial(callable $function, ...$operands): callable
{
    return function (...$moreOperands) use ($function, $operands) {
        return $function(...$operands, ...$moreOperands);
    };
}
