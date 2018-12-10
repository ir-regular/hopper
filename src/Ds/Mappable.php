<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

interface Mappable extends Collection
{
    public function map(callable $closure): Mappable;

    public function lMap(callable $closure): Lazy;
}
