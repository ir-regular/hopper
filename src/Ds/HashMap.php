<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

interface HashMap extends Collection, Indexed, Foldable, Mappable
{
    public function toVector(): Vector;
}
