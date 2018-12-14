<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

interface Indexed extends \ArrayAccess, Collection
{
    public function isKey($key): bool;

    public function get($key, $default = null);

    public function getKeys(): Set;
}
