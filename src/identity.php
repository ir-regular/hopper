<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

/**
 * Returns the same value it was passed.
 *
 * Attn: pass-by-value, not reference; copying will occur.
 *
 * @param mixed $x
 * @return mixed
 */
function identity($x)
{
    return $x;
}
