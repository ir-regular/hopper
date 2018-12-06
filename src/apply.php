<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Sometimes, a library function expects an array input, but the delivery mechanism enforces
 * that values will be passed in separate arguments. So that's when you use apply, basically.
 *
 * @param callable $function
 * @param mixed ...$operands
 * @return mixed
 */
function apply(callable $function, ...$operands)
{
    return $function($operands);
}
