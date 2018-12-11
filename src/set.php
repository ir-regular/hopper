<?php
declare(strict_types=1);

namespace IrRegular\Hopper;

use IrRegular\Hopper\Ds\Set;
use IrRegular\Hopper\Ds\Set\HashMapBased;

function set(iterable $collection): Set
{
    return new HashMapBased($collection);
}
