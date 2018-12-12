<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

interface Sequence extends Collection
{
    public function first();

    public function last();

    public function rest(): Sequence;
}
