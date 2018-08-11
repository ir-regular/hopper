<?php
declare(strict_types=1);

/**
 * Surprise! There is no Language class or interface.
 *
 * This file collects functions that are related to properties of PHP as a language.
 */

/**
 * A value has to be an int or a string for a PHP array to be indexable as such.
 *
 * @param mixed $v
 * @return bool
 */
function is_valid_array_key($v): bool
{
    return is_int($v) || is_string($v);
}

/**
 * A value has to be a non-numeric string for a PHP to treat it as a hash map key (instead of casting to int.)
 *
 * @param mixed $v
 * @return bool
 */
function is_valid_hash_map_key($v): bool
{
    return is_string($v) && !is_numeric($v);
}

/**
 * @param mixed $v
 * @return string
 */
function convert_to_valid_array_key($v): string
{
    if (is_object($v)) {
        return spl_object_hash($v);

    } elseif (is_array($v)) {
        return md5(var_export($v, true)); // ¯\_(ツ)_/¯ I know, risk of collision

    } elseif (is_string($v)) {
        // prefix ensures numeric strings don't get cast to numbers
        return is_numeric($v) ? 'k_' . strval($v) : $v;

    } else {
        // `strval` is safe since it distinguishes between 0 and ''
        return strval($v);
    }
}
