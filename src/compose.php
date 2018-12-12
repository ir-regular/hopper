<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Returns a function that transforms its input by applying each of $functions in turn.
 *
 * @param callable ...$functions
 * @return callable
 */
function compose(callable ...$functions): callable
{
    return function (...$operands) use ($functions) {
        $result = $operands;

        foreach ($functions as $f) {
            $result = $f(...$operands);
            $operands = [$result];
        }

        return $result;
    };
}
