<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Language;

/**
 * A value has to be a non-numeric string for a PHP to treat it as a hash map key (instead of casting to int.)
 *
 * @param mixed $v
 * @return bool
 */
function is_valid_key($v): bool
{
    return is_string($v) && !ctype_digit($v);
}
