<?php
declare(strict_types=1);

/**
 * Surprise! There is no Language class or interface.
 *
 * This file collects functions that are related to properties of PHP as a language.
 */

/**
 * A value has to be a non-numeric string for a PHP to treat it as a hash map key (instead of casting to int.)
 *
 * @param mixed $v
 * @return bool
 */
function is_valid_hash_map_key($v): bool
{
    return is_string($v) && !ctype_digit($v);
}

/**
 * @param mixed $v
 * @return string
 */
function convert_to_valid_hash_map_key($v): string
{
    if (is_string($v)) {
        if (ctype_digit($v)) {
            // prefix ensures numeric strings don't get cast to numbers
            return 'ns_' . strval($v);
        } else {
            return $v; // no conversion - no prefix
        }

    } elseif (is_integer($v)) {
        // prefix ensures ints will be treated as strings
        return 'i_' . strval($v);

    } elseif (is_object($v)) {
        return 'o_' . spl_object_hash($v);

    } elseif (is_array($v)) {
        return 'a_' . md5(var_export($v, true)); // ¯\_(ツ)_/¯ I know, risk of collision

    } else {
        // `strval` is safe since it distinguishes between 0 and ''
        return strval($v);
    }
}
