<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Sometimes, a library function expects several arguments, but the delivery mechanism
 * enforces that values will be passed in an array (or a similar form.)
 *
 * @param callable $function
 * @param iterable $operands
 * @return mixed
 */
function apply(callable $function, iterable $operands)
{
    $operands = values($operands);

    return $function(...$operands);
}
