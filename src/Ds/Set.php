<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

interface Set extends Collection, Foldable, Mappable
{
    public function toVector(): Vector;
}
