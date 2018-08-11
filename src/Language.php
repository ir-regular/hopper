<?php
declare(strict_types=1);

/**
 * Surprise! There is no Language class or interface.
 *
 * This file collects functions that are related to properties of PHP as a language.
 */

/**
 * @param mixed $v
 * @return bool
 */
function is_valid_array_key($v): bool
{
    return is_int($v) || is_string($v);
}

/**
 * @param mixed $v
 * @return string
 */
function convert_to_valid_array_key($v): string
{
    return is_object($v)
        ? spl_object_hash($v)
        : is_array($v)
            ? md5(var_export($v, true)) // ¯\_(ツ)_/¯ I know, risk of collision
            : strval($v);
}
