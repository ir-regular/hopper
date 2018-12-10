<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

interface Lazy extends Collection
{
    public function getGenerator(): \Generator;
}
