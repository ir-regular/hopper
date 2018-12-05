<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

interface Indexed extends \ArrayAccess
{
    public function isKey($key): bool;

    public function get($key, $default = null);

    public function getKeys(): iterable;
}
