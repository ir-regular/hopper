<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

interface Mappable
{
    public function map(callable $closure): Mappable;
}
