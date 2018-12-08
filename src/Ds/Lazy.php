<?php
declare(strict_types=1);

namespace IrRegular\Hopper\Ds;

interface Lazy extends \Traversable, Collection
{
    public function getGenerator(): \Generator;

    public function lMap(callable $closure): Lazy;
}
